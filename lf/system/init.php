<?php

// chdir to lf/ folder and define as ROOT
$folder = dirname(__FILE__).'/../';
if(!chdir($folder)) die('Access Denied to '.$folder); // if unable to cd there, kill script
define('LF', getcwd().'/'); // The absolute path to the lf/ directory is the ROOT of the application

define('ROOT', LF); // backward compatible

require_once('system/bootstrap.php'); // include lf library

// tried putting this in $lf->authenticate
// couldn't login when I did that... will fix later
// Session name needs to be alphanumeric, just MD5 it to keep it unique and to not show the docroot
session_name(md5(ROOT.$_SERVER['SERVER_NAME']));
session_start();

$lf = new LittleFoot(); // initialize $lf with $db connection
$lf->cms(); // execute littlefoot as cms() and render() ouput