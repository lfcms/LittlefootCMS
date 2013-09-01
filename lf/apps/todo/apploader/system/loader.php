<?php

class loader
{
	private $base;
	private $dbconn;
	
	function __construct($baseurl, $dbconn = NULL)
	{
		$this->url = $match[1];
		$this->base = $baseurl;
		$this->dbconn = $dbconn;
	}
	
	public function run($url)
	{		
		
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
		if(!$success) { 
			echo '404 no "'.$request_vars[0].'" method found';
			return 0;
		}
		
		// Pass class methods the rest of the request variables.
		$func = $match[1];
		
		ob_start();
		$result = $class->$func($request_vars);
		$output = ob_get_contents();
		ob_end_clean();
		
		// Swap out base url in output and echo to screen
		$output = str_replace('%baseurl%', $this->base, $output);
		echo $output;
	}
}

?>