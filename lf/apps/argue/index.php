<?php

/*

Create a library of includable classes and files

*/

/*

ID - User - reply_to - message

1 - BIOS - @NULL sup
2 - SomeoneElse - @1 - nm
3 - BIOS - @2 - word

write controller in the index (which is this file)

$database

*/

// Load Classes - Controller, Model, View
require 'classes.mvc.php';

// Start recording the output buffer
ob_start();

// Create new instance of the controller class and run it
$argue = new ArgueController($database, $auth->vars);
$argue->run($variables);

//Capture output
$output = ob_get_contents();

// Do %baseurl% replacement.
$output = str_replace('%baseurl%', 'http://'.$conf['domain'].$conf['subdir'].$widgets['app'], $output);

// Flush out recording buffer memory
ob_end_clean();

?>