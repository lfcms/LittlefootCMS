<?php

class User
{
	private $db;
	
	public $details = array(
		'id' => 0,
		'access' => 'none',
		'user' => 'anonymous',
		'display_name' => 'Anonymous'
	);
	
	public function __construct($session = false)
	{
		if($session)
			$this->setVars($_SESSION['user']['details']);
		
		$this->db = db::init();
	}
	
	public function getVars()
	{
		return $this->details;
	}
	
	public function setVars($details)
	{
		// apply only valid details
		foreach($this->details as $key => $ignore)
			if(isset($details[$key]))
				$this->$key = $details[$key];
		
		return $this;
	}
	
	public function pushToSession()
	{
		$_SESSION['user'] = $this->details;
		
		$_SESSION['user']['login'] = true;
		$_SESSION['user']['start'] = time();
		$_SESSION['user']['expires'] = time() + 60*60*2; // + 2 hours
		
		return $this;
	}
	
	public function __call($name, $args)
	{
		if(!preg_match('/^(get|set)(.+)$/', $name, $match))
			return false;
		
		$method = $match[1].'Magic';
		$var 	= $match[2];

		$this->$method($var, $args);
		
		return $this;
	}
	
	private function getMagic($var, $args)
	{
		return $this->$var;
	}
	
	private function setMagic($var, $args)
	{
		$this->$var = $args[0];
		return $this;
	}
	
	public function query()
	{
		return orm::q('lf_users');
	}
}