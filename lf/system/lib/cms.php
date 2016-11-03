<?php

namespace lf;

/**
 * A shortcut for (new \lf\cms)->getSetting() 
 * 
 * @param $name Name of the setting to retrieve
 * 
 */
function getSetting($name)
{
	return (new \lf\cms)->getSetting($name);
}

/**
 * # cms class
 * 
 * The `cms` class is used primarily to execute apps assigned to navigation items that are browsed by their alias (eg, '/blog' has the `blog` app assigned to it, so when the page loads, the linked app will return its resulting output and then render it the assigned theme). All the operations used to perform this are public.
 * 
 * this is the main class (formerly class littlefoot) that handles all the aspects of the higher level CMS operations such as lf_actions navigation selection from request_uri (formerly littlefoot->navSelect()), app loading from `lf_links`, skin rendering, acl testing (should really be its own class). \lf\Cms
 * 
 * LEGACY APP DOX
 * 
 *  * A base class definition meant to be extended in littlefoot apps
 *
 * # How to App
 *
 * When you `extend app` brings a lot of helpful tools into your environment.
 * Here is a list of what is accessible when developing a littlefoot app. All that follows is accessible in the app's index.php as well.
 *
 * ## $this->
 * 
 * There are several objects available in your environment:
 * 
 * * **$this->lf**: [Littlefoot](http://littlefootcms.com/files/docs/classes/Littlefoot.html) instance
 * 	* ->appurl (rendering of %appurl% without waiting for $lf->render(). Ideal for use with [redirect302();](http://littlefootcms.com/files/docs/index.html#method_redirect302) )
 * 	* ->baseurl (%baseurl%)
 * 	* ->relbase (%relbase%)
 * * **$this->db**: [Database](http://littlefootcms.com/files/docs/classes/Database.html) instance
 * * **$this->auth**: [Auth](http://littlefootcms.com/files/docs/classes/auth.html) instance
 * 
 * ## Static
 * 
 * Classes meant to be called statically:
 * 
 * * [**orm**]((http://littlefootcms.com/files/docs/classes/orm.html#method_q))::q('table_name')
 * * [**dba**](#)::Table_name
 *
 * ## init()
 * 
 * If you need something to run before all functions, you can do it in an 'init()' function. This function is automatically called from __construct() just before any other controller is executed.
 *
 * ## $args
 * 
 * Given `domain.com/myapp/view/5`
 * 
 * ~~~
 * $this->lf->action = array('myapp');
 * $this->lf->vars = array('view', '5');
 * ~~~
 * 
 * Littlefoot serves requests based on the URL. For example, if we ask for "domain.com/blog", it knows to serve "/blog". The "/blog" navigation item has been associated with the "Blog" app in the admin backend. So we wind up being presented with Blog. Simple enough right? Now lets get to $args.

 * The Blog navigation item has been set as an "app". This means that any extra part of the URL past the matching "/blog" request is taken as variables for the "Blog" app rather than a separate navigation item. Alternatively, if it was not set as an "app", we could make child navigation items and serve them without conflict.

 * If we ask for "domain.com/blog/view/5", the "view/5" is taken as a variable for the Blog app. The Blog app utilizes a Model-View-Controller structure and uses its class methods as a kind of router. The variable taken from the url is split on the "/" to give us an array of "(view, 5)". The first part, "view", is used to determine which method of Blog to serve. In this case, it will use the view() method and since the "5" is specified, the Blog is programmed to serve the Blog post with an "id" of 5.
 *
 * ## MVC
 *
 * The URI is chopped up into arguments and those arguments route the controller. 
 * 
 * ### URI routing to controller class methods
 * 
 * Create a file at `ROOT/apps/myapp/index.php` that contains
 * ~~~
 * <?php echo $this->lf->mvc('myapp');
 * ~~~
 * 
 * Then at `ROOT/apps/myapp/controller/myapp.php`, create a file with the following code:
 * 
 * ~~~
 * <?php
 *
 * class myapp {
 *		public function main() {
 *			echo '<a href="%appurl%otherfunction/1">otherfunction</a>';
 * 		}
 *
 *		public function otherfunction() {
 *			echo '<a href="%appurl%">back to main</a>';
 * 		}
 * }
 * ~~~
 *
 * If you go into the [Dashboard](http://littlefootcms.com/byid/24) and assign this app to the navigation, a link will appear on the navigation. When you click it, the screen will display the content of $args 
 * 
 * I strongly recommend reviewing the code of the [Pages](https://github.com/bioshazard/pages) app. It is an excellent example of the intended use of `mvc()`
 * 
 * With the above app assigned in nav at `/theapp/`, the above renders as follows:
 * 
 * ~~~
 * <a href="http://domain.com/theapp/otherfunction">otherfunction</a>
 * ~~~
 * 
 * If you click this link, the app will render as follows:
 * 
 * ~~~
 * <a href="http://domain.com/theapp/">main</a>
 * <pre>
 * array(0 => 'otherfunction', 1 => '1')
 * </pre>
 * ~~~
 * 
 * And links back to the main function. Follow the [Littlefoot app tutorial]() for a more in-depth guide.
 * 
 * ## Variable Scope
 * 
 * If you need a value to be accessible throughout an app (including within a partial) without needing to pass by value or reference, you can simply set the variable as such:
 * 
 * `$this->mySpecialVar = 'some value I want to use everywhere, or just for this part';`
 * `$this->myOtherSpecialVar = array('something' => 'cool');`
 * 
 * And these will be accessible everywhere in the app (helpful for recursive partials)
 * 
 * ## Partials
 * 
 * $this->partial('some-partial', array('myvar' => 'someval');
 * 
 * ```
 * <?php // view/some-partial.php
 * 
 * echo $myvar; // "someval"
 * ```
 * 
 * 
 */
class cms
{
	/** @param $exec '_lfcms' to run full CMS from `lf_actions`/`lf_links` */
	private $exec = '_lfcms';
	
	/** @param $instances session instances. debating on using this or the lf_cache session... */
	static private $instances = array(); // 
	
	/** @param $ini configurable string in database per app `lf_links` table entry */
	private $ini = NULL;
	
	/** @param $version lfcms release version */
	private $version = NULL;
	
	/**
	 * Originally `(new littlefoot)->cms()`
	 * 
	 * Executes the Littlefoot CMS frontend (loading /admin, if requested)
	 * 
	 * @return $this The resulting $this object
	 */
	public function run()
	{
		// load plugins from `lf_plugins` table, execute plugins hooked to 'pre cms run'
		(new \lf\plugin)->run('pre cms run');
		
		// Start the '->run()' timer
		startTimer(__METHOD__);
		
		// test the installation. can we connect to MySQL, etc?
		//(new install)->test();
		
		// initialize request into session. 
		// this can technically be done JIT with `->load()`, 
		// but I prefer to do it myself.
		(new request)->parse()->save();
		
		// load version from LF/system/version file
		$this->loadVersion()		
			// load settings from `lf_settings` table
			->loadSettings()	
			// apply template skin based on `default_skin` setting
			->setTemplateSkin()					
			// Route auth() class per $wwwIndex/_auth/$method
			->route( (new auth), '_auth', false ); 
		
		// load acl object into session
		(new acl)->compile()->save();
		
		// If /admin was requested, load it and stop here
		$this->routeAdmin()					
			// add configured title setting to template title array
			->setSiteTitle()
			// Get data for SimpleCMS, or determine requested Nav ID from request $actions
			->navSelect();
			
		$this
			// exec SimpleCMS or exec linked apps, save output to template content array
			->getcontent($this->select['id']) //; pre( (new \lf\template)->getTitle() ); $this
			// add 3rdparty/icons.css for font awesome and lf.css
			->loadLfCSS()
			// Add stuff to <head> based on if search engine blocker is enabled in lf_settings
			->searchEngineBlocker();
		
		
		
		
		// Display content in skin, return HTML output result
		echo (new template)->render();
	
		// Stop the 'cms' timer. Store elapsed time for debug.
		endTimer(__METHOD__);
		
		if(getSetting('debug') == 'on') 
			$this->printDebug();
		
		(new \lf\plugin)->run('post cms run');
		
		return $this;
	}
	
	public function appSelect($app = NULL)
	{
		
		$apps = scandir( LF.'apps' );
		
		foreach($apps as $app)
		{
			// skip ., .., and .hidden files
			if($app[0] == '.') continue;
			
			// we can only link apps that have an index, otherwise they are admin only
			if( ! is_file( LF.'apps/'.$app.'/index.php' ) )
				$html .= '<option disabled="disabled" value="">'.$app.' (admin only)</option>';
			else
				$html .= '<option value="'.$app.'">'.$app.'</option>';
				
			
		}
		
		return $html;
	}
	
	public function templateSelect($template = NULL)
	{
		$match_file = 'default';
		if( ! is_null( $template ) )
			$match_file = $template;
			
		$pwd = ROOT.'skins';

		// Build template option
		$template_select = '<option';
				
		if($match_file == 'default')
		{
			$template_select .= ' selected="selected"';
			
			$skin = LF.'skins/'.\lf\getSetting('default_skin').'/index.php';
			
			// Get all %replace% keywords for selected template (remove system variables)
			if(!is_file($skin))
			{
				echo 'Currently selected skin does not exist. Please see the Skins tab to correct this.';
				$section_list = array('none');
			}
			else
			{
				$template = file_get_contents($skin);
				preg_match_all("/%([a-z]+)%/", str_replace(array('%baseurl%', '%skinbase%', '%nav%', '%title%'), '', $template), $tokens);
				$section_list = $tokens[1];
			}
		}

		$template_select .= ' value="default">-- Default Skin ('.\lf\getSetting('default_skin').') --</option>';

		foreach(scandir($pwd) as $file)
		{
			if($file == '.' || $file == '..') continue;

			$skin = $pwd.'/'.$file.'/index.php';
			if(is_file($skin))
			{
				$template_select .= '<option';
				
				if($match_file == $file)
				{
					$template_select .= ' selected="selected"';
				}
				
				$template_name = /*$conf['skin'] == $file ? "Default" :*/ ucfirst($file);
				
				$template_select .= ' value="'.$file.'">'.$template_name.'</option>';
			}
		}
		
		return $template_select;
	}
	
	public function hiddenList()
	{
		$hiddenActions = (new \LfActions)->getAllByPosition(0);
		
		$html = '<ul>';
		foreach( $hiddenActions as $action )
		{
			$html .= '<li>';
			//$html .= $action['label'];
			$html .= '<a href="'.\lf\requestGet('ActionUrl').'id/'.$action['id'].'">'.$action['label'].'</a>';
			$html .= '</li>';
		}
		$html .= '</ul>';
		
		return $html;
	}
	
	/**
	 * print HTML comment at the bottom of the source
	 * 
	 * display cool stats and list of required files
	 */
	public function printDebug()
	{
		$exectime = round((new \lf\cache)->getTimerResult('lf\\cms::run'), 6)*(1000);
		$memusage = round(memory_get_peak_usage()/1024/1024,2);
		include LF.'system/template/debug.php';
	}
	
	/**
	 * use template to add lfcss and icon css to top of <head> on render
	 */
	public function loadLfCss()
	{
		(new template)
			->addCss( requestGet('LfUrl').'system/lib/lf.css' )
			->addCss( requestGet('LfUrl').'system/lib/3rdparty/icons.css' );
			
		return $this;
	}
	
	/**
	 * load release version. used mostly for upgrade at the moment. may add to debug output
	 */
	public function loadVersion()
	{
		$this->version = trim(file_get_contents(LF.'system/version'));
		return $this;
	}
	
	/**
	 * return string of loaded lf release version
	 */
	public function getVersion()
	{
		if( is_null( $this->version ) )
			$this->loadVersion();
		
		return $this->version;
	}
	
	/**
	 * enable SimpleCMS for $app. It will treat $app as alias `/` and as if no other nav items exist
	 * 
	 * UI: admin navigation page is replaced with $app admin. other apps are hidden. full site navigation cannot be modified while this is on.
	 */
	public function simpleCms($app)
	{
		$this->exec = $app;
	}
	
	/**
	 * If user asks for 'admin/' in their request_uri, run admin
	 */
	public function routeAdmin()
	{
		// if request is detected as an 'admin' request...
		if( (new \lf\request)->load()->isAdmin() )
		{
			chdir(LF.'system/admin');
			include 'index.php';
			exit;
		}
		// otherwise, return self
		return $this;
	}
	
	/**
	 * load CMS settings into session from `lf_settings`
	 */
	public function loadSettings()
	{	
		(new plugin)->run('pre settings');
		
		foreach( (new \LfSettings)->getAll() as $setting )
			$settings[$setting['var']] = $setting['val'];
		
		set('settings', $settings);
		
		if( isset($settings['debug']) )
			set('debug', $settings['debug']);
		
		return $this;
	}
	
	/**
	 * Return all loaded settings in an array
	 */
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
	
	
	/**
	 * Return loaded CMS setting called $name
	 */
	public function getSetting($name)
	{
		$settings = $this->getSettings();
		return $settings[$name];
	}
	
	/**
	 * Instant MVC: Routing URL request to class methods.
	 * 
	 * request param is used to route to class methods. 
	 * 
	 * Given class `pages_admin` and `param = ['edit', '5']`, mvc would execute the `edit()` method and that method would know to ask request for param[1]
	 * 
	 * ## Usage
	 *
	 * ~~~
	 * include "controller/$controllerName.php";
	 * echo (new \lf\cms)->mvc( (new $controllerName) ); 
	 * ~~~
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
		
		$className = get_class($controller);
		
		if($param === NULL)
			$param = requestGet('Param');
		
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
		
		
		// Load a single plugin instance for this upcoming pile of hook runs
		$appPlugin = (new plugin);
		
		// Before every app
		$appPlugin->run('pre app');
		
		// Before every app called $controller
		$appPlugin->run('pre app '.$className);
		
		// not 100% on what this did as $func is not defined anywhere. likely going to delete this
		// if($func != $param[0]) 
			// $appPlugin->run('pre app '.$controller.' '.$func);
		
		
		$varstr = array();
		// For every parameter, add 1 to implode list until they are all there
		foreach($param as $var) 
		{
			$varstr[] = $var;
			$appPlugin->run('pre app '.$className.' '.implode(' ', $varstr));
		}
		
		// LEGACY: __construct could not be conveniently overwritten before 2.0
		// auto-run init() function if its there
		if(is_callable(array($controller, 'init')))
			echo $controller->init();
		
		echo $controller->$method();
		
		while(count($varstr)) // subtract action until they are all gone
		{	
			//pre( 'post app '.$className.' '.implode(' ', $varstr) );
			
			$appPlugin->run('post app '.$className.' '.implode(' ', $varstr));
			array_pop($varstr);
		}
		
		//if($func != $param[0]) 
		//	$appPlugin->run('post app '.$className.' '.$func);
		
		$appPlugin->run('post app '.$className);
		$appPlugin->run('post app');
		
		return ob_get_clean();
	}
	
	/**
	 * fun note: getSetting was causing a loop when the orm installer tried to ask request for the LF URL. so I moved it into CMS. now request has no ties to ORM
	 * 
	 * 
	 * 
	 **/
	public function handleUrlRewrite()
	{
		// Add in 302 to fix rewrite and prevent duplicate content
		if(getSetting('rewrite') == 'on' && requestGet('Index') == 'index.php/') 
			redirect302( (new request)->load()->rewriteOn()->getActionUrl() );
		else if(requestGet('Index') == '')
			redirect302( (new request)->load()->rewriteOff()->getActionUrl() );
	}
	
	/** 
	 * Routes `action[0]` to local files at `controller/$action[0].php` to let a folder of controller names handle the request action rather than `navSelect()`
	 * 
	 * Runs `(new \lf\cms)->mvc( (new $action[0]) );` after including the file found above.
	 */
	public function multiMVC($default = NULL, $section = 'content', $namespace = '\\')
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
			requestGet('Action')[0], 
			$match
		);

		// default to dashboard class
		if(!$success and !is_null($default)) 
			$match[0] = $default;

		$class = $match[0];
		
		// push all but single left most action 
		$request = (new \lf\request)->load();
		
		// store original state for after upcoming mvc
		$requestBackup = $request;
		
		$request
			->fullActionPop()
			->paramShift()
			->save(); // might move this into each function... wont hurt to run twice...
		
		include "controller/$class.php";
		
		$fullclass = $namespace.$class;
		$MVCresult = $this->mvc(new $fullclass);
		
		(new template)->addContent( 
			str_replace('%appurl%', \lf\requestGet('ActionUrl'), $MVCresult ), 
			$section
		);
		
		// put it back how we found it.
		$requestBackup->save();
		
		return $this;
	}
	
	/** 
	 * I haven't used this recently and will update the docs if I run into it again.
	 * 
	 * used to route based on args[0] as instance
	 *
	 * ### How to use _router
	 *
	 * Dynamically route controller based on a common URI base
	 *
	 * ie, `/_auth/mymethod`, `/_auth/myothermethod`
	 *
	 * ~~~ 
	 * $auth = new auth($this, $this->db);
	 *
	 * // change to auth class 
	 * if($this->action[0] == '_auth' && isset($this->action[1]))
	 * {
	 * 		$out = $auth->_router($this->action);
	 * 		$out = str_replace('%appurl%', $this->base.'_auth/', $out);
	 * 		$content['%content%'][] = $out;
	 * 	
	 * 		// display in skin
	 * 		echo $this->render($content);
	 * 	
	 * 		exit(); // end auth session after render, 
	 * 		// otherwise it will 302 (login/logout)
	 * }
	 * ~~~
	 *
	 * @param array $args URL Variables.
	 *
	 * @param string $default_route Default function for router when none is specified. Uses function "home" by default.
	 *
	 * @param array $filter If set, limit valid functions to those in the array; eg, array('func2', 'func3')
	 *
	 * Routing URL based on /subdir/action1/param1/method1/param2
	 * I moved this from app, but dont plan on actually fixing it until I need it again.
	 * I think when I wrote this, I was doing something studid and had to work around it.
	 * 
	 * @return string Captured output buffer from execution of $this->$method()
	 *
	 */
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
	
	/**
	 * Test URL for given alias (default to given class) in action[0]
	 * 
	 * @param $class Instantiated object with public methods intended to execute based on `getRequest('Action')[0]`
	 * @param $alias Defaults to $class. This only routes if we find $alias in `getRequest('Action')[0]`.
	 * @param $return bool "The output of this should be returned as a captured string rather than immediately rendering and exiting".
	 */
	public function route($instance, $alias = NULL, $return = true)
	{
		$className = get_class($instance);
		// use class name as alias by default
		if(is_null($alias))
			$alias = str_replace('\\', '_', $className);
		
		$timerName = __METHOD__.' '.$className.', '.$alias;
		
		(new plugin)->run('pre '.$className);
		startTimer($timerName);
		
		// store request state before upcoming alteration
		$originalRequest = (new \lf\request)->load();
		
		// get the current action array
		$actionArray = $originalRequest->getAction();
		
		// if the request matches even the first part of the action
		if( $actionArray[0] == $alias )
		{
			// so we can revert after this operation if we just return as a string
			$tempRequest = $originalRequest;
			
			// simulate actionPop for the upcoming MVC operation since we routed on action[0]
			$tempRequest->actionKeep(1)->save();
		
		
		
			$template = (new template)
				->addContent( $this->renderNavCache(), 'nav' )
				->addContent( $this->mvc($instance) );
				
		
			if(!$return)
			{
				// display in skin
				$this->loadLfCss();
				echo (new template)->render();
				endTimer($timerName);
				exit();
			}
		}
		
		// save back original context
		$originalRequest->save();
		
		endTimer($timerName);
		(new plugin)->run('post '.$className);
		return $this;
	}
	
	public function getNavCache()
	{
		return (new cache)->readFile('nav.cache.html');
	}
	
	/** Render baseurl with given arg */
	public function renderNavCache( $baseurl = NULL )
	{
		if( is_null( $baseurl ) )
			$baseurl = requestGet('IndexUrl');
		
		return str_replace('%baseurl%', $baseurl, $this->getNavCache());
	}
	
	/** deprecated */
	public function renderBaseUrl($text)
	{
		return str_replace('%baseurl%', requestGet('IndexUrl'), $text);
	}
	
	public function getApps($id = NULL)
	{
		$this->lf->hook_run('pre template getApps');
		
		// if full cms string provided, return all linked apps
		if($this->lf->simplecms == '_lfcms')
			$this->links = orm::q('lf_links')->filterByinclude($id)->get();
		
		// otherwise, assign only that app
		else
			$this->links[0] = array(
				'id' => 0, 
				'app' => $this->lf->simple_cms,
				'ini' => '',
				'section' => 'content'
			);
		
		$this->lf->hook_run('post template getApps');
		
		return $this;
	}
	
	public function homeTest()
	{
		$template = (new template);
		
		// Determine if home.php should be loaded
		if( isset($this->select['parent']) 
			&& $this->select['parent'] == -1 
			&& $this->select['position'] == 1 
			&& ( is_file($template->getTemplatePath().'home.php') 
				|| is_file($template->getTemplatePath().'home.html')
			)
		)
			$template->setHome(true);
			
		return $this;
	}
	 
	// to append the 'title' variable set in `lf_settings` to whatever title is already in place.
	public function setSiteTitle()
	{
		$siteTitle = getSetting('title');
		
		if( ! is_null( $siteTitle ) )
			(new template)->setTitle($siteTitle);
		
		return $this;
	}
	
	public function searchEngineBlocker()
	{
		// Search engine blocker
		if( getSetting('bots') == 'on' )
			(new template)->addHead('<meta name="robots" content="noindex, nofollow">');
		
		return $this;
	}
	
	
	
	/**
	 * Initializes the 'active plugin list' from `lf_plugins` table
	 */
	// public function loadPlugins()
	// {
		// $result = (new \LfPlugins)->getAll();
		
		// if($result)
			// foreach($result as $plugin)
				// $plugins[ $plugin['hook'] ][ $plugin['plugin'] ] = $plugin['config'];
		
		// (new plugin)->run('plugins loaded');
		
		// return $this;
	// }
	
	/**
	 * checks for and executes an active plugin assigned to the triggered $hook
	 */
	// public function hook_run($hook)
	// {
		// if(!isset($this->plugins[$hook])) 
			// return $this;
		
		// foreach($this->plugins[$hook] as $plugin => $config)
		// {
			// $hookDetails = ' / '.$plugin.' @ '.$hook.' / Config: '.$config;
			
			// (new \lf\cache)->startTimer(__METHOD__.$hookDetails);
			// include ROOT.'plugins/'.$plugin.'/index.php';
			// (new \lf\cache)->endTimer(__METHOD__.$hookDetails);
		// }
		
		// return $this;
	// }
	
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
		(new request)->load()->fullActionPop();
		
		return $this;
	}
	
	// match action to hrefs in LF/cache/nav.cache.html, return match
	// may make SQL query easier, but we still have to get the action id
	public function actionFromCache($action = null)
	{
		if( is_null( $action) )
			$action = \lf\requestGet('Action');
		
		
		// visiting /something/like/this would generate `something/(like/(this/)?)?)` as a test pattern
		$actionRegex = implode('/(', $action);
		
		if( count($action) > 0)
			$actionRegex .= '/'.str_repeat(')?',count($action)-1); // end /delimiter/
		
		$actionRegexTest = '/%baseurl%('.str_replace('/', '\/', $actionRegex).')"/';
		
		// We load the cached navigation...
		$navCache = (new \lf\cache)->readFile('nav.cache.html');
		
		// And see where our request URL best fits of the navigation <li><a> URLs 
		preg_match_all( $actionRegexTest, $navCache, $matches );
		
		return $matches;
	}
	
	/**
	 * # navSelect
	 * 
	 * Determine which navigation item was requested based on wwwAction and navigation hierarchy.
	 * 
	 * Results with `$this->select` containing the `lf_actions` row data of the matching navigation item.
	*/
	public function navSelect()
	{
		startTimer(__METHOD__);
		if(getSetting('simple_cms') != '_lfcms')
			$this->simpleSelect();
		
		// determines current action request
		
		// by default, not found. needed to detect request for / when action at -1, 1 doesnt have an empty alias
		$this->select['alias'] = '404'; 
		
		/* Determine requested nav item from lf_actions */
		// get all possible matches for current request, 
		// always grab the first one in case nothing is selected
		$matches = (new orm)->fetchAll("
			SELECT * FROM lf_actions 
			WHERE alias IN ('".implode("', '", requestGet('Action') )."') 
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
			{
				// save row in case "domain.com/" is requested
				$base_save = $row;
			}
				
			$test_select[$row['parent']][$row['position']] = $row;
		}
		
		// loop through action to determine selected nav
		// trace down to last child
		
		// start at the root nav items
		$parent = -1; 
		// nothing selected to start with
		$selected = array(); 
		// loop through action array
		for($i = 0; $i < count(requestGet('Action')); $i++) 
			// if our compiled parent->position matrix has this parent set
			if(isset($test_select[$parent])) 
				foreach($test_select[$parent] as $position => $nav)	// loop through child items 
					if($nav['alias'] == requestGet('Action')[$i]) // to find each navigation item in the hierarchy
					{
						// we found the match, 
						// move on to next action item matching
						
						// this result in all/that/match(/with/params/after)
						$selected[] = $nav;
						
						$parent = $nav['id'];
						break;
					}
		
		// if a selection was made, alter the action so it has proper params
		if($selected != array())
		{
			// separate action into vars and action base, 
			// pull select nav from inner most child
			// eg, given `someparent/blog/23-test`, pop twice
			(new request)->load()
				->actionKeep( count($selected) )
				->save();
			
			// This is where we find which navigation item we are visiting
			$this->select = end($selected);
		}
		
		
		// If home page is an app and no select was made from getnav(), 
		// set current page as /
		if($this->select['alias'] == '404' && $base_save != NULL)
		{		
			(new request)->load()->fullActionPop()->save(); // pop all actions into param, we are loading the first nav item
			$this->select = $base_save;
		}
		
		// set template to mode = home, if home page is selected
		$this->testHome();
		
		// in case the file doesn't exist
		
		if(!is_file(ROOT.'cache/nav.cache.html')) 
		{
			$pwd = getcwd();
			chdir(ROOT.'system/admin/');
			include 'controller/dashboard.php';
			$dashboard = (new \lf\admin\dashboard)->updatenavcache();
			// run the nav HTML generation script
			//$this->mvc(, NULL, array('updatenavcache')); 
			//shouldl be $this->mvc($dashboard, NULL, array('updatenavcache')); 
			chdir($pwd);
		}
		
		$nav_cache = file_get_contents(ROOT.'cache/nav.cache.html'); // Pull cached navigation HTML output rather than generate it on the fly.
		
		// Update nav_cache to show active items
		
		$actionbuilder = '%baseurl%'; // Start with reference to installation base
		foreach( requestGet('Action') as $action)
		{
			if($action != '')	// Account for empty alias
				$actionbuilder .= $action.'/';	// Loop through the full/path. 
												
			// As the action request URI builds, replace each link matching that set to active.
			$nav_cache = str_replace(
				'<li><a href="'.$actionbuilder.'"', 
				'<li class="active"><a href="'.$actionbuilder.'"', 
				$nav_cache);
		}
		
		// set nav ul class if set
		// Apply class to root <ul> if it is set
		
		// $nav_cache = isset($this->settings['nav_class']) 
			// ? preg_replace('/^<ul>/', '<ul class="'.$this->settings['nav_class'].'">', $nav_cache )
			// : $nav_cache;
		
		// if no items match the request, return 404
		if($this->select['alias'] == '404')
		{
			header('HTTP/1.1 404 Not Found');
			echo '<p>LF 404: No menu items match your request</p>';
			return 0;
		}	
			
		// If simple CMS is not set, add 'nav' to final output content array.
		if(getSetting('simple_cms') == '_lfcms') 		
		(new \lf\template)->addContent( 
			$this->renderBaseUrl($nav_cache),
			'nav'
		);
		
		endTimer(__METHOD__);
		return $this;
	}
	
	public function testHome()
	{
		if($this->select['position'] == 1 && $this->select['parent'] == -1)
			(new template)->setHome(true);
		
		return $this;
	}
	
	public function setTemplateSkin()
	{
		// If template has not be changed from 'default', set as configured default_skin.
		if(!isset($this->select['template']))
			$this->select['template'] = getSetting('default_skin');
		else if($this->select['template'] == 'default')
			$this->select['template'] = getSetting('default_skin');
		else
			$this->select['template'] = 'default';
		
		// need to fix this from XV template
		$template = (new template)->setSkin($this->select['template']);
		
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
	
	public function getcontent($includeId)
	{
		(new plugin)->run('pre '.__METHOD__);
		startTimer(__METHOD__);
		
		
		if( ! (new acl)->load()
				->test( implode( '/', requestGet('Action') ) ) 
		){
			$template = (new template)->load();
			
			$template->addContent( 
				"401 Unauthorized at ".
				requestGet('IndexUrl').
				$template->getLogin() 
			);
			
			return $this;
		}
		
		// Pull $apps list with section=>app
		if(getSetting('simple_cms') != '_lfcms') #DEV
		{
			$apps[0] = array(
				'id' => 0, 
				'app' => getSetting('simple_cms'),
				'ini' => '',
				'section' => 'content'
			);
		}
		else
		{
			$sql = "
				SELECT id, app, ini, section 
				FROM lf_links
				WHERE include = '".$includeId."'
					OR include = '%'
				ORDER BY id
			";
			
			// Grab all active possible connections to currently selected menu item
			$apps = (new orm)->fetchall($sql);
		}
		
		$vars = requestGet('Param');
		foreach($apps as $_app)
		{
				
			// Test ACL for this app
			/*if( ! get('acl')->test(implode('/', requestGet('Action')).'|'.$_app['app'] ) 
				|| ( isset($vars[0]) 
					&& get('acl')->test(implode('/', requestGet('Action')).'|'.$_app['app'].'/'.$vars[0])
			))
			{
				(new template)->addContent("403 Access Denied ".$this->getLogin(), $_app['section']);
				continue;
			}*/
			
			// set app target path
			$path = ROOT.'apps/'.$_app['app'];
			if(!is_file($path.'/index.php')) 
				continue;
			
			// collect app output
			ob_start();
			chdir($path); // set current working dir to app base path
			$start = microtime(true); // timer for app
			
			$apptimer = __METHOD__.
				' / Link Id: '.$_app['id'].
				', App: '.$_app['app'].
				', Position: '.$_app['section'].
				', Config: '.$_app['ini'];
			
			startTimer($apptimer);
			include 'index.php'; // execute app
			endTimer($apptimer);
			$output = '
				<div id="'.$_app['app'].'-'.$_app['id'].'" class="app-'.$_app['app'].'">'.
					ob_get_clean().
				'</div>';
			
			// and save
			(new template)->addContent(
				resolveAppUrl($output),
				$_app['section']
			);
		}
		
		// cd back to LF root for the rest of the execution
		chdir(LF); 
		
		(new plugin)->run('post lf getcontent');
		
		endTimer(__METHOD__);
		
		return $this;
	}
	
	/**
	 * Used for loading partial views given an argument
	 * 
	 * Note: Remember to echo the returned output, otherwise it will not print
	 * 
	 * @param string $file The name of the view. Loaded from view/$file.php
	 * @param array $args Associative array of $var => $val passed to the partial.
	 */
	public function partial($partial, $args = array())
	{
		//foreach($args as $var => $val)
		//	$$var = $val;
		extract($args);
			
		ob_start();
		include 'view/'.$partial.'.php';
		return ob_get_clean();
	}
}