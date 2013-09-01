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
	
	public function __construct($lf, $dbconn, $ini = '', $vars = array())
	{
		$this->db = $dbconn;
		$this->request = $lf; // backward compatible
		$this->lf = $lf;
		$this->ini = $ini;
		
		if(method_exists($this, 'init')) $this->init($vars); // so you can run things on construct without re-making it
	}
	
	public function main($vars)
	{
		echo '::default main function::';
	}
	
	/* used to route based on vars[0]
	
	usage:
		if($vars[0] == '') return $this->main($vars);
		if(intval($vars[0]) != 0)
			return $this->_router($vars, 'home');
			
	function(args):
		$default_route: default function for router when none is specified. uses function "home()" by default
		$filter: if set, limit valid functions to those in the array; eg, array('func2', 'func3')
	
	*/
	protected function _router($vars, $default_route = 'home', $filter = array())
	{
		$this->instbase = $this->lf->appurl.$vars[0].'/'; // url lf->appurl to all
		$this->inst = urldecode($vars[0]);
		
		// Load 
		$vars = array_slice($vars, 1); // move vars over to emulate direct execution
		
		$method = $default_route;
		
		if(isset($vars[0])) // if a base variable is specified,
			if($filter == array()) // if no filter is specified,
				$method = $vars[0];
			else if(in_array($vars[0], $filter)) // if $filter has more than no elements and $vars[0] is in the filter,
				$method = $vars[0];
		
		ob_start();
		$this->$method($vars);
		return str_replace('%insturl%', $this->instbase, ob_get_clean()); // replace appurl with instance base
	}
}

?>