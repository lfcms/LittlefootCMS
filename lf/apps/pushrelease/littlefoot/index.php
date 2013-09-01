<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information.

/*

-index.php
to establish ROOT
run installer if present
apply session per ROOT."domain.com"
init and run() LitteFoot

*/

// lf/ directory is the ROOT of the application
define('ROOT', dirname(__FILE__).'/lf/');
if(!chdir(ROOT)) die('Access Denied to '.ROOT); // if unable to cd there, kill script
include 'system/functions.php';
include 'system/db.class.php'; // load database wrapper

if(is_file('install/install.php')) install(); // check for installer, load if presnt
if(is_file(ROOT.'system.zip')) upgrade(); // if upgrade is ready

include 'system/app.class.php'; // load app base/loader class

if(!is_file('config.php')) // if the config file is missing
	die(ROOT.'config.php missing');  // kill script

require_once('config.php'); // load db config
include 'system/littlefoot.php'; // load system object

// Session name needs to be alphanumeric, remove other characters.
$sess_name = preg_replace('/[^a-zA-Z0-9]/', '', ROOT.$_SERVER['SERVER_NAME']);
session_name($sess_name);
session_start(); 

$lf = new LittleFoot($db); // initialize with db connection
$lf->run($debug); // execute littlefoot and render ouput

?>
