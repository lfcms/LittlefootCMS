<?php

namespace lf;

/**
 * # cms class
 * 
 * this is the main class (formerly class littlefoot) that handles all the aspects of the higher level CMS operations such as lf_actions navigation selection from request_uri (formerly littlefoot->navSelect()), app loading from `lf_links`, skin rendering, acl testing (should really be its own class).
 * 
 * 
 * 
 */
class cms
{
	// simple CMS. 
	private $exec = '_lfcms';
	
	private $ini = NULL;	// configurable string in database per app `lf_links` table entry
	
	
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
	
	public function simpleCms($app)
	{
		$this->exec = $app;
	}
	
	// would replace (new littlefoot)->cms()
	public function run()
	{
		(new cache)->startTimer('cms');
		(new install)->test();
		(new request)->parseUri();
			
		$this->loadVersion() 				// load version from LF/system/version file
			->loadPlugins() 				// load plugins from `lf_plugins` table
			->loadSettings()				// load settings from `lf_settings` table
			->route('auth', '_auth', false) // Route auth() class per $wwwIndex/_auth/$method
			->loadACL()						// load ACL rules from lf_acl_global, lf_acl_inherit, and `lf_acl_user` that affect current $_SESSION user.
			->routeAdmin()					// If /admin was requested, load it and stop here
			->navSelect()					// Get data for SimpleCMS, or determine requested Nav ID from url $actions
			->getcontent(); 				// Deal with SimpleCMS or execute linked apps
		
		echo $this->render(); 				// Display content in skin, return HTML output result

		(new \lf\cache)->endTimer('cms');
		
		if($this->debug == 'on') 
			$this->printDebug();
		
		return $this;
	}
	
	public function routeAdmin()
	{
		// if requested, load admin/
		if($this->admin)
		{
			chdir(LF.'system/admin');
			include 'index.php';
			exit;
		}
		return $this;
	}
	
	public function loadSettings()
	{
		$this->hook_run('pre settings');
		
		foreach((new LfSettings)->getAll() as $setting)
			$this->settings[$setting['var']] = $setting['val'];
		
		
		if(isset($this->settings['debug']))
			$this->debug = $this->settings['debug'];
		
		return $this;
	}
	
	public function getSettings()
	{
		if($this->settings == array())
			$this->loadSettings();
		
		return $this->settings;
	}
	
	/**
	 * Instant MVC: Routing URL request to class methods. Auto load given class name in controller/ folder as Littlefoot app. 
	 *
	 * 
	 * ## Usage
	 *
	 * ~~~
	 * echo $this->lf->mvc($controllerName); 
	 * ~~~
	 * 
	 * ## Backend operation
	 * 
	 * ~~~
	 * include "controller/$controllerName.php";
	 * $class = new $controllerName($this->lf[, $ini[, $vars]])
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
	 *		defaults to $this->lf->$vars generated in $this->lf->request()
	 *
	 */
	public function mvc($controller, $ini = '', $vars = NULL)
	{
		ob_start();
		
		if($vars === NULL) $vars = (new \lf\request)->get('action');
		
		if(!isset($vars[0])) $vars[0] = '';
		
		
		if(is_callable(array($controller, $vars[0])))
			$func = $vars[0];
		else
		{
			if(isset($controller->allow404)) return 404; // rewrite by default
			if(isset($controller->default_method)) // if the $obj specifies a default method, 
				$func = $controller->default_method; // use it
			else
				$func = 'main'; // default to main()
		}
		
		/*$this->hook_run('pre app');
		$this->hook_run('pre app '.$controller);
		if($func != $vars[0]) $this->hook_run('pre app '.$controller.' '.$func);
		
		$varstr = array();
		foreach($vars as $var) // add vars until they are all there
		{
			$varstr[] = $var;
			$this->hook_run('pre app '.$controller.' '.implode(' ', $varstr));
		}*/
		
		echo $controller->$func($vars);
		
		/*while(count($varstr)) // subtract vars until they are all gone
		{	
			$this->hook_run('post app '.$controller.' '.implode(' ', $varstr));
			array_pop($varstr);
		}
		
		if($func != $vars[0]) $this->hook_run('post app '.$controller.' '.$func);
		
		$this->hook_run('post app '.$controller);
		$this->hook_run('post app');*/
		
		return ob_get_clean();
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
			$this->action[0], 
			$match
		);

		// default to dashboard class
		if(!$success and !is_null($default)) 
			$match[0] = $default;

		$class = $match[0];
		
		$this->vars = array_slice($this->action, 1);
		$this->appurl = $this->base.$class.'/';
		
		$MVCresult = $this->mvc($class);
		
		$replace = array('%appurl%' => $this->lf->base.$class.'/');

		$app = str_replace(
			array_keys($replace), 
			array_values($replace), 
			$MVCresult
		);
		
		$this->content['%'.$section.'%'][] = $app;
		
		return $this;
	}
	
	
	
	public function route($class, $alias = NULL, $return = true)
	{
		$this->hook_run('pre '.$class); 
		
		if(is_null($alias))
			$alias = $class;
		
		$app = new $class($this);
		
		// change to auth class 
		if($this->action[0] == $alias && isset($this->action[1]))
		{
			$this->appurl = $this->wwwIndex.'_auth/';
			
			// should really use MVC on this
			
			$out = $app->_router( \lf\www('Action') );
			$out = str_replace('%appurl%', $this->appurl, $out);
			$this->content['content'][] = $out;
			
			if(!$return)
			{
				// display in skin
				echo $this->render();
				exit();
			}
		}
		
		$this->hook_run('post '.$class);
		
		return $this;
	}
	
	public function render($dir = NULL)
	{
		(new \lf\cache)->startTimer(__METHOD__);
		$this->hook_run('pre lf render');
		
		if(is_null($dir))
			chdir(LF.'skins');
		else
			chdir($dir);
		
		$this->loadLfCSS();
		
		
		// Determine if home.php should be loaded
		$file = 'index';
		if( isset($this->select['parent']) 
			&& $this->select['parent'] == -1 
			&& $this->select['position'] == 1 
			&& ( is_file($this->select['template'].'/home.php') 
				|| is_file($this->select['template'].'/home.html')
			)
		)
			$file = 'home';
		
		// Load skin data
		ob_start();
		$skin = $this->select['template'];
		
		//pre($this->content);
		
		if(is_file($skin."/$file.php")) // allow php
			include($skin."/$file.php");
		else if(is_file($skin."/$file.html"))
			readfile($skin."/$file.html");
		else
			echo 'Template files for '.$skin."/$file.php".' missing. Log into admin and select a different template with the Skins tool.';
			
		$template = ob_get_clean();
		
		// | title replacement
		if(isset($this->lf->settings['title']) && $this->lf->settings['title'] != '')
			$this->select['title'] .= ' | '.$this->lf->settings['title'];
		
		
		/*
		// deprecated: use $this->lf->methods() instead
		// Replace all %markers% with $content
		if(isset($this->content))
			foreach($this->content as $key => $value)
				$template = str_replace($key, implode($value), $template);
		// replace global variables
		$global_replace = array(
			'%login%' => $this->getLogin(),
			'%title%' => $this->getTitle(),
			'%skinbase%' => $this->getSkinBase(),
			'%baseurl%' => $this->wwwIndex,
			'%relbase%' => $this->relbase
		);
		$template = str_replace(
			array_keys($global_replace), 
			array_values($global_replace), 
			$template
		);*/
		
		// Search engine blocker
		if(isset($this->lf->settings['bots']) && $this->lf->settings['bots'] == 'on')
			$this->lf->head .= '<meta name="robots" content="noindex, nofollow">';
		
		$template = str_replace('<head>', '<head>'.$this->lf->head, $template);
		
		$this->hook_run('post lf render');
		(new \lf\cache)->endTimer(__METHOD__);
		return $template;
	}
	
	/**
	 * Initializes the 'active plugin list' from `lf_plugins` table
	 */
	public function loadPlugins()
	{
		$result = (new LfPlugins)->getAll();
		
		if($result)
			foreach($result as $plugin)
				$plugins[ $plugin['hook'] ][ $plugin['plugin'] ] = $plugin['config'];
		
		$this->hook_run('plugins loaded');
		
		return $this;
	}
	
	/**
	 * Initializes the plugin listener from lf_plugins table
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
}