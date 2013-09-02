<?php 

// The ROOT of the application is the absolute path to the lf/ directory
define('ROOT', dirname(__FILE__).'/lf/');
if(!chdir(ROOT)) die('Access Denied to '.ROOT); // if unable to cd there, kill script

include 'system/functions.php'; // base functions
include 'system/db.class.php'; // database wrapper

if(is_file('install/install.php')) { include 'install/install.php'; exit(); } // check for installer, load if presnt
if(is_file(ROOT.'system.zip')) upgrade(); // if system/ upgrade is ready
if(is_file(ROOT.'system/upgrade.php')) { include ROOT.'system/upgrade.php'; unlink(ROOT.'system/upgrade.php'); redirect302(); } // load the upgrade script if present

include 'system/init.php';