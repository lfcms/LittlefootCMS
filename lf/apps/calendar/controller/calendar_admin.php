<?php

class calendar_admin extends app
{
	public function main($vars)
	{
		$events = $this->db->fetchall('SELECT * FROM calendar_events ORDER BY date');
		
		echo 'Event List [<a href="%appurl%addevent/">add event</a>]';
		echo '<ul>';
		if($events == array())
			echo '<li>No events</li>';
		else
		{
			$month = '';
			foreach($events as $event)
			{
				$date = strtotime($event['date']);
				if($month != date('F Y', $date)) { $month = date('F Y', $date); echo '<h3>'.$month.'</h3>'; }
				
				echo '<li>[<a onclick="return confirm(\'Do you really want to delete this?\');" href="%appurl%rm/'.$event['id'].'/">x</a>] <a href="%appurl%edit/'.$event['id'].'/">'.$event['title'].'</a></li>';
			}
		}
		echo '</ul>';
	}
	
	public function rm($vars)
	{
		if(!isset($vars[1])) redirect302();
		$this->db->query('DELETE FROM calendar_events WHERE id = '.intval($vars[1]));
		redirect302();
	}
	
	public function addevent($vars)
	{
		echo '
		
			<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
			<!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script> -->
			<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script> 
			
			<script src="%relbase%lf/lib/jquery-ui-timepicker-addon.js"></script> 
			<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
			
			<form action="%appurl%createevent/" method="post">
				<ul>
					<li>Title: <input type="text" name="title" /></li>
					<li>Desc: <textarea name="description" cols="30" rows="10"></textarea></li>
					<li>Date: <input type="text" id="datepicker" name="date" /></li>
					<li><input type="submit" value="Create Event" /></li>
				</ul>
			</form>
			<script type="text/javascript">
			$("#datepicker").datetimepicker({
				dateFormat: "yy-mm-dd",
				timeFormat: "HH:mm:ss"
			});
			</script>
		';
			
		if(is_dir(ROOT.'system/lib/tinymce/'))
			echo file_get_contents(ROOT.'system/lib/tinymce/js.html');
		else
			echo 'No "TinyMCE" package found at '.ROOT.'system/lib/tinymce/';
	}
	
	public function edit($vars)
	{
		if(!isset($vars[1])) redirect302();
		
		$event = $this->db->fetch('SELECT * FROM calendar_events WHERE id = '.intval($vars[1]));
		
		echo '
			<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
			<!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script> -->
			<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script> 
			
			<script src="%relbase%lf/lib/jquery-ui-timepicker-addon.js"></script> 
			<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
			
			<form action="%appurl%update/'.intval($vars[1]).'/" method="post">
				<ul>
					<li><input type="text" name="title" value="'.$event['title'].'" /></li>
					<li><textarea name="description" id="" cols="30" rows="10">'.$event['description'].'</textarea></li>
					<li>Date: <input type="text" id="datepicker" name="date" value="'.$event['date'].'" /></li>
					<li><input type="submit" value="Update Event" /></li>
				</ul>
			</form>
			<script type="text/javascript">
			$("#datepicker").datetimepicker({
				dateFormat: "yy-mm-dd",
				timeFormat: "HH:mm:ss"
			});
			</script>
		';
		
		if(is_dir(ROOT.'system/lib/tinymce/'))
			echo file_get_contents(ROOT.'system/lib/tinymce/js.html');
		else
			echo 'No "TinyMCE" package found at '.ROOT.'system/lib/tinymce/';
	}
	
	public function update($vars)
	{
		if(!isset($vars[1])) redirect302();
		$sql = sprintf("UPDATE calendar_events SET title = '%s', description = '%s', date = '%s' WHERE id = %d",
			mysql_real_escape_string($_POST['title']),
			mysql_real_escape_string($_POST['description']),
			mysql_real_escape_string($_POST['date']),
			intval($vars[1])
		);
		$this->db->query($sql);
		redirect302();
	}
	
	public function createevent($vars)
	{
		$this->db->query(
			sprintf(
				"INSERT INTO calendar_events (`id`, `title`, `description`, `date`) VALUES (NULL, '%s', '%s', '%s')",
				mysql_real_escape_string($_POST['title']),
				mysql_real_escape_string($_POST['description']),
				mysql_real_escape_string($_POST['date'])
			)
		);
		redirect302($this->request->base.'apps/manage/calendar/');
	}
}

?>