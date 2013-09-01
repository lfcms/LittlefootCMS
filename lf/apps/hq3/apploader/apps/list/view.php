<?php

class test
{
	private $dbconn;
	public $html;
	
	function __construct($dbconn = NULL)
	{
		$this->init();
		$this->dbconn = $dbconn;
	}
	
	private function model() {}
	
	private function init()
	{
		// init
	}
	
	public function manage($var)
	{
		$html .= 'manage';	
		return $html;
	}
	
	public function view($var)
	{
		$html = 'view';
		return $html;
	}
}

?>