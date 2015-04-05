<?php

/**  
 *@author Joe S <joe@bioshazard.com>
 *@license LICENSE.txt
 *@package LittlefootCMS
 */

define('ENV', 'PRODUCTION');

define('APP', getcwd().'/'); // in case it is called as a framework

// chdir to lf/ folder and define as ROOT
$folder = dirname(__FILE__).'/../';
if(!chdir($folder)) die('Access Denied to '.$folder); // if unable to cd there, kill script
define('LF', getcwd().'/'); // The absolute path to the lf/ directory is the ROOT of the application

define('ROOT', LF); // backward compatible

// Littlefoot
require 'system/lib/helpers.php'; 		// Helpful functions
require 'system/lib/db.php'; 			// OOP Database Wrapper
require 'system/lib/app.php'; 			// Littlefoot app base class
require 'system/lib/orm.php'; 			// Object Relation Model base
require 'system/lib/recovery/install.php';
require 'system/lib/user.php'; 			// user stuff
require 'system/lib/auth.php'; 			// auth stuff
require 'system/lib/littlefoot.php'; 	// Request, Auth, Nav, Content, Render

// Add local lib paths to include_path
if(is_dir(LF.'lib')) 
	ini_set('include_path', ini_get('include_path').':'.LF.'lib');
if(is_dir(LF.'system/lib')) 
	ini_set('include_path', ini_get('include_path').':'.LF.'system/lib');

// tried putting this in $lf->authenticate
// couldn't login when I did that... will fix later
// Session name needs to be alphanumeric, just MD5 it to keep it unique and to not show the docroot
session_name(md5(ROOT.$_SERVER['SERVER_NAME']));
session_start();