<?php

class tickets extends app
{
	public function main($vars, $category = '')
	{
		$status = 'open';
		if(isset($vars[0]) && $vars[0] != '') $status = $vars[0];
		
		//$categories = $this->db->fetchall("SELECT DISTINCT category FROM hq_tickets WHERE project = '".intval($this->ini)."'");
		$categories = $this->db->fetchall("SELECT id, category FROM hq_categories WHERE project = '".intval($this->ini)."' AND type = 'ticket'");
		
		$where = '';
		if($category != '') $where = " AND t.category = '".intval($category)."'";
		
		$project = '';
		if($this->ini != '')
			$project = "t.project = ".intval($this->ini).' AND ';
			
		$tickets = $this->db->fetchall("
			SELECT t.*
			FROM hq_tickets t
			WHERE ".$project."
				t.status = '".mysql_real_escape_string($status)."'	
				".$where."
			");
		
		$ticket_count = count($tickets);
		
		// hq/project/postid
		foreach($tickets as $post)
			$inst[] = 'hq/'.$post['project'].'/'.$post['id'];
		
		$where = '';
		if(isset($inst))
			$where = "WHERE c.inst IN ('".implode("','", $inst)."')";
			
		$comments = $this->db->fetchall("
			SELECT DISTINCT c.inst, c.from, c.date
			FROM lf_comments c
			".$where."
			ORDER BY c.date ASC
		");
		
		$lastreply = array();
		foreach($comments as $comment)
			$lastreply[$comment['inst']] = $comment;
			
		$posts = array();
		foreach($tickets as $post)
		{
			// last person to comment on each thread
			if(!isset($lastreply['hq/'.$post['project'].'/'.$post['id']]))
			{
				$date = $post['date'];
				$last = '%user:'.$post['owner_id'].'%';
			}
			else
			{
				$date = $lastreply['hq/'.$post['project'].'/'.$post['id']]['date'];
				$last = '%user:'.$lastreply['hq/'.$post['project'].'/'.$post['id']]['from'].'%';
			}
			
			$post['last'] = $last;
			$posts[$date] = $post;
		}
		
		if($status == 'closed')
			krsort($posts); // sort by date key reverse
		else
			ksort($posts); // sort by date key
	
		include 'view/tickets.main.php';
	}
	
	public function cat($vars)
	{
		$cat = $vars[1];
		$vars = array_slice($vars, 2);
		$this->main($vars, $cat);
	}
	
	public function view($vars)
	{
		if(count($_POST))
		{
			$inst = $this->ini.'/tickets/view/'.intval($vars[1]);
			
			$this->db->query("INSERT INTO hq_auditlog (id, inst, user, action, date, preview)
				VALUES (NULL, '".$inst."', ".$this->lf->api('getuid').", 'updated ticket', NOW()), '".mysql_real_escape_string(substr($_POST['content']), 0, 40)."'");
				
			if($_POST['newcat'] != '') 
			{
				$cat = $this->db->fetch("SELECT * FROM hq_categories WHERE project = ".$this->ini." AND category = '".mysql_real_escape_string($_POST['newcat']).'"');
				if(!$cat)
				{
					$this->db->query("
						INSERT INTO hq_categories (`id`, `project`, `type`, `category`)
						VALUES (NULL, '".$this->ini."', 'ticket', '".mysql_real_escape_string($_POST['newcat'])."')
					");
					$id = $this->db->last();
					$_POST['category'] = $id;	
				} else $_POST['category'] = $cat['id'];
			}
		
			$filter = array('open', 'closed', 'backburner');
			$flags = array('none', 'urgent');
			if(in_array($_POST['status'], $filter) 
				&& in_array($_POST['flagged'], $flags) 
				&& $_POST['content'] != '' 
				&& $_POST['title'] != '' 
			)
			{
				$this->db->query("UPDATE hq_tickets SET 
					status = '".mysql_real_escape_string($_POST['status'])."', 
					content = '".mysql_real_escape_string($_POST['content'])."',
					title = '".mysql_real_escape_string($_POST['title'])."',
					flagged = '".mysql_real_escape_string($_POST['flagged'])."',
					assigned = ".intval($_POST['assigned']).",
					category = '".intval($_POST['category'])."',
					project = '".intval($_POST['project'])."'
				WHERE id = ".intval($vars[1]));
			}
			
			redirect302($this->lf->appurl.intval($_POST['project']).'/tickets/view/'.intval($vars[1].'/'));
		}
		
		$post = $this->db->fetch("
			SELECT * FROM hq_tickets
			WHERE project = ".intval($this->ini)."
				AND id = ".intval($vars[1])
		);
		
		//$categories = $this->db->fetchall("SELECT DISTINCT category FROM hq_tickets WHERE project = ".intval($this->ini));
		$categories = $this->db->fetchall("SELECT id, category FROM hq_categories WHERE project = '".intval($this->ini)."' AND type = 'ticket'");
	
		$this->comment_id = 'hq/'.$this->ini.'/'.intval($vars[1]); // should be /tickets/, but that would make me lose all my stuff :C
		//$comments = $this->lf->extmvc('comment', 'comments/comments', $this->comment_id);
		
		ob_start();
		include 'view/tickets.view.php';
		$data = ob_get_clean(); 
		
		if(is_file('../like/controller/like.php')) {
			include '../like/controller/like.php';
			$like = new like($this->request, $this->db);
			$data =  $like->parse($data);
		}
		
		if(is_file('../subscribe/controller/subscribe.php')) {
			include '../subscribe/controller/subscribe.php';
			$subscribe = new subscribe($this->request, $this->db);
			$data = $subscribe->parse($data);
		}
		
		echo $data;
	}
	
	public function comment($vars = array(''))
	{
		$vars = array_slice($vars, 1);
		if(!isset($this->comment_id)) 
		{
			$this->comment_id = $_POST['inst'];
			$inst = $this->comment_id;
			
			$id = explode('/', $inst);
			$id = $id[count($id)-1];
			
			// audit log
			$this->db->query("INSERT INTO hq_auditlog (id, inst, user, action, date, preview)
				VALUES (NULL, '".$this->ini."/tickets/view/".$id."', ".$this->lf->api('getuid').", 'commented on', NOW(), '".mysql_real_escape_string(substr($_POST['msg'], 0, 120))."')");
				
			$this->db->query('UPDATE hq_tickets SET replies = replies + 1 WHERE id = '.intval($id)); // update reply count
			
			// subscribe system
			if(is_file('../subscribe/controller/subscribe.php')) {
				include '../subscribe/controller/subscribe.php';
				$subscribe = new subscribe($this->request, $this->db);
				$subscribe->notify('ticket/'.$id, $this->lf->api('me').' commented on '.$this->lf->appurl.$this->ini.'/tickets/view/'.$id);
			}
		}
		
		//preg_match('/^hq\/(\d+)\/(\d+)$/', $_POST['inst'], $match);
		//$inst = $match[1].'/tickets/view/'.$match[2];
		
		if(isset($vars[1]) && $vars[0] == 'rm') // this should fix the audit log when a comment is removed
		{
			preg_match('/tickets\/view\/(\d+)/', $_SERVER['HTTP_REFERER'], $ticket);
			$ticket = $ticket[1];
			
			$this->db->query("
				UPDATE hq_auditlog 
				SET inst = '[deleted]', preview = '', action = 'deleted a comment from %project:".$this->ini."%' 
				WHERE inst = '".$this->ini.'/tickets/view/'.intval($ticket)."'");
				
			$this->db->query('UPDATE hq_tickets SET replies = replies - 1 WHERE id = '.intval($ticket)); // update reply count
			
		}
		
		echo $this->lf->extmvc('comment', 'comments/comments', $this->comment_id, $vars);
	}
	
	public function newarticle($vars)
	{
		// else { didnt post }
		//$inst_base = $this->instbase;
		
		$cats = $this->db->fetchall("SELECT id, category FROM hq_categories WHERE project = '".intval($this->ini)."' AND type = 'ticket'");
		
		$cat_options = '<option value="0">Uncategorized</option>';
		foreach($cats as $cat)
		{
			$select = '';
			if(isset($vars[1]) && $vars[1] == $cat['id']) $select = ' selected="selected"';
			
			$cat_options .= '<option'.$select.' value="'.$cat['id'].'">'.$cat['category'].'</option>';
		}
		
		// User assigned
		$users = $this->db->fetchall('SELECT id, display_name FROM lf_users');
		$user_options = '<option value="0">None</option>';
		foreach($users as $user)
		{
			/*$selected = '';
			if($user['id'] == $this->lf->api('getuid')) // set as self by default
				$selected = ' selected="selected"';*/
			
			$user_options .= '<option'.$selected.' value="'.$user['id'].'">'.$user['display_name'].'</option>';
		}
		
		include 'view/ticket.newarticle.php';
	}
	
	public function create($vars)
	{
		if(count($_POST) > 0)
		{
			if($_POST['newcat'] != '') 
			{
				$cat = $this->db->fetch("SELECT * FROM hq_categories WHERE project = ".$this->ini." AND category = '".mysql_real_escape_string($_POST['newcat']).'"');
				if(!$cat)
				{
					$this->db->query("
						INSERT INTO hq_categories (`id`, `project`, `type`, `category`)
						VALUES (NULL, '".$this->ini."', 'ticket', '".mysql_real_escape_string($_POST['newcat'])."')
					");
					$id = $this->db->last();
					$_POST['category'] = $id;	
				} else $_POST['category'] = $cat['id'];
			}
			
			$flags = array('none', 'urgent');
			if(!in_array($_POST['flagged'], $flags))
				$_POST['flagged'] = 'none';
				
			$statuses = array('open', 'closed', 'backburner');
			if(!in_array($_POST['status'], $statuses))
				$_POST['status'] = 'open';
			
			$result = $this->db->query("
				INSERT INTO hq_tickets (`id`, `project`, `category`, `title`, `content`, `owner_id`, `date`, `status`, `assigned`, `flagged`)
				VALUES (
					NULL, 
					'".intval($this->ini)."', 
					'".intval($_POST['category'])."',
					'".mysql_real_escape_string($_POST['title'])."', 
					'".mysql_real_escape_string($_POST['content'])."', 
					".$this->request->api('getuid').", 
					NOW(),
					'".mysql_real_escape_string($_POST['status'])."',
					".intval($_POST['assigned']).",
					'".mysql_real_escape_string($_POST['flagged'])."'
				)
			");
			
		}
		
		$id = $this->db->last();
		$inst = $this->ini.'/tickets/view/'.$id;
		
		$this->db->query("INSERT INTO hq_auditlog (id, inst, user, action, date, preview)
			VALUES (NULL, '".$inst."', ".$this->lf->api('getuid').", 'created a new ticket', NOW(), '".mysql_real_escape_string(substr($_POST['content'], 0, 120))."')");
			
		redirect302($this->lf->appurl.$inst);
	}
	
	public function rm($vars)
	{
		$id = intval($vars[1]);
		if($id <= 0) redirect302();

		// Remove thread and comments
		$this->db->query("DELETE FROM hq_tickets WHERE project = '".$this->ini."' AND id = ".$id);
		
		redirect302($this->lf->appurl.$this->ini.'/tickets/');
	}
	
	public function rmcat($vars)
	{
		$this->db->query('DELETE FROM hq_categories WHERE id = '.intval($vars[1]));
		$this->db->query('UPDATE hq_tickets SET category = 0 WHERE project = '.$this->ini.' AND category = '.intval($vars[1]));
		$this->db->query('UPDATE FROM hq_reference SET category = 0  WHERE project = '.$this->ini.' AND category = '.intval($vars[1]));
		
		// if($purge all content)
		//$this->db->query('DELETE FROM hq_tickets WHERE project = '.$this->ini.' AND category = '.intval($vars[1]));
		//$this->db->query('DELETE FROM hq_reference WHERE project = '.$this->ini.' AND category = '.intval($vars[1]));
		
		redirect302($this->lf->appurl.$this->ini.'/tickets/');
		//$this->main();
		//redirect302();
		/*
		if(isset($vars[1]))
			$result = $this->db->query("
				 hq_tickets (`id`, `instance`, `category`, `title`, `content`, `owner_id`, `likes`, `date`)
				VALUES (
					NULL, '".mysql_real_escape_string($_POST['instance'])."', 'uncategorized',
					'New Article', 
					'New Content',
					".$this->request->api('getuid').",
					0,
					NOW() 
				)
			");
		
		redirect302();*/
	}
	
	public function addcategory($vars)
	{
		if(count($_POST) > 0)
		{
			
			$cat = $this->db->fetch("SELECT * FROM hq_categories WHERE project = ".$this->ini." AND type = 'ticket' AND category = '".mysql_real_escape_string($_POST['category'])."'");
			
			if(!$cat)
			{
				$this->db->query("
					INSERT INTO hq_categories (`id`, `project`, `type`, `category`)
					VALUES (NULL, '".$this->ini."', 'ticket', '".mysql_real_escape_string($_POST['category'])."')
				");
				
				$id = $this->db->last();
			}
		}
		
		redirect302($this->lf->appurl.$this->ini.'/tickets/cat/'.$id);
	}
	
	public function like($vars = array(''))
	{
		// $vars[1] == unlike, auditlog
		
		$vars = array_slice($vars, 1);
		//if(!isset($this->comment_id)) $this->comment_id = $_POST['inst'];
		echo $this->lf->extmvc('like', 'like/like', 'likeinst'/*$this->comment_id*/, $vars);
	}
	
	public function subscribe($vars = array(''))
	{
		// $vars[1] == unlike, auditlog
		
		$vars = array_slice($vars, 1);
		//if(!isset($this->comment_id)) $this->comment_id = $_POST['inst'];
		echo $this->lf->extmvc('subscribe', 'subscribe/subscribe', NULL, $vars);
	}
	
	public function editcat($vars)
	{	
		if(count($_POST))
		{
			echo 'Update has not been implemented yet.';		
		}	
		
		$projects = $this->db->fetchall('SELECT id, title FROM hq_projects');
		$cat = $this->db->fetch('SELECT * FROM hq_categories WHERE id = '.intval($vars[1]));
		
		$project_options = '';		
		foreach($projects as $project)
			$project_options .= '<option value="'.$project['id'].'">'.$project['title'].'</option>';
		
		$project_options = str_replace('value="'.$cat['project'].'"', 'value="'.$cat['project'].'" selected="selected"', $project_options);

					
		echo '<h3>Edit Category</h3>';
		echo '<form action="?" method="post">
			<ul>
				<li>Project: <select name="project">'.$project_options.'</select></li>
				<li>Category Name: <input type="text" name="category" value="'.$cat['category'].'" /></li>
				<li><input type="submit" value="Update" /></li>
			</ul>	
		</form>			
		';
			
		echo '<br /><br /><br />[<a onclick="return confirm(\'Are you sure?\');" href="%appurl%rmcat/'.intval($vars[1]).'/">Delete Category</a>]';
	}
}

?>