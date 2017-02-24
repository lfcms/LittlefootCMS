<?php

/**
 *@author Joe S <joe@bioshazard.com>
 *@license LICENSE.txt
 *@package LittlefootCMS
 */

// doesn't support below PHP 5.3
if(version_compare(phpversion(), '5.4')  == -1)
{
	echo 'You are using PHP '.phpversion().' which is <a href="http://php.net/eol.php">End of Life</a>. You need at least PHP 5.4 to run LittlefootCMS. Contact your hosting provider to resolve this.';
	exit;
}

//pre(ini_get('date.timezone'),'var_dump');
date_default_timezone_set('America/New_York');
//phpinfo();
//exit;

define('ENV', 'PRODUCTION');

define('APP', getcwd().'/'); // in case it is called as a framework

// chdir to lf/ folder and define as ROOT
$folder = dirname(__FILE__).'/../';
if(!chdir($folder)) die('Access Denied to '.$folder); // if unable to cd there, kill script
define('LF', getcwd().'/'); // The absolute path to the lf/ directory is the ROOT of the application

define('ROOT', LF); // backward compatibility.


/*
// LF 2.0 - Dawn of the namespaces and session cache

// relies on 1.0 minus the littlefoot class which has been broken into smaller systems
// namely: acl, request, cms, and I add a new session cache class

/* Completely replaces littlefoot->cms. Just have to update your index to...

~~~
<?php

require_once('lf/system/bootstrap.php');
$cms = new \lf\cms();
$cms->run();
~~~
*/

require_once 'system/lib/mem.php'; // Quick memcache style key-value pair storage. (should be used by cache)
require_once 'system/lib/cache.php'; // largely deprecated by mem.php
require_once 'system/lib/helpers.php'; 		// Helpful functions
require_once 'system/lib/request.php'; // Parse $_SERVER['REQUEST_URI'] into usable parts
require_once 'system/lib/user.php'; 			// user stuff
require_once 'system/lib/orm.php'; 			// Object Relation Model base. requires user (should move the db setup to its own thing :\)
require_once 'system/lib/lfcss.php';			// Littlefoot css builder class
require_once 'system/lib/install.php';		// test the install
require_once 'system/lib/auth.php'; 			// auth stuff
require_once 'system/lib/plugin.php'; // Provides hooks, plugins, page request to app execution
require_once 'system/lib/acl.php'; // Tool to check user access to a request against loaded rules
require_once 'system/lib/cms.php'; // Provides hooks, plugins, page request to app execution
require_once 'system/lib/api.php'; // Provides hooks, plugins, page request to app execution
require_once 'system/lib/template.php'; // Template
require_once 'system/lib/nav.php'; // Navigation management

require_once 'system/lib/littlefoot.php'; 	// LEGACY Request, Auth, Nav, Content, Render

// Add local lib paths to include_path
if(is_dir(LF.'lib'))
	ini_set('include_path', ini_get('include_path').':'.LF.'lib');
if(is_dir(LF.'system/lib'))
	ini_set('include_path', ini_get('include_path').':'.LF.'system/lib');

// Session name needs to be alphanumeric,
// just MD5 it to keep it unique and to not show the docroot
session_name(md5(LF.$_SERVER['SERVER_NAME']));
session_start();
// tried putting this in $lf->authenticate
// couldn't login when I did that... will fix later
// actually leaning toward just always having a session going. even if anonymous.