<?php

class User
{
	public $start; // creation time
	private $timeout;
	public $error = array();
	
	protected $password = null;
	
	protected $details = array(
		'id' => 0,
		'access' => 'none',
		'status' => 'pending',
		'user' => '',
		'display_name' => 'Anonymous'
	);
	
	public function __construct($details = null)
    {
		$this->start = time();
	
        if(is_array($details)){
            $this->setDetails($details);
        }
		else if(is_int($details))
		{
			$this->setDetails(
				(new orm)->qUsers('lf')
					->cols('id, access, user, display_name')
					->byId($details)
					->first()
			);
		}
		else if(is_string($details))
		{
			$this->setDetails(
				(new orm)->qUsers('lf')
					->cols('id, access, user, display_name')
					->byDisplay_name($details)
					->first()
			);
		}
		
		// else Anonymous by default
    }
	
	public function setPass($pass)
	{
		$this->details['pass'] = sha1($pass);
		return $this;
	}
	
	public function fromSession()
	{
		if( isset($_SESSION['login']) )
			$this->setDetails($_SESSION['login']->getDetails());
		else
			$this->error[] = 'Not SESSION login found';
		
		return $this;
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
	
	public function doLogin()
	{
		if(!count($_POST))
			return false;
		
		// Get user/pass from $_POST and hash pass
		$username = $_POST['user'];
		$password = sha1($_POST['pass']);

		$login = (new orm)->qUsers('lf')
			->cols('id, user, email, display_name, access')
			->byUser($_POST['user'])
			->byPass(sha1($_POST['pass']))
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
		if(isset($login['access']) && $login['access'] == 'admin') 
		{
			// If I ever want to do anything during admin request
			// like recaptcha...
		}
		
		if($this->error != array()) 
			$_SESSION['_lf_login_error'] = implode(', ', $this->error);
		
		return $this
			->setDetails($login)
			->toSession();
	}
	
	public function isValidLogin()
	{
		return $this->getId() > 0;
	}
	
	public function isTimedOut()
	{
		return $this->logoutTime < time();
	}
	
	public function getTimeout()
	{
		return $this->timeout;
	}
	
	public function refreshTimeout()
	{
		$this->timeout = time() + 60*60*2; // + 2 hours
		return $this;
	}
	
	public function toSession()
	{
        $_SESSION['login'] = $this->refreshTimeout();
		
        return $this;
	}
	
	public function save()
	{
		$id = $this->details['id'];	
		unset($this->details['id']);
		
		if($id == 0)
			$this->details['id'] = (new orm)->qUsers('lf')->insertArray($this->details);
		else
		{
			(new orm)->qUsers('lf')->updateByid($id, $this->details);
			$this->details['id'] = $id;
		}
		
		unset($this->details['pass']);
		
		return $this;
	}
	
	// not feelin this. may drop it
	public function q()
	{
		return (new orm)->q('lf_users');
	}
	
	/**
	 * Magic
	 */
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
		$var = strtolower($var);
		return $this->details[$var];
	}
	
	private function setMagic($var, $args)
	{
		$var = strtolower($var);
		$this->details[$var] = $args[0];
		return $this;
	}
}