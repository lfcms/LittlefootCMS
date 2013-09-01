<?php

$request = $this->action;

$this->base .= 'admin/';

// maybe you are an admin, but I need you to login first
if($this->auth['user'] == 'anonymous')
{
	ob_start();
	include('view/login.php'); 
	echo str_replace('%skinbase%', $this->relbase.'lf/system/admin/skin/starlight/', ob_get_clean());
	exit;
}

// only admins can see this page
if($this->auth['access'] != 'admin')
	die('
		<h1>Access Denied.</h1>
		<p><a href="..">..</a></p>
		<p><a href="'.$this->base.'?logout=true">Sign in as different user.</a></p>
	');

// Get a list of admin tools
foreach(scandir('controller') as $controller)
{
	if($controller == '.' || $controller == '..') continue;
	$controllers[] = str_replace('.php', '', $controller);
}

// Check for valid request
$success = preg_match('/^('.implode('|',$controllers).')$/', $request[0], $match);

// default to dashboard class
if(!$success) $match[0] = 'dashboard';

$this->vars = array_slice($this->action, 1);

ob_start();
$class = $match[0];
echo $this->apploader($class);
$replace = array(
	'%baseurl%' => $this->base,
	'%relbase%' 	=> $this->relbase,
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

$admin_skin = 'fresh';

ob_start();
include('skin/'.$admin_skin.'/index.php');

echo str_replace('%skinbase%', $this->relbase.'lf/system/admin/skin/'.$admin_skin.'/', ob_get_clean());