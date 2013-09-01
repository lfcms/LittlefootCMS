<?php

include 'system/database.class.php';
include 'system/loader.php';
include 'system/functions.php';

// Ensure trailing slash. (this should be done in .htaccess)
if(substr($_SERVER['REQUEST_URI'], -1) != '/')
	redirect301($_SERVER['REQUEST_URI']);

// Display errors in production mode
ini_set('display_errors', 1);

$subdir = 'projects/apploader/';

// Crop Request URI
$request = str_replace($subdir, "", $_SERVER['REQUEST_URI']); // Drop installation subfolder from request URI
$request = str_replace('index.php/', "", $request, $rewrite); // Check for rewrite, remove "index.php/" if present in request
// Maybe look into using [SCRIPT_NAME]

// Generate base url from subdir and rewrite variables
$baseurl = 'http://dev.bioshazard.com/'.$subdir;
//$baseurl .= $rewrite ? 'index.php/' : '' ;

// Extract/Sanatize values from URL. $success = # of matches found
// Address default assignment of blank request: /
$success = preg_match_all('/([a-zA-Z0-9\-\_]+)\//', $request, $match);
if(!$success) { $match[1] = array('test', 'manage'); }

//Database
$db = new Database(
	array(
		'host' => 'localhost',
		'user' => 'jcstillc',
		'pass' => 'asdf896325',
		'name' => 'jcstillc_dev'
	)
);

//New controller object.
$cmd = new loader($baseurl, $db);
$cmd->run($match[1]); // feed in url request variables

?>