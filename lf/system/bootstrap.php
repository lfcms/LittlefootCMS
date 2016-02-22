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

// Littlefoot 1.0
require 'system/lib/helpers.php'; 		// Helpful functions
require 'system/lib/orm.php'; 			// Object Relation Model base
require 'system/lib/lfcss.php';			// Littlefoot css builder class
require 'system/lib/user.php'; 			// user stuff
require 'system/lib/recovery/install.php'; // I want to move this out of recovery/, but will keep everything else in there.
require 'system/lib/auth.php'; 			// auth stuff
require 'system/lib/littlefoot.php'; 	// LEGACY Request, Auth, Nav, Content, Render

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

 		

require 'system/lib/cache.php'; // Quick memcache style key-value pair storage
require 'system/lib/request.php'; // Parse $_SERVER['REQUEST_URI'] into usable parts
require 'system/lib/acl.php'; // Tool to check user access to a request against loaded rules
require 'system/lib/cms.php'; // Provides hooks, plugins, page request to app execution

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