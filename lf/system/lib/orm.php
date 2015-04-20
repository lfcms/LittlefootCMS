<?php

/**
 * Abstracted object relation mapping for database manipulation.
 *
 * # Example Usage
 *
 * ~~~
 * // SELECT * FROM mydb.my_table WHERE id = 34
 * // returns an array
 * orm::q('my_table')->filterByid(34)->get();
 *
 * // SELECT * FROM mydb.my_table WHERE cost > 25
 * // returns an array
 * orm::q('my_table')->filterBycost('>', 25)->get();
 *
 * // UPDATE mydb.my_table SET title = 'new title' WHERE id = 34
 * // returns result of $this->db->query($sql);
 * orm::q('my_table')->settitle('new title')->filterByid(34)->save();
 *
 * // UPDATE mydb.my_table SET title = 'new title' WHERE id = 34
 * // returns result of $this->db->query($sql);
 * orm::q('my_table')->updateById(34, $_POST);
 *
 * // INSERT INTO mydb.my_table (id, title, body) VALUES (NULL, 'my title', 'my body')
 * $_POST = array('title' => 'my title', 'body' => 'my body');
 * orm::q('my_table')->insertArray($_POST);
 *
 * // DELETE FROM mydb.my_table WHERE id = 12
 * orm::q('my_table')->filterByid(12)->delete();
 * ~~~
 */
class orm {

	/** @var bool $debug Prints resulting $sql after execution. */
	public $debug = false;
	
	/** @var string $sql Variable used to construct the SQL query at execution */
	private $sql;

	/** @var Database $db Database wrapper object. $this->db */
	private $db;
	
	/** @var string $table Stores the table specified at orm::q('my_table') */
	protected $table = '';
	
	/** @var string $crud Chosen CRUD operation (select, insert, update, delete) */
	private $crud = 'select';
	
	/** @var array $data Array of data ($col => $val). Used in CRUD operations. */
	public $data = array();
	
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
	
	/**
	 * Initialize the orm class. Store the Database wrapper and the specified table which is ideally called from orm::q('my_table')
	 *
	 * @param Database $db Database wrapper
	 */
	public function __construct($table = '', $db = NULL)
	{
		if(is_null($db))
			$db = db::init();
		
		if($table != '')
			$this->table = $table;
		
		$this->db = $db;
	}
	
	public function __destruct()
	{
		if($this->debug)
			echo $this->sql;
	}
	
	public function __get($name)
	{
		if(!is_null($this->result))
			return $this->currentRow()[$name];
		
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
	
	// wildcard catchall for shortcut requests (filter, set, etc)
	public function __call($method, $args) {
		
		// look for valid request
		if(!preg_match('/^(getBy|getAllBy|by|filterBy|set|find|q|query)(.+)/', $method, $method_parse))
			return $this->throwException('Invalid method called');
		
		// parse out method and column reference
		$m = $method_parse[1];
		if($m == 'by') // 'by' is an alias to filterBy()
			$m = 'filterBy';
		if($m == 'find')
			$m = 'findMagic';
		if($m == 'q') // 'q' is an alias to query()
			$m = 'query';
		
		
		if($m == 'getBy') // 'by' is an alias to filterBy()
		{
			$m = 'filterBy';
			$this->$m($method_parse[2], $args);
			return $this->get();
		}
		
		if($m == 'getAllBy') // 'by' is an alias to filterBy()
		{
			$m = 'filterBy';
			$this->$m($method_parse[2], $args);
			return $this->getAll();
		}
		
		return $this->$m($method_parse[2], $args);
    }
	
	private function throwException($msg = '')
	{
		//$this->error[] = $msg;
		return $this;
	}
	
	/**
	 * Accessible like qPages('lf') or queryUsers('lf')
	 * 
	 * 
	 * 
	 */
	private function query($table, $args)
	{
		$table = strtolower($table);
		
		$prefix = '';
		if($args != array() && $args[0] != '')
			$prefix = $args[0].'_';
		
		$this->table = $prefix.$table;
		
		return $this;
	}
	
	// i kinda want to call this function load()
	public function find($args = null)
	{
		if(isset($args[0]))
			$this->limit($args[0]);
		
		$crud = $this->crud;
		$this->result = $this->$crud();
		return $this;
	}
	
	private function findMagic($columns, $args)
	{
		if(preg_match_all('/[A-Z][a-z]*/', $columns, $match))
			$this->cols(implode(', ', $match[0]));

		if(isset($args[0]))
			$this->limit($args[0]);
		
		return $this->find();
	}
	
	// shortcut to allow column in called function title
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
		
		if(!is_numeric($value))
			$value = "'".$this->db->escape($value)."'";
		
		$this->conditions[] = $column.' '.$condition.' '.$value;
		
		return $this;
	}
	
	public function cols($cols)
	{
		if(is_array($cols))
			$cols = implode(', ', $cols);
		
		$this->data = $cols;
		return $this;
	}

	public function count()
	{
		$this->data = 'count(*) as count';
		
		$crud = $this->crud;
		$result = $this->$crud();
		if(isset($result[0]))
			$result = $result[0];
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
	
	// shortcut to allow column in called function title
	private function set($column, $args)
	{
		$value = $args[0];
		if(isset($args[1]))
			$condition = $args[1];
		else
			$condition = '=';
		
		if(!is_numeric($value))
			$value = "'".$this->db->escape($value)."'";
		
		$this->data[$column] = $value;
		
		return $this;
	}
	
	// get into insert statement
	public function add()
	{
		$this->crud = 'insert';
		return $this;
	}
		
	// save or update entry
	public function save()
	{
		if($this->crud != 'insert')
			$this->crud = 'update';
			
		$crud = $this->crud;
		return $this->$crud();
	}
	
	// compile SQL and return result of query
	public function first()
	{
		return $this->find(1)->get(0);
	}
	
	// CRUD functions.
	private function insert() //create
	{
		if(!count($this->data)) return null;
		
		$cols = '`'.implode('`, `', array_keys($this->data)).'`';
		$values = implode(', ', array_values($this->data));
		
		$sql = 'INSERT INTO '.$this->table.' ('.$cols.') VALUES ('.$values.')';
		
		$this->sql = $sql;
		$result = $this->db->query($sql);
		
		if(!$result) return null;
		else return $this->db->last();
	}
	private function update()
	{
		$sql = 'UPDATE '.$this->table.' SET ';
		
		if(count($this->data))
		{
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
			
		$sql .= $this->limit;
		
		$this->sql = $sql;
		
		return $this->db->query($sql);
	}
	private function select() // read
	{
		$sql = 'SELECT ';
		
		if($this->data == array()) 
			$sql .= '*';
		else
			$sql .= $this->data;
			
		$sql .= ' FROM '.$this->table;
		
		if($this->where != '')
			$sql .= ' WHERE '.$this->where;
		else if(count($this->conditions))
			$sql .= ' WHERE '.implode(' AND ', $this->conditions);
		
		$sql .= $this->order;
		$sql .= $this->limit;
		
		$this->sql = $sql;
		
		return $this->db->fetchall($sql);
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
		
		return $this->db->query($sql);
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
					return false;
		
			return $this->nextRow();
		}
		else $this->find();
		
		return $this->nextRow();
	}
	
	private function currentRow()
	{
		if($this->row >= count($this->result))
			return false;
			
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
			$setcol = "set$col";
			$page->$setcol($val);
			
		}
		$page->save();
	}
}
