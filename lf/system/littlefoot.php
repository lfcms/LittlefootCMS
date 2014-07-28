<?php

class Littlefoot
{
	private $db;
	public $auth; // use api to read
	public $auth_obj; // system/lib/auth.php
	public $absbase;
	public $base;
	public $basenoget;
	public $relbase;
	public $appurl; // allow it to change
	
	public $action;
	public $select; // chosen nav item from request (include nav id)
	private $alias;
	
	public $admin;
	
	public $get;
	public $post;
	public $vars;
	
	private $start;
	public $debug;
	private $note;
	private $error;
	private $version;
	
	private $app_timer = array();
	public $function_timer = array();
	public $settings;
	public $msgg = '';
	 
	private $plugin_listen = array();
	
	public $head = ''; // just to put stuff in <head>
	
	public function __construct($db)
	{
		$this->lf = &$this; // universal usage of $this->lf
		
		$this->start = microtime(true);
		$this->version = file_get_contents(ROOT.'system/version');
		$this->db = new Database($db);
		
		// Recover session variables from last page load
		if(!isset($_SESSION['_auth'])) $_SESSION['_auth'] = '';
		$this->auth = $_SESSION['_auth'];
		if(!isset($this->auth['acl'])) $this->auth['acl'] = array();
		
		include ROOT.'system/lib/recaptchalib.php';
	}
	
	public function __destruct()
	{
		// Save auth variables for next page load.
		unset($this->auth['acl']); // so it is not in session
		$_SESSION['_auth'] = $this->auth;
		
		//var_dump($this->settings['debug']);
		//exit();
		
		//if($this->debug)
		
		// actual speed
		if($this->settings['debug'] == 'on')
		{
			echo '<!-- lf Stat Info
Version: '.$this->version.'
PHP Execution Time: '.round((microtime(true) - $this->start), 6)*(1000).'ms
Peak Memory Usage: '.round(memory_get_peak_usage()/1024/1024,2).' MB
Num Queries: '.$this->db->getNumQueries().'
Littlefoot function load times:
	';
			foreach($this->function_timer as $function => $time)
				echo ''.round($time, 6)*(1000).'ms - '.$function.'
	';
			echo '
App load times:
	';
			foreach($this->app_timer as $app => $time)
				echo ''.round($time, 6)*(1000).'ms - '.$app.'
	';
			echo '
-->';
		}
	}
	
	public function cms($debug = false) // run littlefoot as CMS
	{
		// Apply settings 
		$this->db->query('SELECT * FROM lf_settings');
		while($row = $this->db->fetch())
			$this->settings[$row['var']] = $row['val'];
		
		// JSON => array
		$this->lf->settings['plugins'] = json_decode($this->lf->settings['plugins'], 1);
		
	#	// load plugins old
	#	foreach(scandir('plugins') as $file)
	#	{
	#		if(substr($file, -4) != '.php') continue;
	#		include 'plugins/'.$file;
	#	}
		
		//plug-ins v2
		//if(is_dir('plugins/plugins_loaded_FALSE'))
		//	foreach(preg_grep('/^([^.])/', scandir('plugins/plugins_loaded')) as $plugin)
		//		include 'plugins/plugins_loaded/'.$plugin;
		
		$this->hook_run('plugins_loaded');
			
		// redirect to URL specified in 'force_url' setting
		if(isset($this->settings['force_url']) && $this->settings['force_url'] != '')
		{
			$relbase = preg_replace('/index.php.*/', '', $_SERVER['PHP_SELF']);
			$request = $_SERVER['HTTP_HOST'].$relbase;
			$compare = preg_replace('/^https?:\/\//', '', $this->settings['force_url']);
			
			// ty Anoop K [ http://stackoverflow.com/questions/4503135/php-get-site-url-protocol-http-vs-https ]
			$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			
			if($request != $compare)
			{
				$redirect = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				$redirect = preg_replace('/^'.preg_quote($request, '/').'/', $compare, $redirect);
				redirect302($protocol.$redirect);
			}
		}
		
		$this->select['template'] = $this->settings['default_skin'];
		$this->select['title'] = 'LFCMS';
		
		$this->hook_run('settings_loaded');
		
		$this->absbase = ROOT; // backward compatible // getcwd().'/';
		$this->select['alias'] = '404';
		
		if(is_dir(ROOT.'lib')) ini_set('include_path', ini_get('include_path').':'.ROOT.'lib');
		if(is_dir(ROOT.'system/lib')) ini_set('include_path', ini_get('include_path').':'.ROOT.'system/lib');
		
		if($debug || (isset($this->settings['debug']) && $this->settings['debug'] == 'on')) $this->debug = true;
		
		
		
		
		
		
		
		
		
		
		
		// request
		$funcstart = microtime(true);
		$admin = $this->request();
		$this->function_timer['request'] = microtime(true) - $funcstart;
		$funcstart = microtime(true);
		
		// auth
		$this->authenticate();
		$this->function_timer['auth'] = microtime(true) - $funcstart;
		$funcstart = microtime(true);
		
		
		
		
		
		
		/*
		// to post to a specific app without loading the rest of the CMS (should be to link in db, not app folder, this does not seem secure at the moment)
		if( isset($this->action[0]) && $this->action[0] == 'post' && 
			preg_match('/[0-9]+/', $this->action[1], $match) && count($_POST) )
		{
			$link = $match[0];
			$this->vars = array_slice($this->action, 2);
			//include('system/post.php');
			$this->post($match[0]);
			return 0;
		}*/
		
		// if requested, load admin/
		if($admin)
		{
			chdir('system/admin');
			
			$admin_skin = 'leap'; // this needs to be an option instead of hard coded
			
			// maybe you are an admin, but I need you to login first
			if($this->auth['access'] != 'admin' && strpos($this->auth['access'], 'app_') === false)
			{
				//$publickey = '6LffguESAAAAAKaa8ZrGpyzUNi-zNlQbKlcq8piD'; // littlefootcms public key
				$recaptcha = '';//recaptcha_get_html($publickey);
				
				ob_start();
				include('skin/'.$admin_skin.'/login.php'); 
				echo str_replace('%skinbase%', $this->relbase.'lf/system/admin/skin/'.$admin_skin.'/', ob_get_clean());
				exit; 
			}
			
			if($this->auth['access'] == 'admin')
			{
				include('loader.php');
				$this->function_timer['admin'] = microtime(true) - $funcstart;
				$this->app_timer['no apps, just admin'] = 0;
				return 0;
			}
			
			if(strpos($this->auth['access'], 'app_') !== false)
			{
				$admin_skin = 'fresh';
				$app = explode('_', $this->auth['access']);
				$app_name = $app[1];
				$app = $this->loadapp($app_name, true, '', $this->action);
				
				$app = str_replace('%appurl%', $this->base.'admin/', $app);
				
				ob_start();
				include('skin/'.$admin_skin.'/index.php');
				$out = str_replace('%skinbase%', $this->relbase.'lf/system/admin/skin/'.$admin_skin.'/', ob_get_clean());
				$out = str_replace('%baseurl%', $this->base.'admin/', $out);
				$out = str_replace('%relbase%', $this->relbase, $out);
				$out = str_replace('Littlefoot CMS', ucfirst($app_name).' Admin', $out);
				$out = str_replace(array('<nav>', '</nav>'), '', $out);
				$out = str_replace('class="content"', 'class="content" style="margin: 10px;"', $out);
				
				echo $out;
				return 0;
			}
				
		}
								
		$this->auth['acl'] = $this->apply_acl();
		/*
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
								}*/
		
		if($this->settings['simple_cms'] != '_lfcms') #DEV
		{
			$this->select['title'] = 'SimpleCMS';
			$this->select['template'] = $this->settings['default_skin'];
			
			$this->vars = $this->action;
			$this->action = array('');
		}
		else
		{
			// generate nav bar and process request against available actions
			$nav = $this->nav();
			$this->function_timer['nav'] = microtime(true) - $funcstart;
			$funcstart = microtime(true);
			
			// if no items match the request, return 404
			if($this->select['alias'] == '404')
			{
				header('HTTP/1.1 404 Not Found');
				echo '<p>404 No menu items match your request</p>';
				return 0;
			}
		}
		
		$this->appurl = $this->base.implode('/', $this->action).'/';
		
		// apply acl, check auth for current page.
		//$this->auth['acl'] = $this->apply_acl();
		if(!$this->acl_test(implode('/', $this->action))) {
		// if(!$this->acl_test($this->select['id'])) {
			$content['%content%'][] = "403 Access Denied %login%";
		}
		else
		{
			// get content from apps
			$content = $this->getcontent();
			$this->function_timer['getcontent'] = microtime(true) - $funcstart;
			$funcstart = microtime(true);
		}
		
		if($this->settings['simple_cms'] == '_lfcms') #DEV
		{
			$content['%nav%'][] = $nav;
		}
		
		// display in skin
		$output = $this->render($content);
		$this->function_timer['render'] = microtime(true) - $funcstart;
		/*
								//CACHING - will not account for update to page...
								if(isset($this->settings['cache']) && $this->settings['cache'] = 'on')
								{
									$auth = $this->auth;
									unset($auth['last_request'], $auth['timeout']);
									$file = md5(json_encode($this->base.implode('/', $this->action).implode('/', $this->vars)).json_encode($auth).json_encode($this->baseacl)).'output.html';
									file_put_contents(ROOT.'cache/'.$file, $output);
								}*/
								
		echo $output;
	}
	
	public function request()
	{
		// detect file being used as base (for API)
		$filename = 'index.php';
		if(preg_match('/^(.*)\/([^\/]+\.php)$/', $_SERVER['SCRIPT_NAME'], $match))
			$filename = $match[2];
		
		// Extract subdir
		$pos = strpos($_SERVER['SCRIPT_NAME'], $filename);
		$subdir = $pos != 0 ? substr($_SERVER['SCRIPT_NAME'], 1, $pos-1) : '/';
		
		// Break up request URI and extract GET request
		$url = explode('?', $_SERVER['REQUEST_URI'], 2);
		if(substr($url[0], -1) != '/') $url[0] .= '/'; //Force trailing slash
		
		$this->get = $_GET;
		$this->post = $_POST;
		
		// Detect subdirectory, use of index.php, request of admin, other URI variables and the GET request
		preg_match('/(\/'.str_replace('/', '\/', $subdir).')(.+.php\/)?(admin\/)?([^\?]*)(.*)/', $url[0], $request);
		//$regex = '/('.str_replace('/', '\/', $subdir).')(.+.php\/)?/';
		//preg_match($regex, $url[0], $request);
		
		$fixrewrite = false; // add in 302 to fix rewrite duplicate content #FIX
		if($this->settings['rewrite'] == 'on') 
		{
			if($request[2] == 'index.php/') $fixrewrite = true;
			$request[2] = '';
		}
		if($this->settings['rewrite'] == 'off')
		{
			if($request[2] == '') $fixrewrite = true;
			$request[2] = $filename.'/';
		}
		
		if($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443)
			$port = ':'.$_SERVER['SERVER_PORT']; 
		else $port = '';
		
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
		{
			$protocol = 'https://';
		} else
			$protocol = 'http://';
		
		
		$this->base = $protocol.$_SERVER['HTTP_HOST'].$port.$request[1].$request[2]; // account for use of index.php/
		$this->relbase = $request[1]; // /subdir/ for use with web relative file reference
		
		$this->basenoget = $this->base.$request[3].$request[4];
		
		if($fixrewrite) redirect302($this->base.$request[3].$request[4].$request[5]);
		
		if(substr_count($request[4], '/') > 60) die('That is a ridiculous number of slashes in your URI.');
		else
		{
			$this->action = explode('/', $request[4], -1);
			
			// Ensure action variable isn't empty
			if(count($this->action) < 1)
				$this->action[] = '';
		}
		
		$this->admin = $request[3] == 'admin/' ? true : false; // for API
		
		// Whether or not this is an admin/ request
		return $request[3] == 'admin/' ? true : false;
	}
	
	public function authenticate()
	{
		$this->hook_run('pre_auth'); 
		
		// eventually, I want to use this object as the $this->auth variable (like ->db) instead of an array. ie, $this->lf->auth->getuid();
		include 'system/lib/auth.php';
		$auth = new auth($this, $this->db);
		
		
		// change to auth class 
		if($this->action[0] == '_auth' && isset($this->action[1]))
		{
			$out = $auth->_router($this->action);
			$out = str_replace('%appurl%', $this->base.'_auth/', $out);
			$content['%content%'][] = $out;
			
			// display in skin
			echo $this->render($content);
			
			exit(); // end auth session after render, 
			// otherwise it will 302 (login/logout)
		}
		
		$this->auth = $auth;
		
		$auth = $auth->auth;
		
		// for tinymce ajax file manager auth
		if(isset($auth['access']) && $auth['access'] == 'admin')
			$_SESSION['ajax_user'] = true;
		else
			$_SESSION['ajax_user'] = false;
		
		// If no user is currently set...
		if(!isset($auth['user']))
		{
			// default to anonymous
			$auth['user'] = 'anonymous';
			$auth['display_name'] = 'Anonymous';
			$auth['id'] = 0;
			$auth['access'] = 'none';
		}
		
		$this->auth = $auth;
	}
	
	private function apply_acl()
	{
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
		
		// get a list of groups from inheritance
		$groups = get_acl_groups($inherit, $this->auth['access']);
		$groups[] = $this->auth['access'];
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
		return $acl;
	}
	
	public function acl_test($action)
	{	// action = 'action/app|var'
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
	
	private function nav()
	{
		// need to utilize cache instead of query
	
	
		/* Determine requested nav item from lf_actions */
		
		// get all possible matches for current request, always grab the first one in case nothing is selected
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
			if($row['position'] == 1 && $row['parent'] == -1) // save item in first spot of base menu if it is an app, just in case nothing matches
				$base_save = $row; // save row in case "domain.com/" is requested
				
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
						$selected[] = $nav;
						$parent = $nav['id'];
						break; // we found the match, move on to next action item
					}
		
		if($selected != array())
		{
			// separate action into vars and action base, pull select nav from inner most child
			$this->vars = array_slice($this->action, count($selected));
			$this->action = array_slice($this->action, 0, count($selected));
			$this->select = end($selected);
		}
		
		// If home page is an app and no select was made from getnav(), set current page as /
		if($this->select['alias'] == '404' && $base_save != NULL)
		{		
			$this->select = $base_save;
			$this->vars = $this->action; //
			$this->action = array(''); // And now littlefoot() thinks that we requested just /
		}
		
		
		if(!is_file(ROOT.'cache/nav.cache.html')) // in case the file doesn't exist
		{
			$pwd = getcwd();
			chdir(ROOT.'system/admin/');
			$this->mvc('dashboard', NULL, array('updatenavcache'));
			chdir($pwd);
		}
		$nav_cache = file_get_contents(ROOT.'cache/nav.cache.html');
		
		// Update nav_cache to show active items
		$actionbuilder = '%baseurl%';
		foreach($this->action as $action)
		{
			if($action != '') $actionbuilder .= $action.'/';
			$nav_cache = str_replace('<li><a href="'.$actionbuilder.'"', '<li class="active"><a href="'.$actionbuilder.'"', $nav_cache);
		}
		
		if($this->select['template'] == 'default')
			$this->select['template'] = $this->settings['default_skin'];
		
		// set nav ul class if set
		$class = isset($this->settings['nav_class']) ? $this->settings['nav_class'] : 'navigation';
		
		// Apply class to root <ul> if it is set
		if($class != '') $nav_cache = preg_replace('/^<ul>/', '<ul class="'.$class.'">', $nav_cache);
		
		return $nav_cache;
	}
	
	private function getcontent()
	{
		$funcstart = microtime(true);
		
		
		if($this->settings['simple_cms'] != '_lfcms') #DEV
		{
			$apps = array(0 =>
				array(
					'id' => 0, 
					'app' => $this->settings['simple_cms'],
					'ini' => '',
					'section' => 'content'
				)
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
		$content = array();
		$vars = $this->vars;
		foreach($apps as $_app)
		{
			if(!$this->acl_test(implode('/', $this->action).'|'.$_app['app']) || (isset($vars[0]) 
				&& !$this->acl_test(implode('/', $this->action).'|'.$_app['app'].'/'.$vars[0]))) 
			{
				$content['%'.$_app['section'].'%'][] = "403 Access Denied %login%";
				continue;
			}
			
			// set app target path
			$path = ROOT.'apps/'.$_app['app'];
			if(!is_file($path.'/index.php')) continue;
			
			$output = ''; // backward compatible
			
			$appurl = $this->base.implode('/',$this->action);
			if($this->action[0] != '') $appurl .= '/'; // account for home page
			$this->appurl = $appurl;
			
			// collect app output
			ob_start();
			chdir($path); // set current working dir to app base path
			
			$start = microtime(true); // timer for app
			
			include 'index.php'; // execute app
			
			$this->app_timer['Link Id: '.$_app['id'].', App: '.$_app['app'].', Position: '.$_app['section']] = microtime(true) - $start; //timer for app
			
			echo $output; // backward compatible
			
			$replace = array(				
				//'%baseurl%' => $this->base, // domain.com/subdir/(index.php/)?
				'%appurl%' => $appurl, // %baseurl%action/
				//'%relbase%' => $this->relbase, // domain.com/subdir/
				'%post%' => $this->base.'post/'.$_app['id'].'/' // %baseurl%post/link_id/
			);
			
			$output = '
				<div id="'.$_app['app'].'-'.$_app['id'].'" class="app-'.$_app['app'].'">'.
					ob_get_clean().
				'</div>';
				
			// replace %keywords% and save
			$content['%'.$_app['section'].'%'][] = str_replace(array_keys($replace), array_values($replace), $output);
			
			// reset for next go around
			$this->appurl = '';
		}
		
		chdir(ROOT); // cd back to ROOT for the rest of the app
		
		// DEV DEV DEV DEV // plugins 2.0
		/*foreach($this->lf->settings['plugins']['postcontent'] as $plugin => $devnull)
			include ROOT.'plugins/'.$plugin.'/index.php';*/
		// END DEV DEV DEV DEV
		
		return $content;
	}
	
	public function render($replace)
	{
		ob_start();
		include ROOT.'system/template/login.php';
		$login = ob_get_clean();
		
		// home.php
		$file = 'index';
		if($this->select['parent'] == -1 && $this->select['position'] == 1 && ( is_file(ROOT.'skins/'.$this->select['template'].'/home.php') || is_file(ROOT.'skins/'.$this->select['template'].'/home.html')))
			$file = 'home';
		
		
		// Get Template code
		ob_start();
		if(is_file(ROOT.'skins/'.$this->select['template']."/$file.php"))
			include(ROOT.'skins/'.$this->select['template']."/$file.php");
		else if(is_file(ROOT.'skins/'.$this->select['template']."/$file.html"))
			readfile(ROOT.'skins/'.$this->select['template']."/$file.html");
		else
			echo 'Template files missing. Log into admin and select a different template with the Skins tool.';
			
		$template = ob_get_clean();
		
		// Replace all %markers% with $content
		if(isset($replace))
			foreach($replace as $key => $value)
				$template = str_replace($key, implode($value), $template);
		
		// replace global variables
		$global_replace = array(
			'%login%' => $login,
			'%title%' => $this->select['title']." | ".$_SERVER['HTTP_HOST'],
			'%skinbase%' => $this->relbase.'lf/skins/'.$this->select['template'],
			'%baseurl%' => $this->base,
			'%relbase%' => $this->relbase
		);
		$template = str_replace(array_keys($global_replace), array_values($global_replace), $template);
		
		
		if($this->settings['debug'] == 'on')
		{
			ob_start();
			echo '<div style="clear: both; text-align: center; color: #333; background: #FFF; width:500px; margin: 20px auto; padding:10px;" >
					<h2 style="color: #999;">Debug Information</h2>
					<p>This debug information was printed from lf->render(). If you want the whole CMS speeds, check the bottom of the source code</p>
					<p style="color: #333">Version: '.$this->version.'</p>
					<p style="color: #333">Execution Time: '.round((microtime(true) - $this->start), 6)*(1000).'ms</p>
					<p style="color: #333">Memory Usage: '.round(memory_get_peak_usage()/1024,2).' kb</p>
					Littlefoot function load times:
					<table style="margin: auto; color: #000;">
			';
			foreach($this->function_timer as $function => $time)
				echo '<tr><td>'.$function.'</td><td>'.round($time, 6)*(1000).'ms</td></tr>';
			echo '
					</table>
					App load times:
					<table style="margin: auto; color: #000;">
			';
			foreach($this->app_timer as $app => $time)
				echo '<tr><td>'.$app.'</td><td>'.round($time, 6)*(1000).'ms</td></tr>';
			echo '</table></div>';
			$debug = ob_get_clean();
			
			$template = str_replace('%debug%', $debug, $template);
		}
		
		$template = str_replace('</head>', $this->lf->head.'</head>', $template);
		
		// Clean up unused %replace%
		return preg_replace('/%[a-z]+%/', '', $template);
	}
	
	// Auto load given class name in controller/ folder.
	// Quick way to MVC with multiple class/method requests hooked into URL
	public function mvc($controller, $ini = '', $vars = NULL)
	{
		ob_start();
		if($vars === NULL) $vars = $this->vars;
		if(!isset($vars[0])) $vars[0] = '';
		
		if(!is_file('controller/'.$controller.'.php')) // If controller file is missing at /docroot/lf/apps/<app_name>/controller/<load>.php
			return 'Invalid request. File not found at '.getcwd().'/controller/'.$controller.'.php';
		
		if(!class_exists($controller)) // include specified controller class
			include 'controller/' . $controller . '.php';
		
		$class = new $controller($this, $this->db, $ini, $vars); // init class specified by $controller
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
		
		echo $class->$func($vars);
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
	
	//public function loadapp2($app, $ini = '', $vars = array()){
		
	//}
	
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
	public function apploader($load, $ini = '', $vars = NULL) { return $this->mvc($load, $ini, $vars); }
	
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
	
	/*private load_plugin() {
	
	}*/
	
	
	// Add plugin function to execute when $hook happens
	private function hook_add($hook, $function)
	{
		if(!is_callable($function)) return false;
		
		$this->plugin_listen[$hook][] = $function;
		
		return true;
	}
	
	// Run hooks to execute plugins attached to them
	public function hook_run($hook)
	{
		if(!isset($this->plugin_listen[$hook])) return false;
		
		$return = array();
		foreach($this->plugin_listen[$hook] as $function)
			$return[$function] = $function($this);
			
		return $return;
	}
	
	
	// public, read-only access to private variables
	public function api($var)
	{
		if($var == 'getuid')	return $this->auth['id'];
		if($var == 'me')		return $this->auth['display_name'];
		if($var == 'version')	return $this->version;
		if($var == 'isadmin')	return $this->auth['access'] == 'admin';
	}
}

?>
