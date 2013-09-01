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
		?>
		<h2>Sign up!</h2>
		<form action="%appurl%create/" method="post">
			<ul>
				<li>User:<br /><input type="text" name="user" /></li>
				<li>Pass:<br /><input type="password" name="pass"/></li>
				<li>Email:<br /><input type="text" name="email" /></li>
				<li><input type="submit" value="Sign Up!"/></li>
			</ul>
		</form>
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
			else if(!preg_match('/^[a-z0-9._%-]+@[a-z0-9.-]+\.[a-z]{2,4}$/', strtolower($_POST['email']), $email))
			{
				echo 'Invalid email.';
				$this->main($vars);
			}
			else
			{
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
						'pending',
						'user'
					)
				";
				$result = $this->db->query($sql);
				
				echo 'Account Created. Login above.<br />';
				echo 'User: '.$user[0].'<br />';
				echo 'Pass: '.$_POST['pass'].'<br />';
			}
		}
	}
}

?>