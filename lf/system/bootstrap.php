<?php

/**  
 *@author Joe S <joe@bioshazard.com>
 *@license LICENSE.txt
 *@package LittlefootCMS
 */

define('ENV', 'PRODUCTION');

// Littlefoot
require 'system/functions.php'; // Helpful functions
require 'system/db.class.php'; // Database Wrapper
require 'system/app.class.php'; // Littlefoot app base class
require 'system/lib/orm.php'; // Object Relation Manager
require 'system/lib/dba.php'; // Database Abstraction (should be switched with ORM)
require 'system/lib/recovery/install.php';
require 'system/littlefoot.php'; // Littlefoot CMS (Request, Auth, Run assigned Apps, Render on template)
require 'system/lib/auth.php'; // auth stuff