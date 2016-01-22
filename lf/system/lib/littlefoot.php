<?php

/**
 * @package LittlefootCMS
 * @organization eFlip.com, LLC
 * 
 * Littlefoot framework environment
 * 
 * 
 * ## Environment
 * 
 * While in the Littlefoot context (executing inside the class), one has access to the entire $this->lf Environment
 * 
 * [Programming in Littlefoot](http://littlefootcms.com/byid/21)
 *
 * ### Quick Reference
 *
 * These things are accessible in littlefoot apps.
 *
 * * [$this->db](http://littlefootcms.com/files/docs/classes/Database.html)
 * * $this->auth
 * * functions.php
 * * orm::q('table_name')
 *
 * ### $this->lf
 *
 * `$this->lf = &$this` allows a consistent environment between Littlefoot and classes that extend 'app'. So I use $this->lf even while in the littlefoot context
 * 
 * | $this public variables | string replace in render() | Example render |
 * | --- | --- | --- |
 * | ->relbase | %relbase% | http://domain.com/littlefoot/ |
 * | ->appurl | %appurl% | http://domain.com/littlefoot/blog/ |
 * | ->base | %baseurl% | http://domain.com/littlefoot/(index.php/)? |
 * 
 * 
 */
class Littlefoot
{
	/** @var Database $db Database Wrapper */
	public $db;
	
	/** @var Littlefoot $lf Littlefoot instance. = &$this; */
	public $lf;
	
	/** @var array $auth Current an array of auth information. Hoping to move to an auth object. Accessible through $lf->api() */
	public $auth;
	
	/** @var auth $auth_obj The auth object. Hoping to replace the old array system with it. */
	public $auth_obj; // system/lib/auth.php
	
	/** @var string $absbase Backward compatible. Replaced by defined 'ROOT' */
	public $absbase;
	
	/** @var string $base Pre-rendered data for %baseurl% */
	public $base;
	
	/** @var string $basenoget Pre-rendered data for %baseurl%. With $_GET data stripped. */
	public $basenoget;
	
	/** @var string $relbase Pre-rendered data for %relbase%. */
	public $relbase;
	
	/** @var string $appurl Pre-rendered data for %appurl% (per app). */
	public $appurl; // allow it to change
	
	/** @var array $action After $lf->request(), this array is filled with the chopped up URI */
	public $action;
	
	/** @var array $vars After $lf->nav(), the URI is chopped up into the navigation elements followed by the app variables. */
	public $vars = array();
	
	/** @var array $select Array of data for present request (template, nav_id, alias) */
	public $select;
	
	/** @var string $alias Not sure if I still use this. It would be 'alias' from $select */
	private $alias;
	
	/** @var bool $admin If /admin is requested, this is set to true to fork to admin during $lf->cms() */
	public $admin;
	
	/** @var array $get Copied contents of $_GET */
	public $get;
	
	/** @var array $get Copied contents of $_POST */
	public $post;
	
	/** @var float $start Start time of $lf execution. */
	private $start;
	
	/** @var bool $debug Whether or not to display errors and render execution times. */
	public $debug;
	
	/** @var string $msgg Not sure if used. */
	public $msgg = '';
	
	/** @var string $note Old message function. Not sure if used. */
	private $note;
	
	/** @var string $error I think this is also an old message function. Not sure. */
	private $error;
	
	/** @var string $version Current littlefoot version. Pulled from ROOT.'system/version' */
	private $version;
	
	/** @var array $app_timer A list of execution times for each function */
	private $app_timer = array();
	
	/** @var array $function_timer A list of execution times for each function */
	public $function_timer = array();
	
	/** @var string[] $settings array of littlefoot settings pulled from lf_settings */
	public $settings;
	 
	/** @var array $plugins Array of plugins waiting to run array('pre_auth' => 'plugin1', 'plugin2', 'etc'). */
	private $plugins = array();
	
	/** @var string $head just to put stuff in <head>. this prints just before </head> in DOM */
	public $head = '';
	
	/** @var string $domain The domain used to access this application */
	public $domain = '';
	
	/** @var string $user user() object */
	public $user = '';
	
	/**
	 * Initialize Littlefoot Object
	 * 
	 * Only intended to give access to methods, don't commit to doing anything on __construct.
	 * 
	 * All standard heavy lifting shifted to run in ->cms();
	 * 
	 * $this->lf = &$this; // ensures universal availability of "$this->lf"
	 * 
	 */
	public function __construct($db = NULL)
	{
		$this->start = microtime(true); // start timing the WHOLE operation
		$this->lf = &$this; 			// ensures universal availability of "$this->lf"
	}
	
	public function __destruct()
	{
		if($this->debug == 'on') 
			$this->printDebug();
	}
	
	// print HTML comment at the bottom of the source
	// display cool stats and list of required files
	public function printDebug()
	{
		$exectime = round((microtime(true) - $this->start), 6)*(1000);
		$memusage = round(memory_get_peak_usage()/1024/1024,2);
		include LF.'system/template/debug.php';
	}
	
	public function loadVersion()
	{
		$this->version = file_get_contents(LF.'system/version');
		return $this;
	}
	
	/**
	 * Execute as CMS
	 * 
	 * Run littlefoot as CMS with request routed to app based on lf_actions and lf_links tables
	 * 
	 * ## Flow
	 *
	 * 1. pull lf_settings
	 *
	 * 1. pull plugins
	 * 
	 * 1. default $this->lf->select values (template, title, alias=404)
	 * 
	 * 1. redirect force URL //move this to request()
	 * 
	 * 1. $this->request() // should move to __construct()
	 * 
	 * 1. $this->authenticate() // should use auth() class in cms()
	 * 
	 * 1. admin?
	 * 
	 * 1. apply acl // should be called from auth() class
	 * 
	 * 1. simplecms?or:nav(is404?)
	 * 
	 * 1. testACL?403 // should be called from auth() class (or in a separate ACL object)
	 * 
	 * 1. getcontent() //contains simplecms?mvc
	 * 
	 * 1. simplecms?%nav%
	 * 
	 * 1. echo [render()](http://littlefootcms.com/files/docs/classes/Littlefoot.html#method_render)
	 * 
	 * @param string $debug Is debug set to true
	 * 
	 */
	public function cms()
	{
		$this->db = (new orm)->initDb();	// set up local db object, backward compatible. 
												// modern apps do not rely on $this->db to do db stuff. the orm class is used instead.
		
		(new \lf\install)->test(); 				// test that we can connect to the db and have data, 
												// otherwise present db config form
		
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
	
	public function startTimer($key = 'DEFAULT')
	{
		$this->timer[$key] = microtime(true);
		$this->hook_run('pre '.$key);
		return $this;
	}
	
	public function endTimer($key = 'DEFAULT')
	{
		$this->hook_run('post '.$key);
		$this->timer[$key] = microtime(true) - $this->timer[$key];
		return $this;
	}

	public function checkCSRF($timeout = 3600)
	{
		if(count($_POST))
		{
			try
			{
				// Run CSRF check, on POST data, in exception mode, with a validity of 10 minutes, in one-time mode.
				NoCSRF::check( 'csrf_token', $_POST, true, $timeout, false );
				// form parsing, DB inserts, etc.
				unset($_POST['csrf_token']);
			}
			catch ( Exception $e )
			{
				// CSRF attack detected
				die('Session timed out');
			}
		}
		
		return $this;
	}
	
	public function addCSRF($out)
	{
		/* csrf form auth */

		// Generate CSRF token to use in form hidden field
		$token = NoCSRF::generate( 'csrf_token' );
		preg_match_all('/<form[^>]*action="([^"]+)"[^>]*>/', $out, $match);
		for($i = 0; $i < count($match[0]); $i++)
		{
			$out = str_replace($match[0][$i], $match[0][$i].' <input type="hidden" name="csrf_token" value="'.$token.'" />', $out);
			
		}

		return $out;
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
	 
	/*
	private function cache() // not a thing yet
	{
		//CACHING - will not account for update to page...
		if(isset($this->settings['cache']) && $this->settings['cache'] = 'on')
		{
			$auth = $this->auth;
			unset($auth['last_request'], $auth['timeout']);
			$file = md5(json_encode($this->base.implode('/', $this->action).implode('/', $this->vars)).json_encode($auth).json_encode($this->baseacl)).'output.html';
			if(is_file(ROOT.'cache/'.$file))
			{
				readfile(ROOT.'cache/'.$file);
				exit();
			}
		}
		
		//CACHING - will not account for update to page...
		if(isset($this->settings['cache']) && $this->settings['cache'] = 'on')
		{
			$auth = $this->auth;
			unset($auth['last_request'], $auth['timeout']);
			$file = md5(json_encode($this->base.implode('/', $this->action).implode('/', $this->vars)).json_encode($auth).json_encode($this->baseacl)).'output.html';
			file_put_contents(ROOT.'cache/'.$file, $output);
		}
	}
	*/
	
	/**
	 * Parses $_SERVER variables to environment for use within apps
	 * and $this->lf->cms\(\)
	 *
	 * @return bool True if admin/ requested. False if not.
	 */
	public function request()
	{
		$this->startTimer(__METHOD__);
		
		$this->hook_run('pre lf request');
		
		// Assign default request values
		$this->select['template'] = $this->settings['default_skin'];
		$this->select['title'] = 'LFCMS';
		$this->select['alias'] = '404';
		
		// ty Anoop K [ http://stackoverflow.com/questions/4503135/php-get-site-url-protocol-http-vs-https ]
	    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

	    $this->protocol = $protocol;

		// redirect to URL specified in 'force_url' setting if not already being accessed that way
	    if(isset($this->settings['force_url']) && $this->settings['force_url'] != '' )
		{
			$relbase = preg_replace('/index.php.*/', '', $_SERVER['PHP_SELF']);
			$request = $_SERVER['HTTP_HOST'].$relbase;
			$compare = preg_replace('/^https?:\/\//', '', $this->settings['force_url']);

			if($request != $compare)
			{
				$redirect = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				redirect302($protocol.$redirect);
			}
		}

		// detect file being used as base (for API)
		$filename = 'index.php';
		if(preg_match('/^(.*)\/([^\/]+\.php)$/', $_SERVER['SCRIPT_NAME'], $match))
			$filename = $match[2];
		
		// Extract subdir
		$pos = strpos($_SERVER['SCRIPT_NAME'], $filename);
		$subdir = $pos != 0 ? substr($_SERVER['SCRIPT_NAME'], 1, $pos-1) : '/';
		
		// Break up request URI on ? and extract GET request
		$url = explode('?', $_SERVER['REQUEST_URI'], 2);
		if(substr($url[0], -1) != '/')
			$url[0] .= '/'; //Force trailing slash
		
		if(!isset($url[1])) $url[1] = '';
		
		$this->get = $_GET;
		$this->post = $_POST;
		$this->rawGet = $url[0];
		$this->rawQuery = $url[1];
		
		// Detect subdirectory, use of index.php, request of admin, other URI variables and the GET request
		$urlregex = '/'. 	// beginning regex delimiter
			'^(\/'.str_replace('/', '\/', $subdir).')'.	// match the subdir
			'(.+.php\/)?'.	// figure out what the user is calling their index.php
			'(admin\/)?'.	// detect if request involves admin/ access
			'(.*)'.			// capture the rest of the string, this is the "action" by default
			'/'; 			// end regex delimiter
		preg_match($urlregex, $url[0], $request);
		
		// Simplify request matches
		$subdir = $request[1];
		$index  = $request[2];
		$admin  = $request[3];
		$action = $request[4];
		
		// set admin boolean from regex result
		$this->admin = $admin == 'admin/' ? true : false;
		
		// Add in 302 to fix rewrite and prevent duplicate content
		$fixrewrite = false;
		if($this->settings['rewrite'] == 'on')
		{
			if($index == 'index.php/') 
				$fixrewrite = true;
			$index = '';
		}
		if($this->settings['rewrite'] == 'off')
		{
			if($index == '') 
				$fixrewrite = true;
			$index = $filename.'/';
		}
		
		// set port if non-standard 80 and 443
		if($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443)
			$port = ':'.$_SERVER['SERVER_PORT']; 
		else 
			$port = '';
		
		// determine if we are https:// or not, set protocol
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			$protocol = 'https://';
		else
			$protocol = 'http://';
		
		// www.domain.com
		$this->domain = $_SERVER['HTTP_HOST'];
		
		// http://www.domain.com/littlefoot/
		$this->wwwInstall 	= $protocol.$_SERVER['HTTP_HOST'].$subdir;
		
		// http://www.domain.com/littlefoot/lf/
		$this->wwwLF 	= $protocol.$_SERVER['HTTP_HOST'].$subdir.'lf/';
		
		// http://www.domain.com/littlefoot/index.php/
		$this->wwwIndex 	= $protocol.$_SERVER['HTTP_HOST'].$subdir.$index;
		
		// http://www.domain.com/littlefoot/index.php/admin/
		$this->wwwAdmin		= $this->wwwIndex.'admin/';
		
		// If rewrite needed fixed, this will redirect to the proper location given the request.
		if($fixrewrite) 
			redirect302($this->wwwIndex.$admin.$this->action);
		
		// explode the remaining URL component to see what was requested, delimiting on '/'
		$this->action = explode('/', $action, -1);
		if(count($this->action) < 1) // If the action array has no elements,
			$this->action[] = '';	 // Set first action as alias '' (empty string)
		
		$this->hook_run('post lf request');
		$this->endTimer(__METHOD__);
		
		// Backward compatible, dont use these.
		// They are only still hear cuz my old apps still use these :P
		$this->base = $protocol.$_SERVER['HTTP_HOST'].$subdir.$index;
		$this->baseurl = $this->base; // keep $Xurl usage
		$this->relbase = $subdir; // /subdir/ for use with web relative file reference
		$this->basenoget = $this->base.$admin.$action;
		
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
	
	private function loadACL()
	{
		$this->startTimer(__METHOD__);
		
		// inherit
		$inherit = array();
		$this->db->query('SELECT * FROM lf_acl_inherit');
		while($row = $this->db->fetch())
			$inherit[$row['group']][] = $row['inherits']; // sort output as $group => array($inherit1, $inherit2)
		
		// recurse through inheritance, get list of children.
		function get_acl_groups($inherit, $process)
		{
			if(!isset($inherit[$process])) return array(); // anon will trigger this
			
			$groups = $inherit[$process]; // $groups = an array of groups inherited by the $process group
			foreach($groups as $group)
				if(isset($inherit[$group]))
					$groups = array_merge( $groups, get_acl_groups($inherit, $group) ); 
			return array_unique($groups);
		}
		
		$user = new User();
		
		// get a list of groups from inheritance
		$groups = get_acl_groups($inherit, $user->getAccess());
		$groups[] = $user->getAccess();
		$groupsql = "'".implode("', '", $groups)."'"; // and get them ready for SQL
		
		// Build user ACL from above group list and individual rules
		$acl = array();
		$baseacl = array();
		//$baseacl = array();
		$rows = $this->db->fetchall("
			SELECT action, perm FROM lf_acl_user 
			WHERE affects = '".$this->api('getuid')."' 
			  OR affects IN (".$groupsql.")
		"); // ) AND action = '".implode('/', $this->action)."'
		foreach($rows as $row)
			$acl[$row['action']] = $row['perm'];
		
		// build base acl
		$rows = $this->db->fetchall("SELECT action, perm FROM lf_acl_global"); // WHERE action = '".implode('/', $this->action)."'
		foreach($rows as $row)
			$baseacl[$row['action']] = $row['perm'];
		
		$this->baseacl = $baseacl;
		$this->auth['acl'] = $acl;
		
		// should make this into magic __call per http://stackoverflow.com/a/3716750
		$this->endTimer(__METHOD__); 
		
		return $this;
	}
	
	public function aclTest($action)
	{	// action = 'action/app|var1/var2'
		$acl = $this->auth['acl'];
		$baseacl = $this->baseacl;
		//foreach($actions // recursive permission search
		
		// if the user has an ACL denying from current action, deny access.
		if(isset($acl[$action]) && $acl[$action] == 0)
			return false;
		
		// If a base acl rule says that an action is restricted
		if(isset($baseacl[$action]) && $baseacl[$action] == 0)
			// if user has acl to override the base acl
			if(isset($acl[$action]) && $acl[$action] == 1)
				return true;
			else // otherwise, deny per base acl
				return false;
		
		// access is granted by default
		return true;
	}
	
	public function simpleSelect($app = NULL)
	{
		// If this is being called without an app listed, assume simpleCMS
		if(is_null($app))
			$app = $this->settings['simple_cms'];
		
		$this->select['template'] = $this->settings['default_skin'];
		$this->select['title'] = $app;
		$this->select['alias'] = '';
		$this->vars = $this->action;
		$this->action = array('');
		
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
	 * $regex = '/'.implode('\/(', $this->actions).str_repeat( ')?' , count($this->actions) - 1 ).'/';
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
	$regex = '/'.implode('\/(', $this->actions).str_repeat( ')?' , count($this->actions) - 1 ).'/';
	preg_match_all($regex, $navcache, $selected); // would yeild all parents and chosen navigation item,
													// exposing the other actions as app variables.
	*/
	public function navSelect()
	{
		$this->startTimer(__METHOD__);
		if($this->settings['simple_cms'] != '_lfcms')
			$this->simpleSelect();
		
		
		
		
		/* Determine requested nav item from lf_actions */
		// get all possible matches for current request, 
		// always grab the first one in case nothing is selected
		$matches = $this->db->fetchall("
			SELECT * FROM lf_actions 
			WHERE alias IN ('".implode("', '", $this->action)."') 
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
		$parent = -1;
		$selected = array();
		for($i = 0; $i < count($this->action); $i++)
			if(isset($test_select[$parent]))
				foreach($test_select[$parent] as $position => $nav)
					if($nav['alias'] == $this->action[$i])
					{
						// we found the match, 
						// move on to next action item
						$selected[] = $nav;
						$parent = $nav['id'];
						break;
					}
		
		if($selected != array())
		{
			// separate action into vars and action base, 
			// pull select nav from inner most child
			$this->vars = array_slice($this->action, count($selected));
			$this->action = array_slice($this->action, 0, count($selected));
			$this->select = end($selected);
		}
		
		// If home page is an app and no select was made from getnav(), 
		// set current page as /
		if($this->select['alias'] == '404' && $base_save != NULL)
		{		
			$this->select = $base_save;
			$this->vars = $this->action; // the whole URL is now variables
			
			// And now littlefoot() thinks that we requested just /
			$this->action = array('');
		}
		
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
		foreach($this->action as $action)
		{
			if($action != '')	// Account for empty alias
				$actionbuilder .= $action.'/';	// Loop through the full/path. 
												
			// As the action request URI builds, replace each link matching that set to active.
			$nav_cache = str_replace(
				'<li><a href="'.$actionbuilder.'"', 
				'<li class="active"><a href="'.$actionbuilder.'"', 
				$nav_cache);
		}
		
		// If template has not be changed from 'default', set as configured default_skin.
		if($this->select['template'] == 'default')
			$this->select['template'] = $this->settings['default_skin'];
		
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
		
		// navcache needs this
		$nav_cache = str_replace('%baseurl%', $this->lf->wwwIndex, $nav_cache);
		
		$this->nav_cache = $nav_cache;
		$this->endTimer(__METHOD__);
		return $this;
	}
	
	public function getcontent()
	{
		$this->startTimer(__METHOD__);
		$funcstart = microtime(true);
		$this->hook_run('pre lf getcontent');
		
		if(!$this->aclTest(implode('/', $this->action)))
		{
			$this->content['content'][] = "401 Unauthorized at "
				.implode('/', $this->action)
				.$this->getLogin();
			return $this;
		}
		
		// Pull $apps list with section=>app
		if($this->settings['simple_cms'] != '_lfcms') #DEV
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
			$apps = $this->db->fetchall($sql);
		}
		
		// run them and save the output
		if(isset($this->content))
			$content = $this->content;
		else
			$content = array();
		
		$vars = $this->vars;
		foreach($apps as $_app)
		{
			// Test ACL for this app
			if(!$this->aclTest(implode('/', $this->action).'|'.$_app['app']) || (isset($vars[0]) && !$this->aclTest(implode('/', $this->action).'|'.$_app['app'].'/'.$vars[0]))) 
			{
				$content[$_app['section']][] = "403 Access Denied ".$this->lf->getLogin();
				continue;
			}
			
			// set app target path
			$path = ROOT.'apps/'.$_app['app'];
			if(!is_file($path.'/index.php')) continue;
			
			// figure out appurl (/action1/action2/ referring to this app)
			$appurl = $this->base.implode('/',$this->action);
			if($this->action[0] != '') 
				$appurl .= '/'; // account for home page
			$this->appurl = $appurl;
			
			// appbase (relbase for the app)
			$appbase = $this->relbase.implode('/',$this->action);
			if($this->action[0] != '') 
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
			
			$this->lf->startTimer($apptimer);
			include 'index.php'; // execute app
			$this->lf->endTimer($apptimer);
			
			$output = '
				<div id="'.$_app['app'].'-'.$_app['id'].'" class="app-'.$_app['app'].'">'.
					ob_get_clean().
				'</div>';
			
			// replace %keywords%
			$output = str_replace(
				'%appurl%', 
				$appurl, 
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
			
		$this->endTimer(__METHOD__);
		
		return $this;
	}
	
	public function legacyStringReplace($text)
	{
		$template = $text;
		/*
		// deprecated: use $this->lf->methods() instead
		// Replace all %markers% with $content
		if(isset($this->content))
			foreach($this->content as $key => $value)
				$template = str_replace($key, implode($value), $template);*/
				
		// replace global variables
		$global_replace = array(
			'%login%' => $this->getLogin(),
			'%title%' => $this->getTitle(),
			'%skinbase%' => $this->getSkinBase(),
			'%baseurl%' => $this->wwwIndex,
			'%relbase%' => $this->wwwLF
		);
		$template = str_replace(
			array_keys($global_replace), 
			array_values($global_replace), 
			$template
		);
		
		return $template;
		
	}
	
	// so you can breakpoint in an oop daisy chain: $this->lf->route(asdf)->oopbreak()->morestuffthatwonthappennow()
	public function oopbreak($var = "OOPBREAK")
	{
		pre($var,'var_dump');
		exit();
	}
	
	public function printLogin()
	{
		include LF.'system/template/login.php';
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
		return $this->wwwLF.'skins/'.$this->select['template'];
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
	
	public function printContent($key = 'content')
	{
		if(isset($this->content[$key]))
			return implode($this->content[$key]);
		else
			return 'Content not found: No such key "'.$key.'" set';
		
			/*foreach($this->content as $key => $value)
				$template = str_replace($key, implode($value), $template);*/
	}
	
	public function render($dir = NULL)
	{
		$this->startTimer(__METHOD__);
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
		$this->endTimer(__METHOD__);
		return $template;
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
	
	public function loadLfCSS()
	{
		$this->head .=  
			'<link rel="stylesheet" href="'.$this->lf->relbase.'lf/system/lib/lf.css" />
			<link rel="stylesheet" href="'.$this->lf->relbase.'lf/system/lib/3rdparty/icons.css" />';
			
		return $this;
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
		
		$this->hook_run('pre app');
		$this->hook_run('pre app '.$controller);
		if($func != $vars[0]) $this->hook_run('pre app '.$controller.' '.$func);
		
		$varstr = array();
		foreach($vars as $var) // add vars until they are all there
		{
			$varstr[] = $var;
			$this->hook_run('pre app '.$controller.' '.implode(' ', $varstr));
		}
		
		echo $class->$func($vars);
		
		while(count($varstr)) // subtract vars until they are all gone
		{	
			$this->hook_run('post app '.$controller.' '.implode(' ', $varstr));
			array_pop($varstr);
		}
		
		if($func != $vars[0]) $this->hook_run('post app '.$controller.' '.$func);
		
		$this->hook_run('post app '.$controller);
		$this->hook_run('post app');
		
		return ob_get_clean();
	}
	
	// mount, app/controller, $ini, $vars
	public function extmvc($mount, $controller_path, $ini ='', $vars = array(''))
	{
		//$vars = array_slice($vars, 1); // to get vars from subdir mount
		$controller_path = explode('/', $controller_path);
		
		$cwd = getcwd();
		chdir(ROOT.'apps/'.$controller_path[0]);
		$return = $this->mvc($controller_path[1], $ini, $vars);
		$return = str_replace('%appurl%', '%appurl%'.$mount.'/', $return);
		chdir($cwd);
		
		return $return;
	}
	
	public function megamvc($default = NULL, $offset = 0)
	{
		// if you specify $default, route on that by default
		if(!is_null($default) && $this->action[0] == '')
			$this->action[0] = $default;
			
		$this->vars = array_slice($this->action, $offset+1);
		return $this->mvc($this->action[$offset]);
	}
	
	// mount, app/controller, $ini, $vars
	public function loadapp($app, $admin, $ini ='', $vars = array(''), $custompath = NULL)
	{
		ob_start();
		$old = $this->vars;
		$this->vars = $vars;
		$var = $vars; // backward compatible
		
		
		$this->request = $this; // backward compatible with admin
		$cwd = getcwd();
		
		if($custompath == NULL)
			chdir(ROOT.'apps/'.$app); // just go into the app's folder
		else
			chdir($custompath);
		
		$_app['ini'] = $ini;
		
		if($admin) $file = 'admin.php';
		else $file = 'index.php';
		
		if(is_file($file)) include $file;
		else echo 'No such file';
		
		chdir($cwd);
		$this->vars = $old;
		
		return ob_get_clean();
	}
	
	// Backward compatible
	public function apploader($load, $ini = '', $vars = NULL) 
		{ return $this->mvc($load, $ini, $vars); }
	
	// should turn this into an API system for direct-to-app calls via json request.
	private function post($id)
	{
		$vars = $this->vars;
		$output = '';

		if($this->db->query('SELECT * FROM lf_links WHERE id = '.intval($link).' LIMIT 1'))
			$_app = $this->db->fetch();
		else
			die('invalid request');
		
		$path = ROOT.'apps/'.$_app['app'].'/index.php';
		if(is_file($path))
		{
			ob_start();
			include($path);
			$output = ob_get_clean();
			
			$output = str_replace(
				array(
					'%baseurl%',
					'%appurl%',
					'%post%'
				),
				array(
					$this->base,
					$this->base,
					$this->base.'post/'.$_app['id'].'/'
				),
				$output
			);
		}

		// by default, return to referer
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		//echo $output;
	}
	
	/**
	 * Initializes the plugin listener from lf_plugins table
	 */
	public function loadPlugins()
	{
		$result = (new LfPlugins)->getAll(); //(new orm)->qPlugins('lf')->getAll();
		
		if($result)
			foreach($result as $plugin)
				$this->plugins[ $plugin['hook'] ][ $plugin['plugin'] ] = $plugin['config'];
		
		$this->hook_run('plugins loaded');
		
		return $this;
	}
	
	/*
	// Add plugin function to execute when $hook happens
	private function hook_add($hook, $function)
	{
		if(!is_callable($function)) return false;
		
		$this->plugin_listen[$hook][] = $function;
		
		return true;
	}*/
	
	// Run hooks to execute plugins attached to them
	public function hook_run($hook)
	{
		if(!isset($this->plugins[$hook])) 
			return $this;
		
		foreach($this->plugins[$hook] as $plugin => $config)
		{
			$hookDetails = ' / '.$plugin.' @ '.$hook.' / Config: '.$config;
			
			$this->startTimer(__METHOD__.$hookDetails);
			include ROOT.'plugins/'.$plugin.'/index.php';
			$this->endTimer(__METHOD__.$hookDetails);
		}
		
		return $this;
	}
	/*
	public function __call($name, $arguments)
	{
		//preg_match('/^route(uid|)'
	}*/
	
	// backward compatible
	public function api($var)
	{
		$user = new User();
		$user->fromSession();
		
		if($var == 'getuid')	return $user->getid();
		if($var == 'me')		return $user->getdisplay_name();
		if($var == 'version')	return $this->version;
		if($var == 'isadmin')	return $user->hasAccess('admin');
	}
	
	public function adminTokenReplace($out)
	{
		$admin_skin = 'default';
		
		$out = str_replace('%skinbase%', $this->relbase.'lf/system/admin/skin/'.$admin_skin.'/', $out);
		$out = str_replace('%baseurl%', $this->base, $out);
		$out = str_replace('%relbase%', $this->relbase, $out);
		$out = str_replace('%skinbase%', $this->relbase.'lf/system/admin/skin/'.$admin_skin.'/', $out);
		return $out;
	}
}
