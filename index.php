<?php

require_once('lf/system/bootstrap.php'); // include lf library

// instantiate this way so the bootstrap can check the version without a parse error. 
// should just be (new Littlefoot)->cms();
$lf = new LittleFoot();

$lf->cms(); // execute littlefoot as ->cms() and ->render() output