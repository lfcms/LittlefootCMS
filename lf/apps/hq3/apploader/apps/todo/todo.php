<?php

/*

__construct is the controller

private functions are the models, loads view at the end.

*/

class todo
{
	private $dbconn;
	public $html;
	private $model;
	
	function __construct($dbconn = NULL)
	{
		$this->dbconn = $dbconn;
	}
	
	public function rm($var)
	{
		$db = $this->dbconn;
		
		// Sanatize Due Date entry
		$success = preg_match('/^\d+$/', $var[1], $match);
		
		if($success)
		{
			$sql = "DELETE FROM	todo_notes WHERE id = '".$match[0]."'";
			$db->query($sql);
		}
		redirect301('http://dev.bioshazard.com/projects/apploader/index.php/todo/listitems/');
	}
	
	public function update($var)
	{
		$db = $this->dbconn;
		
		$id = is_numeric($var[1]) ? $var[1] : 'NULL';
		
		if($_POST['duedate'] == '')
		{
			$datetime = 'NOW()';
		}
		else
		{
		
			// Sanatize Due Date entry
			$success = preg_match('/^(\d{2}\/\d{2})\/(\d{4})\s(\d{2}\:\d{2})/', $_POST['duedate'], $match);
			$match[1] = str_replace('/', '-', $match[1]);
			$datetime = "'".$match[2].'-'.$match[1].' '.$match[3].":00'";
		}
		
		if($success)
		{
			echo $id;
			if($id == 'NULL')
				$sql = "
					INSERT INTO todo_notes ( `id`, `note`, `date`, `type` ) 
					VALUES ( NULL, '".mysql_real_escape_string($_POST['content'])."',".$datetime.",'".mysql_real_escape_string($_POST['type'])."')
				";
			else
				$sql = "
					UPDATE todo_notes SET
						note = '".mysql_real_escape_string($_POST['content'])."',
						date = ".$datetime.",
						type = '".mysql_real_escape_string($_POST['type'])."'
					WHERE
						id = ".$id."
				";
				
			$db->query($sql);
		}
		
		redirect301('http://dev.bioshazard.com/projects/apploader/index.php/todo/listitems/');
	}
	
	public function listitems($var)
	{
		// Print existing items.
		$data = $this->get_data('all');
		
		//Check for number
		$edit = is_numeric($var[1]) ? $var[1] : 'all';
		
		// Print to screen
		include 'view_main.php';
	}
	
	private function get_data($var = 'all')
	{
		$db = $this->dbconn;
		$sql = 'SELECT * FROM todo_notes ORDER BY type, date ASC';
		if(is_numeric($var))
			$sql .= ' WHERE id = '.$var;
		
		//echo $sql;
		
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