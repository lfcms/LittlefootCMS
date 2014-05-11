<?php

class Database
{
	private $db_link;
	private $mysqli;
	private $db_result;
	private $query_count;
	private $db_name;
	private $conf;
	public $error = '';
	
	function __construct( $database_config )
	{
		$this->mysqli = new mysqli( 
			$database_config['host'], 
			$database_config['user'],
			$database_config['pass'] 
		);
		
		if($this->mysqli->connect_errno)
			$this->error[] = "Connection failed (".$this->mysqli->connect_errno."): " .$this->mysqli->connect_error;
		else if(!$this->mysqli->select_db( $database_config['name']))
			$this->error[] = $this->mysqli->error;
		
		$this->query_count = 0;
		$this->tblprefix = $database_config['prefix'];
		$this->conf = $database_config;
	}
	
	function __destruct()
	{
		$this->mysqli->close();
	}
	
	function free()
	{
		$this->dbresult->free();
	}
	
	function query($q, $big = false)
	{
		if($big)	$this->db_result = $this->mysqli->query($q, MYSQLI_USE_RESULT);
		else		$this->db_result = $this->mysqli->query($q);
		
		$this->query_count++;
		
		return $this->db_result;
	}
	
	function fetch($result = NULL)
	{
		if(is_string($result)) // allow for direct SQL fetch
		{
			$this->db_result = $this->query($result);
			$result = $this->db_result;
		}
		
		if($result == NULL) $result = $this->db_result;
		
		$this->query_count++;
		
		if($this->db_result->num_rows === 0) return false;
		
		return $this->db_result->fetch_assoc();
	}
	
	function fetchall($result = NULL)
	{
		if(is_string($result)) // allow for direct SQL fetch
		{
			$this->db_result = $this->query($result);
			$result = $this->db_result;
		}
		
		if($result == NULL) $result = $this->db_result;
		if($result === false) return false;
		
		$ret = array();
		while($row = $this->db_result->fetch_assoc()) { 
			$ret[] = $row;
		}
		
		$this->query_count++;
		
		return $ret;
	}
	
	function getNumQueries()
	{
		return $this->query_count;
	}
	
	function last()
	{
		return $this->mysqli->insert_id;
	}
	
	function getLastResult()
	{
		return $this->db_result;
	}
	
	function is_table($table)
	{
		$result = $this->fetch("
			select count(TABLE_NAME) as is_table
			from information_schema.TABLES 
			WHERE TABLE_SCHEMA = '".$this->conf['name']."' 
				AND TABLE_NAME = '".mysql_real_escape_string($table)."'
		");
		return $result['is_table'] ? 1 : 0;
	}
	
	function affected()
	{
		return $this->mysqli->affected_rows;
	}
	
	function escape($str)
	{
		return $this->mysqli->escape_string($str);
	}
	
	function import($file)
	{
		// Get SQL Dump file
		$dump = file_get_contents($file);
		
		// Extract queries from file
		preg_match_all("/(?:^|\n)([A-Z][^;]+);/", $dump, $match);
		
		ob_start();
		// Run queries
		foreach($match[1] as $sql)
			$this->query($sql);
		return ob_get_clean();
	}
	
	function dump($table = '', $folder = NULL)
	{
		if($folder !== NULL)
			$folder = ROOT.'lf/backup/';
			
		shell_exec('/usr/bin/mysqldump -u"'.$this->conf['user'].'" -p"'.$this->conf['pass'].'" '.$this->conf['name'].' '.$table.' > '.$folder.$this->conf['name'].'.sql');
	}
}

?>