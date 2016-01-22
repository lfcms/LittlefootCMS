<?php

namespace lf;

/**
 * # cms class
 * 
 * this is the main class (formerly class littlefoot) that handles all the asepcts of the higher level CMS operations such as lf_actions navigation selection from request_uri (formerly littlefoot->navSelect()), app loading, skin rendering, acl testing (should really be its own class).
 * 
 * 
 * 
 */
class cms
{
	// simple CMS. 
	private $exec = '_lfcms';
	
	public function simpleCms($app)
	{
		$this->exec = $app;
	}
	
	public function run()
	{
		
		(new install)->test();
		
		$request = (new \lf\request)->parseUri();
		
		$this
			->loadSettings()
			->loadSettings()
			->route('auth', '_auth', false) // Route auth() class per $wwwIndex/_auth/$method
			->routeAdmin();
			
		$this->loadVersion() 				// load version from LF/system/version file
			->loadPlugins() 				// load plugins from `lf_plugins` table
			->loadSettings()				// load settings from `lf_settings` table
			->request()						// Parse $_SERVER['REQUEST_URI']; into pieces Littlefoot can understand
			->route('auth', '_auth', false) // Route auth() class per $wwwIndex/_auth/$method
			->loadACL()						// load ACL rules from lf_acl_global, lf_acl_inherit, and `lf_acl_user` that affect current $_SESSION user.
			->routeAdmin()					// If /admin was requested, load it and stop here
			->navSelect()					// Get data for SimpleCMS, or determine requested Nav ID from url $actions
			->getcontent(); 				// Deal with SimpleCMS or execute linked apps
		
		echo $this->render(); 				// Display content in skin, return HTML output result
		
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
		
		foreach((new orm)->qSettings('lf')->getAll() as $setting)
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
		if($vars === NULL) $vars = $this->vars;
		if(!isset($vars[0])) $vars[0] = '';
		
		if(!is_file('controller/'.$controller.'.php')) // If controller file is missing at /docroot/lf/apps/<app_name>/controller/<load>.php
			return 'Invalid request. File not found at '.getcwd().'/controller/'.$controller.'.php';
		
		if(!class_exists($controller)) // include specified controller class
			include 'controller/' . $controller . '.php';
		
		$class = new $controller($this->lf, $ini, $vars); // init class specified by $controller
		if(is_callable(array($class, $vars[0])))
			$func = $vars[0];
		else
		{
			if(isset($class->allow404)) return 404; // rewrite by default
			if(isset($class->default_method)) // if the $obj specifies a default method, 
				$func = $class->default_method; // use it
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
		
		echo $class->$func($vars);
		
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
			
			$out = $app->_router($this->action);
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
}