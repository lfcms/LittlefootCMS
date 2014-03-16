<?php

class users extends app
{
	public function main($vars)
	{		
		$result = $this->db->query('SELECT id, user, email, display_name, access FROM lf_users ORDER BY user');
		$row = $this->db->fetch();
		$row_id = $row['id'];
		unset($row['id']);
		$headers = implode('</th><th>', array_keys($row));
		include 'model/getusers.php';
		
		$action = 'create';
		$link = '';
		$id = '';
		$values = array(
			'',
			'',
			'',
			'',
			''
		);
		
		
		
		include 'view/users.view.php';
	}
	
	public function edit($vars)
	{
		$save = $this->db->fetch('
			SELECT id, user, email, display_name, access 
			FROM lf_users WHERE id = '.intval($vars[1]));
		//$row_id = $row['id'];
		//unset($row['id']);
		//$headers = implode('</th><th>', array_keys($row));
		//include 'model/getusers.php';
		
		$action = 'update';
		$link = ' ( <a href="%appurl%newuser/">Create New User</a> )';
		
		$id = '<input type="hidden" name="id" value="'.$save['id'].'" />';
		
		
		
		$values = array(
			'user' => $save['user'],
			'email' => $save['email'],
			'nick' => $save['display_name'],
			'group' => $save['access']
		);
		
		include 'view/users.create.php';
	}
	
	public function update($vars)
	{
		$template = array(
			'html' => '<td>(<a href="%href%">%text%</a>)</td>',
			'replace' => array( '%href%', '%text%')
		);
		
		$vars = $this->request->post;
		$id = mysql_real_escape_string($vars['id']);
		
		$insert = array(
			"user = '"			.mysql_real_escape_string($vars['user']) ."'",
			"email = '"		.mysql_real_escape_string($vars['email'])."'",
			"display_name = '"	.mysql_real_escape_string($vars['nick'])."'",
			"access = '"		.mysql_real_escape_string($vars['group'])."'",
		);
		
		if($vars['pass'] != '')
			$insert[] = "pass = '".sha1($vars['pass'])."'";
		
		$sql = "UPDATE lf_users 
				SET ".implode(', ', $insert)." 
				WHERE id = ".$id;
		$this->db->query($sql);
		
		redirect302();
	}
	
	public function newuser($vars)
	{
		$result = $this->db->query('SELECT id, user, email, display_name, access FROM lf_users ORDER BY user');
		$row = $this->db->fetch();
		$row_id = $row['id'];
		unset($row['id']);
		$headers = implode('</th><th>', array_keys($row));
		
		include 'model/getusers.php';
		
		$action = 'create';
		$link = '';
		$id = '';
		
		if(!count($_POST))
			$values = array(
				'user' => '',
				'email' => '',
				'nick' => '',
				'access' => '',
				'sendmail' => ''
			);
		else
			$values = $_POST;
		
		include 'view/users.create.php';
	}
	
	public function create($vars)
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
			$error[] = '"Confirm Password" feild does not match "Password"';
		}
		
		if(isset($error))
		{
			echo '<span class="admin_error">Unable to create user:<br/>* '.implode('<br />* ', $error).'</span>';
			return $this->newuser($vars);
		}
		
		$sql = "SELECT id FROM lf_users WHERE id = ".$this->request->api('getuid')." AND pass = '".sha1($_POST['adminpass'])."'";
		
		$result = $this->db->fetch($sql);
		if($result['id'] != $this->request->api('getuid')) redirect302();
		
		$vars = $_POST;
					
		$insert = array(
			'user' 			=> mysql_real_escape_string($vars['user']),
			'pass' 			=> sha1($vars['pass']),
			'email' 		=> mysql_real_escape_string($vars['email']),
			'display_name' 	=> mysql_real_escape_string($vars['nick']),
			'hash'			=> '',
			'status'		=> 'valid',
			'access'		=> mysql_real_escape_string($vars['group']),
		);
		
		$sql = "
			INSERT INTO 
				lf_users 	( `id`, `last_request`, `".implode('`, `',array_keys($insert))."`)
				VALUES	( NULL, NOW(), '".implode("', '",array_values($insert))."')
		";
		$this->db->query($sql);
		
		if(isset($_POST['sendmail']))
			mail($vars['email'], 'You have a new account at '.$_SERVER['SERVER_NAME'], 'Hello,

You can log in to your new account with the following credentials:

Host: http://'.$_SERVER['SERVER_NAME'].$this->request->relbase.'
User: '.$vars['user'].'
Pass: '.$vars['pass'].'

Do not reply to this email. It was generated automatically.', 
'From: noreply@'.$_SERVER['SERVER_NAME']);
		
		redirect302($this->request->appurl);
	}
	
	public function rm($vars)
	{
		$sql = "DELETE FROM lf_users WHERE id = ".intval($vars[1]);
		$this->db->query($sql);
		
		header("Location: ".$this->request->appurl);
		exit();
	}
}

?>