<?php

/**
 * A base class definition meant to be extended in littlefoot apps
 *
 * ## Usage
 * 
 * ~~~
 * class myApp extends app {
 * 		function main($args) {
 *			echo '<pre>';
 *			var_dump($args, $this);
 *			echo '</pre>';
 *  	}
 * 
 * }
 * ~~~
 */
class app
{
	/** @var Database Datbase wrapper accessible via $this->db */
	public $db;
	
	/** @var string Configuration string set in /admin */
	protected $ini;
	
	/** @var Littlefoot Backword compatibility. Synonymous with $this->lf */
	protected $request;
	
	/** @var Littlefoot Littlefoot object. Access to ACL data and URL variables. */
	protected $lf;
	
	/** @var auth Auth object. Access to access data (username, id, etc) */
	protected $auth;
	
	/**
	 * Initializes the app environment for use with $this->lf->mvc() routing
	 * 
	 * @param Littlefoot $lf The single Littlefoot instance
	 * 
	 * @param Database $dbconn Database wrapper
	 * 
	 * @param string $ini Configured INI value
	 *
	 * @param array $args URL Variables. 
	 */
	public function __construct($lf, $dbconn , $ini = '', $args = array())
	{
		$this->db = $dbconn;
		$this->request = $lf; // backward compatible
		$this->lf = $lf->lf->lf->lf; // lol recursion
		$this->auth = $lf->auth_obj;
		$this->ini = $ini;
		$this->args = $args;
		
		// so you can run things on construct without re-making it
		if(method_exists($this, 'init')) $this->init($args); 
	}
	
	/**
	 * Default main() function. Should be replaced in all classes extended from app
	 */
	public function main($args)
	{
		echo '::default main function::';
	}
	
	/** 
	 * used to route based on args[0] as instance
	 *
	 * ### Usage
	 *		if($args[0] == '') return $this->main($args);
	 *		if(intval($args[0]) != 0) // if you want to force a number
	 *			return $this->_router($args);
	 *
	 * @param array $args URL Variables.
	 *
	 * @param string $default_route Default function for router when none is specified. uses function "home" by default
	 *
	 * @param array $filter If set, limit valid functions to those in the array; eg, array('func2', 'func3')
	 *
	 * @return string Captured output buffer from execution of $method
	
	*/
	public function _router($args, $default_route = 'home', $filter = array())
	{
		$this->instbase = $this->lf->appurl.$args[0].'/'; // url lf->appurl to all
		$this->inst = urldecode($args[0]); // can handle any string
		
		// Load 
		$args = array_slice($args, 1); // move vars over to emulate direct execution
		
		/** @var string variable used to execute method based on $default_route( or $args[0] if set) */
		$method = $default_route;
		
		// if a base variable is specified,
		if(isset($args[0])) 
			// if no filter is specified,
			if($filter == array()) 
				$method = $args[0];
			// if $filter has more than no elements and $args[0] is in the filter,
			else if(in_array($args[0], $filter)) 
				$method = $args[0];
		
		// begin output capture
		ob_start();
		
		// execute given method of $this object
		$this->$method($args);
		
		// replace appurl with instance base and return
		return str_replace('%insturl%', $this->instbase, ob_get_clean()); 
	}
	
	// notice('some message to store in session')
	// notice() // prints the message
	public function notice($msg = '', $namespace = 'lf')
	{
		if($msg != '')
		{
			$_SESSION['notice_'.$namespace][] = $msg;
		}
		else if(isset($_SESSION['notice_'.$namespace]))
		{
			$temp = $_SESSION['notice_'.$namespace];
			unset($_SESSION['notice_'.$namespace]);
			return implode(', ', $temp);
		}
	}
}

?>