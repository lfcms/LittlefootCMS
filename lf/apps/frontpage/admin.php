<?php 

$msg = '';

if( isset($var[0], $var[1]) && $var[0] == 'edit' )
{
	$success = preg_match('/[0-9]+/', $var[1], $match);
	if(!$success) exit();
	
	if(count($_POST) > 0)
	{	
		$result = $this->db->query("
			UPDATE lf_frontpage SET 
				title 	= '".htmlspecialchars($_POST['title'], ENT_QUOTES)."', 
				content = '".mysql_real_escape_string($_POST['content'])."' 
			WHERE id = ".$match[0]
		);
		$msg = 'Saved.';
	}
	// else { didnt post }
	
	$result = $this->db->query("SELECT * FROM lf_frontpage WHERE id = ".$match[0]);
	$row = mysql_fetch_assoc($result);
	$html = '
		<form action="%appurl%edit/'.$row['id'].'/" method="post">
			<input type="submit" value="Save" /> '.$msg.'
			<br /><br />
			<input style="font-size: 22px; padding: 5px; width:100%" name="title" value="'.htmlspecialchars($row['title'], ENT_QUOTES).'" />
			<br /><br />
			<textarea  rows="50" name="content">'.htmlspecialchars($row['content'], ENT_QUOTES).'</textarea>
			<br />
			<input type="submit" value="Save" /> '.$msg.'
		</form>
	';
} 
else if( isset($var[0]) && $var[0] == 'rm' )
{
	$result = $this->db->query("DELETE FROM lf_frontpage WHERE id = ".intval($var[1]));
	
	header('HTTP/1.1 302 Moved Temporarily');
	header('Location: '. $_SERVER['HTTP_REFERER']);
	exit();
} 
else if( isset($var[0]) && $var[0] == 'new' )
{
	if(count($_POST) > 0)
	{	
		$result = $this->db->query("
			INSERT INTO 
				lf_frontpage 
				(`id`, `title`, `content`)
			VALUES 
				(NULL, '".mysql_real_escape_string($_POST['title'])."', '".mysql_real_escape_string($_POST['content'])."' )
		");
		$msg = 'Page Created.';
	} 
	// else { didnt post }
	$html = '
		<form action="%appurl%new/" method="post">
			<input type="submit" value="Submit" /> '.$msg.'
			<br /><br />
			<input style="font-size: 22px; padding: 5px; width:100%" name="title" />
			<br /><br />
			<textarea name="content" rows="50" style="background: #333"></textarea>
			<br />
			<input type="submit" value="Submit" /> '.$msg.'
		</form>
	';
} 
else 
{
	
	// No article selected
	//$html .= '<h4><a href="%appurl%">Pages</a> / Manage</h4>';
	$html = '<h3>(<a href="%appurl%new/">Create New Page</a>)</h3>';
	$html .= '<p>Select an article below to edit it.</p>';
	$html .= '<ol>';
	
	$result = $this->db->query('SELECT id, title FROM lf_frontpage ORDER BY id');
	while($row = mysql_fetch_assoc($result))
	{
		$html .= '<li>[<a href="%appurl%rm/'.$row['id'].'/">x</a>] <a href="%appurl%edit/'.$row['id'].'/">'.$row['title'].'</a></li>';
	}
		
	$html .= '</ol>';
}

echo $html;

?>