<?php

/*

__construct is the controller

private functions are the models, loads view at the end.

*/

class test
{
	private $dbconn;
	public $html;
	private $model;
	
	function __construct($dbconn = NULL)
	{
		$this->dbconn = $dbconn;
	}
	
	public function view($var)
	{
		$html = 'view';
		return $html;
	}
	
	public function listall($var)
	{
		//Check for number
		$send = is_numeric($var[1]) ? $var[1] : 'all';
		$data = $this->get_data($send);
		
		// Print to screen
		include 'view_listall.php';
	}
	
	private function get_data($var = 'all')
	{
		$db = $this->dbconn;
		$sql = 'SELECT * FROM apploader_test';
		if(is_numeric($var))
			$sql .= ' WHERE id = '.$var;
		
		echo $sql;
		
		$result = $db->query($sql);
		
		$rows = array();
		
		while($row = mysql_fetch_assoc($result))
			$rows[] = $row;
		
		return $rows;
	}
}

?>