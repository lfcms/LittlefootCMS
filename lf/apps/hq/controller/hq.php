<?php

class hq extends app
{
	public $default_method = '_router';
	
	public function _router($vars)
	{
		echo '|'.$this->lf->appurl.'|';
		
		if($vars[0] == '') return $this->main($vars);
		
		$this->instbase = $this->lf->appurl.$vars[0].'/';
		$this->inst = urldecode($vars[0]);
		
		// Load 
		$vars = array_slice($vars, 1); // move vars over to emulate direct execution
		// disable $method = 'home'; // default method, also acts as router
		//  if(isset($vars[0])) $method = $vars[0];
		$this->home($vars);
		
		echo '<div style="clear:both; margin-bottom: 10px;"></div>';
	}
	
	public function main($vars)
	{
		echo '<h2><a href="%appurl%">HQ</a></h2>
			<h3>Projects</h3>
			<form action="%appurl%addproject/" method="post"><input type="text" name="project" placeholder="New project" /></form>
		';
		$projects = $this->db->fetchall('SELECT * FROM hq_projects');
		foreach($projects as $project)
		{
			echo '<a href="%appurl%'.$project['id'].'/">'.$project['title'].'</a><br />';
		}
	}
	
	private function home($vars) // second router
	{
		$project = $this->db->fetch("SELECT * FROM hq_projects WHERE id = ".intval($this->inst));
		
		if(!$project) return 'No project found';
		
		$wiki = $project['wiki'];
		$project = $project['title'];
		
		//print_r($vars);
		echo '<h2><a href="%appurl%">HQ</a> / <a href="'.$this->instbase.'">'.$project.'</a></h2>
			<p><a href="'.$this->instbase.'tickets/">Tickets</a> | <a href="'.$this->instbase.'reference/">Reference</a> | <a href="'.$this->instbase.'calendar/">Calendar</a></p>';
		
		if(!isset($vars[0])) { include 'view/home.php';  return; }
		
		// else, route request:
		
		$load = $vars[0];
		$vars = array_slice($vars, 1); // move vars over to emulate direct execution
		
		if(!isset($vars[0])) $vars[0] = '';
		
		// Load
		switch($load)
		{
			case 'tickets':
				$this->tickets($vars);
				break;
			case 'reference':
				$this->reference($vars);
				break;
			case 'calendar':
				$this->calendar($vars);
				//echo $this->lf->extmvc($this->inst.'/calendar', 'hq/calendar', $this->inst);
				break;
		}
		//$vars = array_slice($vars, 1); // move vars over to emulate direct execution
		//if(isset($vars[0])) $method = $vars[0];
		//$this->$method($vars);	
	}
	
	private function tickets($vars)
	{
		echo $this->lf->extmvc($this->inst.'/tickets', 'hq/tickets', $this->inst, $vars);
	}
	
	private function reference($vars)
	{
		echo $this->lf->extmvc($this->inst.'/reference', 'hq/reference', $this->inst, $vars);
	}
	
	private function calendar($vars)
	{
		echo $this->lf->extmvc($this->inst.'/calendar', 'hq/calendar', $this->inst, $vars);
	}
	
	public function addproject($vars)
	{
		if(count($_POST) > 0)
		{
			$result = $this->db->query("
				INSERT INTO hq_projects (`id`, `title`, `wiki`)
				VALUES (
					NULL, '".mysql_real_escape_string($_POST['project'])."', 'New Project'
				)
			");
			
			$id = $this->db->last();
			
			redirect302($this->lf->appurl.$id);
		}
		
		redirect302();
	}
}