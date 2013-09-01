<?php

class ArgueController
{
	private $model;
	private $view;
	private $auth;
	
	function __construct($db = null, $auth = null)
	{
		$this->model = new ArgueModel($db); unset($db);
		$this->auth = $auth;
	}
	
	function run($request)
	{
		// Get list of public methods; unset __construct;
		$methods = get_class_methods($this->model); unset($methods[0]);	
		$success = preg_match( // Sanitize based on available methods
			'/^('.implode('|', $methods).')$/', 
			$request[0], 
			$match
		);
		
		// If a match is not found from the implode, give default request
		if(!$success) { $match[1] = 'recent'; }
		
		// Pass class methods the rest of the request variables.
		$func = $match[1];
		
		// get data from model for this action, same for view
		$result = $this->model->$func($request_vars, $this->auth);
		
		// Deal with output
		if($_GET['json'] == 'enabled') { echo json_encode($result); }
		else { include 'view.'.$func.'.php'; }
	}
}

class ArgueModel
{
	private $db;
	
	function __construct($db = null)
	{
		$this->db = $db;
	}
	
	public function myrecent($vars, $auth)
	{
		$db = $this->db;
		
		$sql = "
			SELECT 
				m.id,
				m.message, 
				u.id as uid,
				u.user
			FROM social_message m 
			LEFT JOIN users u 
				ON u.id = m.user
			WHERE
				m.user = ".$auth['id']."
		";
		
		$result = $db->query($sql);
		
		while($row = mysql_fetch_assoc($result))
		{
			$ret[] = $row;
			$getreplies[] = 'm.reply = '.$row['id'];
		}
		
		$sql = "
			SELECT 
				m.id,
				m.message,
				m.reply,
				u.id as uid,
				u.user
			FROM social_message m 
			INNER JOIN users u 
				ON u.id = m.user
			WHERE
				".implode(' OR ', $getreplies)."
		";
		
		echo $sql;
		
		$result = $db->query($sql);
		
		while($row = mysql_fetch_assoc($result))
			$ret[] = $row;
			
		return $ret;
	}
	
	public function recent($vars, $auth)
	{
		$db = $this->db;
		
		$sql = "
			SELECT 
				m.id,
				m.message, 
				m.reply,
				u.id as uid,
				u.user
			FROM social_message m 
			LEFT JOIN users u 
				ON u.id = m.user
		";
		
		//if(isset($vars[1])) $sql .= " WHERE user = '".$vars[1]."'";
		
		//$sql .= "SELECT * FROM social_message";
		
		$result = $db->query($sql);
		
		while($row = mysql_fetch_assoc($result))
			$ret[] = $row;
		
		return $ret;
	}
}

?>