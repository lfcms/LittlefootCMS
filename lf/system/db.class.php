<?php

class Database
{
	private $db_link;
	private $db_result;
	private $query_count;
	private $db_name;
	private $conf;
	public $error = '';
	
	function __construct( $database_config )
	{
		$this->db_link = mysql_connect( 
			$database_config['host'], 
			$database_config['user'],
			$database_config['pass'] 
		);
		
		if(!$this->db_link)
			$this->error[] = mysql_errno($this->db_link) . ": " . mysql_error($this->db_link);
		
		if(!mysql_select_db( $database_config['name'], $this->db_link ))
			$this->error[] = mysql_errno($this->db_link) . ": " . mysql_error($this->db_link);
		
		
		
		$this->query_count = 0;
		$this->tblprefix = $database_config['prefix'];
		$this->conf = $database_config;
	}
	
	function __destruct()
	{
		mysql_close($this->db_link);
	}
	
	function query($q)
	{
		$this->db_result = mysql_query($q, $this->db_link);
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
		
		if(mysql_num_rows($result) === 0) return false;
		
		return mysql_fetch_assoc($result);
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
		while($row = mysql_fetch_assoc($result)) { 
			$ret[] = $row;
		}
		
		return $ret;
	}
	
	function getNumQueries()
	{
		return $this->query_count;
	}
	
	function last()
	{
		return mysql_insert_id($this->db_link);
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
		return mysql_affected_rows($this->db_link);
	}
	
	function import($file)
	{
		// Get SQL Dump file
		$dump = file_get_contents($file);
		
		// Extract queries from file
		preg_match_all("/(?:^|\n)([A-Z][^;]+);/", $dump, $match);
		
		// Run queries
		foreach($match[1] as $sql)
			@mysql_query($sql, $this->db_link);
	}
	
	function dump($table = '', $folder = NULL)
	{
		if($folder !== NULL)
			$folder = ROOT.'lf/backup/';
			
		shell_exec('/usr/bin/mysqldump -u"'.$this->conf['user'].'" -p"'.$this->conf['pass'].'" '.$this->conf['name'].' '.$table.' > '.$folder.$this->conf['name'].'.sql');
	}
}

?>