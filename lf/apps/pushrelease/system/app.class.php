<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information.

class app
{
	protected $db;
	protected $ini;
	protected $request;
	
	public function __construct($request, $dbconn, $ini = '')
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->ini = $ini;
	}
	
	public function main($vars)
	{
		echo '::APP_MAIN::';
	}
}

?>