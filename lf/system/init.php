<?php 

// init.php is deprecated, but this is an easy solution for backward compatibility to OOOLLLDDD installations.

include dirname(__FILE__).'/bootstrap.php'; 
$lf = new LittleFoot(); // initialize $lf with $db connection
$lf->cms(); // execute littlefoot as ->cms() and ->render() output