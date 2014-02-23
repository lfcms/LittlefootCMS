<?php

/*

check for issues, provide options to resolve issues

provide option to finish upgrade, delete file

*/

include 'config.php';
$conf = $db;
$db = new Database($conf);

// 1.13.5-r129
$index = "<?php 

// The absolute path to the lf/ directory is the ROOT of the application
define('ROOT', dirname(__FILE__).'/lf/');
if(!chdir(ROOT)) die('Access Denied to '.ROOT); // if unable to cd there, kill script

include 'system/functions.php'; // base functions
include 'system/db.class.php'; // database wrapper

if(is_file('install/install.php')) { include 'install/install.php'; exit(); } // check for installer, load if presnt
if(is_file(ROOT.'system.zip')) upgrade(); // if system/ upgrade is ready
if(is_file(ROOT.'system/upgrade.php')) { include ROOT.'system/upgrade.php'; unlink(ROOT.'system/upgrade.php'); exit(); } // load the upgrade script if present

include 'system/init.php';";
file_put_contents(ROOT.'../index.php', $index);

$acl = array();
$acl_user = $db->fetchall("SHOW COLUMNS FROM `lf_acl_user`");
foreach($acl_user as $user) $acl[] = $user['Field'];
if(in_array('nav_id', $acl)) $db->query('ALTER TABLE lf_acl_user DROP COLUMN nav_id');

$acl = array();
$acl_user = $db->fetchall("SHOW COLUMNS FROM `lf_acl_global`");
foreach($acl_user as $user) $acl[] = $user['Field'];
if(in_array('nav_id', $acl)) $db->query('ALTER TABLE lf_acl_global DROP COLUMN nav_id');

$db->query("UPDATE lf_users SET status = 'valid' WHERE status = 'online'");


// 1.13.5-r130

// update user table
$columns = array();
$cols = $db->fetchall("SHOW COLUMNS FROM lf_users");
foreach($cols as $col) $columns[] = $col['Field'];
if(in_array('salt', $columns)) $db->query('ALTER TABLE lf_users DROP COLUMN salt');
if(!in_array('hash', $columns)) $db->query('ALTER TABLE lf_users ADD hash VARCHAR( 40 ) NOT NULL');

// add settings
$rewrite = $db->fetch("SELECT * FROM lf_settings WHERE var = 'rewrite'");
if(!$rewrite) $db->query("INSERT INTO lf_settings (id, var, val) VALUES ( NULL, 'rewrite', 'off')");
$debug = $db->fetch("SELECT * FROM lf_settings WHERE var = 'debug'");
if(!$debug) $db->query("INSERT INTO lf_settings (id, var, val) VALUES ( NULL, 'debug', 'off')");
$url = $db->fetch("SELECT * FROM lf_settings WHERE var = 'force_url'");
if(!$url) $db->query("INSERT INTO lf_settings (id, var, val) VALUES ( NULL, 'force_url', '')");
$nav = $db->fetch("SELECT * FROM lf_settings WHERE var = 'nav_class'");
if(!$nav) $db->query("INSERT INTO lf_settings (id, var, val) VALUES ( NULL, 'nav_class', '')");
$simple = $db->fetch("SELECT * FROM lf_settings WHERE var = 'simple_cms'");
if(!$simple) $db->query("INSERT INTO lf_settings (id, var, val) VALUES ( NULL, 'simple_cms', '_lfcms')");

// for handling signup within system/
$signup = $db->fetch("SELECT * FROM lf_settings WHERE var = 'signup'");
if(!$signup) $db->query("INSERT INTO lf_settings (id, var, val) VALUES ( NULL, 'signup', 'disabled')");

echo 'Upgrade complete. <a href="?exit">Click here to remove upgrade utility and return to LittlefootCMS.';

if(!isset($_GET['exit'])) exit();
