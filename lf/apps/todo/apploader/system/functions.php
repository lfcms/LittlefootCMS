<?php

// Implement __autoload for easy class inclusion
function __autoload($name)
{
	if(is_file('apps/'.$name.'/'.$name.'.php'))
		include 'apps/'.$name.'/'.$name.'.php';
	else die('app "'.$name.'" not installed');
}

function redirect301($url)
{
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ".$url);
	header("Connection: close");
	exit();
}

?>