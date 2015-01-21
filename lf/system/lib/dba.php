<?php

/**
 * @ignore
 */
class dba
{
	public $asdf = 'asdf';
	public $orm;
	
	public function __construct($args)
	{
		if(!class_exists('orm')) include ROOT.'system/lib/orm.php';
		$this->orm = orm::q($args);
	}
	
	static public function __callStatic($method, $args) {
		
		//find25IdTitlewhereId
		
		//var_dump($method,$args);
		
		// else if [A-Z]; then instanciate
		if(preg_match('/^[A-Z_][A-Za-z_]+$/', $method, $parse)) // check for Table_name
		{
			$dba = new dba($parse[0]);
			return $dba;
		}
		// if [a-z]; then find, etc
		else if(preg_match('/^(find)(\d*)(([A-Z][a-z_]+)*)(where([A-Z][a-z_]+)*)?/', $method, $parse)) // check for requestSomething
		{
			echo 'lowercaseStatic';
			var_dump($parse);
		}
		else
		{
			return null;
		}
    }
	
	
	// sub call to instantiated dba
	public function __call($method, $args) {
		//find25IdTitlewhereId
		
		//var_dump($method,$args);
		
		// else if [A-Z]; then instanciate
		if(preg_match('/^[A-Z_][A-Za-z_]+$/', $method, $parse)) // check for Table_name
		{
			echo 'Upper';
			//var_dump($parse);
			//var_dump($parse);
			
			$dba = new dba($parse[0]);
			return $dba;
		}
		// if [a-z]; then find, etc
		else
		{
			
			$where = '';
			echo $method.': ';
			
			if(preg_match('/^(find)(\d*)(([A-Z][a-z_]+)*)/', $method, $parse)) // check for requestSomething
			{
				$action = $parse[1];
				$limit = $parse[2];
				
				
				$parts = explode('where', $parse[3]);
				$columns = $parts[0];
				$where = $parts[1];
			
				if(preg_match_all('/[A-Z][a-z_]+/', $columns, $parse)) // check for requestSomething
					$columns = $parse[0];
					
				if(preg_match_all('/[A-Z][a-z_]+/', $where, $parse)) // check for requestSomething
					$where = $parse[0];
				
				
			}
			else
			{
				return null;
			}
			
				echo 'col/where: ';
				var_dump($columns, $where, $action, $limit);
			
			
			// find 25 id, user where id > 25
			
			//orm::user->cols('id,user')->filterByid('>', 25);
			
			$filterByAction = 'filterBy'.$action;
			return $this->orm
				->cols(implode(',', $columns))
				->$filterByAction()
				->get();
		}
    }
	
	public function __get($name) {
		
		//echo $name;
		
		
		
		
		$properties = array('rowcount', 'first');
		
		// look for valid request
		if(!preg_match('/^('.implode('|', $properties).')(.*)/', $name, $request))
			return null;
			
		// parse out method and column reference
		$method = $request[1];
		$data = $request[2];
		
		//return $this->$method($data);
		
		$dba = new dba('asdf');
    }
	
	private function asdf($asdf){
		echo $asdf;
	}
	
	private function rowcount($data)
	{
		echo $data;
	}
}