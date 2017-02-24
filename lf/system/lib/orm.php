<?php

namespace lf;

/**
 * Littlefoot ORM
 * > Object-relational mapping (ORM, O/RM, and O/R mapping) in computer science is a programming technique for converting data between incompatible type systems in object-oriented programming languages. This creates, in effect, a "virtual object database" that can be used from within the programming language. - [Wikipedia, the free encyclopedia](http://en.wikipedia.org/wiki/Object-relational_mapping)
 * 
 * Littlefoot comes with a [schema-agnostic ORM class](https://github.com/eflip/LittlefootCMS/blob/development/lf/system/lib/orm.php) which offers an object oriented approach to database manipulation. The idea is to store different pieces of a query as variables in the object and allow you to manipulate those variables using various object methods before execution. This ORM [implements](https://github.com/eflip/LittlefootCMS/blob/development/lf/system/lib/orm.php#L408) PHP's magic method [__call](http://php.net/manual/en/language.oop5.overloading.php#object.call) which allows arbitrary column names to be used in the method call using regex.
 * 
 * ## Auto Load
 * 
 * You can query for all rows from any database table of format `abc_efg` (letters separated by underscore), with `(new AbcEfg)->find()`
 * 
 * 
 * There is a fun shortcut to the above where you can just call new class instances out of a given table name using `__autoload`.
 * 
 * `$users = (new LfUsers);`
 * 
 * If you were to call `(new BlogThreads)`, the autoload function would quickly define a class called `BlogThreads` extended from the `orm` on the fly with a table set as `blog_threads`.
 * 
 * ### extends
 * 
 * You can extend from orm.
 * 
 * `class myPages extends orm { public $table = 'app_pages'; }`
 * 
 * And for lulz, you can extend from an __autoload'd class name to define the table on the fly.
 * 
 * `class myPages extends AppPages {}`
 * 
 * ### Database Object
 * 
 * The first time this is called, [a new mysqli object will be created](https://github.com/eflip/LittlefootCMS/blob/0f2a55346a590cc75676f201a78403a3dd65cf1e/lf/system/lib/orm.php#L180) and stored in $_SESSION['db']. After that, the same mysqli object is simply returned. If you are in the littlefoot context, this is already likely accessible at `$this->db`.
 * 
 * 
 * Littlefoot ORM: SQLQuery
 *
 * unless you define it otherwise, new <classname> will be hijacked
 *
 * ~~~
 * $blogThreads = (new BlogThreads)
 * 	->byId(12)
 * 	->find();
 *
 *
 * class mynewclass extends BlogComments { }
 *
 * blogComments = (new mynewclass)->find();
 *
 * pre($blogComments);
 * ~~~
 *
 */
class orm implements \IteratorAggregate
{

	/** @var bool $debug Prints resulting $sql after execution. */
	public $debug = false;

	/** @var string $sql Variable used to construct the SQL query at execution */
	private $sql;

	/** @var Database $db Database wrapper object. $this->db */
	private $db;
	
	/** @var String $cols Should make this an array of columns for easier manipulation */
	//private $cols = '';
	private $columns = [];

	/** @var string $table Stores the table specified at orm::q('my_table') */
	protected $table = NULL;

	/** @var string $crud Chosen CRUD operation (select, insert, update, delete) */
	private $crud = 'select';

	/** @var array $conf Database config, in case you need to do things with that information. */
	private $conf = NULL;

	/** @var string $distinctCol Add DISTINCT limitation to SQL query */
	private $distinctCol = false;

	/** @var array $data Array of data ($col => $val). Used in CRUD operations. */
	public $data = array();
	// TODO: Make this the raw data, process it into a separate active SQL data set so this can be used like the result set

	/** @var array $joins Array of join operations. */
	public $joins = array();

	/** @var array $conditions Array of conditions ('var > val', 'var2 = val2'). Used in where clause. */
	public $conditions = array();

	/** @var string $where Where clause override. */
	public $where = '';

	/** @var string $order Literal string after "ORDER BY". Usage: "id" (sort by id); "position DESC" (sort by position descending) */
	public $order = '';

	/** @var string $limit Limit clause. Usage: "1" or "1, 3" */
	public $limit = '';

	/** @var orm $result on find(), save array() result */
	public $result = NULL;

	/** @var int $row counter for incremental ->get() */
	public $row = 0;

	/** @var int $row counter for incremental ->get() */
	public $error = array();

	/** @var mysqli_result $mysqli_result the last mysqli_result. Need to move this to `$_SESSION['mysqli_result'] = array();` */
	private $mysqli_result = NULL;

	/** @var mysqli_result $mysqli_result the last mysqli_result. Need to move this to `$_SESSION['mysqli_result'] = array();` */
	public $mysqli = NULL;

	/** $var string $pkIndex default column to update on. */
	public $pkIndex = 'id';
	
	/**
	 * Initialize `$this->mysqli_result` with `initDb()`. Pulls from $_SESSION if a previous connection already exists.
	 *
	 * You can initialize the orm as follows:
	 *
	 * * `$myOrm = (new orm);`
	 * * But you can't do much with it as the table target is fundamental to making a quer
	 * * Store the Database wrapper and the specified table which is ideally called from orm::q('my_table')
	 *
	 * @param Database $db Database wrapper
	 */
	public function __construct()
	{
		// when creating a new orm instance, pull mysqli from session, or try to connect.
		// prints install form when config is missing or your connection is broken.
		$this->initDb();
	}

	/**
	 * Close MySQLI connection object
	 */
	public function __destruct()
	{
		if($this->debug)
			pre( $this->sql );
	}

	/**
	 * to JSON
	 */
	public function json()
	{
		return json_encode($this->result);
	}		
	
	/**
	 * Given a database configuration, the object is instantiated. If there is an error, it is accessible at $this->error. Configuration is saved to $this->conf
	 * 
	 * leave mysqli object in session, but close it once the script finishes via ___LastSay
	 * 
	 *  should be a private function for __construct
	 */
	public function initDb()
	{
		// Request the mysqli object from session
		$mysqli = $this->fromSession();
		
		// If we got back an actual mysqli_request,
		if( is_a($mysqli, 'mysqli_result') )
		{
			// Save this value as the internal mysqli value
			$this->mysqli = $mysqli;
			
			// And return, tho this __METHOD__ only called from __construct
			return $this;
		} // else, just continue below:
		
		$this
			// load Config,
			->loadConfig()
			// and connect to MySQLi
			->connectMysqli()
			->toSession();
		
		// I dont use this :\
		// I dont even save this to session... idk if I want to use it
		//$this->tblprefix = $database_config['prefix'];

		return $this;
    }
	
	private function fromSession()
	{
		// $mysqli = NULL;
		// if( isset( $_SESSION['db'] ) )
			// $mysqli = $_SESSION['db'];
		
		$this->mysqli = mem::get('db');
		
		return $this;
	}
	
	private function toSession()
	{
		// save database object to session
		//$_SESSION['db'] = $this->mysqli;
		mem::set('db', $this->mysqli);
		
		return $this;
	}
	
	/**
	 * Retrieve the number of queries made during this page load. 
	 */
	public function getNumQueries()
	{
		
	}
	
	private function loadConfig()
	{
		// If the config file does not exist, 
		if( ! is_file( LF.'config.php' ) )
		{
			// Record this problem
			$this->error[] = '<div class="error">No config file found, please configure MySQL Access</div>';
			
			// `->runInstaller()` will `exit()` before returning
			$this->runInstaller();
		} // else, just continue below:
		
		// Include that file we tested for earlier
		include LF.'config.php';
		
		// idk if I want to make a whole separate thing for non-errors... ill fix this later. It is now 'later'. Regretting not just fixing this initially.
		$this->error[] = '<div class="notice"><i class="fa fa-check"></i> config.php found</div>';
		
		// if we didnt find any $db set in the config file
		if( !isset( $db ) )
		{
			$this->error[] = '<div class="error">$db not set in config</div>';
			$this->runInstaller();
		} // else, continue:
		
		$this->error[] = '<div class="notice"><i class="fa fa-check"></i> $db value found</div>';
		
		$this->conf = $db;
		
		return $this;
	}
	
	private function connectMysqli()
	{
		// create a new mysqli instance
		$mysqli = $this->newMysqli($this->conf);
		
		// Did we have trouble connecting to the database?
		if($mysqli->connect_errno)
		{
			$this->error[] = '<div class="error"><i class="fa fa-exclamation-triangle"></i> Connection failed ('.$mysqli->connect_errno.'): '.$mysqli->connect_error.'</div>';
			$this->runInstaller();
		}
		
		// do we fail to select our database?
		if( ! $mysqli->select_db( $this->conf['name']))
		{
			$this->error[] = '<div class="error"><i class="fa fa-exclamation-triangle"></i> '.$mysqli->error.'</div>';
			$this->runInstaller();
		}
		
		$this->mysqli = $mysqli;
		
		return $this;
	}
	
	public function postValidate()
	{
		if($_POST['db']['host'] == '')   $this->errors[] = "Missing 'Database Hostname' information";
		if($_POST['db']['user'] == '')   $this->errors[] = "Missing 'Database Username' information";
		//if($_POST['pass'] == '')   $this->errors[] = "Missing 'Database Password' information";
		if($_POST['db']['dbname'] == '') $this->errors[] = "Missing 'Database Name' information";
		if($_POST['admin']['user'] == '')  $this->errors[] = "Missing 'Admin Username' information";
		if($_POST['admin']['pass'] == '')  $this->errors[] = "Missing 'Admin Password' information";
		
		if(count($this->errors) > 0)
		{
			$_POST = array();
			return $this->runInstaller();
		}
		
		return $this;
	}
	
	public function newMysqli($database_config)
	{
		return @new \mysqli(
			$database_config['host'],
			$database_config['user'],
			$database_config['pass']
		);
	}
	
	private function runInstaller()
	{
		if( count( $_POST ) )
		{
			$this->postInstaller();
		}
		
		// guess form field contents
		$host = 'localhost';
		$dbname = get_current_user().'_lf';
		$user = get_current_user();
		
		include LF.'system/lib/recovery/install.form.php';
		exit;
	}
	
	private function writeConfig()
	{
		$this->postValidate();
		
		// Take config.php template, replace credentials with $_POST data
		$dbConfigFile = file_get_contents(LF.'config-dist.php');
		$dbCredentials = array(
			'localhost' 		=> $_POST['db']['host'],
			'mysql_user'		=> $_POST['db']['user'],
			'mysql_passwd' 		=> $_POST['db']['pass'],
			'mysql_database' 	=> $_POST['db']['dbname'],
		);
		
		// Replace keys with values
		$dbConfigFile = str_replace(
			array_keys($dbCredentials), 
			array_values($dbCredentials), 
			$dbConfigFile);
		
		// If the config.php is not already there, write it
		if( !is_file(LF.'config.php') || ( isset($_POST['overwrite']) && $_POST['overwrite'] == 'on' ) )
		{
			if(!file_put_contents(LF.'config.php', $dbConfigFile))
			{
				$this->errors[] = 'Unable to write to "'.LF.'config.php"';
				
				// Get permissions and owner of LF folder
				$perms = substr(sprintf('%o', fileperms(LF)), -4);
				$ownerUID = fileowner(LF);
				
				// Print current ownership
				$this->errors[] = '"'.LF.'" Owner: "'.$ownerUID.'", Perms: '.$perms;
				
				// Print how to fix
				if(extension_loaded('posix'))
				{
					$processUser = posix_getpwuid(posix_geteuid());
					$processUserName = $processUser['name'];
					$this->errors[] = "POSIX detected user '$processUserName' needs write access to the lf/ folder.";
				}
				else
				{
					$this->errors[] = "PHP module 'POSIX' is not loaded, so I can't auto-detect which user needs write permissions<br />"
						.'"'.LF.'" needs to be writable by the user running this PHP script. Check the system processes to see who owns the process as it runs or find a System Administrator.';
				}
			}
		}	
		
		// Verify that we wound up with a config.php
		if(!is_file(LF.'config.php'))
			$this->errors[] = 'Config file missing after write attempt.';
		
		if(count($this->errors) > 0) 
		{
			$_POST = array(); // this is so bad... but its all private, so no one should depend on this feature
			return $this->runInstaller();
		}
	}
	
	private function postInstaller()
	{
		$this->writeConfig();
		
		if( isset($_POST['data']) 
				&& $_POST['data'] == 'on' 
				&& is_file('config.php') )
			$this->importRecoveryData();
		
		redirect302( requestGet('AdminUrl') );
	}
	
	/**
	 * If we are to import the MySQL data...
	 */
	private function importRecoveryData()
	{
		$admin = $_POST['admin'];
		$_POST = array(); // doing this because in ->runInstaller, there is a ->post() that will loop if this is enabled since I am using ORM again. BAAAAD :C
		// the (new orm) will try to do an import, if it is unable to, the installform will trigger
		
		// Run the default lf.sql
		(new orm)->import(ROOT.'system/lib/recovery/lf.sql', false);
		
		// Add admin user
		(new user)
			->setDisplay_name(ucfirst($admin['user']))
			->setEmail($admin['email'])
			->setUser($admin['user'])
			->setPass($admin['pass'])
			->setStatus('valid')
			->setAccess('admin')
			->save()
			->toSession(); // and auto login as that new user
		
		
		$_SESSION['upgrade'] = false;
		
		return $this;
	}

	/**
	 * So you can loop through an object collection
	 *
	 * ~~~
	 * $pages = (new LfPages)->find();
	 * $count=0;
	 * foreach($pages as $page)
	 * {
	 * 	$page->settitle('New '.$count++)->debug()->save();
	 * }
	 * ~~~
	 *
	 */
	public function getIterator() {
		//return new ArrayIterator( $this->result );
		
		$return = [];
		foreach($this->getAll() as $row)
			$return[] = (new orm($this->table))->setArray($row);

		return new \ArrayIterator( $return );
	}
	
	/**
	 * Free last database result
	 */
	function free()
	{
		$this->mysqli_result->free();
	}

	/**
	 * Returns the last query result good for debugging
	 */
	function getLastResult()
	{
		return $this->mysqli_result;
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
				AND TABLE_NAME = '".$this->escape($table)."'
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

	/**
	 * Absorbed db class functions
	 */

	public function fetch($query = NULL)
	{
		if(is_null($query))
			$result = $this->mysqli_result;
		else if(is_object($query))
			$result = $query;
		else // if is string
			$result = $this->query($query);
			
		return $result->fetch_assoc();
	}

	public function fetchAll($query = NULL)
	{
		// TODO: should move this duplicate operation to the query function
		$result = NULL;
		if(is_null($query))
			$result = $this->mysqli_result;
		else if(is_object($query))
			$result = $query;
		else
			$result = $this->query($query);

		// supposedly ::fetch_all() works here, but I couldn't figure it out
		$rows = array();
		if($result)
			while($row = $result->fetch_assoc())
				$rows[] = $row;

		return $rows;
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
	 * Run query, return SQL result, increment SQL counter
	 * 
	 * `$sqlResult = (new orm)->query('SELECT * FROM lf_users');`
	 *
	 * @param string $q MySQL Query
	 * @param bool $big If the request is big
	 */
	function query($q, $big = false)
	{
		$this->mysqli_result = $this->mysqli->query($q);
		$this->query_count++;
		if($this->mysqli->error)
			$this->error[] = $this->mysqli->connect_errno.": " .$this->mysqli->connect_error;

		return $this->mysqli_result;
	}

	public function __get($name)
	{
		if(!is_null($this->result))
			return $this->currentRow()[$name];

		//pre($this->data, 'var_dump');

		if(array_key_exists($name, $this->data))
			return $this->data[$name];

		return null;
	}

	public function debug()
	{
		$this->debug = true;
		return $this;
	}

	public function __toString()
	{
		ob_start();
		$counter = 1;

		foreach($this->getAll() as $row)
		{
			echo '<br />Row #'.$counter.'<br />';
			if(!$row) echo 'No row!';
			else
			foreach($row as $col => $val)
				echo $col.': '.$val.'<br />';

			$counter++;
		}

		return ob_get_clean();
	}

	// dont really use this any more
	/**
	 * Called statically (ie "orm::q()")
	 *
	 * @param string $table Specifies the table to run queries on
	 *
	 * @return orm object
	 */
	public static function q($table)
	{
		return new orm($table);
	}

	// deprecated
	// wildcard catchall for shortcut requests (filter, set, etc)
	public static function __callStatic($method, $args) {

		// look for valid request
		if(!preg_match('/^(q|query)(.+)/', $method, $method_parse))
			return null;

		// parse out method and column reference
		$method = 'query';
		$magic = $method_parse[2];

		return orm::$method($magic, $args);
    }

	
	public function distinct($column)
	{
		$this->distinctCol = $column;
		return $this;
	}

	/**
	 * magic method to allow for a variable method call grammar that describes an operation
	 *
	 * This allows you to call functions like `->getAllByCategory('Cats')` and `joinOnId...`
	 *
	 * $methodRegex = '/^(set|by|filterBy|deleteBy|getBy|getAllBy|findByAll|findBy|find|query|q|(?:l|f|r|i)?joinOn)(.+)/';
	 * // wildcard catchall for shortcut requests (filter, set, etc)
	 * old `$magicRegex = '/^(deleteBy|getBy|getAllBy|by|filterBy|set|findBy|find|query|q|(?:lo?J|ro?J|fo?J|io?J|o?j)oin)(.+)/';`
	 */
	public function __call($magicMethod, $args)
	{
		// given a method call with any of these from the beginning,
		$magicPrefixList = [
			'filterBy',
			// 'findAllBy', no such thing. find() gets it all by default to $this->results
			'getAllBy',
			'deleteBy',
			'findBy',
			'getBy',
			'query',
			'find',
			'setAs',	// for setting fields to NOW() or UUID(). eg `setAsUuid('id')`
			'set',
			'by',
			'q',
			'(?:lo?J|ro?J|fo?J|io?J|o?j)oin'
		];
		
		// grab the prefix used as capture group 1, 
		// grab whatever else the user said in capture group 2
		// must be like setAsCamel()
		$prefixMatchPattern = '/^('.implode('|', $magicPrefixList).')([A-Z].*)/';
		
		// test the pattern, 
		if(!preg_match($prefixMatchPattern, $magicMethod, $captures))
			// return on fail, 
			return $this->throwException('Invalid method called');
		// but proceed with match otherwise
		
		// this choses the magic method to process...
		$magicPrefix = $captures[1];
		
		// the user supplied, captured suffix of the method they used
		$magicSuffix = $captures[2];
		
		// Lets do magic with what we parsed!		

		// handle joinMagic()
		if(preg_match('/^(lo?J|ro?J|fo?J|io?J|o?j)oin$/', $magicPrefix, $match))
			return $this->joinMagic($match[1], $magicSuffix, $args);
		
		// SetAs
		if($magicPrefix == 'setAs')
			return $this->setAsMagic($magicSuffix, $args);
		
		// Run a find after the by()
		if($magicPrefix == 'findBy') 
			return $this
				->filterBy($magicSuffix, $args)
				->find();

		// Run a delete after the by()
		if($magicPrefix == 'deleteBy') 
			return $this
				->filterBy($magicSuffix, $args)
				->delete();

		// Run a get after the by()
		if($magicPrefix == 'getBy') 
			return $this
				->filterBy($magicSuffix, $args)
				->get();

		// Get all rows found after the by()
		if($magicPrefix == 'getAllBy')
			return $this
				->filterBy($magicSuffix, $args)
				->getAll();
		
		// I really should keep returning like above, but this is less code
		
		// 'by' is an alias to filterBy()
		if($magicPrefix == 'by') 
			$magicPrefix = 'filterBy';
		
		// if we match a "find" prefix to the magic method, we have to pass it through the magic funciton handler
		if($magicPrefix == 'find')
			$magicPrefix = 'findMagic';
		
		// This is really old. Lets you set a table. 'q' is an alias
		if($magicPrefix == 'query' || $magicSuffix == 'q') 
			$magicPrefix = 'queryMagic';

		// if we get this far, we are just running a single method from the above match and passing the arguments
		return $this->$magicPrefix($magicSuffix, $args);
    }
	
	/**
	 * Generate a matrix by organizing query result set based on key hierarchy
	 * 
	 * Instead of doing
	 * 
	 * ~~~
	 * $comments = (new \MyComment)->getAll();
	 * $recursedComments = [];
	 * foreach($comments as $comment)
	 * 		$recursedComments[$comment['reply']][$comment['id']] = $comment;
	 * ~~~
	 * 
	 * Just do `$recursedComments = (new \MyComment)->matrix(['reply','id']);`
	 */
	public function matrix($keys)
	{
		if( ! is_array($keys) )
		{
			$this->errors[] = 'matrix() $keys should be an array';
			return NULL;
		}
		
		if( count($keys) == 0 )
		{
			$this->errors[] = 'matrix() $keys has no elements';
			return NULL;
		}
		
		$results = $this->getAll();
		
		// grab the last key name so we can use it to store the whole row data
		$lastKey = array_pop($keys);
		
		$matrix = [];
		foreach($results as $row)
		{
			// reset pointer back to matrix root
			$matrixPointer =& $matrix;
			
			// build the initial hierarchy: $matrix[reply][id] = $comment;
			foreach($keys as $key)
			{
				// gotta have something to point to
				if( ! isset( $matrixPointer[$row[$key]] ) )
					$matrixPointer[$row[$key]] = [];
				
				// set pointer as last child for next iteration
				$matrixPointer =& $matrixPointer[$row[$key]];
			}
			
			$matrixPointer[$row[$lastKey]] = $row;
		}
		
		return $matrix;
	}

	private function throwException($msg = '')
	{
		//$this->error[] = $msg;
		return $this;
	}

	/**
	 * # BROKEN
	 *
	 * I broke this somehow and rather than fix it, I rely on the __autoload method (ie, new LfActions).
	 *
	 * Accessible like qPages('lf') or queryUsers('lf')
	 */
	private function queryMagic($table, $args)
	{
		$table = strtolower($table);

		$prefix = '';
		if($args != array() && $args[0] != '')
			$prefix = $args[0].'_';

		$this->table = $prefix.$table;

		return $this;
	}

	public function setPk($column)
	{
		$this->pkIndex = $column;
		return $this;
	}

	// find() is a good function name
	public function find($by = NULL)
	{
		// filter request by mask if provided
		if( ! is_null($by) )
			foreach($by as $field => $value)
				$this->filterBy($field, $value);
		
		// temp variable for method call below
		$crud = $this->crud;

		// run the query
		$this->result = $this->$crud();
		$this->row = 0;

		// update crud in case of save after find()
		$this->crud = "update";

		return $this;
	}

	/**
	 * push current result row into data value
	 *
	 * ideal to run after query to ->save()
	 *
	 */
	public function qFromResult()
	{
		$result = $this->currentRow();

		foreach($result as $col => $val)
		{
			$setcol = "set$col";
			$this->$setcol($val);
		}

		$this->conditions = array($this->pkIndex.' = '.$this->data[$this->pkIndex]);
		unset($this->data[$this->pkIndex]);

		return $this;
	}

	//private function findByMagic($key, 
	
	private function findMagic($columns, $args)
	{
		if(preg_match_all('/[A-Z][a-z]*/', $columns, $match))
			$this->cols(implode(', ', $match[0]));

		if(isset($args[0]))
			$this->limit($args[0]);

		return $this->find();
	}


	/**
	 *
	 * Return a string <table>.<column> to define the key on which to join
	 *
	 * ## Example
	 *
	 * ~~~
	 * echo (new BlogComments)->withFk('parent_id');
	 * ~~~
	 *
	 * Would print the string 'blog_comments.parent_id';
	 *
	 *
	 */
	public function withFk($foriegn_key)
	{
		return $this->table.'.'.$foriegn_key;
	}

	public function join($cmd)
	{
		// easier than a 10 variable method argument list
		extract($cmd);
		
		// define join command
		$join = 'JOIN';
		if(isset($prefix))
		{
			if($prefix == 'rJ') $join = 'RIGHT JOIN';
			if($prefix == 'iJ') $join = 'INNER JOIN';
			if($prefix == 'lJ') $join = 'LEFT JOIN';
			if($prefix == 'fJ') $join = 'FULL JOIN';
			if($prefix == 'roJ') $join = 'RIGHT OUTER JOIN';
			if($prefix == 'ioJ') $join = 'INNER OUTER JOIN';
			if($prefix == 'loJ') $join = 'LEFT OUTER JOIN';
			if($prefix == 'foJ') $join = 'FULL OUTER JOIN';
		}
		
		if(isset($select))
		{
			// make sure we also select from the other table (in case they didnt provide columns yet
			if($this->columns == array())
				$this->columns[] = $this->table.".*";
			
			// add the select columns to the list as this table's field
			foreach($select as $column)
				$this->columns[] = "$table.$column";
		}

		$this->joins[] = $join.' '.$table.' ON '.$table.'.'.$foreignkey.' = '.$this->table.'.'.$localkey;
		
		return $this;
	}
	
	/**
	 * ## Example
	 *
	 *
	 * ~~~
	 * $blogPost = (new BlogThreads)
	 * ->joinOnId( (new BlogComments)->withFk('parent_id') )
	 * ->findById(12);
	 * ~~~
	 *
	 *
	 * @param string $foreignKey The local column to use with a join
	 *
	 * @param string $args `$args[0]` is the table.column string (ideally generated with `$this->withFk`).
	 * 
	 * ~~~
	 * $posts = (new \ForumPosts)
	 * 				->joinAuthorOnId('lf_users', ['display_name', 'email'])
	 * 				->getAll();
	 * ~~~
	 */
	private function joinMagic($prefix, $after, $args)
	{
		// will be [irlf] or j, join() handles the prefix if its not J
		$joinData['prefix'] = $prefix;
		
		// table set from arg0
		$joinData['table'] = $args[0];
		
		// cols set from arg1
		if(isset($args[1]))
			$joinData['select'] = $args[1];
		
		//ljoin(AuthorOnId)
		
		if(preg_match('/^([A-Z][a-z_]*)On([A-Z][a-z_]*)$/', $after, $keys))
		{
			$joinData['localkey'] = $keys[1];
			$joinData['foreignkey'] = $keys[2];
		}
		
		$this->join($joinData);
		
		// TODO: Add functionality to handle objects passed as $args[0]

		/*if(preg_match('/^'.$table.'On(.+)/', $table, $match))
		{
			$column = $match[1];
		}
		else
		{
			//if(is_object($table)
				//$on = $table->fk['']

			$this->joins[] = 'LEFT JOIN '.$table.' ON '.$table.'.id = '.$this->table.'.id';
		}*/

		return $this;
	}
	
	// RESTful API - may be extended to actually work against HTTP requests...
	public function api($verb, $resource, $payload = null)
	{
		$resource = '\\orm\\'.$resource;
		
		switch($verb)
		{
			case 'GET':
				return (new $resource)->find($payload);
			case 'POST':
				return (new $resource)->insertArray($payload);
			case 'PUT':
				return (new $resource)->updateById($id, $payload);
			case 'DELETE':
				return (new $resource)->deleteById($id);
		}
	}
	
	public function desc($table = null)
	{
		if( is_null( $table ) )
			$table = $this->table;
		
		if( !is_null( $table ) )
			return $this->fetchall('DESC `'.$table.'`');
		
		return $table; // this will be null if it gets to here
	}

	// usage: orm::q('lf_users')->filterByid('>', 20);
	private function filterBy($column, $args)
	{
		// to conditionally allow a condition to be specified before a value
		if(isset($args[1]))
		{
			$value = $args[1];
			$condition = $args[0];
		}
		else
		{
			$value = $args[0];
			$condition = '=';
		}

		/*if(is_object($value))
			foreach($value as $iter)
				$this->joins[] = $iter;*/

		if(is_array($value))
		{
			$condition = 'IN';

			if( !isset($value[0]) )
				$value = '()';
			else if(!is_numeric($value[0]))
				$value = '("'.implode('", "', $value).'")';
			else
				$value = '('.implode(', ', $value).')';
		}
		else if(!is_numeric($value))
			$value = "'".$this->escape($value)."'";

		$this->conditions[] = $this->table.'.'.$column.' '.$condition.' '.$value;

		return $this;
	}

	public function cols($cols)
	{
		$this->columns = array();
		if(is_array($cols))
			$this->columns = $cols;
		else
			foreach( explode(',', $cols) as $col )
				$this->columns[] = trim($col);

		return $this;
	}

	// public function addCols($cols, $table = NULL)
	// {
		// if( is_null($table) )
			// $table = $this->table;
		
		// $this->columns = array();
		// if(is_array($cols))
		// {
			// foreach( $cols as $col )
				// array_push($this->columns, $table.'.'.$col);
		// }
		// else
			// foreach( explode(',', $cols) as $col )
				// array_merge($this->columns, [$table.'.'.$col]);

		// return $this;
	// }

	public function resultCount()
	{
		return count($this->result);
	}
	
	// number of rows in table
	public function rowCount()
	{
		$save = $this->columns;
		$this->columns = array('count(*) as count');

		$crud = $this->crud;
		$result = $this->$crud();
		
		if(isset($result[0]))
			$result = $result[0];
		
		$this->columns = $save; // I just wanted a row count, no need to trash my object
		return $result['count'];
	}

	// Where override
	public function where($clause)
	{
		$this->where = $clause;
		return $this;
	}

	public function order($column = 'id', $sort = 'ASC')
	{
		$this->order = ' ORDER BY '.$column.' '.$sort;
		return $this;
	}

	// Add limit to query
	public function limit($limit)
	{
		$this->limit = ' LIMIT '.$limit;
		return $this;
	}

	public function setArray($set)
	{
		foreach($set as $col => $val)
			$this->set($col, array($val));

		return $this;
	}
/* deprecated by setAsMagic()
	// set given column as NOW()
	public function setAsNow($column)
	{
		$this->data[$column] = 'NOW()';
		return $this;
	}*/
	
	public function setAsMagic($suffix, $args)
	{		
		$column = $args[0]; // eg, `id`
		$sqlFunction = $suffix; //eg, `NOW()`
		$this->data[$column] = $sqlFunction.'()';
		
		return $this;
	}

	// shortcut to allow column in called function title
	private function set($column, $args)
	{
		$value = $args[0];
		if(isset($args[1]))
			$condition = $args[1];
		else
			$condition = '=';

		$data_value = '';

		if(is_bool($value))
		{
			$data_value = $value ? 'true' : 'false';
		}
		else if(!is_numeric($value))
		{
			$data_value = "'".$this->escape($value)."'";
		}
		else
			$data_value = $this->escape($value);


		$this->data[$column] = $data_value;

		return $this;
	}

	/**
	 * Set object CRUD action as 'INSERT into insert statement
	 */
	public function add()
	{
		$this->crud = 'insert';
		return $this;
	}

	public function saveAll()
	{
		if(is_null($this->result))
			return $this->error('No result to save');

		// save your place if you are navigating about
		$before = $this->row;

		foreach($this->result as $id => $row)
		{
			$this->row = $id;
			$this->save();
		}

		$this->row = $before; // remember `$before = $this->row;`?

		return $this;
	}

	// save or update entry
	public function save()
	{
		if($this->crud != 'insert')
			$this->crud = 'update';

		$crudFunction = $this->crud;
		return $this->$crudFunction();
	}

	public function _getSQL()
	{
		return $this->sql;
	}

	// compile SQL and return result of query
	public function first()
	{
		return $this->find(1)->get(0);
	}

	public function last()
	{
		return $this->mysqli->insert_id;
	}

	// Low level CRUD functions.
	private function insert() //create
	{
		if(!count($this->data)) 
			return null;

		$cols = '`'.implode('`, `', array_keys($this->data)).'`';
		$values = implode(', ', array_values($this->data));

		$sql = 'INSERT INTO '.$this->table.' ('.$cols.') VALUES ('.$values.')';

		$this->sql = $sql;
		$result = $this->query($sql);

		if(!$result)
			return null;
		else
			return $this->last();
	}

	private function select() // read
	{
		$sql['crud'] = 'SELECT';

		if($this->distinctCol)
			$sql['distinct'] = 'DISTINCT '.$this->distinctCol;
		else if($this->columns == array())
			$sql['columns'] = '*';
		else
			$sql['columns'] = implode(', ', $this->columns);

		$sql['from'] = 'FROM '.$this->table;

		if($this->joins != array())
			$sql['joins'] = implode(' ', $this->joins);

		if($this->where != '')
			$sql['where'] = 'WHERE '.$this->where;
		else if(count($this->conditions))
			$sql['where'] = 'WHERE '.implode(' AND ', $this->conditions);

		$sql['order'] = $this->order;
		$sql['limit'] = $this->limit;

		$this->sql = $sql;

		return $this->fetchall(implode(' ', $sql));
	}

	private function update()
	{
		$sql = 'UPDATE '.$this->table.' SET ';

		if(count($this->data))
		{
			if(is_string($this->data))
				$this->data = explode(', ', $this->data); // when logging in to admin, $this->data is a comma separated list of user columns

			$set = array();

			foreach($this->data as $col => $val)
			{
				$set[] = "$col = $val";
			}
			$sql .= implode(', ', $set);
		}

		if($this->where != '')
			$sql .= ' WHERE '.$this->where;
		else if(count($this->conditions))
			$sql .= ' WHERE '.implode(' AND ', $this->conditions);
		else if(isset($this->data['id']))
		{
			$sql .= ' WHERE id = '.$this->data['id']; // still shows in the SET assignments, but is not a problem so ill fix it later...
			unset($this->data['id']);
		}

		$sql .= $this->limit;

		$this->sql = $sql;

		return $this->query($sql);
	}
	public function delete()
	{
		$sql = 'DELETE FROM '.$this->table;

		if($this->where != '')
			$sql .= ' WHERE '.$this->where;
		else if(count($this->conditions))
			$sql .= ' WHERE '.implode(' AND ', $this->conditions);

		$sql .= $this->limit;

		$this->sql = $sql;

		return $this->query($sql);
	}

	// Higher Level CRUD

	public function get($row = null)
	{
		if(!is_null($this->result))
		{
			if(!is_null($row))
				if(isset($this->result[$row]))
					return $this->result[$row];
				else
					return NULL;

			return $this->nextRow();
		}
		else $this->find();

		return $this->nextRow();
	}

	private function currentRow()
	{
		if($this->row >= count($this->result))
		{
			//$this->errors[] = 'Row counter out of bounds';
			return null; // should be returning null when there isnt something
		}
		
		return $this->result[$this->row];
	}

	private function nextRow()
	{
		$result = $this->currentRow();
		$this->row++;
		return $result;
	}

	public function getAll()
	{
		if(!is_null($this->result))
			return $this->result;

		return $this->find()->result;
	}

	public function get1byid($id)
	{
		return $this->filterByid($id)->first();
	}

	public function rm1byid($id)
	{
		return $this->filterByid($id)->delete();
	}

	public function update1byid($id, $data)
	{
		$page = $this->filterByid($id);
		foreach($data as $col => $val)
		{
			$setcol = "set$col";
			$page->$setcol($val);
		}
		$page->save();
	}

	public function add1($data)
	{
		/*$insert = $this->add();
		foreach($data as $col => $val)
		{
			$setcol = "set$col";
			$insert->$setcol($val);
		}
		return $insert->save();*/
		//backward compatible
		return $this->insertArray($data);
	}

	public function insertArray($data)
	{
		$insert = $this->add();
		foreach($data as $col => $val)
		{
			$col = ucfirst($col);
			$setcol = "set$col";
			$insert->$setcol($val);
		}
		return $insert->save();
	}

	public function updateById($id, $data)
	{
		$page = $this->filterByid($id);
		foreach($data as $col => $val)
		{
			$col = ucfirst($col);
			$setcol = "set$col";
			$page->$setcol($val);

		}
		$page->save();
	}

	public function rawResult()
	{
		return $this->mysqli_result;
	}

	/**
	 * Prints number of rows in the MySQL result
	 *
	 * @param bool $make_conversion_easier Don't think this does anything.
	 */
	function numrows($make_conversion_easier = true)
	{
		return $this->mysqli_result->num_rows;
	}
}

/** My nasty solution to closing the db connection after page execution is completed */
class ___LastSay
{
	public function __destruct()
	{
		$db = mem::get('db');
		/*
		ob_start();
		pre($db, 'var_dump');
		$pre = ob_get_clean();
		
		stderr($pre);*/
		
		if( ! is_null( $db ) )
			$db->close();
	}
}
$varNameDoesntMatterSoLongAsItDestructsAfterTheScriptEnds = new ___LastSay();





/**
 * If the class is not already defined, you can instantiate a new class through autoload.
 *
 * `$users = new LfUsers();` would auto generate the follow class definition:
 *
 * ```
 * class LfUsers extends orm
 * {
 * 		private $table = 'lf_users';
 * }
 * ```
 * There is a fun shortcut to the above where you can just call new class instances out of a given table name using `__autoload`.
 *
 * Try it: `var_dump(new LfUsers);`
 * 
 * If you were to call `(new BlogThreads)`, the autoload function would quickly define a class called `BlogThreads` extended from the `orm` on the fly with a table set as `blog_threads`.
 * 
 * ### Dev Note
 * 
 * Just noticed that this will catch things like `(new User)` when people meant `(new \lf\user)` or anything else they meant to type instead. Its a convenient way to call, but needs fixed. Maybe a namespace?
 */
spl_autoload_register( function ($class_name) {
	
	// orm\blog_threads format
	if(preg_match('/^orm\\\(.+)/', $class_name, $match))
	{
		// no support for more than just table yet
		$rawuri = $match[1];
		
		preg_match_all('/([^\\\]+)\\\?/', $rawuri, $match);
		$args = $match[1];
		$table = $args[0]; 
		
		
		$guts['table'] = 'public $table = "'.$table.'";';
			
		$namespace = '';
		// $namespace = '\\orm\\first_table\\on\\next_table
		
		// ty chelmertz http://stackoverflow.com/a/13504972
		$jitClassDefinition = sprintf(
			'namespace orm; class %s extends \\lf\\orm { '.implode(' ', $guts).' }'
			, $table 
		);
			
		#strdump($jitClassDefinition);
		eval($jitClassDefinition);  
		
		return;
	}
	
	
	
	
	
	
	
	// to let other autoload registers load things from namespaced things.
	if( strpos($class_name, '\\') !== false )
		return;
	
	// BlogThreads will use the table 'blog_threads'
	if(preg_match_all('/^([A-Z][^A-Z]+)+$/', $class_name, $matches))
	{
		//pre($matches);
		
		
		$capitalFollowing = '/([a-z])([A-Z])/';
		$separate = '\1_\2';
		$camelCaseTable = $matches[0][0];
		$table = strtolower(
			preg_replace($capitalFollowing, $separate, $camelCaseTable)
		);
		
		$guts['table'] = 'public $table = "'.$table.'";';
		$guts['method'] = 'public function debug() { 
			echo "This was made from the extender thing"; }';

		//pre('class %s extends \\lf\\orm { '.implode(' ', $guts).' }');
		
		// ty chelmertz http://stackoverflow.com/a/13504972
		eval(sprintf(
			'class %s extends \\lf\\orm { '.implode(' ', $guts).' }',
			$class_name
		));    
	}
});

//echo (new \orm\lf_users);

/**
 * Made another one that catches stuff like `\table\lf_settings` rather than autoloading in the global namespace
 */
spl_autoload_register( function ($class_name) {
	
	// lmk if you think this is too strict of a pattern. also idk how 3 backslashes makes this work... but it works
	if( ! preg_match('/^db\\\([a-zA-Z0-9_\-]+)$/', $class_name, $match) )
		return;

	$guts['table'] = 'public $table = "'.strtolower($match[1]).'";';

	//$guts['method'] = 'public function test() { echo "Hey there"; }';
	
	// using HEREDOC because backslashes are tedious to escape
	$classDefinition = <<<HEREDOC
	namespace db;

	class lf_settings extends \lf\orm
	{
		%guts%
	}
HEREDOC;
	
	eval(str_replace(
		'%guts%', 
		implode(' ', $guts), 
		$classDefinition
	));		
});

//backward compatible after db methods were taken by orm
class db extends orm {}
