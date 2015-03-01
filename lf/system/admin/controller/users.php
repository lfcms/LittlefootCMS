<?php

/**
 * @ignore
 */
class users extends app
{	
	public function main($args)
	{
		$users = orm::q('lf_users')->order()->get();
		$usercount = count($users); 
		include 'view/users.main.php';
	}
	
	public function edit($args)
	{
		$user = orm::q('lf_users')->filterByid($args[1])->first();
		include 'view/users.edit.php'; 
	}
	
	public function update($args)
	{
		// If a password was provided, apply it.
		if($_POST['pass'] != '')
		{
			if($_POST['pass'] != $_POST['pass2'])
			{
				$this->notice('User Saved');
				redirect302($this->lf->appurl);
			}
			
			$_POST['pass'] = sha1($_POST['pass']);
		}
		// Else, discard
		else unset($_POST['pass']);
		
		unset($_POST['pass2']);
		orm::q('lf_users')->debug()->updateById($args[1], $_POST);
		
		$this->notice('User Saved');
		
		redirect302($this->lf->appurl);
	}
	
	public function newuser($args)
	{
		include 'view/users.create.php';
	}
	
	public function create($args)
	{
		$postnames = array(
			'user' => "Username",
			'pass' => "Password",
			'pass2' => "Confirm Password",
			'email' => "Email",
			'nick' => "Nickname",
			'group' => "Group",
			'adminpass' => "Admin password"
		);
		
		foreach($postnames as $name => $text)
			if(!isset($_POST[$name]) || $_POST[$name] == '')
				$error[] = "'$text' is empty";
		
		if($_POST['pass'] != $_POST['pass2'])
		{
			$error[] = '"Confirm Password" field does not match "Password"';
		}
		
		if(isset($error))
		{
			$this->notice('Unable to create user:<br/>* '.implode('<br />* ', $error));
			//redirect302();
			echo "ERROR";
		}
		
		// Verify that an admin requested this action
		$sql = "SELECT id FROM lf_users WHERE id = ".$this->request->api('getuid')." AND pass = '".sha1($_POST['adminpass'])."'";
		$result = $this->db->fetch($sql);
		if($result['id'] != $this->request->api('getuid'))
		{
			$this->notice('Admin password rejected.');
			redirect302();
		}
		
		
		
		
		
		$vars = $_POST;
		$insert = array(
			'user' 			=> $this->db->escape($vars['user']),
			'pass' 			=> sha1($vars['pass']),
			'email' 		=> $this->db->escape($vars['email']),
			'display_name' 	=> $this->db->escape($vars['nick']),
			'hash'			=> '',
			'status'		=> 'valid',
			'access'		=> $this->db->escape($vars['group']),
		);
		
		$sql = "
			INSERT INTO 
				lf_users 	( `id`, `last_request`, `".implode('`, `',array_keys($insert))."`)
				VALUES	( NULL, NOW(), '".implode("', '",array_values($insert))."')
		";
		
		$this->db->query($sql);
		
		if(isset($_POST['sendmail']))
			mail(
				$_POST['email'], 
				'You have a new account at '.$_SERVER['SERVER_NAME'], 
/*outdented to not break email*/				
'Hello,

You can log in to your new account with the following credentials:

Host: http://'.$_SERVER['SERVER_NAME'].$this->request->relbase.'
User: '.$_POST['user'].'
Pass: '.$_POST['pass'].'

Do not reply to this email. It was generated automatically.', 
'From: noreply@'.$_SERVER['SERVER_NAME']);
		
		$this->notice('User "'.$_POST['user'].'" created');
		
		redirect302($this->lf->appurl);
	}
	
	public function rm($vars)
	{
		$sql = "DELETE FROM lf_users WHERE id = ".intval($vars[1]);
		$this->db->query($sql);
		
		redirect302($this->request->appurl);
	}
}

?>