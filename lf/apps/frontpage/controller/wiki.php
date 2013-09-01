<?php

/*

public functions are the controllers
private functions are the models
view loads at the end

*/

class wiki
{
	private $request;
	private $html;
	private $pwd;
	private $dbconn;
	
	public function __construct($request, $dbconn, $ini)
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->project_id = intval($ini);
	}
	
	public function main($var)
	{
		if(count($_POST) > 0)
		{	
			$result = $this->db->query("
				UPDATE hq_projects 
				SET 
					title 	= '".htmlspecialchars($_POST['title'], ENT_QUOTES)."', 
					wiki = '".mysql_real_escape_string($_POST['content'])."' 
				WHERE id = ".$match[0]
			);
			$msg = 'Saved.';
		}
		
		$this->db->query("SELECT id, title, wiki FROM hq_projects WHERE id = ".$this->project_id);
		$row = $this->db->fetch();
		
		?>
		<fieldset>
			<legend>Edit</legend>
			<style type="text/css">
				fieldset { padding: 10px; width: 98%; margin: 0;}
				#addnote textarea, #addnote input { width: 100%; font-size: 18px;}
			</style>
			<form action="%appurl%update/<?php echo $this->project_id; ?>/" method="post" id="addnote">
				<ul>
					<?php echo '<li>[<a href="%appurl%../">Close</a>]</li>'; ?>
					<li>Project Title<br /><input type="text" name="title"<?php echo ' value="'.$row['title'].'"'; ?> /></li>
					<li>Wiki<br /><textarea name="content" id="" width="100%" rows="10"><?php echo $row['wiki']; ?></textarea></li>
					<li><br /><input type="submit" value="Update Entry" /></li>
				</ul>
			</form>
		</fieldset>
		<?php
		
		if(is_dir(ROOT.'system/lib/tinymce/'))
			readfile(ROOT.'system/lib/tinymce/js.html');
		else
			echo 'No "TinyMCE" package found at '.$this->request->absbase.'system/lib/tinymce/';
	}
	
	public function update($var)
	{
		if(count($_POST) > 0)
		{	
			$result = $this->db->query("
				UPDATE hq_projects 
				SET 
					title 	= '".htmlspecialchars($_POST['title'], ENT_QUOTES)."', 
					wiki = '".mysql_real_escape_string($_POST['content'])."' 
				WHERE id = ".intval($var[1])
			);
		}
			
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		exit();
	}
}

?>