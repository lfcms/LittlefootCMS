<?php

namespace lf;

// routes to model methods, ideally prepared for RESTful communication
class api
{
	public function rest()
	{
		$action = \lf\requestGet('Action');
		
		$headers = getallheaders();
			
		// test for API token, expand on this later
		$api_key = $headers['X-Api-Key'];
		if($api_key != '612e648bf9594adb50844cad6895f2cf')
			return $this;
		
		if( count($action) < 2 )
		{
			echo '403';
		}
		
		// default process
		$class = '\\lf\\'.$action[0];
		$method = $action[1];
		$id = null;
		// set id if applicable
		if(isset($action[2]))
		{
			$id = $action[2];
		}
		
		// check for app api RESTful routing, this will be made dynamic later
		if($action[0] == 'blog')
		{
			chdir(LF.'apps/blog');
			include 'model/blog.php';
			$class = '\\blog';
		}
		
		// display result as JSON
		header('Content-Type: application/json');
		return json_encode( 
				[ "result" => (new $class)->$method($id) ] 
		);
	}
}