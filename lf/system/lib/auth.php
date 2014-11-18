<?php

/**
 * Auth class
 * 
 * ## Usage
 * 
 * This is mostly just used by littlefoot. When authorization is called, it routes through this. 
 *	
 * ~~~ 
 * $auth = new auth($this, $this->db);
 *
 * // change to auth class 
 * if($this->action[0] == '_auth' && isset($this->action[1]))
 * {
 * 		$out = $auth->_router($this->action);
 * 		$out = str_replace('%appurl%', $this->base.'_auth/', $out);
 * 		$content['%content%'][] = $out;
 * 	
 * 		// display in skin
 * 		echo $this->render($content);
 * 	
 * 		exit(); // end auth session after render, 
 * 		// otherwise it will 302 (login/logout)
 * }
 * ~~~
 * 
 */
class auth extends app
{
	public $auth;
	
	protected function init($args)
	{
		$auth = $this->lf->auth;
		
		// disabled for now #debug, should add a settings option to enable this
		if(isset($auth['timeout']) && $auth['timeout'] < time() && false) 
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
			
			$auth['timeout'] = time() + 60*60*12; // timeout in 12 hours
		}
		
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
	
	public function login($args)
	{
		$auth = $this->auth;
		
		$loggedin = false;
		
		// Get user/pass from $_POST and hash pass
		$username = $_POST['user'];
		$password = sha1($_POST['pass']);

		//Get user
		$sql = sprintf("
				SELECT id, pass, user, email, last_request, display_name, access, status
				FROM lf_users WHERE user = '%s'
				LIMIT 1
			", 
			$this->db->escape($username)
		);
		
		//Execute Query
		$result = $this->db->query($sql);
		
		/*echo '<pre>SQL: ';
		print_r($sql);
		echo '
		numrows: ';
		var_dump($this->db->numrows());*/
		
		//Check if user exists
		if($this->db->numrows() == 0) // if random user tried, add to their guess count
		{
			//if(!isset($_SESSION['authguess'])) $_SESSION['authguess'] = 0;
			//$_SESSION['authguess']++;
			$this->error = "Incorrect Username or Password";
		echo $this->error;
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
				$auth = $this->lf->auth;
				$this->error = "Incorrect Username or Password";
			}
			/*else 
			{ 
				$this->db->query('UPDATE lf_users SET loginfailcnt = 0 WHERE id = '.$auth['id']);
			}*/
		}
		
		if(isset($auth['status']) && $auth['status'] != 'valid')
		{
			if($auth['status'] == 'banned') 
				$this->error = "You are banned.";
			else 
				$this->error = "You need to validate your account first.";
			
			$auth = $this->lf->auth;
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
			
			// disabled, you can log in as admin anywhere
			//if(!isset($_POST['adminlogin'])) { $auth['access'] = 'user'; }
		}
		
		$this->lf->auth = $auth;
		
		if(isset($this->error)) $_SESSION['_lf_login_error'] = $this->error;
		
		/*print_r($auth);
		echo '</pre>';
		exit();*/
		
		redirect302();
	}
	
	public function logout($args)
	{
		// reset session
		session_destroy();
		redirect302();
	}
	
	//default
	public function signup($vars)
	{
			
		if($this->lf->api('getuid') != 0) // logged in
		{
			if(isset($_SESSION['dest_url']))
			{
				$dest = $_SESSION['dest_url'];
				unset($_SESSION['dest_url']);
				redirect302($dest);
			}
			else
				redirect302($this->lf->base);
		}
		
		if(is_file(ROOT.'system/template/signup.local'))
			include ROOT.'system/template/signup.local';
		else
			include ROOT.'system/template/signup.php';
	}
	
	
	public function create($vars)
	{/*
		// DEV DEV DEV DEV // plugins 2.0
		if(isset($this->lf->settings['plugins']['pre-auth-create']))
			foreach($this->lf->settings['plugins']['pre-auth-create'] as $plugin => $devnull)
				include ROOT.'plugins/'.$plugin.'/index.php';
		// END DEV DEV DEV DEV
		*/
		$sql = "
			SELECT email, user 
			FROM lf_users 
			WHERE user = '".$this->db->escape($_POST['user'])."' 
				OR email = '".$this->db->escape($_POST['email'])."'
		";
		
		$result = $this->db->query($sql);
		
		$email = false; $user = false;
		if($result)
			while($row = $this->db->fetch())
			{
				if($row['email'] == $_POST['email']) $email = true;
				if($row['user'] == $_POST['user']) $user = true;
			}
		
		if($email || $user)
		{
			if($email)			echo 'Email';
			if($email && $user)	echo ' and ';
			if($user)			echo 'Username';
								echo ' already in use.';
			
			$this->signup($vars);
		}
		else
		{
			// not in use, create account
			/*if(!preg_match('/^[0-9a-zA-Z]+$/', $_POST['user'], $user))
			{
				echo 'Invalid username. Must be alphanumeric.';
				$this->main($vars);
				
			}
			else */if(!preg_match('/^[a-z0-9._%-+]+@[a-z0-9.-]+\.[a-z]{2,4}$/', strtolower($_POST['email']), $email))
			{
				echo 'Invalid email.';
				$this->signup($vars);
			}
			/*else if($_POST['terms'] != 'on')
			{
				echo 'Accept the terms and conditions.';
				$this->main($vars);
			}*/
			else
			{
				$hash = sha1((rand()*date('U')).$user[0]);
				$sql = "
					INSERT INTO lf_users (`id`, `user`, `pass`, `email`, `display_name`, `hash`, `last_request`, `status`, `access`)
					VALUES ( 
						NULL, 
						'".$this->db->escape($_POST['user'])."',  
						'".sha1($_POST['pass'])."', 
						'".$this->db->escape($_POST['email'])."',  
						'".$user[0]."',
						'".$hash."',
						NOW(),
						'pending',
						'user'
					)
				";
				$result = $this->db->query($sql);
				
				$msg = '
Hello,

Thank you for signing up at '.$_SERVER['SERVER_NAME'].'. Please validate you account by visiting the following link:

'.$this->lf->base.'_auth/validate/'.$hash;

				if(mail($_POST['email'], 'Validate your account at '.$_SERVER['SERVER_NAME'], $msg, 'From: signup@'.$_SERVER['SERVER_NAME']))
					echo 'Account Created. Check your email to validate your account. Be sure to check your spam folder too!';
				else
					echo 'Account Created. Mail failed to send. Contact an admin.';
			}
		}
	}
	
	public function validate($vars)
	{
		if(!isset($vars[1]) || strlen($vars[1]) != 40 || !preg_match('/^[a-f0-9]+$/', $vars[1], $match)) return 'Wrong validation code.';
		
		$hash = $match[0];
		$result = $this->db->fetch("SELECT id FROM lf_users WHERE hash = '".$hash."' AND status = 'pending'");
		if(!$result) return 'Wrong validation code.';
		
		$id = $result['id'];
		$result = $this->db->query("UPDATE lf_users SET status = 'valid', hash = '' WHERE id = ".$id);
		
		echo 'Account validated. Please <a href="%baseurl%profile/">log in</a>';
	}
	
	public function forgotform($vars)
	{
		if(is_file(ROOT.'system/template/forgot.local'))
			include ROOT.'system/template/forgot.local';
		else
			include ROOT.'system/template/forgot.php';
	}
	
	public function forgotresult($vars)
	{
		$user = $this->db->fetch("SELECT * FROM lf_users WHERE email = '".$this->db->escape($_POST['email'])."'");
		if(!$user) redirect302($this->lf->appurl);
		
		$hash = sha1(rand().date('U'));
		
		$url = $this->lf->base.'_auth/resetpassform/'.$user['id'].'/'.$hash;
		
		if(mail(
			$user['email'], 
			'Password Reset | '.$_SERVER['SERVER_NAME'], 
			'Please click the following link to reset your account password: '.$url, 'From: validate@'.$_SERVER['SERVER_NAME']
		))
		{
			$this->db->query("UPDATE lf_users SET hash = '".$hash."' WHERE id = ".$user['id']);			
			echo '<p>Password reset email sent. Please check your email. Be sure to check your spam folder too!</p>';
		} else echo 'Failed to sent. Contact an admin.';
	}
	
	public function resetpassform($vars)
	{
		
		$user = $this->db->fetch("SELECT * FROM lf_users WHERE id = ".intval($vars[1])." AND hash = '".$this->db->escape($vars[2])."'");
		if($user)
		{
			echo '
				<h2>Reset your password</h2>
				<form action="%appurl%resetpass/" method="post">
					<input type="hidden" name="hash" value="'.$user['hash'].'" />
					<input type="hidden" name="id" value="'.$user['id'].'" />
					Password: <input type="password" name="pass" /> 
						<input type="submit" value="Reset Password" />
				</form>
			';
		}
		else echo 'Bad link';
	}
	
	public function resetpass($vars)
	{
		$user = $this->db->fetch("SELECT * FROM lf_users WHERE id = ".intval($_POST['id'])." AND hash = '".$this->db->escape($_POST['hash'])."'");
		if($user)
		{
			$this->db->query("UPDATE lf_users SET hash = '', pass = '".sha1($_POST['pass'])."' WHERE id = ".$user['id']);
			echo 'New password set. <a href="%baseurl%">Return to main site</a>';
		} else echo 'Bad form data';
	}
	
	
}

?>
