<?php

class orm {

	private $db;
	
	public $crud = 'select';
	public $conditions = array(); // array of conditions
	public $limit = '';
	
	public $where = '';
	
	
	
	
	
	
	public $query = array();
	
	
	private $table;
	
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
	
	// query builder base
	public function q($table) 
	{
		return new orm($this->db, $table);
	}
	
	// CRUD functions
	public function create()
	{
		
	}
	public function update()
	{
		
	}
	public function select() // read
	{
		$sql = 'SELECT * FROM '.$this->table;
		
		if(count($this->conditions))
			$sql .= ' WHERE '.implode(' AND ', $this->conditions);
		
		$sql .= $this->limit;
		
		echo $sql.'<br />';
		
		return $this->db->fetchall($sql);
	}
	public function delete()
	{
		
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
	public function filterBy($column, $args)
	{
		$value = $args[0];
		if(isset($args[1]))
			$condition = $args[1];
		else
			$condition = '=';
		
		if(!is_numeric($value))
			$value = "'".$this->db->escape($value)."'";
		
		$this->conditions[] = $column.' '.$condition.' '.$value;
		
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
	
	}
	
	
	
	/*public function select1()
	{
		//return $this->output();
		return $this->db->fetch('SELECT * FROM '.$this->table.' LIMIT 1');
	}*/
}