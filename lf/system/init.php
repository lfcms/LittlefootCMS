<?php

// chdir to lf/ folder and define as ROOT
$folder = dirname(__FILE__).'/../';
if(!chdir($folder)) die('Access Denied to '.$folder); // if unable to cd there, kill script
define('ROOT', getcwd().'/'); // The absolute path to the lf/ directory is the ROOT of the application

require_once('system/bootstrap.php'); // include lf library

// tried putting this in $lf->authenticate
// couldn't login when I did that... will fix later
// Session name needs to be alphanumeric, just MD5 it to keep it unique and to not show the docroot
session_name(md5(ROOT.$_SERVER['SERVER_NAME']));
session_start();

// check to make sure configuration file is there
// config.php contains database credentials
if(!is_file('config.php')) 	
	install::noconfig();
else
	include 'config.php'; // load $db config

$lf = new LittleFoot($db); // initialize $lf with $db connection
$lf->cms($debug); // execute littlefoot as cms() and render() ouput