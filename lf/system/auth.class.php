<?php

// $this->auth = new Auth();

class Auth
{
	public function __construct($auth = NULL)
	{
		if($auth === NULL)
			$auth = $_SESSION['_auth'];
	}
	
	public login()
	{
		
	}
	
	
	
	
	
	
		$this->lf = $lf;
		
		// If no user is currently set...
		if(!isset($auth['user']))
		{
			// default to anonymous
			$auth['user'] = 'anonymous';
			$auth['display_name'] = 'Anonymous';
			$auth['id'] = 0;
			$auth['access'] = 'none';
		}
		
		// if anon or admin login
			// check for submit->login
				// check for good user/pass
				// 
				
		// If anonymous...
		if($auth['user'] == 'anonymous' || (count($_POST) && isset($_POST['adminlogin'])) )
		{
			// check for normal login
			if(isset($_GET['_auth']) && $_GET['_auth'] == 'login')
			{
				$loggedin = false;
				
				// Get user/pass from $_POST and hash pass
				preg_match('/[a-zA-Z0-9]+/', $this->post['user'], $filter);
				$username = $filter != array() ? $filter[0] : '';
				$password = sha1($this->post['pass']);
		
				//Get user
				$sql = sprintf("
						SELECT id, pass, user, email, last_request, display_name, access, status
						FROM lf_users WHERE user = '%s'
						LIMIT 1
					", 
					mysql_real_escape_string($username)
				);
				
				//Execute Query
				$result = $this->db->query($sql);
				
				//Check if user exists
				if(mysql_num_rows($result) == 0) // if random user tried, add to their guess count
				{
					//if(!isset($_SESSION['authguess'])) $_SESSION['authguess'] = 0;
					//$_SESSION['authguess']++;
					$this->error = "Incorrect Username or Password";
				}
				/*else if ($auth['loginfailcnt'] > 7)
				{
					$this->error = "Reset your account with the link we emailed you.";
				}
				*/
				else
				{
					$auth = $this->db->fetch($result);
					
					if($auth['pass'] != $password) // dont allow them to guess your username after 7 tries
					{
						/*$deny = '';
						if($auth['loginfailcnt'] > 6)
						{
							mail($auth['email'], 'Your Account Locked at '.$_SERVER['HTTP_HOST'], 'Reset your account at : '.$this->base);
							$deny = ", status = 'disabled'"; // change status
						}
						
						$this->db->query('UPDATE lf_users SET loginfailcnt = loginfailcnt + 1 WHERE id = '.$auth['id']);
						*/
						$auth = $this->auth;
						$this->error = "Incorrect Username or Password";
					}
					/*else 
					{ 
						$this->db->query('UPDATE lf_users SET loginfailcnt = 0 WHERE id = '.$auth['id']);
					}*/
				}
				
				if(isset($auth['status']) && $auth['status'] != 'valid')
				{
					if($auth['status'] == 'banned') $this->error = "You are banned.";
					else $this->error = "You need to validate your account first.";
					$auth = $this->auth;
				}
				else if(isset($auth['access']) && $auth['access'] == 'admin') // if admin, check for reCaptcha
				{
					/*if(isset($_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]))
					{
						//require_once(ROOT.'system/lib/recaptchalib.php');
						$privatekey = "6LffguESAAAAACsudOF71gJLJE_qmvl4ey37qx8l";
						$resp = recaptcha_check_answer ($privatekey,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
						
						if (!$resp->is_valid) {
							$this->error = "Wrong reCaptcha";
							$auth = $this->auth;
						}
					}*/
					
					if(!isset($_POST['adminlogin'])) { $auth['access'] = 'user'; }
				}
				
				// dont let those apps see your password.
				$_POST = array();
			}
			else if(is_file('lib/facebook.php')) //otherwise, try to authenticate with facebook
			{
				// Facebook login
				include 'lib/facebook.php';
				
				// Facebook login wrapper
				
				if(isset($auth['facebook']))
					$_SESSION = $auth['facebook'];
				else
					$_SESSION = array();
				
				$facebook = new Facebook(array(
				  'appId'  => '331251286935295',
				  'secret' => '1442db0f6a7675d44d9a5022ac23c04d',
				));

				$userId = $facebook->getUser();
				
				$auth['facebook'] = $_SESSION;
				
				
				// logged in via fb
				if ($userId) { 
					$userInfo = $facebook->api('/' + $userId);
					
					//Get user with facebook id
					$sql = "
						SELECT u.id, u.user, u.last_request, u.display_name, a.acl
						FROM lf_users u
						LEFT JOIN lf_admins a
							ON a.uid = u.id
						WHERE u.fbid = ".$userId." LIMIT 1
					";
					
					//Execute Query
					$result = $this->db->query($sql);
					
					if(!mysql_num_rows($result)) // if no user is found with this fbid
					{
						// create user account
						$sql = "
							INSERT INTO lf_users
								(`id`, `user`, `pass`, `email`, `display_name`, `salt`, `last_request`, `status`, `access`, `fbid`)
							VALUES
								(NULL, '".str_replace(' ', '', lcfirst($userInfo['name'])).substr($userId, 0, 4)."', 'null', 'null', '".$userInfo['name']."', 'null', NOW(), 'null', 'null', ".$userId.")
						";
						//Execute Query
						$result = $this->db->query($sql);
						
						$auth = array(
							'id' => mysql_insert_id(),
							'user' => $userId,
							'display_name' => $userInfo['name'],
							'acl' => array('null')
						);
					}
					else
					{
						$auth = $this->db->fetch($result);
						$auth['acl'] = explode(',', $auth['acl']);
					}
					
					// Backward compatible
					$auth['access'] = 'public';
					if(in_array('superadmin', $auth['acl'])) $auth['access'] = 'admin';
				}
			} 
		}
		else // if currently logged in
		{
			// check for logout request && ignore facebook redirecting from ?logout
			if(isset($_GET['_auth']) && $_GET['_auth'] == 'logout' && !strpos($_SERVER['HTTP_REFERER'], 'facebook'))
			{
				// reset session
				session_destroy();
				$auth = array();
				$this->note = 'logout';
				
				redirect302($this->basenoget);
			}
			
			else if(isset($auth['timeout']) && $auth['timeout'] < time() && false) // disabled for now #debug
			{
				//save user for quick re-login
				$user = $auth['user'];
				
				//session_destroy();
				$this->error = "You timed out. Please log back in.";
				
				// default to anonymous
				$auth = array();
			}
			
			else
			{
				$auth['last_request'] = date('Y-m-d G:i:s');
				$auth['timeout'] = time() + 60*30; // timeout in 30 minutes
			}
		}
		
		// for tinymce ajax file manager auth
		if(isset($auth['access']) && $auth['access'] == 'admin')
			$_SESSION['ajax_user'] = true;
		else
			$_SESSION['ajax_user'] = false;
		
		// If no user is currently set...
		if(!isset($auth['user']))
		{
			// default to anonymous
			$auth['user'] = 'anonymous';
			$auth['display_name'] = 'Anonymous';
			$auth['id'] = 0;
			$auth['access'] = 'none';
		}
		
		$this->auth = $auth;
	}
}