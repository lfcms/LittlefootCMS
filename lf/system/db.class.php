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
	
	/**
	 * Run query, return result, increment SQL counter
	 * 
	 * @param string $q MySQL Query
	 * 
	 * @param bool $big If the request is big
	 */
	function query($q, $big = false)
	{
		if($big)	$this->db_result = $this->mysqli->query($q, MYSQLI_USE_RESULT);
		else		$this->db_result = $this->mysqli->query($q);
		
		$this->query_count++;
		
		return $this->db_result;
	}
	
	/**
	 * Fetch single row of results of a query, returns array(id => 1, col1' => 'val1', ...)
	 * 
	 * @param string $result If this is a string, it is run as SQL and the first row is returned. If it is a MySQL resource, the fetch is run from the resource.
	 */
	function fetch($result = NULL)
	{
		if(is_string($result)) // allow for direct SQL fetch
		{
			$this->db_result = $this->query($result);
			$result = $this->db_result;
		}
		
		// if no argument given, default result is last SQL result
		if($result == NULL) $result = $this->db_result;
		
		$this->query_count++;
		
		// return false if no rows
		if($this->db_result->num_rows === 0) return false;
		
		return $this->db_result->fetch_assoc();
	}
	
	/**
	 * Fetch all rows from a query, returns matrix array of the rows: array(0 => array(id = 1, col1 => 'val1', ...), 1 => array(id => 2, col1 => 'val1', ...))
	 * 
	 * @param string $result If this is a string, it is run as SQL and all rows are returned. If it is a MySQL resource, the fetch is run from the resource.
	 */
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
	
	
	/**
	 * Returns number of queries run for the instance
	 */
	function getNumQueries()
	{
		return $this->query_count;
	}
	
	function numrows($make_conversion_easier = true)
	{
		return $this->db_result->num_rows;
	}
	
	
	/**
	 * Returns the row id of the last affected row. ideal for redirecting to directly edit a newly added entry.
	 */
	function last()
	{
		return $this->mysqli->insert_id;
	}
	
	/**
	 * Returns the last query result good for debugging
	 */
	function getLastResult()
	{
		return $this->db_result;
	}
	
	
	/**
	 * Queries the information schema for a table called $table in this database
	 */
	function is_table($table)
	{
		$result = $this->fetch("
			select count(TABLE_NAME) as is_table
			from information_schema.TABLES 
			WHERE TABLE_SCHEMA = '".$this->conf['name']."' 
				AND TABLE_NAME = '".$this->db->escape($table)."'
		");
		return $result['is_table'] ? 1 : 0;
	}
	
	
	/**
	 * { return $this->mysqli->affected_rows; }
	 */
	function affected()
	{
		return $this->mysqli->affected_rows;
	}
	
	
	/**
	 * $string is usually user-supplied supplied data. Don't forget to sanatize input!
	 */
	function escape($str)
	{
		return $this->mysqli->escape_string($str);
	}
	
	
	/**
	 * SQL commands are preg_match()'d out of $file and run in a loop with errors suppressed
	 */
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
	/**
	 * $folder is the destination, $table will print only the table instead of the whole database
	 */
	
	function dump($table = '', $folder = NULL)
	{
		if($folder !== NULL)
			$folder = ROOT.'lf/backup/';
			
		shell_exec('/usr/bin/mysqldump -u"'.$this->conf['user'].'" -p"'.$this->conf['pass'].'" '.$this->conf['name'].' '.$table.' > '.$folder.$this->conf['name'].'.sql');
	}
}

?>