<?php

class orm {

	private $debug;
	private $sql;

	private $db;
	private $table;
	
	public $crud = 'select';
	public $data = array(); // array of data ($col => $val)
	public $conditions = array(); // array of conditions
	public $where = '';
	public $order = '';
	public $limit = '';
	

	// this can be the query builder
	public function __construct($db, $table = '')
	{
		$this->table = $table;
		$this->db = $db;
	}
	
	public function __destruct()
	{
		//if($this->debug)
			//echo $this->sql;
	}
	
	// query builder
	public function q($table, $debug = false) 
	{
		$this->debug = $debug;
		return new orm($this->db, $table);
	}
	
	
	// __toString
	public function __toString()
	{
		ob_start();
		$counter = 1;
		foreach($this->get() as $row)
		{
			echo 'Row #'.$counter.'<br />';
			foreach($row as $col => $val)
			{
				echo $col.': '.$val.'<br />';
			}
			$counter++;
		}
		
		return ob_get_clean();
	}
	
	// wildcard catchall for shortcut requests (filter, set, etc)
	public function __call($method, $args) {
		
		// look for valid request
		if(!preg_match('/^(filterBy|set)(.*)/', $method, $method_parse))
			return null;
		
		// parse out method and column reference
		$m = $method_parse[1];
		$column = $method_parse[2];
		
		return $this->$m($column, $args);
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
	
	public function cols($cols)
	{
		if(is_array($cols))
			$cols = implode(', ', $cols);
		
		$this->data = $cols;
		return $this;
	}
	
	// Where override
	public function where($clause)
	{
		$this->where = $clause;
		return $this;
	}
	
	public function order($column, $sort = 'ASC')
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
	
		
	// compile SQL and return result of query
	public function get()
	{
		$crud = $this->crud;
		return $this->$crud();
	}
	
	// save or update entry
	public function save()
	{
		if($this->crud != 'insert')
			$this->crud = 'update';
			
		$crud = $this->crud;
		return $this->$crud();
	}
	
	
	// CRUD functions
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
	
	
	
	
	
	/*public function select1()
	{
		//return $this->output();
		return $this->db->fetch('SELECT * FROM '.$this->table.' LIMIT 1');
	}*/
}