<?php

class blog_admin extends app
{
	public function main($vars)
	{
		$result = $this->db->fetchall('SELECT DISTINCT instance FROM io_threads ORDER BY instance');
	
		echo '<form action="%appurl%addinst/" method="post"><input type="text" name="instance" placeholder="New Instance" /></form>';
		if($result)
		foreach($result as $instance)
		{
			$instance = $instance['instance'];
			echo '[<a href="%appurl%rminst/'.urlencode($instance).'/">x</a>] <a href="%appurl%inst/'.urlencode($instance).'/">'.$instance.'</a><br />';
		}
		/*
		// No article selected
		?>
		<h3>[<a href="%appurl%blog/new/">Post New Article</a>]</h3>
		<p>Select an article below to edit it.</p>
		<ol>
		<?php
		$result = $this->db->query('SELECT id, title FROM io_threads ORDER BY id');
		while($row = mysql_fetch_assoc($result))
		{
			echo '<li>[<a href="%appurl%blog/rm/'.$row['id'].'/">x</a>] <a href="%appurl%edit/'.$row['id'].'/">'.$row['title'].'</a></li>';
		}
		
		?></ol><?php*/
	}
	
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
	}
	
	private function cat($vars)
	{
		$this->view($vars, $vars[1]);
	}
	
	private function view($vars, $category = '')
	{
		$inst = $this->inst;
		$inst_base = $this->inst_base;
		
		$where = '';
		if($category != '') $where = " category = '".$category."' AND ";
		
		$posts = $this->db->fetchall("SELECT id, title, category FROM io_threads WHERE".$where." instance = '".mysql_real_escape_string($inst)."' ORDER BY category, id DESC");
		
		// No article selected
		?>
		<p>Select an article below to edit it or [<a href="<?php echo $inst_base; ?>newarticle/<?php echo $category; ?>">Post New Article</a>]</p>
		<ol>
		<?php
		
		$cat = '';
		foreach($posts as $post)
		{
			if($cat != $post['category'])
			{
				$cat = $post['category'];
				echo '<h4>'.$cat.'</h4>';
			}
			
			echo '<li>[<a onclick="return confirm(\'Do you really want to delete this?\');"  href="'.$inst_base.'rm/'.$post['id'].'/">x</a>] <a href="'.$inst_base.'edit/'.$post['id'].'/">'.$post['title'].'</a></li>';
		}
		?></ol><?php
	}
	
	private function edit($vars)
	{
		$inst = $this->inst;
		$inst_base = $this->inst_base;
		
		$id = intval($vars[1]);
		if($id <= 0) return;
		
		$msg = '';
		if(count($_POST) > 0)
		{
			
			if($_POST['newcat'] != '') $_POST['category'] = $_POST['newcat'];
			
			$result = $this->db->query("
				UPDATE io_threads 
				SET 
					title 	= '".mysql_real_escape_string($_POST['title'])."', 
					content = '".mysql_real_escape_string($_POST['content'])."',
					category = '".mysql_real_escape_string($_POST['category'])."'
				WHERE id = ".$id
			);
			$msg = 'Saved.';
			redirect302();
		}
		
		$result = $this->db->query("SELECT * FROM io_threads WHERE id = ".$id);
		$row = $this->db->fetch($result);
		
		$cats = $this->cats;
		
		$cat_options = '';
		foreach($cats as $cat)
		{
			$selected = $cat == $row['category'] ? ' selected="selected"' : '';
			$cat_options .= '<option'.$selected.' value="'.$cat.'">'.$cat.'</option>';
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
	}
	
	
	private function create($vars)
	{
		if(count($_POST) > 0)
		{
			if($_POST['newcat'] != '') $_POST['category'] = $_POST['newcat'];
			
			$result = $this->db->query("
				INSERT INTO io_threads (`id`, `instance`, `category`, `title`, `content`, `owner_id`, `likes`, `date`)
				VALUES (
					NULL, 
					'".mysql_real_escape_string($this->inst)."', 
					'".mysql_real_escape_string($_POST['category'])."',
					'".mysql_real_escape_string($_POST['title'])."', 
					'".mysql_real_escape_string($_POST['content'])."', 
					".$this->request->api('getuid').", 
					0, NOW() 
				)
			");
			$msg = 'Page Created.';
		}
		
		redirect302($this->inst_base);
	}
	
	private function newarticle($vars)
	{
		// else { didnt post }
		$inst_base = $this->inst_base;
		$inst = $this->inst;
		
		foreach($this->cats as $cat)
		{
			$select = '';
			if(isset($vars[1]) && $vars[1] == $cat) $select = ' selected="selected"';
			
			$cat_options .= '<option'.$select.' value="'.$cat.'">'.$cat.'</option>';
		}
		
		?>
		<style type="text/css">
			.add_thread .title { margin-bottom: 10px; padding: 5px; width: 100%; font-size:20px; }
		</style>
		<form action="<?php echo $inst_base; ?>create/" method="post" class="add_thread">
			<input type="submit" class="submit" value="Post" /><br /><br />
			<input type="text" name="title" value="New Title" class="title" />
			Category: <select name="category" id=""><?php echo $cat_options; ?></select> or <input type="text" name="newcat" placeholder="New Category" /><br /><br />
			<textarea name="content"></textarea>
			<input type="hidden" name="access" value="public" />
		</form>
		<?php
	}
	
	private function rm($vars)
	{
		$id = intval($vars[1]);
		if($id <= 0) return;

		// Remove thread and comments
		$this->db->query("DELETE FROM io_threads WHERE instance = '".$this->inst."' AND id = ".$id);		
		if($this->db->affected() == 1)
			$this->db->query("DELETE FROM io_messages WHERE parent_id = ".$id);
		
		redirect302();
	}
	
	public function addcat()
	{
		if(count($_POST) > 0)
			$result = $this->db->query("
				INSERT INTO io_threads (`id`, `instance`, `category`, `title`, `content`, `owner_id`, `likes`, `date`)
				VALUES (
					NULL, '".$this->inst."', '".mysql_real_escape_string($_POST['category'])."',
					'New Article', 
					'New Content',
					".$this->request->api('getuid').",
					0,
					NOW() 
				)
			");
		
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
				 io_threads (`id`, `instance`, `category`, `title`, `content`, `owner_id`, `likes`, `date`)
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
	
	public function addinst($vars)
	{
		if(count($_POST) > 0)
			$result = $this->db->query("
				INSERT INTO io_threads (`id`, `instance`, `category`, `title`, `content`, `owner_id`, `likes`, `date`)
				VALUES (
					NULL, '".mysql_real_escape_string($_POST['instance'])."', 'uncategorized',
					'New Article', 
					'New Content',
					".$this->request->api('getuid').",
					0,
					NOW() 
				)
			");
		
		redirect302();
	}
}


if(is_file(ROOT.'system/lib/tinymce/js.html'))
	readfile(ROOT.'system/lib/tinymce/js.html');
else
	echo 'No "TinyMCE" package found at '.ROOT.'system/lib/tinymce/';

?>
