<?php

class reference extends app
{
	protected function init($vars)
	{
		echo '
			<style type="text/css">
				.app-hq #projects, .app-hq #categories { float: left; display: block; width: 175px; }
				.app-hq #note { margin-left: 185px; }
				.app-hq h4 { margin-top: 10px;}
			</style>';
	}
	
	public function main($vars)
	{
		$status = 'open';
		if(isset($vars[0]) && $vars[0] != '') $status = $vars[0];
		
		
		$categories = $this->db->fetchall("SELECT DISTINCT category FROM hq_reference WHERE project = '".intval($this->ini)."'");
		
		$category = '';
		
		$posts = $this->db->fetchall("
			SELECT * FROM hq_reference
			WHERE project = ".intval($this->ini)." 
				AND status = '".mysql_real_escape_string($status)."'
			ORDER BY category, id DESC");
			
		include 'view/reference.main.php';
	}
	
	public function cat($vars)
	{
		$status = 'open';
		if(isset($vars[2])) $status = $vars[2];
		
		$categories = $this->db->fetchall("SELECT DISTINCT category FROM hq_reference WHERE project = '".intval($this->ini)."'");
		
		$category = '';
		if($vars[1] != '') $category = urldecode($vars[1]);
		
		$where = '';
		if($category != '') $where = " AND category = '".mysql_real_escape_string($category)."'";
		
		$posts = $this->db->fetchall("
			SELECT * FROM hq_reference
			WHERE project = ".intval($this->ini)." 
				AND status = '".mysql_real_escape_string($status)."'
				".$where."
			ORDER BY category, id DESC");
			
		//include 'view/reference.cat.php';
			
		include 'view/reference.main.php';
	}
	
	public function view($vars)
	{
		if(count($_POST))
		{
			if($_POST['newcat'] != '') $_POST['category'] = $_POST['newcat'];
		
			$filter = array('open', 'archive');
			if(in_array($_POST['status'], $filter) && $_POST['content'] != '' && $_POST['title'] != '' && $_POST['category'] != '')
				$this->db->query("UPDATE hq_reference SET 
					status = '".mysql_real_escape_string($_POST['status'])."', 
					content = '".mysql_real_escape_string($_POST['content'])."',
					title = '".mysql_real_escape_string($_POST['title'])."',
					category = '".mysql_real_escape_string($_POST['category'])."'
				WHERE id = ".intval($vars[1]));
		}
		
		$post = $this->db->fetch("
			SELECT b.*, u.user as user FROM hq_reference b 
			LEFT JOIN lf_users u ON b.owner_id = u.id 
			WHERE b.project = ".intval($this->ini)."
				AND b.id = ".intval($vars[1])
		);
		
		$categories = $this->db->fetchall("SELECT DISTINCT category FROM hq_reference WHERE project = ".intval($this->ini));
	
		$this->comment_id = 'hq/'.$this->ini.'/reference/'.intval($vars[1]);
		//$comments = $this->lf->extmvc('comment', 'comments/comments', $this->comment_id);
		
		include 'view/reference.view.php';
	}
	
	public function comment($vars = array(''))
	{
		$vars = array_slice($vars, 1);
		if(!isset($this->comment_id)) $this->comment_id = $_POST['inst'];
		echo $this->lf->extmvc('comment', 'comments/comments', $this->comment_id, $vars);
	}
	/*
	public function edit($vars)
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
				UPDATE hq_reference 
				SET 
					title 	= '".mysql_real_escape_string($_POST['title'])."', 
					content = '".mysql_real_escape_string($_POST['content'])."',
					category = '".mysql_real_escape_string($_POST['category'])."'
				WHERE id = ".$id
			);
			$msg = 'Saved.';
			redirect302();
		}
		
		$row = $this->db->fetch("SELECT * FROM hq_reference WHERE id = ".$id);
		
		$cats = $this->db->fetchall("SELECT DISTINCT category FROM hq_reference WHERE project = '".mysql_real_escape_string($inst)."'");
		
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
	}*/
	
	
	public function create($vars)
	{
		if(count($_POST) > 0)
		{
			if($_POST['newcat'] != '') $_POST['category'] = $_POST['newcat'];
			
			$result = $this->db->query("
				INSERT INTO hq_reference (`id`, `project`, `category`, `title`, `content`, `owner_id`, `date`, `status`)
				VALUES (
					NULL, 
					'".mysql_real_escape_string($this->ini)."', 
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
		
		redirect302($this->lf->appurl.$this->ini.'/reference/view/'.$id.'/');
	}
	
	public function newarticle($vars)
	{
		// else { didnt post }
		//$inst_base = $this->instbase;
		
		$cats = $this->db->fetchall("SELECT DISTINCT category FROM hq_reference WHERE project = ".intval($this->ini));
		
		$cat_options = '';
		foreach($cats as $cat)
		{
			$select = '';
			if(isset($vars[1]) && urldecode($vars[1]) == $cat['category']) $select = ' selected="selected"';
			
			$cat_options .= '<option'.$select.' value="'.$cat['category'].'">'.$cat['category'].'</option>';
		}
		
		include 'view/ticket.newarticle.php';
	}
	
	public function rm($vars)
	{
		$id = intval($vars[1]);
		if($id <= 0) redirect302();

		// Remove thread and comments
		$this->db->query("DELETE FROM hq_reference WHERE project = '".$this->ini."' AND id = ".$id);
		
		redirect302();
	}
	
	public function rminst($vars)
	{
		echo 'Instance delete isnt implemented yet. Do remove an instance, delete all posts inside it.';
		//$this->main();
		//redirect302();
		/*
		if(isset($vars[1]))
			$result = $this->db->query("
				 hq_reference (`id`, `instance`, `category`, `title`, `content`, `owner_id`, `likes`, `date`)
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
			$result = $this->db->query("
				INSERT INTO hq_reference (`id`, `project`, `category`, `title`, `content`, `owner_id`, `date`, `status`)
				VALUES (
					NULL, '".$this->ini."', '".mysql_real_escape_string($_POST['category'])."',
					'New Article', 
					'New Content',
					".$this->request->api('getuid').",
					NOW(),
					'open'
				)
			");
			
			$id = $this->db->last();
			
			redirect302($this->lf->appurl.$this->ini.'/reference/view/'.$id.'/edit');
		}
		
		redirect302();
	}
}

?>