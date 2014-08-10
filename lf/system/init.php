<?php

// Session name needs to be alphanumeric, just MD5 it to keep it unique and to not show the docroot
session_name(md5(ROOT.$_SERVER['SERVER_NAME']));
session_start(); 

include 'system/functions.php'; // base functions
include 'system/db.class.php'; // database wrapper
include 'system/app.class.php';
include 'system/lib/orm.php';
include 'system/lib/recovery/install.php';
include 'system/littlefoot.php';

if(file_get_contents(ROOT.'system/version') != '1-DEV')
	error_reporting(0); // my orm stuff upsets the strict PHP standards. I will remove this after I fix that :P

if(!is_file('config.php')) 	install::noconfig();
else 						include 'config.php'; // load db config

$lf = new LittleFoot($db); // initialize with db connection
$lf->cms($debug); // execute littlefoot as cms and render ouput
