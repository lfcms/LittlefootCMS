<?php

// apploader for separate apps.
	// this addresses the inability to have a home page composed of 

$frame = '';
$result = $this->db->fetch('SELECT content FROM lf_frontpage WHERE id = '.intval($_app['ini']));
if($result)
	$frame = $result['content'];
	
$count = 1;

$cwd = getcwd();
preg_match_all('/{([^}]+)}/', $frame, $match);
foreach($match[1] as $replace)
{
	$ini = NULL;
	$vars = '';
	
	if(!preg_match('/^'.
		'(?:([^#?]+))'. // app
		'(?:#([^?]+))?'. // vars
		'(?:\?(.*))?'. // ini
	'$/', $replace, $parts)) continue;
	
	$app = $parts[1];
	if($parts[2] != '') 
		$vars = explode('/', $parts[2]);
	if($parts[3] != '') 
		$ini = $parts[3];
	
	chdir('../'.$app);
	$ftimer = microtime(true);
	//$frame = str_replace('{'.$replace.'}', $this->apploader($app, $ini, $vars), $frame);
	$frame = str_replace('{'.$replace.'}', $this->apploader($app, $ini, $vars), $frame); // needs to be preg replace so it is replaced 1 time   
	$this->app_timer['Frontpage ('.$count++.') - '.$app.'/'.$vars[0]] = microtime(true) - $ftimer; //timer for app
}
chdir($cwd);
echo $frame;

?>