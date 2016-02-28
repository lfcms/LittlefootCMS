<?php

defined('LF') or die('LF undefined');

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

// set admin_skin to default in session
\lf\set('skin', 'default', 'admin');

// Load user from session
$user = (new \lf\User)->fromSession();

// if the session user does not have admin access,
if($user->hasAccess('admin') )
{
	//formauth
	require_once(ROOT.'system/lib/3rdparty/nocsrf.php');
	// todo: fix CSRF to session, not page load.
	//$this->checkCSRF();

	//$this->base .= 'admin/'; // backward compatible

	// get latest version
	if(!isset($_SESSION['upgrade']))
	{
		$newversion = curl_get_contents('http://littlefootcms.com/files/build-release/littlefoot/lf/system/version');
		
		if($this->version != $newversion && $this->version != '1-DEV')
			$_SESSION['upgrade'] = $newversion;
		else
			$_SESSION['upgrade'] = false; // dont alert to upgrade for 1-DEV
	}
	
	if(\lf\requestGet('Action')[0] == '')
		\lf\requestGet('Action')[0] = 'dashboard';
	
	// Nav item
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
		$match = -1;
		foreach($links[2] as $id => $request)
			if($request == \lf\requestGet('Action')[0].'/') 
				$match = $id;
		
		if($match != -1)
		{
			$replace = str_replace(
				'<li>',
				'<li class="active blue light_a">',
				$links[0][$match]
			);
			
			$nav = str_replace($links[0][$match], $replace, $nav);
		}
	
	
	$this->select['template'] = 'default';
	// multimvc handles its own 'addcontent', but that maybe should be in template too...
	$this->multiMVC('dashboard', 'content', '\\lf\\admin\\');
	
	$this->loadLfCss();
	
	(new \lf\template)
		->addContent($nav, 'nav')
		->setSkin('default');
	
	echo (new \lf\template)->render();
	
	//$renderResult = (new \lf\cms)->legacyTokenReplace($renderResult);
	
	//echo $this->addCSRF($renderResult);
}
else
{
	// Display login form
	include('skin/'.\lf\get('skin','admin').'/login.php');
	//include(LF.'system/template/login.php');
	exit;
}






exit;

/*

$request = $this->action;// backward compatible
 
$admin_skin = 'default'; // this needs to be an option instead of hard coded

// so baseurl never changes from installation directory. 
// make a new one for local admin reference.
$this->adminBase = $this->base.'admin/';

$this->adminurl = $this->adminBase; // backward compatible

// Generate new User() and test access
$user = new User();
$user->fromSession();

*/


// should make separate 'group' defintions
if( ! $user->hasaccess('admin') ) 
	/*&& strpos($this->auth['access'], 'app_') === false*/
{

	//$publickey = '6LffguESAAAAAKaa8ZrGpyzUNi-zNlQbKlcq8piD'; // littlefootcms public key
	$recaptcha = '';//recaptcha_get_html($publickey);
	
	ob_start();
	include('skin/'.$admin_skin.'/login.php');
	$out = ob_get_clean();

	$out = $this->lf->adminTokenReplace($out);
	
	echo $out;
} 
else if($user->hasaccess('admin'))
{
	//formauth
	require_once(ROOT.'system/lib/3rdparty/nocsrf.php');
	$this->checkCSRF();

	$this->base .= 'admin/'; // backward compatible

	// get latest version
	if(!isset($_SESSION['upgrade']))
	{
		$newversion = curl_get_contents('http://littlefootcms.com/files/build-release/littlefoot/lf/system/version');
		if($this->lf->version != $newversion && $this->lf->version != '1-DEV')
			$_SESSION['upgrade'] = $newversion;
		else
			$_SESSION['upgrade'] = false; // dont alert to upgrade for 1-DEV
	}
	
	if($this->lf->action[0] == '')
		$this->lf->action[0] = 'dashboard';
	
	// Nav item
	ob_start();
	include('view/nav.php');
	$nav = ob_get_clean();

	// find active nav item
	preg_match_all(
		'/<li><a class="[^"]+" href="('
			.preg_quote($this->base, '/')
			.'([^\"]+))"/', 
		$nav, 
		$links
	);
	$match = -1;
	foreach($links[2] as $id => $request)
		if($request == $this->lf->action[0].'/') 
			$match = $id;
	
	if($match != -1)
	{
		$replace = str_replace(
			'<li>',
			'<li class="active blue light_a">',
			$links[0][$match]
		);
		
		$nav = str_replace($links[0][$match], $replace, $nav);
	}
	
	$this->content['%nav%'][] = $nav;
	
	$this->select['template'] = 'default';
	
	
	$renderResult = $this
		->multiMVC('dashboard')
		->render(LF.'system/admin/skin');
	
	$renderResult = $this->lf->adminTokenReplace($renderResult);
	
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