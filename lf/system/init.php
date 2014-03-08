<?php
/* conflict 1 1 */
if(!is_file('config.php')) die(ROOT.'config.php missing'); // if the config file is missing, kill script

// Session name needs to be alphanumeric, just MD5 it to keep it unique and to not show the docroot
session_name(md5(ROOT.$_SERVER['SERVER_NAME']));
session_start(); 

require_once('config.php'); // load db config
include 'system/app.class.php'; // load app base class
include 'system/littlefoot.php'; // load main littlefoot object

$lf = new LittleFoot($db); // initialize with db connection
$lf->run($debug); // execute littlefoot and render ouput