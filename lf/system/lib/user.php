<?php

namespace lf;

/**
 * user object
 * - session write
 * - sql table write
 * - resolveIds
 */

class user
{
	public $start; // creation time
	private $timeout;
	public $error = array();

	protected $password = null;

	protected $details = array(
		'id' => 0,
		'uid' => 0,
		'access' => 'none',
		'status' => 'pending',
		'user' => '',
		'display_name' => 'Anonymous'
	);
	
	public function idFromSession()
	{
		return $this->fromSession()->getId();
	}
	
	// resolve {user:34} to user 34's display_name. {user:0} resolves to "Anonymous".
	public function resolveIds($out, $wholeLastName = false)
	{
		// move this to core user class
		if(preg_match_all('/{user:(\d+)}/',$out,$match))
		{
			$ids = array_unique($match[1]);
			$out = str_replace('{user:0}', 'Anonymous', $out);
			foreach( (new \LfUsers)->cols('id, display_name')->getAllById($ids) as $user )
			{
				if($wholeLastName)
					$name = $user['display_name'];
				else
				{
					$names = explode(' ', $user['display_name']);
					if(isset($names[1]))
						$name = $names[0].' '.$names[1][0].'.'; // shorten last name to initial
					else {
						$name = $names[0];
					}
				}

				$out = str_replace('{user:'.$user['id'].'}', $name, $out);
			}
		}

		return $out;
	}

	public function __construct($details = null)
    {
		$this->start = time();
		
		// do you have $details in an array as I store it normally?
        if(is_array($details)){
            $this->setDetails($details);
        }
		
		// is it a user id? I can just look that up for you ezpz
		else if(is_int($details))
		{
			$this->setDetails(
				(new LfUsers)
					->cols('id, access, user, display_name')
					->byId($details)
					->first()
			);
		}
		
		// I search by user `display_name` as well
		else if(is_string($details))
		{
			$this->setDetails(
				(new LfUsers)
					->cols('id, access, user, display_name')
					->byDisplay_name($details)
					->first()
			);
		}

		// else new instance is Anonymous by default
    }

	public function selectBox($uid = 0)
	{
	   $users = (new LfUsers)
		   ->cols('id, display_name')
		   ->order('display_name')
		   ->find();

	   $select = '<select name="uid" id="">';
	   $select .= '<option value="">Select User</option>';
	   foreach($users->getAll() as $user)
	   {
		   $selected = '';
		   if($user['id'] == $uid)
				$selected = 'selected="selected"';

		   $select .= '<option '.$selected.' value="'.$user['id'].'">
							'.$user['display_name'].'
					   </option>';
	   }
	   $select .= '</select>';

	   return $select;
	}

	public function setPass($pass)
	{
		$this->details['pass'] = sha1($pass);
		return $this;
	}

	public function fromSession()
	{
		if( isset($_SESSION['login']) )
			return $_SESSION['login'];
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
	
	public function setLdap($ldap)
	{
		$this->ldap = $ldap;
		return $this;
	}
	
	public function loadLdap()
	{
		$this->setLdap( (new \lf\cms)->getSetting('ldap') );
		return $this;
	}

	public function authenticate($username, $password)
	{
		// Traditional Database lookup
		return (new \LfUsers)
			->cols('id, user, email, display_name, access')
			->byUser($username)
			->byPass(sha1($password))
			->first();
	}
	
	## NEW
	/** 
	 * doLogin - given a $_POST, authenticate against `lf_users` or optionally configured LDAP
	 * 
	 * 
	 * 
	 * 
	 * 
	 * 
	*/
	public function loginFromPost()
	{
		// cant log in without
		if(!count($_POST))
		{
			$this->error[] = '$_POST is empty';
			return $this;
		}
		
		// Get user/pass from $_POST and hash pass
		$username = $_POST['user'];
		$password = $_POST['pass'];

		// See if LDAP is configured
		$ldapConf = getSetting('ldap');
		
		// If LDAP is not configured or is blank, just log in normally
		if( is_null($ldapConf) || $ldapConf == '')
		{
			$this->debug[] = 'no ldap configured';
			$login = $this->authenticate( $username, $password );
		}
		// Otherwise, if LDAP is configured, attempt LDAP login, fall back on SQL login
		else
		{
			$this->debug[] = 'ldap configured';
			
			// Authenticate against LDAP if configured in `lf_settings`
			include LF.'system/lib/ldap.php';
			$login = (new \lf\ldap)->login( $ldapConf, $username, $password );
			
		}
		
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
		else
			$this->setDetails($login);

		/*pre($_SESSION);
		pre($this->debug);
		pre($login);
		exit;*/
		
		return $this;
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
			$this->details['id'] = (new \lf\orm)->qUsers('lf')->insertArray($this->details);
		else
		{
			(new \lf\orm)->qUsers('lf')->updateByid($id, $this->details);
			$this->details['id'] = $id;
		}

		unset($this->details['pass']);

		return $this;
	}

	// not feelin this. may drop it
	public function q()
	{
		return (new \lf\orm)->q('lf_users');
	}

	/**
	 * Magic method for routing get|set method calls instead of writing a bunch out
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
