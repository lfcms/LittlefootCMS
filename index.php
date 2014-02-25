<?php 

// The absolute path to the lf/ directory is the ROOT of the application
define('ROOT', dirname(__FILE__).'/lf/');
if(!chdir(ROOT)) die('Access Denied to '.ROOT); // if unable to cd there, kill script

include 'system/functions.php'; // base functions
include 'system/db.class.php'; // database wrapper

if(is_file('install/install.php')) {  // check for installer, load if present
	include 'install/install.php'; 
	exit();
}

include 'system/init.php';