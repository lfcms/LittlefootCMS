<?php

// Implement class variable to move the contents of the __construct function into the admin class
// Use same implode method to pregmatch requets to apps

class TestClass
{
	public $pubvar;
	private $privar;
	
	function __construct($request = NULL)
	{
		// Generate list of available options
		$methods = get_class_methods($this);
		unset($methods[0]); // Remove reference to __construct
		
		// Execute function if a match is found
		preg_match('/^('.implode('|', $methods).')$/', $request[0], $match); // Sanitize based on available methods
		if($match[1] != '') // If a match is found from the implode
		{
			$func = $match[1];
			$this->$func(array_slice($request, 1)); // Pass matching function the rest of the request variables.
		}
		else echo 'invalid';
	}
	
	private function read($var)
	{
		echo $var;
	}
	
	private function write($var)
	{
		print_r($var);
	}
	
	private function other($var)
	{
		$this->write($var);
	}
}

$class_name = 'TestClass';

$class = new $class_name($variables);

$output .= 'Test';

?>