<?php


	
// Generate list of apps for App Gallery
$apps = array();
foreach(scandir(ROOT.'apps') as $file)
{
	if($file == '.' || $file == '..') continue;

	$app = ROOT.'apps/'.$file;

	if(is_dir($app))
		$apps[] = $file;
}