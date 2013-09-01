<?php

class help
{
	private $db;
	private $request;
	
	function __construct($request, $dbconn)
	{
		$this->db = $dbconn;
		$this->request = $request;
	}
	
	public function main($vars)
	{
		include 'view/help.php';
	}
}

?>