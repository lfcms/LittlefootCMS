<?php

class User
{	
	public $login;
	public $start;
	public $timeout;
	public $error = array();
	
	// user details
	public $details = array(
		'id' => 0,
		'access' => 'none',
		'user' => 'anonymous',
		'display_name' => 'Anonymous'
	);
	
	public function __construct($session = true)
	{
		if($session)
			$this->setDetails($_SESSION['user']['details']);
	}
	
	public function getDetails()
	{
		return $this->details;
	}
	
	public function hasAccess($access)
	{
		// move to using an array of access for inherits
		return $this->getaccess() == $access;
	}
	
	public function setDetails($details)
	{
		// apply only valid details
		foreach($this->details as $key => $ignore)
			if(isset($details[$key]))
				$this->details[$key] = $details[$key];
		
		return $this;
	}
	
	public function refreshTimeout()
	{
		//$_SESSION['user']['login'] = true;
		$_SESSION['user']['start'] = time();
		$_SESSION['user']['expires'] = time() + 60*60*2; // + 2 hours
		
		return $this;
	}
	
	public function doLogin()
	{
		if(!count($_POST))
			return false;
		
		// Get user/pass from $_POST and hash pass
		$username = $_POST['user'];
		$password = sha1($_POST['pass']);

		$login = User::q()
			->cols('id, user, email, display_name, access')
			->filterByuser($_POST['user'])
			->filterBypass(sha1($_POST['pass']))
			->first();
		
		unset($_POST);
		
		// return with error if post fails
		if(!$login)
		{
			$this->error[] = "Incorrect Username or Password";
			return $this;
		}
		
		if(isset($login['status']) && $login['status'] != 'valid')
		{
			if($login['status'] == 'banned') 
				$this->error = "You are banned.";
			else 
				$this->error = "You need to validate your account first.";
			
			return $this;
		}
		
		// if admin, check for reCaptcha
		else if(isset($login['access']) && $login['access'] == 'admin') 
		{
			// If I ever want to do anything during admin request
			// like recaptcha...
		}
		
		if($this->error != array()) 
			$_SESSION['_lf_login_error'] = implode(', ', $this->error);
		
		$this->setDetails($login)->toSession();
		
		return $this;
	}
	
	public function isValidLogin()
	{
		return $_SESSION['user']['id'] > 0;
	}
	
	public function timedOut()
	{
		$timeout = $this->getTimeout();
		return $timeout['expires'] < time();
	}
	
	public function getTimeout()
	{
		return array(
			'start' => $_SESSION['user']['start'],
			'expires' => $_SESSION['user']['expires']
		);
	}
	
	public function toSession()
	{
		$_SESSION['user']['details'] = $this->details;
		
		return $this->refreshTimeout();
	}
	
	public function __call($name, $args)
	{
		if(!preg_match('/^(get|set)(.+)$/', $name, $match))
			return false;
		
		$method = $match[1].'Magic';
		$var 	= $match[2];
		
		return $this->$method($var, $args);
	}
	
	private function getMagic($var, $args)
	{
		return $this->details[$var];
	}
	
	private function setMagic($var, $args)
	{
		$this->details[$var] = $args[0];
		return $this;
	}
	
	public function q()
	{
		return orm::q('lf_users');
	}
}