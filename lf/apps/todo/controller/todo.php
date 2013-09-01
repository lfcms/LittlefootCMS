<?php

/*

public functions are the controllers
private functions are the models
view loads at the end

*/

class todo
{
	private $request;
	private $html;
	private $pwd;
	private $dbconn;
	public $default_method = 'type';
	
	public function __construct($request, $dbconn, $ini = 'notes')
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->pwd = $request->absbase.'/apps';
		$this->ini = 'todo_'.$ini;
		
		if(!$this->db->is_table($this->ini))
			$this->db->query('
				CREATE TABLE `'.$this->ini.'` (
				  `id` int(5) NOT NULL auto_increment,
				  `owner` int(11) NOT NULL,
				  `note` text NOT NULL,
				  `date` datetime NOT NULL,
				  `type` varchar(50) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=136 DEFAULT CHARSET=latin1
			');
	}
	
	public function rm($var)
	{
		$db = $this->db;
		
		// Sanatize Due Date entry
		$success = preg_match('/^\d+$/', $var[1], $match);
		
		if($success)
		{
			$sql = "DELETE FROM	".$this->ini." WHERE id = '".$match[0]."'";
			$db->query($sql);
		}
		
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function update($var)
	{
		$db = $this->db;
		
		if(!isset($var[1])) $var[1] = '';
		$id = is_numeric($var[1]) ? $var[1] : 'NULL';
		
		$datetime = 'NOW()';
		//$success = preg_match('/^(\d{2}\/\d{2})\/(\d{4})\s(\d{2}\:\d{2})/', $_POST['duedate'], $match);
		//if(!$success) exit();
	
		// Sanatize Due Date entry
		/*$match[1] = str_replace('/', '-', $match[1]);
		$datetime = "'".$match[2].'-'.$match[1].' '.$match[3].":00'";*/
		
		if($id == 'NULL')
			$sql = "
				INSERT INTO ".$this->ini." ( `id`, `owner`, `note`, `date`, `type` ) 
				VALUES ( NULL, ".$this->request->api('getuid').", '".mysql_real_escape_string($_POST['content'])."', ".$datetime.", '".mysql_real_escape_string($_POST['type'])."')
			";
		else
			$sql = "
				UPDATE ".$this->ini." SET
					note = '".mysql_real_escape_string($_POST['content'])."',
					date = ".$datetime.",
					type = '".mysql_real_escape_string($_POST['type'])."'
				WHERE id = ".$id."
			";
		$db->query($sql);
		
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function main($var)
	{/*
		// Print existing items.
		$data = $this->get_data('all');
		
		if(!isset($var[1])) $var[1] = '';
		
		//Check for number
		$edit = is_numeric($var[1]) ? $var[1] : 'all';
		*/
		
		$db = $this->db;
		$db->query('SELECT DISTINCT type FROM '.$this->ini.' ORDER BY type');
		$apps = $db->fetchall();
		
		// Print to screen
		include 'view/main.php';
	}
	
	public function type($var)
	{
		//
		echo '<h3>'.substr($this->ini, 5).'</h3>';
		
		// Print existing items.
		$data = $this->get_data('all');
		
		if(!isset($var[1])) $var[1] = '';
		if(!isset($var[2])) $var[2] = '';
		
		//Check for number
		$edit = is_numeric($var[2]) ? $var[2] : 'all';
		
		$apps = $this->db->fetchall('SELECT DISTINCT type FROM '.$this->ini.' WHERE owner = '.$this->request->api('getuid').' ORDER BY type');
		
		$data = $this->db->fetchall("
			SELECT * FROM ".$this->ini." 
			WHERE type = '".mysql_real_escape_string(urldecode($var[1]))."'  
				AND owner = '".$this->request->api('getuid')."'
			ORDER BY date DESC
		");
		
		// Print to screen
		include 'view/type.php';
	}
	
	public function view($var)
	{
		// Print existing items.
		$data = $this->get_data('all');
		
		//Check for number
		$edit = is_numeric($var[1]) ? $var[1] : 'all';
		
		// Print to screen
		include 'view/type.php';
	}
	
	private function get_data($var = 'all')
	{
		$db = $this->db;
		$sql = 'SELECT * FROM '.$this->ini.' WHERE owner = '.$this->request->api('getuid');
		if(is_numeric($var))
			$sql .= ' AND id = '.$var;
		$sql .= ' ORDER BY type, date ASC';
		$result = $db->query($sql);
		
		$rows = array();
		while($row = mysql_fetch_assoc($result))
		{
			$success = preg_match('/^(\d{4})\-(\d{2}\-\d{2})\s(\d{2}\:\d{2})\:\d{2}/', $row['date'], $match);
			$match[2] = str_replace('-', '/', $match[2]);
			$row['date'] = $match[2].'/'.$match[1].' '.$match[3];
			
			$rows[] = $row;
		}
		
		return $rows;
	}
}

?>
