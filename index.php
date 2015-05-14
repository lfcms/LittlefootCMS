<?php

require_once('lf/system/bootstrap.php'); // include lf library

$action = (new LfActions)->find();

foreach($action as $k => $v)
	echo $k.' = '.implode(',', array_values($v)).'<br />';



// instantiate this way so the bootstrap can check the version without a parse error. 
// should just be (new Littlefoot)->cms();
$lf = new LittleFoot();

$lf->cms(); // execute littlefoot as ->cms() and ->render() output