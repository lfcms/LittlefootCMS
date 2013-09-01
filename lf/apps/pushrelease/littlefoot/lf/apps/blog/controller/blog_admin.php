<?php

class blog_admin
{
	private $request;
	private $html;
	private $pwd;
	private $dbconn;
	
	public function __construct($request, $dbconn, $ini = '')
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->pwd = $request->absbase.'/apps';
		$this->ini = $ini;
		
		
		if(is_dir($this->request->absbase.'system/lib/tinymce/'))
			readfile('js.html');
		else
			echo 'No "TinyMCE" package found at '.$this->request->absbase.'system/lib/tinymce/';
	}
	
	//default
	public function main($vars)
	{
		// No article selected
		?>
		<h3>[<a href="%baseurl%apps/manage/blog/newpost/">Post New Article</a>]</h3>
		<p>Select an article below to edit it.</p>
		<ol>
		<?php
		$result = $this->db->query('SELECT id, title FROM io_threads ORDER BY id');
		while($row = mysql_fetch_assoc($result))
		{
			echo '<li>[<a href="%baseurl%apps/manage/blog/rm/'.$row['id'].'/">x</a>] <a href="%baseurl%apps/manage/blog/edit/'.$row['id'].'/">'.$row['title'].'</a></li>';
		}
			
		?></ol><?php
	}
	
	public function rm($var)
	{
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		
		$success = preg_match('/[0-9]+/', $var[1], $match);
		if(!$success) exit();

		// Remove thread and comments
		$result = $this->db->query("DELETE FROM io_threads WHERE id = ".$match[0]);
		$result = $this->db->query("DELETE FROM io_messages WHERE parent_id = ".$match[0]);
		exit();
	}
	
	public function create($var)
	{
		if(count($_POST) > 0)
		{	
			$result = $this->db->query("
				INSERT INTO io_threads (`id`, `title`, `content`, `owner_id`, `likes`, `date`)
				VALUES (
					NULL, 
					'".htmlspecialchars($_POST['title'], ENT_QUOTES)."', 
					'".mysql_real_escape_string($_POST['content'])."', 
					".$this->request->api('getuid').", 
					0,
					NOW() 
				)
			");
			$msg = 'Page Created.';
		}
		$var = array();
		$_POST = array();
		
		$this->main($var);
	}
	
	public function edit($vars)
	{
		$success = preg_match('/[0-9]+/', $vars[1], $match);
		if(!$success) exit();
		
		$msg = '';
		if(count($_POST) > 0)
		{	
			$result = $this->db->query("
				UPDATE io_threads 
				SET 
					title 	= '".htmlspecialchars(mysql_real_escape_string($_POST['title']), ENT_QUOTES)."', 
					content = '".mysql_real_escape_string($_POST['content'])."' 
				WHERE id = ".$match[0]
			);
			$msg = 'Saved.';
		}
		
		$result = $this->db->query("SELECT * FROM io_threads WHERE id = ".$match[0]);
		$row = $this->db->fetch($result);
		?>
			<form action="%baseurl%apps/manage/blog/edit/<?=$row['id'];?>/" method="post">
				<input type="submit" value="Save" /> <?=$msg;?>
				<br /><br />
				<input style="font-size: 22px; padding: 5px; width:100%" name="title" value="<?=htmlspecialchars($row['title'], ENT_QUOTES);?>" />
				<br /><br />
				<textarea name="content"><?=htmlspecialchars($row['content'], ENT_QUOTES);?></textarea>
				<br />
				<input type="submit" value="Save" /> <?=$msg;?>
			</form>
		<?php
	}
	
	public function newpost($vars)
	{
		?>
		<style type="text/css">
			.add_thread .title { margin-bottom: 10px; padding: 5px; width: 100%; font-size:20px; }
		</style>
		<form action="%appurl%manage/blog/create/" method="post" class="add_thread">
			<input type="submit" class="submit" value="Post" />
			<input type="text" name="title" value="New Title" class="title" />
			<textarea name="content"></textarea>
			<input type="hidden" name="access" value="public" />
		</form>
		<?php
	}
}

?>