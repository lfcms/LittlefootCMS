<?php

// take some features out and this becomes a forum

class todo extends app
{
	public $default_method = '_project';
	
	protected function init($vars)
	{
		$this->ini = 'todo_dev';
		
		if(!$this->db->is_table($this->ini))
			$this->db->query('
				CREATE TABLE `'.$this->ini.'` (
				  `id` int(5) NOT NULL auto_increment,
				  `project` varchar(50) NOT NULL,
				  `owner` int(11) NOT NULL,
				  `title` text NOT NULL,
				  `note` text NOT NULL,
				  `date` datetime NOT NULL,
				  `type` varchar(50) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=136 DEFAULT CHARSET=latin1
			');
	}
	
	public function _project($vars)
	{
		if($vars[0] == '') return $this->projectlist($vars);
		
		$inst = urldecode($vars[0]);
		$vars = array_slice($vars, 1);
		$this->inst = $inst;
		$this->instbase = $this->lf->appurl.$inst.'/';
		
		echo '
		<style type="text/css">
			.app-todo ul { padding: 0; margin: 0; }
		</style>
		<h2><a href="%appurl%">HQ</a> / <a href="'.$this->instbase.'">'.$this->inst.'</a></h2>';
		
		//print_r($vars);
		
		if(!isset($vars[0])) $vars[0] = 'latest'; // where latest and open
		$function = $vars[0];
		
		$this->$function($vars);
		
		
		
		/*
		$cats = $this->db->fetchall("SELECT DISTINCT category FROM io_threads WHERE instance = '".$inst."' ORDER BY category");
		if(!$cats) redirect302($this->request->base.'apps/manage/blog/');
		else
		foreach($cats as $cat)
			$this->cats[] = $cat['category'];
			
		echo '<div style="float: left; width: 200px;"><h4>Categories</h4>';
		echo '<form action="'.$this->inst_base.'addcat/" method="post"><input type="text" name="category" placeholder="New category" /></form>';
		foreach($this->cats as $cat)
			echo '<a href="'.$this->inst_base.'cat/'.$cat.'/">'.$cat.'</a><br />';
		echo '</div>';
		echo '<div style="margin-left: 200px">';
		
		if(!isset($vars[0])) $vars[0] = 'view';
		
		$function = $vars[0];
		$this->$function($vars);
		
		echo '</div>';*/
	}
	
	private function projectlist($vars)
	{		
		echo '
		<style type="text/css">
			.app-todo ul { padding: 0; margin: 0; }
		</style>
		<h2><a href="%appurl%">HQ</a></h2>';
		// categories
		$projects = $this->db->fetchall('SELECT DISTINCT project FROM '.$this->ini.' ORDER BY project');
		$data = $this->db->fetchall("SELECT * FROM ".$this->ini." ORDER BY date DESC LIMIT 50");
		
		// Print to screen
		include 'view/latest.php';
	}
	
	private function latest($vars)
	{
		$projects = $this->db->fetchall('SELECT DISTINCT project FROM '.$this->ini.' ORDER BY project');
		
		$data = $this->db->fetchall("SELECT * FROM ".$this->ini." WHERE project = '".mysql_real_escape_string($this->inst)."' LIMIT 50");
		
		// Print to screen
		include 'view/latest.php';
	}
	/*
	public function inst($vars)
	{
		if(!isset($vars[1])) return $this->main();
		
		$inst = urldecode($vars[1]);
		$vars = array_slice($vars, 2);
		$this->inst = $inst;
		$this->inst_base = $this->request->base.'apps/manage/blog/inst/'.urlencode($inst).'/';
		
		echo '<h3>Instance: <a href="'.$this->inst_base.'">'.$this->inst.'</a></h3>';
		
		$cats = $this->db->fetchall("SELECT DISTINCT category FROM io_threads WHERE instance = '".$inst."' ORDER BY category");
		if(!$cats) redirect302($this->request->base.'apps/manage/blog/');
		else
		foreach($cats as $cat)
			$this->cats[] = $cat['category'];
			
		echo '<div style="float: left; width: 200px;"><h4>Categories</h4>';
		echo '<form action="'.$this->inst_base.'addcat/" method="post"><input type="text" name="category" placeholder="New category" /></form>';
		foreach($this->cats as $cat)
			echo '<a href="'.$this->inst_base.'cat/'.$cat.'/">'.$cat.'</a><br />';
		echo '</div>';
		echo '<div style="margin-left: 200px">';
		
		if(!isset($vars[0])) $vars[0] = 'view';
		
		$function = $vars[0];
		$this->$function($vars);
		
		echo '</div>';
	}*/
	
	public function view($vars)
	{
		if(!isset($vars[1])) redirect302();
		
		$data = $this->db->fetch("SELECT * FROM ".$this->ini." WHERE project = '".mysql_real_escape_string($this->inst)."' AND id = ".intval($vars[1]));
		
		print_r($data);
		
		$cwd = getcwd();
		chdir('../comments');
		$comments = $this->lf->mvc('comments', 'todo/'.$vars[1]);
		echo str_replace('%appurl%', '%appurl%comment/', $comments);
		chdir($cwd);
	}
		
	public function comment($vars)
	{
		
		$vars = array_slice($vars, 1);
		print_r($vars);
		print_r($_POST);
		
		$cwd = getcwd();
		chdir('../comments');
		$comments = $this->request->apploader('comments', 'blog/'.intval($_POST['inst']), $vars);
		$comments = str_replace('%appurl%', '%appurl%comment/', $comments);
		chdir($cwd);
		
		echo $comments;
	}
}