<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information.

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