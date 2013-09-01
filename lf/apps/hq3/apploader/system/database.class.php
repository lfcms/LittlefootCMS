<?php

// Database Wrapper
class Database
{
	private $database_link;
	private $database_result;
	private $query_count;
	
	function __construct( $database_config )
	{
		$this->db_link = mysql_connect( 
			$database_config['host'], 
			$database_config['user'], 
			$database_config['pass'] 
		);
		
		mysql_select_db( $database_config['name'], $this->db_link );
		$this->query_count = 0;
	}
	
	function __destruct()
	{
		mysql_close($this->db_link);
	}
	
	function query($q)
	{
		$this->db_result = mysql_query($q);
		$this->query_count++;
		return $this->db_result;
	}
	
	function getNumQueries()
	{
		return $this->query_count;
	}
	
	function getLastResult()
	{
		return $this->db_result;
	}
}

?>