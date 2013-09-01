<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information.

class Littlefoot
{
	private $db;
	private $auth; // use api to read
	public $absbase;
	public $base;
	public $relbase;
	
	public $action;
	private $select; // chosen nav item from request (include nav id)
	private $alias;
	
	public $get;
	public $post;
	public $vars;
	
	private $start;
	private $debug;
	private $note;
	private $error;
	
	private $app_timer = array();
	public $function_timer = array();
	public $settings;
	public $msgg = '';
	 
	private $plugin_listen = array();
	
	public function __construct($db)
	{
		$this->start = microtime(true);
		
		if(false)
		// load plugins
		foreach(scandir('plugins') as $file)
		{
			if(substr($file, -4) != '.php') continue;
			include 'plugins/'.$file;
		}
		
		//plug-ins v2
		if(is_dir('plugins/plugins_loaded_FALSE'))
			foreach(preg_grep('/^([^.])/', scandir('plugins/plugins_loaded')) as $plugin)
				include 'plugins/plugins_loaded/'.$plugin;
		
		$this->hook_run('plugins_loaded');
		
		// Recover auth variables from last page load
		if(!isset($_SESSION['auth'])) $_SESSION['auth'] = '';
		$this->auth = $_SESSION['auth'];
		$this->db = new Database($db);
		
		// Apply settings 
		$this->db->query('SELECT * FROM lf_settings');
		while($row = $this->db->fetch())
			$this->settings[$row['var']] = $row['val'];
			
		$this->hook_run('settings_loaded');
			
		$this->absbase = ROOT; // backward compatible // getcwd().'/';
		$this->select['alias'] = '404';
		
		if(is_dir(ROOT.'lib')) ini_set('include_path', ini_get('include_path').':'.ROOT.'lib');
	}
	
	public function __destruct()
	{
		// Save auth variables for next page load.
		unset($this->auth['acl']); // so it is not in session
		$_SESSION['auth'] = $this->auth;
		
		if($this->debug)
		{
			echo '
				<div style="clear: both; text-align: center; color: #999; background: #FFF; width:500px; margin: 20px auto; padding:10px;" >
					<h2 style="color: #999;">Debug Information</h2>
					<p>Execution Time: '.round((microtime(true) - $this->start), 6)*(1000).'ms</p>
					<p>Memory Usage: '.round(memory_get_peak_usage()/1024,2).' kb</p>
					XMS function load times:
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
			echo '
					</table>
				</div>
			';
		}
	}
	
	public function run($debug = false)
	{
		if($debug) $this->debug = true;
		
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
		// to post to a specific app without loading the rest of the CMS (should be to link in db, not app folder)
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
			include('loader.php');
			$this->function_timer['admin'] = microtime(true) - $funcstart;
			$this->app_timer['no apps, just admin'] = 0;
			return 0;
		}
		
		$class = '';
		if(isset($this->settings['nav_class']))
			$class = $this->settings['nav_class'];
		
		// generate nav bar and process request against available actions
		$nav = $this->nav($class);
		$this->function_timer['nav'] = microtime(true) - $funcstart;
		$funcstart = microtime(true);
		
		// if no items match the request, return 404
		if($this->select['alias'] == '404')
		{
			header('HTTP/1.1 404 Not Found');
			echo '<p>404 No menu items match your request</p>';
			return 0;
		}
		
		// apply acl, check auth for current page.
		$this->auth['acl'] = $this->apply_acl();
		if(!$this->acl_test(implode('/', $this->action))) {
		// if(!$this->acl_test($this->select['id'])) {
			header('HTTP/1.1 403 Forbidden');
			$content['%content%'][] = "403 Access Denied";
		}
		else
		{
			// get content from apps
			$content = $this->getcontent();
			$this->function_timer['getcontent'] = microtime(true) - $funcstart;
			$funcstart = microtime(true);
		}
		
		$content['%nav%'][] = $nav;
		
		// display in skin
		$this->render($content);
		$this->function_timer['render'] = microtime(true) - $funcstart;
	}
	
	private function request()
	{
		// Extract subdir
		$pos = strpos($_SERVER['SCRIPT_NAME'], 'index.php');
		$subdir = $pos != 0 ? substr($_SERVER['SCRIPT_NAME'], 1, $pos-1) : '/';
		
		// Break up request URI and extract GET request
		$url = explode('?', $_SERVER['REQUEST_URI'], 2);
		if(substr($url[0], -1) != '/') $url[0] .= '/'; // Force trailing slash
		
		$this->get = $_GET;
		$this->post = $_POST;
		
		// Detect subdirectory, use of index.php, request of admin, other URI variables and the GET request
		preg_match(
			'/(\/'.str_replace('/', '\/', $subdir).')(index.php\/)?(admin\/)?([^\?]*)(.*)/',
			$url[0],
			$request
		);
		
		$this->relbase = $request[1]; // /subdir/ for use with web relative file reference
		
		// add in 302 to fix rewrite duplicate content #FIX
		if($this->settings['rewrite'] == 'off')
			$request[2] = 'index.php/'; //$this->settings[$row['var']];
		else
			$request[2] = '';
			
		if($_SERVER['SERVER_PORT'] != 80)
			$port = ':'.$_SERVER['SERVER_PORT'];
		else $port = '';
			
		$this->base = 'http://'.$_SERVER['SERVER_NAME'].$port.$request[1].$request[2]; // account for use of index.php/
		
		if(substr_count($request[4], '/') > 30) die('That is a ridiculous number of slashes in your URI.');
		else
		{
			$this->action = explode('/', $request[4], -1);
			
			// Ensure action variable isn't empty
			if(count($this->action) < 1)
				$this->action[] = '';
		}
		
		// Whether or not this is an admin/ request
		return $request[3] == 'admin/' ? true : false;
	}
	
	private function authenticate()
	{
		$this->hook_run('pre_auth');
		
		$auth = $this->auth;
		
		// If no user is currently set...
		if(!isset($auth['user']))
		{
			// default to anonymous
			$auth['user'] = 'anonymous';
			$auth['id'] = 0;
			$auth['access'] = 'none';
		}
		
		// If anonymous...
		if($auth['user'] == 'anonymous')
		{
			// check for normal login
			if(isset($this->post['submit']) && $this->post['submit'] == 'Log In')
			{
				// Get user/pass from $_POST and hash pass
				preg_match('/[a-zA-Z0-9]+/', $this->post['user'], $filter);
				$username = $filter != array() ? $filter[0] : '';
				$password = sha1($this->post['pass']);
		
				//Get user
				$sql = sprintf("
						SELECT id, user, last_request, display_name, access
						FROM lf_users
						WHERE user = '%s' AND pass = '%s'
						LIMIT 1
					",
					mysql_real_escape_string($username),
					$password
				);
				
				//Execute Query
				$result = $this->db->query($sql);
				
				//Check if user exists
				if(mysql_num_rows($result) == 0)
				{
					$this->error = "Incorrect Username or Password";
				}
				else $auth = $this->db->fetch($result);
				
				// dont let those apps see your password.
				unset($_POST['pass'], $this->post['pass']);
			}
			else if(is_file('lib/facebook.php')) //otherwise, try to authenticate with facebook
			{
				// Facebook login
				include 'lib/facebook.php';
				
				// Facebook login wrapper
				
				if(isset($auth['facebook']))
					$_SESSION = $auth['facebook'];
				else
					$_SESSION = array();
				
				$facebook = new Facebook(array(
				  'appId'  => '331251286935295',
				  'secret' => '1442db0f6a7675d44d9a5022ac23c04d',
				));

				$userId = $facebook->getUser();
				
				$auth['facebook'] = $_SESSION;
				
				
				// logged in via fb
				if ($userId) { 
					$userInfo = $facebook->api('/' + $userId);
					
					//Get user with facebook id
					$sql = "
						SELECT u.id, u.user, u.last_request, u.display_name, a.acl
						FROM lf_users u
						LEFT JOIN lf_admins a
							ON a.uid = u.id
						WHERE u.fbid = ".$userId." LIMIT 1
					";
					
					//Execute Query
					$result = $this->db->query($sql);
					
					if(!mysql_num_rows($result)) // if no user is found with this fbid
					{
						// create user account
						$sql = "
							INSERT INTO lf_users
								(`id`, `user`, `pass`, `email`, `display_name`, `salt`, `last_request`, `status`, `access`, `fbid`)
							VALUES
								(NULL, '".str_replace(' ', '', lcfirst($userInfo['name'])).substr($userId, 0, 4)."', 'null', 'null', '".$userInfo['name']."', 'null', NOW(), 'null', 'null', ".$userId.")
						";
						//Execute Query
						$result = $this->db->query($sql);
						
						$auth = array(
							'id' => mysql_insert_id(),
							'user' => $userId,
							'display_name' => $userInfo['name'],
							'acl' => array('null')
						);
					}
					else
					{
						$auth = $this->db->fetch($result);
						$auth['acl'] = explode(',', $auth['acl']);
					}
					
					// Backward compatible
					$auth['access'] = 'public';
					if(in_array('superadmin', $auth['acl'])) $auth['access'] = 'admin';
				}
			} 
		}
		else // if currently logged in
		{
			// check for logout request && ignore facebook redirecting from ?logout
			if(isset($this->get['logout']) && !strpos($_SERVER['HTTP_REFERER'], 'facebook'))
			{
				// reset session
				session_destroy();
				$auth = array();
				$this->note = 'logout';
			}
			
			else if(isset($auth['timeout']) && $auth['timeout'] < time() && false) // disabled for now #debug
			{
				//save user for quick re-login
				$user = $auth['user'];
				
				//session_destroy();
				$this->error = "You timed out. Please log back in.";
				
				// default to anonymous
				$auth = array();
				
			}
			
			else
			{
				$auth['last_request'] = date('Y-m-d G:i:s');
				$auth['timeout'] = time() + 60*30; // timeout in 30 minutes
			}
		}
		
		// for tinymce ajax file manager auth
		if(isset($auth['access']) && $auth['access'] == 'admin')
			$auth['ajax_user'] = true;
		else
			$auth['ajax_user'] = false;
		
		// If no user is currently set...
		if(!isset($auth['user']))
		{
			// default to anonymous
			$auth['user'] = 'anonymous';
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
					$groups = array_merge(
						$groups,
						get_acl_groups($inherit, $group)
					); 
			return array_unique($groups);
		}
		
		//echo ' select: '.$this->select['id']; // use this instead: id|app/method
		
		// get a list of groups from inheritance
		$groups = get_acl_groups($inherit, $this->auth['access']);
		$groups[] = $this->auth['access'];
		$affects = "'".implode("', '", $groups)."'"; // and get them ready for SQL
		
		// Build user ACL from above group list and individual rules
		$acl = array();
		$baseacl = array();
		//$baseacl = array();
		$rows = $this->db->fetchall("
			SELECT action, perm FROM lf_acl_user 
			WHERE
				affects = '".$this->api('getuid')."' 
				OR affects IN (".$affects.")
			
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
	
	private function nav($class = '')
	{
		$funcstart = microtime(true);
		
		// Grab all possible actions
		$this->db->query("SELECT * FROM lf_actions ORDER BY ABS(position) ASC");
		
		$base_save = NULL;
		$menu = array();
		while($row = $this->db->fetch())
		{
			// stack multiple 0 positions toward the negative
			if($row['position'] == 0 && isset($menu[$row['parent']]))
				$row['position'] = min(array_keys($menu[$row['parent']])) - 1;
			
			if($row['position'] == 1 && $row['parent'] == -1 && $row['app']) // save item in first spot of base menu
				$base_save = $row; // save row in case "domain.com/" is requested
			
			// Make a matrix sorted by parent and position
			$menu[$row['parent']][$row['position']] = $row;
		}
		// Generate nav bar and determine user request
		$nav = $this->getnav($menu);
		
		// If home page is an app and no select was made from getnav(), set current page as /
		if($this->select['alias'] == '404' && $base_save != NULL)
		{
			if($base_save['template'] == 'default')
				$base_save['template'] = $this->settings['default_skin'];
				
			$this->select = $base_save;
			$this->vars = $this->action; //
			$this->action = array(''); // And now littlefoot() thinks that we requested just /
		}
		
		// Apply class to root <ul> if it is set
		if($class != '')
			$nav = preg_replace('/^<ul>/', '<ul class="'.$class.'">', $nav);
			
		return $nav;
	}
	
	// cycle through menu items and built a hierachy based on each item's parent id
	// also, determine the menu item being requested by the user
	private function getnav($menu, $parent = -1, $prefix = '')
	{
		$items = $menu[$parent];
		
		$html = '<ul>';
		foreach($items as $item) // loop through the i
		{
			$newprefix = $prefix;
			$newprefix[] = $item['alias'];
			
			// If the item is marked as an 'app', check if the URL request could be for it
			if($item['app'] == 1 && $newprefix == array_slice($this->action, 0, count($newprefix)))
			{
				$this->vars = array_slice($this->action, count($newprefix));
				$this->action = $newprefix; // since you found the match, allow that code to be true
			}
			
			$selected = '';
			// if this menu item is what we are looking for, save it
			if($this->action == $newprefix)
			{
				if($item['template'] == 'default')
					$item['template'] = $this->settings['default_skin'];
				
				$this->select = $item;
				$selected = ' class="active"';
			}
			
			// if this is a normal nav item, and not a hook...
			if($item['position'] > 0)
			{
				// Generate printable request in/this/form
				$link = implode('/',$newprefix);
				if(strlen($link) != 0) 
					$link .= '/';
				
				// and generate the <li></li> element content
				$html .= '
					<li'.$selected.'>
						<a href="'.$this->base.$link.'" title="'.$item['title'].'">'.
							$item['label'].
						'</a>';
			
				// Process any submenus before closing <li>
				if(isset($menu[$item['id']]))
					$html .= $this->getnav($menu, $item['id'], $newprefix);
					
				$html .= '
					</li>';
			}
		}
		$html .= '
			</ul>';
		
		return $html;
	}
	
	private function getcontent()
	{
		$funcstart = microtime(true);
		$sql = "
			SELECT id, app, ini, section 
			FROM lf_links
			WHERE include = '".$this->select['id']."'
				OR include = '%'
			ORDER BY id
		";
		
		// Grab all active possible connections to currently selected menu item
		$apps = $this->db->fetchall($sql);
		
		$vars = $this->vars;
		
		// run them and save the output
		$content = array();
		
		//while($_app = $this->db->fetch($data))
		foreach($apps as $_app)
		{
			if(!$this->acl_test(implode('/', $this->action).'|'.$_app['app']) 
				|| (isset($vars[0])	&& !$this->acl_test(implode('/', $this->action).'|'.$_app['app'].'/'.$vars[0])))
			{
				$content['%'.$_app['section'].'%'][] = "403 Access Denied";
				continue;
			}
			
			// set app target path
			$path = ROOT.'apps/'.$_app['app'];
			if(!is_file($path.'/index.php')) continue;
			
			$output = ''; // backward compatible
			
			// collect app output
			ob_start();
			chdir($path); // set current working dir to app base path
			
			$start = microtime(true); // timer for app
			include 'index.php'; // execute app
			$this->app_timer['Link Id: '.$_app['id'].', App: '.$_app['app'].', Position: '.$_app['section']] = microtime(true) - $start; //timer for app
			
			echo $output; // backward compatible
			
			// replace %keywords% and save
			$appurl = $this->base.implode('/',$this->action);
			if($this->action[0] != '') $appurl .= '/'; // account for home page
			
			$content['%'.$_app['section'].'%'][] = str_replace(
				array(
					'%baseurl%', // domain.com/subdir/(index.php/)?
					'%appurl%', // %baseurl%action/
					'%relbase%', // domain.com/subdir/
					'%post%' // %baseurl%post/link_id/
				),
				array(
					$this->base,
					$appurl,
					$this->relbase,
					$this->base.'post/'.$_app['id'].'/'
				),
				ob_get_clean()
			);
		}
		
		chdir(ROOT); // cd back to ROOT for the rest of the app
		
		return $content;
	}
	
	private function render($replace)
	{
		$funcstart = microtime(true);
		
		// Get Template code
		ob_start();	
		readfile(ROOT.'skins/'.$this->select['template'].'/index.php');
		$template = ob_get_clean();
		
		// Setup Base URL and replace base url
		$url = array(
			$this->relbase.'lf/skins',
			$this->select['template']
		);
		
		$template = str_replace(array("%baseurl%", '%skinbase%'), $this->relbase.'lf/skins/'.$this->select['template'], $template);
		
		// Take Care of title and navigation
		$template = str_replace("%title%", $this->select['title']." | ".$_SERVER['SERVER_NAME'], $template);
		
		// Replace all %markers%
		if(isset($replace))
			foreach($replace as $key => $value)
			{
				$template =
					str_replace(
						$key,
						implode($value), // collapse all output content item attached to this key
						$template
					);
			}
		
		// Clean up unused %replace%
		$template = preg_replace('/%[a-z]+%/', '', $template);
		
		echo $template;
	}
	
	// Auto load given class name in controller/ folder.
	// Quick way to MVC with multiple class/method requests hooked into URL
	public function apploader($load, $ini = '', $vars = NULL)
	{
		if($vars == NULL) $vars = $this->vars;
			
		if(!is_file('controller/'.$load.'.php')) // If controller file is missing at /docroot/lf/apps/<app_name>/controller/<load>.php
			return 'Invalid request. File not found at '.getcwd().'/controller/'.$load.'.php';
		
		if(!class_exists($load))
			include 'controller/'.$load.'.php'; // include specified controller class
		
		$class = new $load($this, $this->db, $ini); // init class specified by $load
		$methods = get_class_methods($class); // Get list of public methods
		$methods = array_diff($methods, array('__construct', '__destruct'));
		
		if(!isset($vars[0])) $vars[0] = '';
		$success = preg_match('/^('.implode('|', $methods).')$/', $vars[0], $match);
		if(!$success)
		{
			if(isset($class->allow404)) return 404;
			if(isset($class->default_method)) // if the $obj specifies a default method, 
				$vars[0] = $class->default_method; // use it
			else
				$vars[0] = 'main'; // default to failing the method check below
		}
		
		$func = $vars[0];
		ob_start();
		echo $class->$func($vars);
		return ob_get_clean();
	}
	
	private function post()
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
		if($var == 'me')		return $this->auth['user'];
	}
}

?>