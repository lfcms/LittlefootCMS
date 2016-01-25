<?php

require_once('lf/system/bootstrap.php');	// Bootstrap the littlefoot PHP suite.
$cms = new \lf\cms(); 						// Could have just done (new \lf\cms)->run();,
$cms->run();								// but I like to be able to catch if you are `PHP 5.3`.