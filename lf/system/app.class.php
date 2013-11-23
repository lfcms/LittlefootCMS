<?php

/*

router()
{
	
}

*/

class app
{
	protected $db;
	protected $ini;
	protected $request;
	protected $lf;
	protected $auth;
	
	/* $dbconn is no longer needed, but too much relies on it, so im leavin it */
	public function __construct($lf, $dbconn , $ini = '', $args = array())
	{
		$this->db = $dbconn;
		$this->request = $lf; // backward compatible
		$this->lf = $lf->lf->lf->lf; // lol recursion
		$this->auth = $lf->auth_obj;
		$this->ini = $ini;
		
		// so you can run things on construct without re-making it
		if(method_exists($this, 'init')) $this->init($args); 
	}
	
	public function main($args)
	{
		echo '::default main function::';
	}
	
	/* used to route based on args[0]
	
	usage:
		if($args[0] == '') return $this->main($args);
		if(intval($args[0]) != 0)
			return $this->_router($args, 'home');
			
	function(args):
		$default_route: default function for router when none is specified. uses function "home()" by default
		$filter: if set, limit valid functions to those in the array; eg, array('func2', 'func3')
	
	*/
	protected function _router($args, $default_route = 'home', $filter = array())
	{
		$this->instbase = $this->lf->appurl.$args[0].'/'; // url lf->appurl to all
		$this->inst = urldecode($args[0]);
		
		// Load 
		$args = array_slice($args, 1); // move vars over to emulate direct execution
		
		$method = $default_route;
		
		if(isset($args[0])) // if a base variable is specified,
			if($filter == array()) // if no filter is specified,
				$method = $args[0];
			else if(in_array($args[0], $filter)) // if $filter has more than no elements and $args[0] is in the filter,
				$method = $args[0];
		
		ob_start();
		$this->$method($args);
		return str_replace('%insturl%', $this->instbase, ob_get_clean()); // replace appurl with instance base
	}
}

?>