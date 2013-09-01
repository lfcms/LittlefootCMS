<?php

/*

public functions are the controllers
private functions are the models
view loads at the end

*/
/*
class subscribe
{
	private $request;
	private $html;
	private $pwd;
	private $dbconn;
	
	public function __construct($request, $dbconn, $ini)
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->ini = $ini;
	}
	
	public function main()
	{
		echo 'clients page';
		echo '<a href=""></a>';
	}
}*/

?>



<?php

class subscribe
{
	private $db = NULL;
	private $request;
	
	function __construct($request, $dbconn)
	{
		$this->db = $dbconn;
		$this->request = $request;
	}
	
	public function main($vars)
	{
		/*
		$result = $this->db->query('SELECT id, name, email FROM subscribe_clients ORDER BY name');
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
		
		include 'view/users.view.php';*/
		
		
		$subscriptions = $this->db->fetchall('
			SELECT c.id, c.name, c.email, p.title, p.amount, p.cycle
			FROM subscribe_clients c
			LEFT JOIN subscribe_pay p
				ON c.id = p.client_id
			ORDER BY name
		');
		
		print_r($subscriptions);
		
		
			
		$client = '';
		$count = 0;
		foreach($subscriptions as $sub)
		{
			if($sub['email'] != $client)
			{
				$client = $sub['email'];
				$header = '<h3>'.$sub['name'].'</h3>';
			}
			
			if(isset($header))
			{
				if($count++ > 0) echo '</li></ul>';
					
				echo $header;
				echo '<ul>';
			}
			
			unset($header);
			if($sub['title'] == NULL) continue;
			
			echo '<li>';
			echo $sub['title'].' - '.$sub['cycle'].' @ $'.$sub['amount'];
			echo '</li>';
			
		}
		
		//$result = $this->db->query('SELECT id, user, email, display_name, access FROM lf_users ORDER BY user');
		/*
		echo '<pre>';
		print_r($this->db->fetchall('desc lf_users'));
		echo '</pre>';*/
	}
	
	public function bills($vars)
	{
		$subscriptions = $this->db->fetchall('
			SELECT c.id, c.name, c.email, p.title, p.amount, p.cycle
			FROM subscribe_pay p
			LEFT JOIN subscribe_clients c
				ON c.id = p.client_id
			ORDER BY name
		');
		
			
		$client = '';
		$count = 0;
		foreach($subscriptions as $sub)
		{
			if($sub['email'] != $client)
			{
				$client = $sub['email'];
				$header = '<h3>'.$sub['name'].'</h3>';
			}
			
			if(isset($header))
			{
				if($count++ > 0) echo '</li></ul>';
					
				echo $header;
				echo '<ul>';
			}
			
			echo '<li>';
			echo $sub['title'].' - '.$sub['cycle'].' @ $'.$sub['amount'];
			echo '</li>';
			
			unset($header);
		}
	}
	
	public function edit($vars)
	{
		$template = array(
			'html' => '<td>(<a href="%href%">%text%</a>)</td>',
			'replace' => array( '%href%', '%text%')
		);
		
		$result = $this->db->query('SELECT id, email, name FROM subscribe_clients ORDER BY user');
		$row = $this->db->fetch();
		$row_id = $row['id'];
		unset($row['id']);
		$headers = implode('</th><th>', array_keys($row));
		include 'model/getusers.php';
		
		$action = 'update';
		$link = ' ( <a href="%appurl%">Create New User</a> )';
		
		$id = '<input type="hidden" name="id" value="'.$save['id'].'" />';
		$values = array(
			'value="'.$save['name'].'"',
			'value="'.$save['email'].'"'
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