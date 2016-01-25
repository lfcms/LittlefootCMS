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
			foreach( (new LfUsers)->cols('id, display_name')->getAllById($ids) as $user )
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

        if(is_array($details)){
            $this->setDetails($details);
        }
		else if(is_int($details))
		{
			$this->setDetails(
				(new LfUsers)
					->cols('id, access, user, display_name')
					->byId($details)
					->first()
			);
		}
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

	## NEW
	public function doLogin($ldap = NULL)
	{
		if(!count($_POST))
			return false;

		// Get user/pass from $_POST and hash pass
		$username = $_POST['user'];
		$password = $_POST['pass'];


		# If LDAP is configured
		if(!is_null($ldap))
		{
			$this->debug[] = 'ldap configured';
			## See if we can authenticate against the configured LDAP
			$ldapResults = $this->ldapLogin($ldap, $username, $password);

			## If login succeeds,
			if($ldapResults)
			{
				$this->debug[] = 'ldap login success';

				### See if the user we just authenticated as is valid
				$login = (new LfUsers)
					->cols('id, user, email, display_name, access')
					->byUser($username)
					->first();

				### If the LDAP login is valid, but we dont have a local user
				if(!$login)
				{
					$this->debug[] = 'ldap valid, adding user';
					$ldapResults = $ldapResults[0];
					(new LfUsers)->add()
						->setUser($ldapResults['cn'][0])
						->setEmail($ldapResults['cn'][0])
						->setStatus('valid')
						->setAccess('user')
						->setDisplay_name($ldapResults['displayname'][0])
						->save();

					$login = (new LfUsers)
						->cols('id, user, email, display_name, access')
						->byUser($ldapResults['cn'][0])
						->first();
				}
			}
			else
			{
				$this->debug[] = 'not in ldap';
				$this->debug[] = $username.' '.$password;
				### Attempt login normally
				$login = (new LfUsers)
					->cols('id, user, email, display_name, access')
					->byUser($username)
					->byPass(sha1($password))
					->first();
			}
		}
		else
		{
			$this->debug[] = 'no ldap configured';

			// Traditional Database lookup
			$login = (new LfUsers)
				->cols('id, user, email, display_name, access')
				->byUser($username)
				->byPass(sha1($password))
				->first();
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
			$this
				->setDetails($login)
				->toSession();

		/*pre($_SESSION);
		pre($this->debug);
		pre($login);
		exit;*/
	}

	## NEW
	public function ldapLogin($server, $user, $pass)
	{
		$server = json_decode(str_replace("'", '"', $server), true);

		$host = $server['host'];
		$port = $server['port'];
		$basedn = $server['basedn'];

		## Connect to the LDAP server.
		$ds=ldap_connect($host, $port);
		if(!$ds)
		{
			echo "Unable to connect to LDAP server";
			return false;
		}

		## Make it work, because otherwise it won't.
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

		### Bind anonymously
		$r=ldap_bind($ds);     // this is an "anonymous" bind, typically
							   // read-only access

		// Search email entry
		$sr=ldap_search($ds, $basedn, 'cn='.$user);

		if( ldap_count_entries($ds, $sr) == 0 )
		{
			//echo "No entry found for ".$user.'.';
			return false;
		}

		//echo '<img src="data:image/jpeg;base64,'.base64_encode($info['jpegphoto'][0]).'" /><br />';

		$info = ldap_get_entries($ds, $sr);
		$dn = $info[0]['dn']; // get dn of first

		## Bind with credentials for found DN
		$r = ldap_bind($ds, $dn, $_POST['pass']);

		ldap_close($ds);

		if($r)
			return $info;

		return $r;
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
