<?php 

if($this->request->api('getuid') == 0) exit();

$msg = '';
if( isset($var[0], $var[1]) && $var[0] == 'edit')
{
	$success = preg_match('/[0-9]+/', $var[1], $match);
	if(!$success) exit();
	
	if(count($_POST) > 0)
	{	
		$sql = "
			UPDATE lf_gallery 
			SET 
				title = '".htmlspecialchars(mysql_real_escape_string($_POST['title']), ENT_QUOTES)."', 
				description = '".mysql_real_escape_string($_POST['description'])."'
			WHERE id = ".$match[0];
		$result = $this->db->query($sql);
		$msg = 'Saved.';
	}
	
	$result = $this->db->query("SELECT * FROM lf_gallery WHERE id = ".$match[0]);
	$row = $this->db->fetch($result);
	?>
		<form action="%baseurl%apps/manage/gallery/edit/<?=$row['id'];?>/" method="post">
			<input type="submit" value="Save" /> <?=$msg;?>
			<br /><br />
			<input style="font-size: 22px; padding: 5px; width:100%" name="title" value="<?=htmlspecialchars($row['title'], ENT_QUOTES);?>" />
			<br /><br />
			<?php echo '<img  style="float: left; " width="300px" src="%relbase%lf/media/gallery/'.strtolower(str_replace(' ', '_', $row['album'])).'/'.$row['img'].'" alt="" />'; ?>
			
			<div style="margin-left: 310px; ">
				<textarea name="description"><?=$row['description'];?></textarea>
			</div>
			<br />
			<input type="submit" value="Save" /> <?=$msg;?>
		</form>
	<?php
	
	if(is_dir(ROOT.'system/lib/tinymce/'))
		readfile(dirname(__FILE__).'/js.html');
	else
		echo 'No "TinyMCE" package found at '.$this->request->absbase.'system/lib/tinymce/';
	
}
else if( isset($var[0]) && $var[0] == 'rm' )
{	
	header('HTTP/1.1 302 Moved Temporarily');
	header('Location: '. $_SERVER['HTTP_REFERER']);
	
	$success = preg_match('/[0-9]+/', $var[1], $match);
	if(!$success) exit();
	
	$this->db->query("SELECT * FROM lf_gallery WHERE id = ".intval($var[1]));
	$img = $this->db->fetch();

	// clean up and set album
	$img['album'] = preg_replace('/[^a-zA-Z0-9\s]/', '', $img['album']);
	$album = ROOT."media/gallery/".strtolower(str_replace(' ', '_', $img['album'])).'/'; // set album var and make space into underscore for file
	
	if (file_exists($album.$_FILES["img"]["name"])) unlink($album.$_FILES["img"]["name"]);
	
	// Remove thread and comments
	$result = $this->db->query("DELETE FROM lf_gallery WHERE id = ".$match[0]);
	
	exit();
}
else if( isset($var[0]) && $var[0] == 'view' )
{
	$this->db->query("SELECT * FROM lf_gallery WHERE album = '".mysql_real_escape_string($var[1])."'");
	$imgs = $this->db->fetchall();
	
	?>
	<style type="text/css">
		#img_list { list-style: none; }
		#img_list li { float: left; border: 1px solid #000; margin: 5px; padding: 5px; }
		#img_list span { display: block; margin-top: 5px; }
	</style>
	<?php
	echo '<h2>Gallery: '.$var[1].'</h2>';
	echo '<ul id="img_list">';
	foreach($imgs as $img)
	{
		echo '<li>';
		echo '<img height="300px" src="%relbase%lf/media/gallery/'.strtolower(str_replace(' ', '_', $img['album'])).'/'.$img['img'].'" alt="" />';
		echo '<span>[<a href="%appurl%manage/gallery/rm/'.$img['id'].'/">x</a>] <a href="%appurl%manage/gallery/edit/'.$img['id'].'/">'.$img['title'].'</a></span>';
		//print_r($img);
		echo '</li>';
	}
	echo '</ul>';
}
else if( isset($var[0]) && $var[0] == 'add' )
{	
	//print_r($_POST); echo '<br />';
	//print_r($_FILES);
	
	$allowedExts = array("jpg", "jpeg", "gif", "png");
	//$extension = end(explode(".", $_FILES["img"]["name"]));
	$file = explode(".", $_FILES["img"]["name"]);
	$extension = $file[1];
	if (
		/*$_FILES["img"]["size"] < 1000000
		&&*/ in_array($extension, $allowedExts)
		&& (
			$_FILES["img"]["type"] == "image/gif"
			|| $_FILES["img"]["type"] == "image/jpeg"
			|| $_FILES["img"]["type"] == "image/pjpeg"
		)
	) 
	{
		if ($_FILES["img"]["error"] > 0) {
			echo "Return Code: " . $_FILES["img"]["error"] . "<br />";
		} else {
			/*echo "Upload: " . $_FILES["img"]["name"] . "<br />";
			echo "Type: " . $_FILES["img"]["type"] . "<br />";
			echo "Size: " . ($_FILES["img"]["size"] / 1024) . " Kb<br />";
			echo "Temp file: " . $_FILES["img"]["tmp_name"] . "<br />";*/
			
			// clean up and set album
			$_POST['album'] = preg_replace('/[^a-zA-Z0-9\s]/', '', $_POST['album']);
			$album = ROOT."media/gallery/".strtolower(str_replace(' ', '_', $_POST['album'])).'/'; // set album var and make space into underscore for file
			
			if(!is_dir($album)) mkdir($album, 0755, true);
			
			if (file_exists($album.$_FILES["img"]["name"])) {
				echo $_FILES["img"]["name"] . " already exists. ";
			} else {
				move_uploaded_file(
					$_FILES["img"]["tmp_name"],
					$album.$_FILES["img"]["name"]
				);
				echo "Stored in: " . $album . $_FILES["img"]["name"];
				
				$result = $this->db->query("
					INSERT INTO lf_gallery (`id`, `album`, `title`, `description`, `img`)
					VALUES (
						NULL,
						'".htmlspecialchars($_POST['album'], ENT_QUOTES)."', 
						'".htmlspecialchars($_POST['title'], ENT_QUOTES)."',
						'".mysql_real_escape_string($_POST['description'])."',
						'".$_FILES["img"]["name"]."'
					)
				");
			}
		}
	} else {
		echo "Invalid file";
	}
	
	echo '<br /><a href="%baseurl%apps/manage/gallery/">Go back</a>';
}
else if( isset($var[0]) && $var[0] == 'multi')
{
	echo '
		
	<script type="text/javascript">
	$(document).ready(function() {';
		readfile('js/jquery.filedrop.js');
		readfile('js/script.js');
	echo '});
	</script>
	<style type="text/css">';
	readfile('multi.css');
	echo '
		</style>
		
		<div id="dropbox">
			<span class="message">Drop images here to upload. <br /><i>(they will only be visible to you)</i></span>
		</div>';
}
else if( isset($var[0]) && $var[0] == 'new' )
{


	// else { didnt post }
	
	?>
	<style type="text/css">
		.add_thread .album { margin-bottom: 10px; padding: 5px; width: 100%; font-size:20px; }
	</style>
	<form action="%baseurl%apps/manage/gallery/add/" method="post" class="add_thread" enctype="multipart/form-data">
		<input type="submit" class="submit" value="Add to gallery" /><br /><br />
		Album Title: <input type="text" name="album" value="Album" class="album" /><br />
		Image Title: <input type="text" name="title" value="" class="album" /><br />
		Image File: <input type="file" name="img" /><br /><br />
		Image Description: <br />
		<textarea name="description" id="" cols="30" rows="10"></textarea>
	</form>
	<?php
	
	if(is_dir($this->request->absbase.'system/lib/tinymce/'))
		readfile(dirname(__FILE__).'/js.html');
	else
		echo 'No "TinyMCE" package found at '.$this->request->absbase.'system/lib/tinymce/';
}
else
{
	// No article selected
	?>
	<h3>[<a href="%baseurl%apps/manage/gallery/new/">Add Image</a>]</h3>
	<p>Select a gallery below or create a new album in the above form.</p>
	<ol>
	<?php
	$result = $this->db->query('SELECT DISTINCT album FROM lf_gallery');
	while($row = mysql_fetch_assoc($result))
	{
		echo '<li>[<a href="%baseurl%apps/manage/gallery/rm/'.$row['album'].'/">x</a>] <a href="%baseurl%apps/manage/gallery/view/'.$row['album'].'/">'.$row['album'].'</a></li>';
	}
		
	?></ol><?php
}

?>