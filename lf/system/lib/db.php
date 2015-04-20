<?php

/**
 * Database wrapper object
 * 
 * ## Instantiation
 * 
 * $config['host'] = 'localhost';
 * $config['user'] = 'user_mydb';
 * $config['pass'] = '*******';
 * $config['name'] = 'user_myuser';
 * 
 * $db = new Database($config);
 * 
 * ## Example Usage
 * 
 * $user = $db->fetch('SELECT * FROM lf_users'); // return associative array of first row from `lf_users` table.
 * 
 * $userlist = $db->fetchall('SELECT id, user FROM lf_users'); // return multiple rows as an array
 * 
 * 
 * ## Littlefoot
 * 
 * In a littlefoot app a database object is accessible at `$this->db`
 * 
 */
class db
{
	/** @var string[] $error Multiple errors can occur. They are stored in this array. */
	public $error = '';
	
	/** @var MySQLi $mysql MySQLi connection object */
	protected $mysqli;
	
	/** @var Result Most recent MySQL result */
	private $db_result;
	
	/** @var int $query_count This is incremented every time a query is performed */
	private $query_count;
	
	/** @var string[] $conf Database configuration is saved for auto dump/import */
	protected $conf;
	
	
	// ty Phil Cross @ http://stackoverflow.com/a/16914104
	private static $instance;
	private $connection;
	
	/**
	 * Given a database configuration, the object is instantiated. If there is an error, it is accessible at $this->error. Configuration is saved to $this->conf
	 */
	private function __construct()
    {
		// check to make sure configuration file is there
		// config.php contains database credentials
		if(!is_file(LF.'config.php')) 	
			(new install)->noconfig();
		else
			include LF.'config.php'; // load $db config

		$database_config = $db;
		$this->conf = $db;
        
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
    }
	
	public static function init()
	{
		if(is_null(self::$instance))
			self::$instance = new db();
		
		return self::$instance;
	}
	
	// wildcard catchall for shortcut requests (filter, set, etc)
	public function __call($method, $args) {
		
		// look for valid request
		if(!preg_match('/^(filterBy|set)(.*)/', $method, $method_parse))
		{
			// if it doesn't match any of my stuff, try it on the mysqli connection object
			if(method_exists($this->mysqli, $method))
			{
				 return call_user_func_array(array($this->mysqli, $method), $args);
			} else {
				 trigger_error('Unknown Method ' . $method . '()', E_USER_WARNING);
				 return false;
			}
		}
		
		// parse out method and column reference
		$m = $method_parse[1];
		$column = $method_parse[2];
		
		return $this->$m($column, $args);
    }
	
	/**
	 * Close MySQLI connection object
	 */
	function __destruct()
	{
		if($this->mysqli)
			$this->mysqli->close();
	}
	
	/**
	 * Free last database result
	 */
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
	 * Fetch single row of results of a query, returns array('id' => 1,'col1' => 'val1')
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
	 * Fetch all rows from a query, returns matrix array of the rows: array(0 => array(id = 1, col1 => 'val1'), 1 => array(id => 2, col1 => 'val1'))
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
	
	/**
	 * Prints number of rows in the MySQL result
	 * 
	 * @param bool $make_conversion_easier Don't think this does anything.
	 */
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
	 * 
	 * @param string $table Check to see if $table exists.
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
	 * $str is usually user-supplied supplied data. Don't forget to sanatize input!
	 * 
	 * @param string $string String to be escaped for database input.
	 */
	function escape($str)
	{
		return $this->mysqli->escape_string($str);
	}
	
	
	/**
	 * SQL commands are preg_match()'d out of $file and run in a loop with errors suppressed
	 * 
	 * @param string $file Path to .sql backup file to be imported into the configured database.
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
	 * Dumps database or table to file.
	 * 
	 * @param string $table empty by default. If specified, only that table will be dumped from the database
	 * @param string $folder Defaults to LF.'lf/backup/'.
	 */
	function dump($table = '', $folder = NULL)
	{
		if($folder !== NULL)
			$folder = LF.'lf/backup/';
			
		shell_exec('/usr/bin/mysqldump -u"'.$this->conf['user'].'" -p"'.$this->conf['pass'].'" '.$this->conf['name'].' '.$table.' > '.$folder.$this->conf['name'].'.sql');
	}
}

?>