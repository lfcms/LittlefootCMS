<?php

/**  
 *@author Joe S <joe@bioshazard.com>
 *@license LICENSE.txt
 *@package LittlefootCMS
 */

define('ENV', 'PRODUCTION');

// Littlefoot
require 'system/lib/helpers.php'; // Helpful functions
require 'system/lib/db.php'; // OOP Database Wrapper
require 'system/lib/app.php'; // Littlefoot app base class
require 'system/lib/orm.php'; // Object Relation Model base
require 'system/lib/recovery/install.php';
require 'system/lib/auth.php'; // auth stuff
require 'system/lib/littlefoot.php'; // Request, Auth, Nav, Content, Render

// Add local lib paths to include_path
if(is_dir(LF.'lib')) 
	ini_set('include_path', ini_get('include_path').':'.LF.'lib');
if(is_dir(LF.'system/lib')) 
	ini_set('include_path', ini_get('include_path').':'.LF.'system/lib');