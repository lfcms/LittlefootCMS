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

if(!is_file('config.php')) 	install::noconfig();
else 						include 'config.php'; // load db config

$lf = new LittleFoot($db); // initialize with db connection
$lf->cms($debug); // execute littlefoot as cms and render ouput