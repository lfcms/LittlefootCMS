<?php

define('ROOT', __DIR__.'/../'); // default to lf install dir
define('APP', getcwd().'/'); // app root based on pwd

chdir(ROOT);

if(!isset($db)) include 'config.php';

include 'system/functions.php'; // so they
include 'system/db.class.php'; // can use
include 'system/app.class.php'; // all the
//include 'system/lib/auth.php'; // cool functions
include 'system/littlefoot.php'; 

$lf = new Littlefoot($db);
$lf->request();
$lf->authenticate();

chdir(APP); // give cwd back to app


function loader($lf)
{
	//$request = $lf->action;

	//$admin_skin = 'fresh';
/*
	// maybe you are an admin, but I need you to login first
	if($lf->api('isadmin'))
	{
		//$publickey = '6LffguESAAAAAKaa8ZrGpyzUNi-zNlQbKlcq8piD'; // littlefootcms public key
		$recaptcha = '';//recaptcha_get_html($publickey);
		
		/*ob_start();
		include('skin/'.$admin_skin.'/login.php'); 
		echo str_replace('%skinbase%', $lf->relbase.'lf/system/admin/skin/'.$admin_skin.'/', ob_get_clean());
		exit;*/
		
		
		/*
		echo '%login%';
		exit;
	}*/

	// only admins can see this page
	//if($lf->auth['access'] != 'admin')
	//	redirect302($lf->base);

	//$lf->base .= 'admin/';

	$app = $lf->action[0]; // app
	$method = $lf->action[1]; // method
	$lf->vars = array_slice($lf->action, 2); // vars
	$lf->action = array_slice($lf->action, 0, 2);
	
	// Get a list of admin tools
	foreach(scandir('controller') as $controller)
	{
		if($controller[0] == '.') continue;
		$controllers[] = str_replace('.php', '', $controller);
	}

	print_r($vars);
	
	// Check for valid request
	$success = preg_match('/^('.implode('|',$controllers).')$/', $request[0], $match);

	// default to dashboard class
	if(!$success) $match[0] = 'dashboard';

	$lf->vars = array_slice($lf->action, 1);

	//formauth
	require_once(ROOT.'system/lib/nocsrf.php');
	if(count($_POST))
	{
		try
		{
			// Run CSRF check, on POST data, in exception mode, with a validity of 10 minutes, in one-time mode.
			NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
			// form parsing, DB inserts, etc.
			unset($_POST['csrf_token']);
		}
		catch ( Exception $e )
		{
			// CSRF attack detected
			die('CSRF attack detected. Access Denied');
		}
	}

	ob_start();
	$class = $match[0];
	$lf->appurl = $lf->base.$class.'/';
	echo $lf->apploader($class);
	$replace = array(
		'%baseurl%' => $lf->base,
		'%relbase%' => $lf->relbase,
		'%appurl%' 	=> $lf->base.$class.'/'
	);

	$app = str_replace(array_keys($replace), array_values($replace), ob_get_clean());

	ob_start();
	include('view/nav.php');
	$nav = ob_get_clean();

	preg_match_all('/<li><a class="[a-z]+" href="('.preg_quote($lf->base, '/').'([^\"]+))"/', $nav, $links);
	$match = -1;
	foreach($links[2] as $id => $request)
		if($request == $class.'/') $match = $id;
	$replace = str_replace('<li>', '<li class="current">', $links[0][$match]);
	$nav = str_replace($links[0][$match], $replace, $nav);

	ob_start();
	include('skin/'.$admin_skin.'/index.php');

	$out = str_replace('%skinbase%', $lf->relbase.'lf/system/admin/skin/'.$admin_skin.'/', ob_get_clean());

	/* csrf form auth */

	// Generate CSRF token to use in form hidden field
	$token = NoCSRF::generate( 'csrf_token' );
	preg_match_all('/<form[^>]*action="([^"]+)"[^>]*>/', $out, $match);
	for($i = 0; $i < count($match[0]); $i++)
		$out = str_replace($match[0][$i], $match[0][$i].' <input type="hidden" name="csrf_token" value="'.$token.'" />', $out);

	return $out;
}