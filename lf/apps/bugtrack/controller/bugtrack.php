<?php

class bugtrack extends app
{
	public $default_method = '_project';
	
	protected function init($vars)
	{
		echo '
		<style type="text/css">
			.app-bugtrack #projects, .app-bugtrack #categories { float: left; display: block; width: 175px; }
			.app-bugtrack #note { margin-left: 185px; }
			.app-bugtrack h2 { margin: 20px 10px; }
			.app-bugtrack h4 { margin-top: 10px;}
		</style>';
	}
	
	public function _project($vars) //router
	{
		if($vars[0] == '') return $this->main($vars);
		
		$inst = urldecode($vars[0]);
		$vars = array_slice($vars, 1);
		$this->inst = $inst;
		$this->instbase = $this->lf->appurl.urlencode($inst).'/';
		
		$function = 'projecthome';
		if(isset($vars[0])) $function = $vars[0];
		
		$this->$function($vars);
	}
	
	public function main($vars)
	{
		$projects = $this->db->fetchall('SELECT DISTINCT project FROM bugtrack ORDER BY project');
		$posts = $this->db->fetchall("SELECT * FROM bugtrack WHERE status = 'open' ORDER BY project ASC, category ASC, id DESC");
		
		$calendar = $this->lf->extmvc('calendar', 'calendar/calendar');
		
		include 'view/bugtrack.main.php';
	}
	
	private function rmproject($vars)
	{
		echo 'Not implemented yet, delete all posts to remove a project';
	}
	
	private function cat($vars)
	{
		$status = 'open';
		if(isset($vars[2]) && $vars[2] == 'closed')
			$status = 'closed';
			
		$this->projecthome($vars, urldecode($vars[1]), $status);
	}
	
	private function closed($vars)
	{
		$this->projecthome($vars, '', 'closed');
	}
	
	private function open($vars)
	{
		$this->projecthome($vars, '', 'open');
	}
	
	private function projecthome($vars, $category = '', $status = 'open')
	{
		$categories = $this->db->fetchall("SELECT DISTINCT category FROM bugtrack WHERE project = '".mysql_real_escape_string($this->inst)."'");
		
		$inst = $this->inst;
		$inst_base = $this->instbase;
		
		$where = '';
		if($category != '') $where = " AND category = '".mysql_real_escape_string($category)."'";
		
		$posts = $this->db->fetchall("
			SELECT * FROM bugtrack 
			WHERE  status = '".mysql_real_escape_string($status)."' 
				AND project = '".mysql_real_escape_string($this->inst)."'".$where." 
			ORDER BY category, id DESC");
		
		include 'view/bugtrack.projecthome.php';
	}
	
	private function view($vars)
	{
		$inst = $this->inst;
		$inst_base = $this->instbase;
	
		if(count($_POST))
		{			
			if($_POST['newcat'] != '') $_POST['category'] = $_POST['newcat'];
		
			$filter = array('open', 'closed');
			if(in_array($_POST['status'], $filter) && $_POST['content'] != '' && $_POST['title'] != '' && $_POST['category'] != '')
				$this->db->query("UPDATE bugtrack SET 
					status = '".mysql_real_escape_string($_POST['status'])."', 
					content = '".mysql_real_escape_string($_POST['content'])."',
					title = '".mysql_real_escape_string($_POST['title'])."',
					category = '".mysql_real_escape_string($_POST['category'])."'
				WHERE id = ".intval($vars[1]));
		}
		
		$post = $this->db->fetch("
			SELECT b.*, u.user as user FROM bugtrack b 
			LEFT JOIN lf_users u ON b.owner_id = u.id 
			WHERE b.project = '".mysql_real_escape_string($inst)."' 
				AND b.id = ".intval($vars[1])
		);
		
		$categories = $this->db->fetchall("SELECT DISTINCT category FROM bugtrack WHERE project = '".mysql_real_escape_string($this->inst)."'");
	
		$this->comment_id = 'bugtrack/'.$this->inst.'/'.intval($vars[1]);
		$comments = $this->lf->extmvc('comment', 'comments/comments', $this->comment_id);
		
		include 'view/bugtrack.view.php';
	}
	
	public function comment($vars = array())
	{
		if(!isset($this->comment_id)) $this->comment_id = $_POST['inst'];
		echo $this->lf->extmvc('comment', 'comments/comments', $this->comment_id, $vars);
	}
	
	private function edit($vars)
	{
		$inst = $this->inst;
		$inst_base = $this->instbase;
		
		$id = intval($vars[1]);
		if($id <= 0) return;
		
		$msg = '';
		if(count($_POST) > 0)
		{
			
			if($_POST['newcat'] != '') $_POST['category'] = $_POST['newcat'];
			
			$result = $this->db->query("
				UPDATE bugtrack 
				SET 
					title 	= '".mysql_real_escape_string($_POST['title'])."', 
					content = '".mysql_real_escape_string($_POST['content'])."',
					category = '".mysql_real_escape_string($_POST['category'])."'
				WHERE id = ".$id
			);
			$msg = 'Saved.';
			redirect302();
		}
		
		$row = $this->db->fetch("SELECT * FROM bugtrack WHERE id = ".$id);
		
		$cats = $this->db->fetchall("SELECT DISTINCT category FROM bugtrack WHERE project = '".mysql_real_escape_string($inst)."'");
		
		$cat_options = '';
		foreach($cats as $cat)
		{
			$selected = $row['category'] == $cat['category'] ? ' selected="selected"' : '';
			$cat_options .= '<option'.$selected.' value="'.$cat['category'].'">'.$cat['category'].'</option>';
		}
		
		?>
		<form action="<?php echo $this->inst_base; ?>edit/<?=$row['id'];?>/" method="post">
			<input type="submit" value="Save" /> <?=$msg;?> [<a href="<?php echo $inst_base; ?>">deselect post</a>]
			<br /><br />
			<input style="font-size: 22px; padding: 5px; width:100%" name="title" value="<?=htmlspecialchars($row['title'], ENT_QUOTES);?>" />
			<br /><br />		
			Category: <select name="category" id=""><?php echo $cat_options; ?></select> or <input type="text" name="newcat" placeholder="New Category" />
			<br /><br />		
			<textarea name="content"><?=htmlspecialchars($row['content'], ENT_QUOTES);?></textarea>
			<br />
			<input type="submit" value="Save" /> <?=$msg;?>
		</form>
		<?php
		
		if(is_file(ROOT.'system/lib/tinymce/js.html'))
			readfile(ROOT.'system/lib/tinymce/js.html');
		else
			echo 'No "TinyMCE" package found at '.ROOT.'system/lib/tinymce/';
	}
	
	
	private function create($vars)
	{
		if(count($_POST) > 0)
		{
			if($_POST['newcat'] != '') $_POST['category'] = $_POST['newcat'];
			
			$result = $this->db->query("
				INSERT INTO bugtrack (`id`, `project`, `category`, `title`, `content`, `owner_id`, `date`, `status`)
				VALUES (
					NULL, 
					'".mysql_real_escape_string($this->inst)."', 
					'".mysql_real_escape_string($_POST['category'])."',
					'".mysql_real_escape_string($_POST['title'])."', 
					'".mysql_real_escape_string($_POST['content'])."', 
					".$this->request->api('getuid').", 
					NOW(),
					'open'
				)
			");
			$msg = 'Page Created.';
		}
		
		$id = $this->db->last();
		
		redirect302($this->instbase.'view/'.$id.'/');
	}
	
	private function newarticle($vars)
	{
		// else { didnt post }
		$inst_base = $this->instbase;
		$inst = $this->inst;
		
		$cats = $this->db->fetchall("SELECT DISTINCT category FROM bugtrack WHERE project = '".mysql_real_escape_string($inst)."'");
		
		$cat_options = '';
		foreach($cats as $cat)
		{
			$select = '';
			if(isset($vars[1]) && urldecode($vars[1]) == $cat['category']) $select = ' selected="selected"';
			
			$cat_options .= '<option'.$select.' value="'.$cat['category'].'">'.$cat['category'].'</option>';
		}
		
		include 'view/bugtrack.newarticle.php';
	}
	
	private function rm($vars)
	{
		$id = intval($vars[1]);
		if($id <= 0) redirect302();

		// Remove thread and comments
		$this->db->query("DELETE FROM bugtrack WHERE project = '".$this->inst."' AND id = ".$id);
		
		redirect302($this->lf->appurl.inst);
	}
	
	public function rminst($vars)
	{
		echo 'Instance delete isnt implemented yet. Do remove an instance, delete all posts inside it.';
		//$this->main();
		//redirect302();
		/*
		if(isset($vars[1]))
			$result = $this->db->query("
				 bugtrack (`id`, `instance`, `category`, `title`, `content`, `owner_id`, `likes`, `date`)
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
	
	public function addproject($vars)
	{
		if(count($_POST) > 0)
		{
			$result = $this->db->query("
				INSERT INTO bugtrack (`id`, `project`, `category`, `title`, `content`, `owner_id`, `date`,`status`)
				VALUES (
					NULL, '".mysql_real_escape_string($_POST['project'])."', 'uncategorized',
					'New Title', 
					'New Content',
					".$this->request->api('getuid').",
					NOW(),
					'open'
				)
			");
			
			$id = $this->db->last();
			
			redirect302($this->lf->appurl.urlencode($_POST['project']).'/view/'.$id.'/edit');
		}
		
		redirect302();
	}
	
	private function addcategory($vars)
	{
		if(count($_POST) > 0)
		{
			$result = $this->db->query("
				INSERT INTO bugtrack (`id`, `project`, `category`, `title`, `content`, `owner_id`, `date`, `status`)
				VALUES (
					NULL, '".$this->inst."', '".mysql_real_escape_string($_POST['category'])."',
					'New Article', 
					'New Content',
					".$this->request->api('getuid').",
					NOW(),
					'open'
				)
			");
			
			$id = $this->db->last();
			
			redirect302($this->lf->appurl.urlencode($this->inst).'/view/'.$id.'/edit');
		}
		
		redirect302();
	}
}

?>
