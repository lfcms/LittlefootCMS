<?php

if(!is_file('config.php')) die(ROOT.'config.php missing'); // if the config file is missing, kill script

// Session name needs to be alphanumeric, just MD5 it to keep it unique and to not show the docroot
session_name(md5(ROOT.$_SERVER['SERVER_NAME']));
session_start(); 

require_once('config.php'); // load db config
include 'system/app.class.php';
include 'system/lib/orm.php';
include 'system/lib/recovery/install.php';
include 'system/littlefoot.php';

$lf = new LittleFoot($db); // initialize with db connection
$lf->cms($debug); // execute littlefoot as cms and render ouput
$lf->cms($debug); // execute littlefoot as cms and render ouput