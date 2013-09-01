<?php

class users
{
	private $db = NULL;
	private $request;
	
	function __construct($request, $dbconn)
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->pwd = $request->absbase.'/apps';
	}
	
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
		$template = array(
			'html' => '<td>(<a href="%href%">%text%</a>)</td>',
			'replace' => array( '%href%', '%text%')
		);
		
		$result = $this->db->query('SELECT id, user, email, display_name, access FROM lf_users ORDER BY user');
		$row = $this->db->fetch();
		$row_id = $row['id'];
		unset($row['id']);
		$headers = implode('</th><th>', array_keys($row));
		include 'model/getusers.php';
		
		$action = 'update';
		$link = ' ( <a href="%appurl%newuser/">Create New User</a> )';
		
		$id = '<input type="hidden" name="id" value="'.$save['id'].'" />';
		$values = array(
			'value="'.$save['user'].'"',
			'value="'.$save['email'].'"',
			'value="'.$save['display_name'].'"',
			'value="'.$save['access'].'"'
		);
		
		include 'view/users.create.php';
	}
	
	public function update($vars)
	{
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
		
		$sql = "UPDATE lf_users SET ".implode(', ', $insert)." WHERE id = ".$id;
		$this->db->query($sql);
		
		header("Location: ".$this->request->appurl);
		exit();
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
		$values = array(
			'',
			'',
			'',
			'',
			''
		);
				
		include 'view/users.create.php';
	}
	
	public function create($vars)
	{
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
		echo $sql;
		$this->db->query($sql);
		
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