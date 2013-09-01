<?php

class app
{
	private $base;
	private $dbconn;
	
	function __construct($baseurl, $dbconn = NULL)
	{
		$this->init();
		
		// Grab requested app, generate new object
		$class_name = $url[0];
		$class = new $class_name($this->dbconn);
		
		// Get rest of url request
		$request_vars = array_slice($url, 1);

		// Get list of public methods; unset __construct;
		$methods = get_class_methods($class); unset($methods[0]);	
		$success = preg_match( // Sanitize based on available methods
			'/^('.implode('|', $methods).')$/', 
			$request_vars[0], 
			$match
		);
		
		// If a match is not found from the implode, give default request
		if(!$success) { $match[1] = 'view'; }
		
		// Pass class methods the rest of the request variables.
		$func = $match[1]; 
		$output = $class->$func($request_vars);
		
		// Swap out base url in output and echo to screen
		$output = str_replace('%baseurl%', $baseurl, $output);
		echo $output;
	}
	
	private function init()
	{
		// init
	}
	
	public function run($url)
	{		
		
	}
}

?>