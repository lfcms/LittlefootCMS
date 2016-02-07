<?php

namespace lf;

/**
 * # cms class
 * 
 * The `cms` class is used primarily to execute apps assigned to navigation items that are browsed by their alias (eg, '/blog' has the `blog` app assigned to it, so when the page loads, the linked app will return its resulting output and then render it the assigned theme). All the operations used to perform this are public.
 * 
 * this is the main class (formerly class littlefoot) that handles all the aspects of the higher level CMS operations such as lf_actions navigation selection from request_uri (formerly littlefoot->navSelect()), app loading from `lf_links`, skin rendering, acl testing (should really be its own class). \lf\Cms
 * 
 * 
 */

// I am really into shortcut functions
function getSetting($name)
{
	return (new \lf\cms)->getSetting($name);
}
 
class cms
{
	// simple CMS. 
	private $exec = '_lfcms';
	static private $instances = array(); // session instances.
	private $ini = NULL;	// configurable string in database per app `lf_links` table entry
	
	private $version = NULL; // lfcms release version
	
	
	// would replace (new littlefoot)->cms()
	public function run()
	{
		(new cache)->startTimer('cms');
		(new install)->test();
		
		set('request', (new request)->parseUri() );
		
		$this->loadVersion() 				// load version from LF/system/version file
			->loadPlugins() 				// load plugins from `lf_plugins` table
			->loadSettings()				// load settings from `lf_settings` table
			->route( (new auth), '_auth', false ); // Route auth() class per $wwwIndex/_auth/$method
		
		set( 'acl', (new acl)->loadAcl() ); // load acl object into session
		
		$this->routeAdmin()					// If /admin was requested, load it and stop here
			->navSelect()					// Get data for SimpleCMS, or determine requested Nav ID from url $actions
			->getcontent(); 				// Deal with SimpleCMS or execute linked apps
		
		echo $this->render(); 				// Display content in skin, return HTML output result

		(new cache)->endTimer('cms');
		
		if($this->debug == 'on') 
			$this->printDebug();
		
		return $this;
	}
	
	// print HTML comment at the bottom of the source
	// display cool stats and list of required files
	public function printDebug()
	{
		$exectime = round((new \lf\cache)->getTimerResult('cms'), 6)*(1000);
		$memusage = round(memory_get_peak_usage()/1024/1024,2);
		include LF.'system/template/debug.php';
	}
	
	public function loadVersion()
	{
		$this->version = file_get_contents(LF.'system/version');
		return $this;
	}
	
	public function getVersion()
	{
		if( is_null( $this->version ) )
			$this->loadVersion();
		
		return $this->version;
	}
	
	public function simpleCms($app)
	{
		$this->exec = $app;
	}
	
	public function routeAdmin()
	{
		// if request is detected as an 'admin' request...
		if( get('request')->isAdmin() )
		{
			chdir(LF.'system/admin');
			include 'index.php';
			exit;
		}
		// otherwise, return self
		return $this;
	}
	
	public function loadSettings()
	{
		$this->hook_run('pre settings');
		
		foreach( (new \LfSettings)->getAll() as $setting )
			$settings[$setting['var']] = $setting['val'];
		
		set('settings', $settings);
		
		if( isset($settings['debug']) )
			set('debug', $settings['debug']);
		
		return $this;
	}
	
	public function getSettings()
	{
		$settings = get('settings'); // try to get from session
		if( is_null($settings) )
		{
			$this->loadSettings();	// push to session
			$settings = get('settings'); // get from resulting session
		}
		
		return $settings;
	}
	
	public function getSetting($name)
	{
		$settings = $this->getSettings();
		return $settings[$name];
	}
	
	/**
	 * Instant MVC: Routing URL request to class methods. Auto load given class name in controller/ folder as Littlefoot app. 
	 *
	 * 
	 * ## Usage
	 *
	 * ~~~
	 * echo $this->mvc($controllerName); 
	 * ~~~
	 * 
	 * ## Backend operation
	 * 
	 * ~~~
	 * include "controller/$controllerName.php";
	 * $class = new $controllerName($this[, $ini[, $vars]])
	 * ~~~
	 *
	 *
	 *
	 * @param string $controller Executes $controller->$vars[0]\(\) defined at ./controller/$controller.php
	 * 
	 * @param string $ini `= '' (by default)` App configuration set [Dashboard](http://littlefootcms.com/byid/24). Used in 'Pages' app to select the page to display on the website.
	 *
	 * @param string[] $vars `= NULL (by default)` The "slash separated" list of strings in the URL after the Navigation alias.
	 *		ie. domain.com/littlefoot/appNavAlias/$vars[0]/$vars[1] 
	 *		defaults to $this->$vars generated in $this->request()
	 *
	 */
	public function mvc($controller, $ini = '', $param = NULL)
	{
		ob_start();
		
		if($param === NULL)
			$param = (new \lf\request)->get('wwwParam');
		
		if(!isset($param[0])) 
			$param[0] = '';
		
		if(is_callable(array($controller, $param[0])))
			$method = $param[0];
		else
		{
			if(isset($controller->allow404)) 
				return 404; // rewrite by default
			
			// if the $obj specifies a default method,
			if(isset($controller->default_method)) 
				$method = $controller->default_method; // use it
			else
				$method = 'main'; // default to main()
		}
		
		/*$this->hook_run('pre app');
		$this->hook_run('pre app '.$controller);
		if($func != $param[0]) $this->hook_run('pre app '.$controller.' '.$func);
		
		$varstr = array();
		foreach($param as $var) // add action until they are all there
		{
			$varstr[] = $var;
			$this->hook_run('pre app '.$controller.' '.implode(' ', $varstr));
		}*/
		
		// auto-run init() function if its there
		if(is_callable(array($controller, 'init')))
			echo $controller->init();
		
		echo $controller->$method();
		
		/*while(count($varstr)) // subtract action until they are all gone
		{	
			$this->hook_run('post app '.$controller.' '.implode(' ', $varstr));
			array_pop($varstr);
		}
		
		if($func != $param[0]) $this->hook_run('post app '.$controller.' '.$func);
		
		$this->hook_run('post app '.$controller);
		$this->hook_run('post app');*/
		
		return ob_get_clean();
	}
	
	// Routing URL based on /subdir/action1/param1/method1/param2
	// I moved this from app, but dont plan on actually fixing it until I need it again.
	// I think when I wrote this, I was doing something studid and had to work around it.
	public function router($args, $default_route = 'home', $filter = array())
	{
		(new \lf\request)->set('instbase', $this->appurl.$args[0].'/'); // url lf->appurl to all
		$this->inst = urldecode($args[0]); // can handle any string
		
		// Load 
		$args = array_slice($args, 1); // move vars over to emulate direct execution
		
		/** @var string variable used to execute method based on $default_route( or $args[0] if set) */
		$method = $default_route;
		
		// if a base variable is specified,
		if(isset($args[0])) 
			// if no filter is specified,
			if($filter == array()) 
				$method = $args[0];
			// if $filter has more than no elements and $args[0] is in the filter,
			else if(in_array($args[0], $filter)) 
				$method = $args[0];
		
		// begin output capture
		ob_start();
		
		// execute given method of $this object
		$this->$method($args);
		
		// replace appurl with instance base and return
		return str_replace('%insturl%', $this->instbase, ob_get_clean()); 
	}
	
	public function multiMVC($default = NULL, $section = 'content')
	{
		// Get a list of admin tools
		foreach(scandir('controller') as $controller)
		{
			if($controller[0] == '.') continue;
			$controllers[] = str_replace('.php', '', $controller);
		}

		// Check for valid request
		$success = preg_match(
			'/^('.implode('|',$controllers).')$/', 
			www('Action')[0], 
			$match
		);

		// default to dashboard class
		if(!$success and !is_null($default)) 
			$match[0] = $default;

		$class = $match[0];
		
		$save = get('request');
		get('request')->actionPop( count(www('Action')) - 1 ); // move the last item off action to param
		
		//$this->vars = array_slice(www('Action'), 1);
		
		include "controller/$class.php";
		$MVCresult = $this->mvc(new $class);
		
		$this->setContent( 
			str_replace('%appurl%', wwwAppUrl(), $MVCresult ), 
			$section
		);
		
		set('request', $save);
		
		return $this;
	}
	
	
	// you need to include the class .php file yourself.
	/**
	 * Test URL for given alias (default to given class) in action[0]
	 * 
	 * @param $class Instantiated object with public methods intended to execute based on `wwwIndexAction();`
	 * @param $alias Defaults to $class. This only routes if we find $alias in $action[0].
	 * @param $return bool "The output of this should be returned as a string rather than immediately rendering and exiting".
	 */
	public function route($class, $alias = NULL, $return = true)
	{
		$className = get_class($class);
		$this->hook_run('pre '.$className);
		(new cache)->startTimer(__METHOD__);
		
		// use class name as alias by default
		if(is_null($alias))
			$alias = str_replace('\\', '_', $className);
		
		// store request state before upcoming alteration
		$preRequest = get('request');
		
		// get the current wwwAction
		$actionArray = $preRequest->get('wwwAction');
		
		// if the request matches even the first part of the action
		if( $actionArray[0] == $alias )
		{
			// so we can revert after this operation if we just return as a string
			$tempRequest = $preRequest;
			
			$tempRequest
				->actionPop()
				->toSession();
		
			$this->content['nav'][] = $this->renderNavCache();
			$this->content['content'][] = $this->mvc($class);
			
			if(!$return)
			{
				// display in skin
				(new cache)->endTimer(__METHOD__);
				echo $this->render();
				exit();
			}
		}
		
		//$preRequest->toSession();
		
		(new cache)->endTimer(__METHOD__);
		$this->hook_run('post '.$className);
		return $this;
	}
	
	public function getNavCache()
	{
		return (new cache)->readFile('nav.cache.html');
	}
	
	public function renderNavCache()
	{
		return $this->renderBaseUrl( $this->getNavCache() );
	}
	
	public function renderBaseUrl($text)
	{
		return str_replace('%baseurl%', www('Index'), $text);
	}
	
	public function printLogin()
	{
		include LF.'system/template/login.php';
		return $this;
	}
	
	public function headAppend($content)
	{
		$this->head[] = $content;
		return $this;
	}
	
	public function loadStylesheet($url)
	{
		$this->headAppend('<link rel="stylesheet" href="'.$url.'apps/git/git.css" />');
		return $this;
	}
	
	public function getLogin()
	{
		ob_start();
		$this->printLogin();
		return ob_get_clean();
	}
	
	public function getSkinBase()
	{
		return www('LF').'skins/'.$this->getTemplateName().'/';
	}
	
	public function setTitle($newTitle)
	{
		$this->select['title'] = $newTitle;
		return $this;
	}
	
	public function getTitle()
	{
		return $this->select['title'];
	}
	
	public function fromSession()
	{
		return (new cache)->sessGet('cms');
	}
	
	public function toSession()
	{
		return (new cache)->sessSet('cms', $this);
	}
	
	public function printContent($key = 'content')
	{
		if(isset($this->content[$key]))
			return implode($this->content[$key]);
		else
			return 'Content not found: No such key "'.$key.'" set';
		
			/*foreach($this->content as $key => $value)
				$template = str_replace($key, implode($value), $template);*/
	}
	
	public function loadLfCSS()
	{
		$this->head .=  
			'<link rel="stylesheet" href="'.www('LF').'system/lib/lf.css" />
			<link rel="stylesheet" href="'.www('LF').'system/lib/3rdparty/icons.css" />';
			
		return $this;
	}
	
	public function getTemplateName()
	{
		if(!isset($this->select['template']))
			$this->select['template'] = $this->getSettings()['default_skin'];
		
		return $this->select['template'];
	}
	
	public function getTemplatePath()
	{
		if( get('request')->isAdmin() )
			return LF.'system/admin/skin/'.$this->getTemplateName();
		
		return LF.'skins/'.$this->getTemplateName();
	}
	
	public function homeTest()
	{
		// Determine if home.php should be loaded
		$file = 'index';
		if( isset($this->select['parent']) 
			&& $this->select['parent'] == -1 
			&& $this->select['position'] == 1 
			&& ( is_file($this->getTemplatePath().'/home.php') 
				|| is_file($this->getTemplatePath().'/home.html')
			)
		)
			$file = 'home';
			
		return $file;
	}
	
	// Load skin data
	public function readTemplate()
	{
		// can we use home.php for this request?
		// it lets us have a unique home page.
		$file = $this->homeTest();
		
		ob_start();
		
		//pre($this->content);
		if(is_file($this->getTemplatePath()."/$file.php")) // allow php
			include($this->getTemplatePath()."/$file.php");
		else if(is_file($this->getTemplatePath()."/$file.html"))
			readfile($this->getTemplatePath()."/$file.html");
		else
			echo 'Template file "'.LF.'skins/'.$skin."/$file.php".'" missing. Log into admin and select a different template with the Skins tool.';
			
		return ob_get_clean();
	}
	
	// to append the 'title' variable set in `lf_settings` to whatever title is already in place.
	public function appendSiteTitle()
	{
		if(isset($this->settings['title']) && $this->settings['title'] != '')
			$this->select['title'] .= ' | '.$this->settings['title'];
		
		return $this;
	}
	
	public function searchEngineBlocker()
	{
		// Search engine blocker
		if(isset($this->settings['bots']) && $this->settings['bots'] == 'on')
			$this->head .= '<meta name="robots" content="noindex, nofollow">';
		
		return $this;
	}
	
	// you can render from a different LF folder. Just chdir to it before render, and it will not know you moved somewhere. this works in the index.php as well
	public function render()
	{
		(new \lf\cache)->startTimer(__METHOD__);
		$this->hook_run('pre lf render')
			->loadLfCSS()
			->appendSiteTitle()
			->searchEngineBlocker();
		
		
		$template = $this->readTemplate();
		
		$template = str_replace('<head>', '<head>'.$this->head, $template);
		
		
		
		$this->hook_run('post lf render');
		(new \lf\cache)->endTimer(__METHOD__);
		return $template;
	}
	
	/**
	 * Initializes the 'active plugin list' from `lf_plugins` table
	 */
	public function loadPlugins()
	{
		$result = (new \LfPlugins)->getAll();
		
		if($result)
			foreach($result as $plugin)
				$plugins[ $plugin['hook'] ][ $plugin['plugin'] ] = $plugin['config'];
		
		$this->hook_run('plugins loaded');
		
		return $this;
	}
	
	/**
	 * checks for and executes an active plugin assigned to the triggered $hook
	 */
	public function hook_run($hook)
	{
		if(!isset($this->plugins[$hook])) 
			return $this;
		
		foreach($this->plugins[$hook] as $plugin => $config)
		{
			$hookDetails = ' / '.$plugin.' @ '.$hook.' / Config: '.$config;
			
			(new \lf\cache)->startTimer(__METHOD__.$hookDetails);
			include ROOT.'plugins/'.$plugin.'/index.php';
			(new \lf\cache)->endTimer(__METHOD__.$hookDetails);
		}
		
		return $this;
	}
	
	// Simple CMS item selection
	public function simpleSelect($app = NULL)
	{
		// If this is being called without an app listed, assume simpleCMS
		if(is_null($app))
			$app = $this->settings['simple_cms'];
		
		$this->select['template'] = $this->getSetting('default_skin');
		$this->select['title'] = $app;
		$this->select['alias'] = '';
		
		// shift everything to param off `/`
		get('request')->fullActionPop();
		
		return $this;
	}
	
	/**
	 * navSelect
	 * 
	 * TODO: need to utilize cache instead of query
	 * 
	 * Something like 
	 *
	 * ```
	 * // something/(like/(this/)?)?)
	 * $regex = '/'.implode('\/(', www('Action')s).str_repeat( ')?' , count(www('Action')s) - 1 ).'/';
	 * preg_match_all($regex, $navcache, $selected); // would yeild all parents and chosen navigation item,
	 * 												// exposing the other actions as app variables.
	 * ```
	 * 
	 * I should really put this into a github issue...
	 * 
	 */
	
	// TODO: need to utilize cache instead of query
	// Something like 
	/*
	
	// something/(like/(this/)?)?)
	$regex = '/'.implode('\/(', www('Action')s).str_repeat( ')?' , count(www('Action')s) - 1 ).'/';
	preg_match_all($regex, $navcache, $selected); // would yeild all parents and chosen navigation item,
													// exposing the other actions as app variables.
	*/
	public function navSelect()
	{
		(new cache)->startTimer(__METHOD__);
		if(getSetting('simple_cms') != '_lfcms')
			$this->simpleSelect();
		
		/* Determine requested nav item from lf_actions */
		// get all possible matches for current request, 
		// always grab the first one in case nothing is selected
		$matches = (new orm)->fetchAll("
			SELECT * FROM lf_actions 
			WHERE alias IN ('".implode("', '", www('Action') )."') 
				OR (position = 1 AND parent = -1)
			ORDER BY  ABS(parent), ABS(position) ASC
		");
		
		
		
		// Assign as parent,position => array()
		$base_save = NULL;
		foreach($matches as $row)
		{
			// save item in first spot of base menu if it is an app, 
			// just in case nothing matches
			if($row['position'] == 1 && $row['parent'] == -1)
				// save row in case "domain.com/" is requested
				$base_save = $row;
				
			$test_select[$row['parent']][$row['position']] = $row;
		}
		
		// loop through action to determine selected nav
		// trace down to last child
		
		$parent = -1; // start at the root nav items
		$selected = array(); // nothing selected to start with
		for($i = 0; $i < count(www('Action')); $i++) // loop through action array
			if(isset($test_select[$parent])) // if our compiled parent->position matrix has this parent set
				foreach($test_select[$parent] as $position => $nav)	// loop through child items 
					if($nav['alias'] == www('Action')[$i]) // to find each navigation item in the hierarchy
					{
						// we found the match, 
						// move on to next action item matching
						$selected[] = $nav;
						$parent = $nav['id'];
						break;
					}
		
		/*		
		pre('TEST SELECT');
		pre($test_select,'var_dump');
		pre('MATCHES');
		pre($matches,'var_dump');
		pre('SELECTED');
		pre($selected,'var_dump');
		pre('REQUEST');
		pre(get('request'),'var_dump');
		*/
		
		// if a selection was made, alter the action so it has proper params
		if($selected != array())
		{
			// separate action into vars and action base, 
			// pull select nav from inner most child
			get('request')->actionPop( count(www('Action')) - count($selected) );
			
			// This is where we find which navigation item we are visiting
			$this->select = end($selected);
		}
		
		
		// If home page is an app and no select was made from getnav(), 
		// set current page as /
		if($this->select['alias'] == '404' && $base_save != NULL)
		{		
			get('request')->fullActionPop(); // pop all actions into param
			$this->select = $base_save;
		}
		
		//pre(get('request'),'var_dump');
		//pre($this->select,'var_dump');
		
		// in case the file doesn't exist
		
		if(!is_file(ROOT.'cache/nav.cache.html')) 
		{
			$pwd = getcwd();
			chdir(ROOT.'system/admin/');
			$this->mvc('dashboard', NULL, array('updatenavcache')); // run the nav HTML generation script
			chdir($pwd);
		}
		
		$nav_cache = file_get_contents(ROOT.'cache/nav.cache.html'); // Pull cached navigation HTML output rather than generate it on the fly.
		
		// Update nav_cache to show active items
		
		$actionbuilder = '%baseurl%'; // Start with reference to installation base
		foreach( get('request')->get('wwwAction') as $action)
		{
			if($action != '')	// Account for empty alias
				$actionbuilder .= $action.'/';	// Loop through the full/path. 
												
			// As the action request URI builds, replace each link matching that set to active.
			$nav_cache = str_replace(
				'<li><a href="'.$actionbuilder.'"', 
				'<li class="active"><a href="'.$actionbuilder.'"', 
				$nav_cache);
		}
		
		$this->resolveDefaultSkin();
		
		// set nav ul class if set
		// Apply class to root <ul> if it is set
		$nav_cache = isset($this->settings['nav_class']) 
			? preg_replace('/^<ul>/', '<ul class="'.$this->settings['nav_class'].'">', $nav_cache )
			: $nav_cache;
		
		// if no items match the request, return 404
		if($this->select['alias'] == '404')
		{
			header('HTTP/1.1 404 Not Found');
			echo '<p>LF 404: No menu items match your request</p>';
			return 0;
		}
		
		$this->content['nav'][] = $this->renderBaseUrl($nav_cache); // replace the %baseurl% placeholders
		
		
		
		(new cache)->endTimer(__METHOD__);
		return $this;
	}
	
	public function resolveDefaultSkin()
	{
		// If template has not be changed from 'default', set as configured default_skin.
		if($this->select['template'] == 'default')
			$this->select['template'] = $this->settings['default_skin'];
		
		return $this;
	}
	
	// Return sorted list of actions
	public function getSortedActions()
	{
		// Query lf_actions navigation items, 
		$result = (new \LfActions)
			->byPosition('!=', 0)
			->order('ABS(position)')
			->getAll();
		
		if( is_null( $result ) )
			return NULL;
		
		// loop to generate parent => child hierarchy
		$actions = array();
		foreach($result as $action)
			$actions[$action['parent']][] = $action;
		
		return $actions;
	}
	
	// Return list of hidden actions
	public function getHiddenActions()
	{
		return (new \LfActions)
			->byPosition(0)
			->order('label')
			->getAll();
	}
	
	// Return a list of links sorted by nav id assignment
	public function getLinks()
	{
		// Pull lf_links, reorganize as $nav_id => $linkdata[]
		$result = (new \LfLinks)->getAll();
		$links = array();
		foreach($result as $link)
			$links[$link['include']][] = $link;
			
		return $links;
	}
	
	public function setContent($data, $namespace = 'content')
	{
		$this->content[$namespace][] = $data;
		return $this;
	}
	
	public function getcontent()
	{
		(new cache)->startTimer(__METHOD__);
		$funcstart = microtime(true);
		$this->hook_run('pre '.__METHOD__);
		
		if( ! get('acl')->aclTest( implode('/', www('Action') ) ) )
		{
			$this->setContent( "401 Unauthorized at ".wwwIndexAction().$this->getLogin() );
			return $this;
		}
		
		// Pull $apps list with section=>app
		if(getSetting('simple_cms') != '_lfcms') #DEV
		{
			$apps[0] = array(
				'id' => 0, 
				'app' => $this->settings['simple_cms'],
				'ini' => '',
				'section' => 'content'
			);
		}
		else
		{
			$sql = "
				SELECT id, app, ini, section 
				FROM lf_links
				WHERE include = '".$this->select['id']."'
					OR include = '%'
				ORDER BY id
			";
			
			// Grab all active possible connections to currently selected menu item
			$apps = (new orm)->fetchall($sql);
		}
		
		// run them and save the output
		if(isset($this->content))
			$content = $this->content;
		else
			$content = array();
		
		$vars = www('Param');
		foreach($apps as $_app)
		{
				
			// Test ACL for this app
			/*if( ! get('acl')->aclTest(implode('/', www('Action')).'|'.$_app['app'] ) 
				|| ( isset($vars[0]) 
					&& get('acl')->aclTest(implode('/', www('Action')).'|'.$_app['app'].'/'.$vars[0])
			))
			{
				$this->setContent("403 Access Denied ".$this->getLogin(), $_app['section']);
				continue;
			}*/
			
			// set app target path
			$path = ROOT.'apps/'.$_app['app'];
			if(!is_file($path.'/index.php')) continue;
			
			// figure out appurl (/action1/action2/ referring to this app)
			$appurl = wwwIndexAction();
			if(www('Action')[0] != '') 
				$appurl .= '/'; // account for home page
			//pre($appurl);
			set('appurl', $appurl);
			
			// appbase (relbase for the app)
			$appbase = $this->relbase.implode('/',www('Action'));
			if(www('Action')[0] != '') 
				$appbase .= '/'; // account for home page
			$this->appbase = $appbase;
			
			// collect app output
			ob_start();
			chdir($path); // set current working dir to app base path
			$start = microtime(true); // timer for app
			
			
			$apptimer = __METHOD__.' / Link Id: '.$_app['id']
			.', App: '.$_app['app']
			.', Position: '.$_app['section']
			.', Config: '.$_app['ini'];
			
			(new cache)->startTimer($apptimer);
			include 'index.php'; // execute app
			(new cache)->endTimer($apptimer);
			
			$output = '
				<div id="'.$_app['app'].'-'.$_app['id'].'" class="app-'.$_app['app'].'">'.
					ob_get_clean().
				'</div>';
			
			// replace %keywords%
			$output = str_replace(
				'%appurl%', 
				get('appurl'), 
				$output
			);
			
			// and save
			$content[$_app['section']][] = $output;
			
			// reset for next go around
			$this->appurl = '';
		
		}
		
		chdir(LF); // cd back to LF root for the rest of the execution
		
		$this->content = $content;
		
		$this->hook_run('post lf getcontent');
		
		if($this->settings['simple_cms'] == '_lfcms') 		// If simple CMS is not set, add 'nav' to final output content array.
			$this->content['nav'][] = $this->nav_cache;
		
		
		(new cache)->endTimer(__METHOD__);
		
		return $this;
	}
	
	/**
	 * Used for loading partial views given an argument
	 * 
	 * @param string $file The name of the view. Loaded from view/$file.php
	 * @param array $args Associative array of $var => $val passed to the partial.
	 */
	public function partial($partial, $args = array())
	{
		foreach($args as $var => $val)
			$$var = $val;
			
		ob_start();
		include 'view/'.$partial.'.php';
		return ob_get_clean();
	}
}