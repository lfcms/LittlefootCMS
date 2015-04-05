<?php

/**
 * Admin launchpoint
 *
 * 1. Set admin url
 *
 * //include('loader.php'); // multiMVC loader
 *
 *
 *
 * # LF Admin
 *
 * %adminurl%
 * pull latest available lf version
 *
 * ## Route action to controller
 *
 * match request[0] to a class in controller/
 * extract variables from request
 * check nocsrf on POST (should do it on GET too...)
 * %variable% replace
 * load nav.php
 * highlight active navigation item
 * hook_run(pre lf render)
 * load $admin_skin
 * replace %skinbase%
 * include $this->lf->head before </head>
 * csrf_token replace in <forms>
 * print final rendered output
 *
 */

$request = $this->action;// backward compatible
 
$admin_skin = 'default'; // this needs to be an option instead of hard coded

// so baseurl never changes from installation directory. 
// make a new one for local admin reference.
$this->adminBase = $this->base.'admin/';

$this->adminurl = $this->adminBase; // backward compatible

// Generate new User() and test access
$user = new User();	

// only admins can see this page
if(!$user->hasAccess('admin'))
	redirect302($this->base);

// should make separate 'group' defintions
if( ! $user->hasaccess('admin') ) 
	/*&& strpos($this->auth['access'], 'app_') === false*/
{
	
	//$publickey = '6LffguESAAAAAKaa8ZrGpyzUNi-zNlQbKlcq8piD'; // littlefootcms public key
	$recaptcha = '';//recaptcha_get_html($publickey);
	
	//pre($_SESSION);
	//exit();
	
	include('skin/'.$admin_skin.'/login.php');
	
	$out = ob_get_clean();

	$out = str_replace('%skinbase%', $this->relbase.'lf/system/admin/skin/'.$admin_skin.'/', $out);
	$out = str_replace('%baseurl%', $this->base.'admin/', $out);
	$out = str_replace('%relbase%', $this->relbase, $out);
	$out = str_replace('%skinbase%', $this->relbase.'lf/system/admin/skin/'.$admin_skin.'/', $out);

	echo $out;
} 
else if($user->hasaccess('admin'))
{
	//formauth
	require_once(ROOT.'system/lib/3rdparty/nocsrf.php');
	if(count($_POST))
	{
		try
		{
			// Run CSRF check, on POST data, in exception mode, with a validity of 10 minutes, in one-time mode.
			NoCSRF::check( 'csrf_token', $_POST, true, 60*60*10, false );
			// form parsing, DB inserts, etc.
			unset($_POST['csrf_token']);
		}
		catch ( Exception $e )
		{
			// CSRF attack detected
			die('Session timed out');
		}
	}

	$this->base .= 'admin/'; // backward compatible

	// get latest version
	if(!isset($_SESSION['upgrade']))
	{
		$newversion = file_get_contents('http://littlefootcms.com/files/build-release/littlefoot/lf/system/version');
		if($this->lf->version != $newversion && $this->lf->version != '1-DEV')
			$_SESSION['upgrade'] = $newversion;
		else
			$_SESSION['upgrade'] = false; // dont alert to upgrade for 1-DEV
	}

	/* */

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
	if(!$success) $match[0] = 'dashboard';

	$this->vars = array_slice($this->action, 1);
	
	ob_start();
	$class = $match[0];
	$this->appurl = $this->base.$class.'/';
	
	echo $this->mvc($class);
	
	$replace = array(
		'%appurl%' 	=> $this->lf->base.$class.'/'
	);

	$app = str_replace(
		array_keys($replace), 
		array_values($replace), 
		ob_get_clean()
	);
	$this->content['%content%'][] = $app;
	
	
	
	
	
	
	ob_start();
	include('view/nav.php');
	$nav = ob_get_clean();

	// find active nav item
	preg_match_all(
		'/<li><a class="[a-z]+" href="('
			.preg_quote($this->base, '/')
			.'([^\"]+))"/', 
		$nav, 
		$links
	);
	$match = -1;
	foreach($links[2] as $id => $request)
		if($request == $class.'/') 
			$match = $id;
	$replace = str_replace(
		'<li>', 
		'<li class="active green light_a">',
		$links[0][$match]
	);
	$nav = str_replace($links[0][$match], $replace, $nav);
	$this->content['%nav%'][] = $nav;
	
	
	
	$this->select['template'] = 'default';
	$renderResult = $this->render(LF.'system/admin/skin');
	
	echo $this->addCSRF($renderResult);
}

/*
else if(strpos($this->auth['access'], 'app_') !== false)
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
}*/