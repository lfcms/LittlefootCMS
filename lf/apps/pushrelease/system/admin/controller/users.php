<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information.

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
		$link = ' ( <a href="%appurl%">Create New User</a> )';
		
		$id = '<input type="hidden" name="id" value="'.$save['id'].'" />';
		$values = array(
			'value="'.$save['user'].'"',
			'value="'.$save['email'].'"',
			'value="'.$save['display_name'].'"',
			'value="'.$save['access'].'"'
		);
		
		include 'view/users.view.php';
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
		
		header("Location: ".$_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function create($vars)
	{
		$vars = $_POST;
		$id = 'NULL';
					
		$insert = array(
			'user' 			=> mysql_real_escape_string($vars['user']),
			'pass' 			=> sha1($vars['pass']),
			'email' 		=> mysql_real_escape_string($vars['email']),
			'display_name' 	=> mysql_real_escape_string($vars['nick']),
			'salt'			=> '',
			'status'		=> 'online',
			'access'		=> mysql_real_escape_string($vars['group']),
		);
		
		$sql = "
			INSERT INTO 
				lf_users 	( `id`, `last_request`, `".implode('`, `',array_keys($insert))."`)
				VALUES	( ".$id.", NOW(), '".implode("', '",array_values($insert))."')
		";
		$this->db->query($sql);
		
		header("Location: ".$_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function rm($vars)
	{
		$sql = "DELETE FROM lf_users WHERE id = ".intval($vars[1]);
		$this->db->query($sql);
		
		header("Location: ".$_SERVER['HTTP_REFERER']);
		exit();
	}
}

?>