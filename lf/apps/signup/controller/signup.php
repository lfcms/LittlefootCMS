<?php

class signup
{
	private $request;
	private $html;
	private $pwd;
	private $dbconn;
	
	public function __construct($request, $dbconn, $ini = '')
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->pwd = $request->absbase.'/apps';
		$this->ini = $ini;
	}
	
	//default
	public function main($vars)
	{
		if($this->request->api('getuid') != 0) redirect302($this->request->base.'profile/');
		
		?>
		<div id="signup-login" style="width: 50%; float: left;">
			<h2>Login</h2>
			<form action="?" method="post">
				Username: <input type="text" name="user" /><br />
				Password: <input type="password" name="pass" /><br />
				<input style="padding: 5px; background: white; border: 1px;" type="submit" name="submit" value="Log In" />
			</form>
		</div>
		
		<div id="signup-form" style="margin-left: 50%;">
			<h2>Sign up!</h2>
			<form action="%appurl%create/" method="post">
				<ul>
					<li>User:<br /><input type="text" name="user" /></li>
					<li>Pass:<br /><input type="password" name="pass"/></li>
					<li>Email:<br /><input type="text" name="email" /></li>
					<li><input style="padding: 5px; background: white; border: 1px;" type="submit" value="Sign Up!"/></li>
				</ul>
			</form>
		</div>
		<?php
	}
	
	public function create($vars)
	{
		$sql = "
			SELECT email, user 
			FROM lf_users 
			WHERE user = '".mysql_real_escape_string($_POST['user'])."' 
				OR email = '".mysql_real_escape_string($_POST['email'])."'
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
			
			$this->main($vars);
		}
		else
		{
			// not in use, create account
			if(!preg_match('/^[0-9a-zA-Z]+$/', $_POST['user'], $user))
			{
				echo 'Invalid username. Must be alphanumeric.';
				$this->main($vars);
				
			}
			else if(!preg_match('/^[a-z0-9._%-+]+@[a-z0-9.-]+\.[a-z]{2,4}$/', strtolower($_POST['email']), $email))
			{
				echo 'Invalid email.';
				$this->main($vars);
			}
			else
			{
				$hash = md5((rand()*date('U')).$user[0]);
				$sql = "
					INSERT INTO lf_users (`id`, `user`, `pass`, `email`, `display_name`, `salt`, `last_request`, `status`, `access`)
					VALUES ( 
						NULL, 
						'".$user[0]."', 
						'".sha1($_POST['pass'])."', 
						'".mysql_real_escape_string($_POST['email'])."',  
						'".$user[0]."',
						'',
						NOW(),
						'".$hash."',
						'user'
					)
				";
				$result = $this->db->query($sql);
				
				$msg = '
Hello,

Thank you for signing up at '.$_SERVER['SERVER_NAME'].'. Please validate you account by visiting the following link:

'.$this->request->base.'signup/validate/'.$hash;

				if(mail($_POST['email'], 'Validate your account at '.$_SERVER['SERVER_NAME'], $msg, 'From: signup@'.$_SERVER['SERVER_NAME']))
					echo 'Account Created. Check your email to validate your account';
				else
					echo 'Account Created. Mail failed to send. Contact an admin.';
			}
		}
	}
	
	public function validate($vars)
	{
		if(!isset($vars[1]) || strlen($vars[1]) != 32 || !preg_match('/^[a-f0-9]+$/', $vars[1], $match)) return 'Wrong validation code.';
		
		$hash = $match[0];
		$result = $this->db->fetch("SELECT id FROM lf_users WHERE status = '".$hash."'");
		if(!$result) return 'Wrong validation code.';
		
		$id = $result['id'];
		$result = $this->db->query("UPDATE lf_users SET status = 'valid' WHERE id = ".$id);
		
		echo 'Account validated. Please log in';
	}
}

?>