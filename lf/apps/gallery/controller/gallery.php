<?php

class gallery
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
	}
	
	//default
	public function main($vars)
	{
		if($this->ini != '') return $this->browse(array(1 => $this->ini));
		?>
		<h2>Gallery</h2>
		<ol>
		<?php
		$result = $this->db->query('SELECT DISTINCT album FROM lf_gallery');
		while($row = mysql_fetch_assoc($result))
		{
			echo '<li>[<a href="%appurl%rm/'.$row['album'].'/">x</a>] <a href="%appurl%browse/'.$row['album'].'/">'.$row['album'].'</a></li>';
		}
		echo '</ol>';
	}
	
	public function browse($vars)
	{
		$this->db->query("SELECT * FROM lf_gallery WHERE album = '".mysql_real_escape_string($vars[1])."'");
		$imgs = $this->db->fetchall();
		
		?>
		<style type="text/css">
			#img_list { list-style: none; }
			#img_list li { float: left; border: 1px solid #000; margin: 5px; padding: 5px; }
			#img_list span { display: block; margin-top: 5px; }
		</style>
		<?php
		echo '<h2>Gallery / '.$vars[1].'</h2>';
		echo '<ul id="img_list">';
		foreach($imgs as $img)
		{
			echo '<li>';
			echo '
				<a href="%appurl%view/'.$img['id'].'/">
					<img height="300px" src="%relbase%lf/media/gallery/'.strtolower(str_replace(' ', '_', $img['album'])).'/'.$img['img'].'" alt="" />
				</a>
			';
			echo '<span><a href="%appurl%view/'.$img['id'].'/">'.$img['title'].'</a></span>';
			//print_r($img);
			echo '</li>';
		}
		echo '</ul>';
	}
	
	public function view($vars)
	{
		$sql = "SELECT * FROM lf_gallery WHERE id = '".intval($vars[1])."'";
		
		if($this->ini != '') $sql .= " AND album = '".$this->ini."'";
		
		if(mysql_num_rows($this->db->query($sql)) == 0)
			return "Invalid Image Request";
			
		$img = $this->db->fetch();
		
		?>
		<style type="text/css">
			#img_list { list-style: none; }
			#img_list li { float: left; border: 1px solid #000; margin: 5px; padding: 5px; }
			#img_list span { display: block; margin-top: 5px; }
		</style>
		<?php
		echo '<h2>'.$img['album'].' / '.$img['title'].'</h2>';
		echo '<img style="float: left; margin-right: 10px; " height="300px" src="%relbase%lf/media/gallery/'.strtolower(str_replace(' ', '_', $img['album'])).'/'.$img['img'].'" alt="" /><br />';
		echo '<p>'.$img['description'].'</p>';
		//print_r($img);
	}
}

?>