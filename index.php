<?php

define('ROOT', dirname(__FILE__).'/lf/'); // The absolute path to the lf/ directory is the ROOT of the application
if(!chdir(ROOT)) die('Access Denied to '.ROOT); // if unable to cd there, kill script
include 'system/init.php';