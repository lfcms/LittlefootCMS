<?php

/**  
 *@author Joe S <joe@bioshazard.com>
 *@license LICENSE.txt
 *@package LittlefootCMS
 */

define('ENV', 'PRODUCTION');

// Littlefoot
require 'system/functions.php'; // Helpful functions
require 'system/db.class.php'; // Legacy Database Wrapper
require 'system/lib/db.php'; // OOP Database Wrapper
require 'system/app.class.php'; // Littlefoot app base class
require 'system/lib/orm.php'; // Object Relation Model base
require 'system/lib/recovery/install.php';
require 'system/littlefoot.php'; // Littlefoot CMS (Request, Auth, Run assigned Apps, Render on template)
require 'system/lib/auth.php'; // auth stuff

// Add local lib paths to include_path
if(is_dir(LF.'lib')) 
	ini_set('include_path', ini_get('include_path').':'.LF.'lib');
if(is_dir(LF.'system/lib')) 
	ini_set('include_path', ini_get('include_path').':'.LF.'system/lib');