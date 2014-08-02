<?php

class orm {

	private $db;
	private $table;
	
	public $crud = 'select';
	public $data = array(); // array of data ($col => $val)
	public $conditions = array(); // array of conditions
	public $limit = '';
	public $where = '';
	
	
	//CONSTANTS
	const EQ = '=';
	const GT = '>';
	const LT = '<';
	const NEQ = '!=';
	

	// this can be the query builder
	public function __construct($db, $table = '')
	{
		$this->table = $table;
		$this->db = $db;
	}
	
	// query builder
	public function q($table) 
	{
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
	
	
	// Where override
	public function where($clause)
	{
		$this->where = $clause;
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
		
		echo $sql.'<br />';
		
		return $this->db->query($sql);
	}
	private function select() // read
	{
		$sql = 'SELECT * FROM '.$this->table;
		
		if($this->where != '')
			$sql .= ' WHERE '.$this->where;
		else if(count($this->conditions))
			$sql .= ' WHERE '.implode(' AND ', $this->conditions);
		
		$sql .= $this->limit;
		
		echo $sql.'<br />';
		
		return $this->db->fetchall($sql);
	}
	private function delete()
	{
		
	}
	
	
	
	
	
	/*public function select1()
	{
		//return $this->output();
		return $this->db->fetch('SELECT * FROM '.$this->table.' LIMIT 1');
	}*/
}