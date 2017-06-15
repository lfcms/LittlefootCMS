<?php

defined('LF') or die('LF undefined');

/**
 * Admin launchpoint
 *
 * The LF admin is just another app. Tightly linked with the \lf\cms class.
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

// $this is expected to be the cms function in context from the route() function.

// set admin_skin to default in session
\lf\set('skin', 'default', 'admin');

// Load user from session
$user = (new \lf\User)->fromSession();

// if the session user does not have admin access,
if( !$user->hasAccess('admin') )
{
	// Display login form
	include('skin/'.\lf\get('skin','admin').'/login.php');
	//include(LF.'system/template/login.php');
	exit;
} else {
	//formauth
	require_once(ROOT.'system/lib/3rdparty/nocsrf.php');
	// todo: fix CSRF to session, not page load.
	// //$this->checkCSRF();
	// $csrf = (new \NoCSRF);
	// if($_SESSION
	// $token = $csrf->generate('admin');

	$currentVersion = (new \lf\cms)->getVersion();
	
	if(!isset($_SESSION['upgrade']))
		if( $currentVersion == 'DEV' )
			$_SESSION['upgrade'] = false;
		else
		{
			$newversion = trim(curl_get_contents('http://littlefootcms.com/files/build-release/littlefoot/lf/system/version'));
		
			if( $currentVersion != $newversion)
				$_SESSION['upgrade'] = $newversion;
			else
				$_SESSION['upgrade'] = false; // dont alert to upgrade for 1-DEV
		}
	
	// Get Admin nav HTML
	ob_start();
	include('view/nav.php');
	$nav = ob_get_clean();

	// find active nav item (doesnt work for main nav)
	preg_match_all(
		'/<li><a class="[^"]+" href="('
			.preg_quote(\lf\requestGet('AdminUrl'), '/')
			.'([^\"]+))"/', 
		$nav, 
		$links
	);
	
	// find the available links by matching the URL parse against the nav content match from above
	$match = -1;
	foreach($links[2] as $id => $request)
		if($request == \lf\requestGet('Action')[0].'/') 
			$match = $id;
	
	// If we found the matching action, swap in blue light to the element class
	if($match != -1)
	{
		$replace = str_replace(
			'<li>',
			'<li class="active blue light_a">',
			$links[0][$match]
		);
		
		$nav = str_replace($links[0][$match], $replace, $nav);
	}
	
	// This will resolve to the CMS settings' default template setting.
	$this->select['template'] = 'default';
	
	// multimvc handles its own 'addcontent', but that maybe should be in template too...
	$defaultControllerRoute = 'dashboard';
	$renderTarget = 'content';
	$classPrefix = '\\lf\\admin\\';
	$this->multiMVC($defaultControllerRoute, $renderTarget, $classPrefix);
	
	$this->loadLfCss();
	
	(new \lf\template)
		->addContent($nav, 'nav')
		->setSkin('default');
	
	echo (new \lf\template)->render();
	
	//$renderResult = (new \lf\cms)->legacyTokenReplace($renderResult);
	
	//echo $this->addCSRF($renderResult);
}