<?php

class hq extends app
{
	protected function init($vars)
	{
		/* header
		
		<h2>HQ / Project?</h2>
		
		if $vars[0] == int
			print project
			$vars = array_slice($vars, 1)
		
		else
			if $vars[0] == ticket
				ticket: active
		
		*/
		
		$url = '';
		
		$projecthtml = '';
		if(intval($vars[0]) != 0)
		{
			$project = $this->db->fetch("SELECT * FROM hq_projects WHERE id = ".intval($vars[0]));
			if(!$project) return 'No project found';
			$projecthtml = '/ <a href="%appurl%'.intval($vars[0]).'/">'.$project['title'].'</a>';
			
			$url = intval($vars[0]).'/';
			
			$vars = array_slice($vars, 1); // to trick the selector below
		}
		
		// take care of the header
		$nav = array('tickets', 'reference', 'calendar');
		$nav_html = '';
		foreach($nav as $item)
		{
			$active = '';
			if(isset($vars[0]))
				$active = $item == $vars[0] ? ' class="active"' : '';
				
			$nav_html .= '<a'.$active.' href="%appurl%'.$url.$item.'">'.ucfirst($item).'</a>';
		}
		
		echo '<div class="header">
				<h2><a href="%appurl%">HQ</a> '.$projecthtml.'</h2>
				<style type="text/css">
					#login { float: right; margin-right: 10px; margin-top: 2px; }
				</style>
				<form id="hq_searchbox" action="%appurl%search/" method="get">
					 <input type="text" name="search" placeholder="Search HQ" />
				</form>
				%login%
			</div>
			<div id="project_nav">'.$nav_html.'</div>';
	
	
	
		// UPGRADE SYSTEM! 
		
		//if($_SERVER['REMOTE_ADDR'] != '67.232.207.108') die('Maintenance');
		
		/*
		// upgrade to reply count
		$tickets = $this->db->fetchall('SELECT * FROM hq_tickets');
		foreach($tickets as $ticket)
		{
			$count = $this->db->fetch("SELECT count(id) as count FROM lf_comments WHERE inst = 'hq/".$ticket['project']."/".$ticket['id']."'");
			$count = $count['count'];
			
			$this->db->query("UPDATE hq_tickets SET replies = ".$count." WHERE id = ".$ticket['id']);
		}
		
		*/
		
		
		/*
		
		
		// Upgrade to categories table
		$tick_cats = array();
		$tickets = $this->db->fetchall('SELECT * FROM hq_tickets');
		foreach($tickets as $ticket)
		{
			@$tick_cats[$ticket['project']][$ticket['category']][$ticket['status']]++;
		}
		
		//tickets
		foreach($tick_cats as $project => $cat)
		{
			foreach($cat as $name => $status)
			{
				$sql = "INSERT INTO hq_categories (id, project, type, category, ".implode(', ', array_keys($status)).") 
					VALUES (NULL, ".$project.", 'ticket', '".$name."', ".implode(', ', $status).")";
				
				$this->db->query($sql);
				$id = $this->db->last();
				echo $sql.'<br />'; 
				
				$sql = "UPDATE hq_tickets SET category = '".$id."' WHERE category = '".$name."' AND project = ".$project;
				$this->db->query($sql);
				echo $sql.'<br />';
			}
		}
		
		$refs = $this->db->fetchall('SELECT * FROM hq_reference');
		$ref_cats = array();
		foreach($refs as $ticket)
		{
			@$ref_cats[$ticket['project']][$ticket['category']][$ticket['status']]++;
		}
		
		echo 'end tickets';
		
		//refs
		foreach($ref_cats as $project => $cat)
		{
			foreach($cat as $name => $status)
			{
				$sql = "INSERT INTO hq_categories (id, project, type, category, ".implode(', ', array_keys($status)).") 
					VALUES (NULL, ".$project.", 'reference', '".$name."', ".implode(', ', $status).")";
				
				//$this->db->query($sql);
				$id = $this->db->last();
				echo $sql.'<br />';
				
				$sql = "UPDATE hq_references SET category = '".$id."' WHERE category = '".$name."' AND project = ".$project;
				$this->db->query($sql);
				echo $sql.'<br />';
			}
		}*/
	}
	
	public function main($vars)
	{
		if(intval($vars[0]) != 0) 
			return $this->_router($vars, 'home');
		
		ob_start();
		echo '
			<div id="projects">
				<h3>Projects</h3>
				<form action="%appurl%addproject/" method="post"><input type="text" name="project" placeholder="New project" /></form>
				<ul id="project_list">';
			$projects = $this->db->fetchall('SELECT * FROM hq_projects ORDER BY title');
			foreach($projects as $project)
				echo '<li><a href="%appurl%'.$project['id'].'/tickets">'.$project['title'].'</a></li>';
				
			echo '</ul>
			</div>
			
			<div id="global_unassigned">';
			$this->unassigned();
			echo '</div>
			
			<div id="global_assigned">';
			$this->assigned();
			echo '</div>';
			
			$this->auditlog();
		
		$out = ob_get_clean();
			
		if(preg_match_all('/%user:([0-9]+)%/', $out, $match))
		{
			$users = array_unique($match[1]);
			$userlist = $this->db->fetchall('SELECT id, display_name FROM lf_users WHERE id IN ('.implode(',', $users).')');
			
			foreach($userlist as $user)
				$out = str_replace('%user:'.$user['id'].'%', $user['display_name'], $out);
		}
			
		echo $out;
	}
	
	public function assigned($project = '', $category = '')
	{
		$tickets = $this->db->fetchall("
			SELECT t.*, p.title as project_title 
			FROM hq_tickets t LEFT JOIN hq_projects p ON p.id = t.project 
			WHERE t.assigned = '".$this->lf->api('getuid')."' AND t.status = 'open'  
			ORDER BY p.title, t.id
		");
			
		echo '<h3>Assigned ('.count($tickets).')</h3>
		<ul>';
		foreach($tickets as $ticket)
		{
			$flag = '';
			if($ticket['flagged'] != 'none')
				$flag = ' class="flagged_'.$ticket['flagged'].'"';
				
			echo '<li'.$flag.'>
				<a href="%appurl%'.$ticket['project'].'">'.$ticket['project_title'].'</a> / 
				<a href="%appurl%'.$ticket['project'].'/tickets/view/'.$ticket['id'].'/">'.htmlentities($ticket['title']).'</a>
			</li>';
		}
		echo '</ul>';
	}
	
	public function unassigned($project = '', $category = '')
	{
		$tickets = $this->db->fetchall("
			SELECT t.*, p.title as project_title 
			FROM hq_tickets t LEFT JOIN hq_projects p ON p.id = t.project 
			WHERE t.assigned = '' AND t.status = 'open'  
			ORDER BY p.title, t.id
		");
			
		echo '<h3>Unassigned ('.count($tickets).')</h3>
		<ul>';
		foreach($tickets as $ticket)
		{
			$flag = '';
			if($ticket['flagged'] != 'none')
				$flag = ' class="flagged_'.$ticket['flagged'].'"';
				
			echo '<li'.$flag.'>
				<a href="%appurl%'.$ticket['project'].'">'.$ticket['project_title'].'</a> / 
				<a href="%appurl%'.$ticket['project'].'/tickets/view/'.$ticket['id'].'/">'.htmlentities($ticket['title']).'</a>
			</li>';
		}
		echo '</ul>';
	
	}
	
	public function auditlog($vars = array())
	{
		if($vars == array())
		{
			$log = $this->db->fetchall("SELECT * FROM hq_auditlog ORDER BY date DESC LIMIT 30");
			echo '<div id="global_audit_log"><h3><a href="%appurl%auditlog/">Audit Log</a></h3>';
		}
		else if($vars[0] == 'auditlog')
		{
			if(isset($vars[1])) $start = intval($vars[1]);
			$length = 41;
			
			$start = $start*$length;
			
			$log = $this->db->fetchall("
				SELECT * FROM hq_auditlog ORDER BY date DESC
				LIMIT ".$start.", ".$length."");
				
			// paginate
			$prev = '';
			$next = '';
			if($start > 0)
				$prev = '<a href="%appurl%auditlog/'.(($start/$length) - 1).'">Prev</a>';
				
			if(count($log) > 40)
			{
				$next = '<a href="%appurl%auditlog/'.($start/$length + 1).'">Next</a>';
				$log = array_slice($log, 0, -1);
			}
			
			echo '<div id="view_audit_log"><h2 class="header"><a href="%appurl%">HQ</a> / <a href="%appurl%auditlog/">Audit Log</a></h2>';
			echo $prev.' '.$next;
			
		} else return;
		
		
		echo '<ul>';
		$audits = '';
		foreach($log as $entry)
		{
			$audits .= '<li>
				<span class="audit_timestamp">'.date('F j g:ia', strtotime($entry['date'])).'</span>
				<div class="audit_note">
					%user:'.$entry['user'].'% 
					'.$entry['action'].' 
					<a href="%appurl%'.$entry['inst'].'">'.$entry['inst'].'</a>
					<span class="audit_preview">'.htmlentities($entry['preview']).'</span>
				</div>
			</li>';
		}
		
		// tickets
		if(preg_match_all('/\d+\/tickets\/view\/(\d+)/', $audits, $match))
		{
			$ids = array_unique($match[1]);
			
			$tickets = $this->db->fetchall('SELECT id, title, project FROM hq_tickets WHERE id IN ('.implode(',', $ids).')');
			foreach($tickets as $ticket)
			{
				$projectlist[$ticket['project']] = 1;
				$audits = str_replace($ticket['project'].'/tickets/view/'.$ticket['id'].'<', '%project:'.$ticket['project'].'% / '.htmlentities($ticket['title']).' ('.$ticket['id'].')<', $audits);
			}
			$projectlist = array_keys($projectlist);
			
			$projects = $this->db->fetchall('SELECT id, title FROM hq_projects WHERE id IN ('.implode(',', $projectlist).')');
			foreach($projects as $project)
				$audits = str_replace('%project:'.$project['id'].'%', $project['title'], $audits);
				
		}
		
		echo $audits;
		echo '</ul></div>';
	}
	
	public function search($vars)
	{
		$search = mysql_real_escape_string($_GET['search']);
		
		$comments = $this->db->fetchall("SELECT * FROM lf_comments WHERE inst LIKE 'hq/%' AND content LIKE '%".$search."%'");
		
		$ticket_ids = array();
		foreach($comments as $comment)
		{
			$inst = explode('/', $comment['inst']);
			if($inst[2] != 'reference')
				$ticket_ids[] = $inst[2];
		}
		$ticket_ids = array_unique($ticket_ids);
		
		$result = $this->db->fetchall("SELECT * FROM hq_tickets WHERE title LIKE '%".$search."%' OR content LIKE '%".$search."%' OR id IN (".implode(', ', $ticket_ids).") ORDER BY date DESC");
		
		echo "<h3>Tickets containing '".$search."':</h3>";
		if($result) 
			foreach($result as $ticket)
				echo '
					<a href="%appurl%'.$ticket['project'].'">%project:'.$ticket['project'].'%</a> / 
					<a href="%appurl%'.$ticket['project'].'/tickets/cat/'.$ticket['category'].'">%category:'.$ticket['category'].'%</a> / 
					<a href="%appurl%'.$ticket['project'].'/tickets/view/'.$ticket['id'].'">'.$ticket['title'].'</a><br />';
		else
			echo 'No results found';
	}
	
	// Home page for projects. Shows Wiki and agenda list
	protected function home($vars)
	{
		$project = $this->db->fetch("SELECT * FROM hq_projects WHERE id = ".intval($this->inst));
		
		if(!$project) return 'No project found';
		
		$wiki = $project['wiki'];
		$project = $project['title'];
		
		$nav = array('tickets', 'reference', 'calendar');
		$nav_html = '';
		foreach($nav as $item)
		{
			
			$active = (isset($vars[0]) && $item == $vars[0]) ? ' class="active"' : '';
			$nav_html .= '<a'.$active.' href="'.$this->instbase.$item.'/">'.ucfirst($item).'</a>';
		}
		
		$agenda = $this->lf->extmvc($this->inst.'/calendar', 'hq/calendar', $this->inst, array('agenda'));
			
		include 'view/home.php';
	}
	
	protected function editwiki()
	{
		$project = $this->db->fetch('SELECT * FROM hq_projects WHERE id = '.intval($this->inst));
		
		readfile(ROOT.'system/lib/tinymce/js.html');
		
		echo '
			<form id="editwiki_form" action="%appurl%'.$this->inst.'/updatewiki" method="post">
				<input type="submit" value="Update Wiki" />
				<input type="text" name="title" value="'.$project['title'].'" />
				<textarea name="wiki" id="wiki" cols="30" rows="10">'.$project['wiki'].'</textarea>
			</form>
			<div id="project_delete">Delete Project: [<a onclick="return confirm(\'Do you really want to delete this project? ALL PROJECT DATA WILL BE ERASED FOREVER.\');"  href="%appurl%rm/'.$this->inst.'/">x</a>]</div>
		';
	}
	
	protected function updatewiki()
	{
		$this->db->query("UPDATE hq_projects SET 
			title = '".mysql_real_escape_string($_POST['title'])."',  
			wiki = '".mysql_real_escape_string($_POST['wiki'])."' 
		WHERE id = ".intval($this->inst));
		
		redirect302($this->lf->appurl.$this->inst);
	}
	
	public function tickets($vars)
	{
		$vars = array_slice($vars, 1);
		
		
		if(!isset($this->inst))
			echo $this->lf->extmvc('tickets', 'hq/tickets', '', $vars);
		else
		{
			echo $this->lf->extmvc($this->inst.'/tickets', 'hq/tickets', $this->inst, $vars);
		}
	}
	
	public function reference($vars)
	{
		
		
		if(!isset($this->inst))
			echo $this->lf->extmvc('reference', 'hq/reference', '', $vars);
		else
			echo $this->lf->extmvc($this->inst.'/reference', 'hq/reference', $this->inst, $vars);
	}
	
	public function calendar($vars)
	{
		$vars = array_slice($vars, 1);
		
		if(!isset($this->inst))
			echo $this->lf->extmvc('calendar', 'hq/calendar', '', $vars);
		else
			echo $this->lf->extmvc($this->inst.'/calendar', 'hq/calendar', $this->inst, $vars);
	}
	
	public function addproject($vars)
	{
		if(count($_POST) > 0)
		{
			$result = $this->db->query("
				INSERT INTO hq_projects (`id`, `title`, `wiki`)
				VALUES ( NULL, '".mysql_real_escape_string($_POST['project'])."', 'New Project' )
			");
			
			$id = $this->db->last();
			
			redirect302($this->lf->appurl.$id);
		}
		
		redirect302();
	}
	
	public function rm($vars)
	{
		if(isset($vars[2]) && $vars[2] == 'confirm' && count($_POST))
		{
			// add referer check
			
			$pid = intval($vars[1]);
			$this->db->query('DELETE FROM hq_projects WHERE id = '.$pid);
			$this->db->query('DELETE FROM hq_categories WHERE project = '.$pid);
			$this->db->query('DELETE FROM hq_tickets WHERE project = '.$pid);
			$this->db->query('DELETE FROM hq_reference WHERE project = '.$pid);
			$this->db->query('DELETE FROM hq_events WHERE project = '.$pid);
			
			redirect302($this->lf->appurl);
		}
		
		echo '<form method="post" action="%appurl%rm/'.$vars[1].'/confirm"><input type="submit" value="Confirm deletion of project" /><input type="hidden" value="true" name="confirm" /></form>';
	}
}
