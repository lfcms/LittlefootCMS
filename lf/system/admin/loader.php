<?php

$request = $this->action;

// only admins can see this page
if($this->auth['access'] != 'admin')
	redirect302($this->base);

$this->base .= 'admin/';

// Get a list of admin tools
foreach(scandir('controller') as $controller)
{
	if($controller[0] == '.') continue;
	$controllers[] = str_replace('.php', '', $controller);
}

// Check for valid request
$success = preg_match('/^('.implode('|',$controllers).')$/', $request[0], $match);

// default to dashboard class
if(!$success) $match[0] = 'dashboard';

$this->vars = array_slice($this->action, 1);

if(!isset($_SESSION['upgrade']))
{
	$newversion = file_get_contents('http://littlefootcms.com/files/build-release/littlefoot/lf/system/version');
	if($this->lf->version != $newversion && $this->lf->version != '1-DEV')
		$_SESSION['upgrade'] = $newversion;
	else
		$_SESSION['upgrade'] = false;
}

//formauth
require_once(ROOT.'system/lib/nocsrf.php');
if(count($_POST))
{
	try
	{
		// Run CSRF check, on POST data, in exception mode, with a validity of 10 minutes, in one-time mode.
		NoCSRF::check( 'csrf_token', $_POST, true, 360*10, false );
		// form parsing, DB inserts, etc.
		unset($_POST['csrf_token']);
	}
	catch ( Exception $e )
	{
		// CSRF attack detected
		die('Session timed out');
	}
}

ob_start();
$class = $match[0];
$this->appurl = $this->base.$class.'/';
echo $this->apploader($class);
$replace = array(
	'%baseurl%' => $this->base,
	'%relbase%' => $this->relbase,
	'%appurl%' 	=> $this->base.$class.'/'
);

$app = str_replace(array_keys($replace), array_values($replace), ob_get_clean());

ob_start();
include('view/nav.php');
$nav = ob_get_clean();

preg_match_all('/<li><a class="[a-z]+" href="('.preg_quote($this->base, '/').'([^\"]+))"/', $nav, $links);
$match = -1;
foreach($links[2] as $id => $request)
	if($request == $class.'/') $match = $id;
$replace = str_replace('<li>', '<li class="current">', $links[0][$match]);
$nav = str_replace($links[0][$match], $replace, $nav);

ob_start();
include('skin/'.$admin_skin.'/index.php');

$out = str_replace('%skinbase%', $this->relbase.'lf/system/admin/skin/'.$admin_skin.'/', ob_get_clean());

/* csrf form auth */

// Generate CSRF token to use in form hidden field
$token = NoCSRF::generate( 'csrf_token' );
preg_match_all('/<form[^>]*action="([^"]+)"[^>]*>/', $out, $match);
for($i = 0; $i < count($match[0]); $i++)
	$out = str_replace($match[0][$i], $match[0][$i].' <input type="hidden" name="csrf_token" value="'.$token.'" />', $out);

echo $out;
