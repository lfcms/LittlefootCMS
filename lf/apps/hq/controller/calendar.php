<?php

class calendar extends app
{
	public $default_method = 'viewdate';
	
	public function view($vars)
	{
		ob_start();
		$event = $this->db->fetch('SELECT * FROM hq_events WHERE id = '.intval($vars[1]));
		echo '<div id="calendar_nav">
			<a class="new_event" href="%appurl%newevent/">New Event</a>
			<a class="view_agenda" href="%appurl%agenda/">Agenda</a>
			<span class="calendar_date_nav">
				<a href="%appurl%viewdate/'.date('Y-m-d', strtotime($event['date'])).'/">'.date('F j, Y', strtotime($event['date'])).'</a> / 
				<a href="%appurl%'.implode('/', $vars).'/">'.$event['title'].'</a>
			</span>
		</div>';
		echo '<p>
			[<a href="%appurl%edit/'.$event['id'].'/">edit</a>] 
			<strong>Date: '.date('F j, Y', strtotime($event['date'])).' @ '.date('g:m a', strtotime($event['date'])).'</strong> 
			{like:event/'.$event['id'].'}
		</p>';
		if($event['ticket_id'] > 0)
			echo '<p><strong>Ticket: <a href="'.$this->lf->appurl.$this->ini.'/tickets/view/'.$event['ticket_id'].'/">'.$event['ticket_id'].'</a></strong></p>';
		echo '<p>'.$event['note'].'</p>';
		
		$this->comment_id = 'hq/'.$this->ini.'/events/'.intval($vars[1]);
		$this->comment();
		$data = ob_get_clean();
		
		
		include '../like/controller/like.php';
		$like = new like($this->request, $this->db);
		echo $like->parse($data);
	}
	
	public function comment($vars = array(''))
	{
		$vars = array_slice($vars, 1);
		if(!isset($this->comment_id)) $this->comment_id = $_POST['inst'];
		echo $this->lf->extmvc('comment', 'comments/comments', $this->comment_id, $vars);
	}
	
	public function viewdate($vars)
	{
		if(!isset($vars[1]) || !preg_match('/^(\d{4})(?:-0?(\d{1,2}))?(?:-0?(\d{1,2}))?$/', $vars[1], $match)) return $this->showcalendar();
		
		if(isset($match[3])) // day requested
		{
			$sql = "
				SELECT * FROM hq_events
				WHERE YEAR(date) = ".$match[1]." AND MONTH(date) = ".$match[2]." AND DAY(date) = ".$match[3]."
				ORDER BY date
			";
			//echo $sql;
			$event = $this->db->fetchall($sql);
			
			if($event == array())
			{
				echo '<div id="calendar_nav">
					<a class="new_event" href="%appurl%newevent/">New Event</a>
					| <a class="view_agenda" href="%appurl%agenda/">Agenda</a>
					| <a href="%appurl%">Calendar</a> / '.date('F j, Y', strtotime($vars[1])).'
				</div>';
				echo '<p>No events found</p>';
			}
			else
			{
				echo '<div id="calendar_nav">
					<a class="new_event" href="%appurl%newevent/">New Event</a>
					<a class="view_agenda" href="%appurl%agenda/">Agenda</a>
					<a href="%appurl%">Calendar</a> / '.date('F d, Y', strtotime($event[0]['date'])).'
				</div>';
				echo '<ul>';
				foreach($event as $item)
					echo '<li><a href="%appurl%view/'.$item['id'].'/">'.$item['title'].' @ '.date('g:m a', strtotime($item['date'])).'</a></li>';
				echo '</ul>';
			}
		}
		else if(isset($match[2])) // month requested
		{
			$this->showcalendar($vars[1]);
		}
		else
		{
			$this->showyear($match[1]);
		}
	}
	
	private function showyear($year = '')
	{
		echo '
			<div id="calendar_nav">
				<a class="new_event" href="%appurl%newevent/">New Event</a> | 
				<a class="view_agenda" href="%appurl%agenda/">Agenda</a> | <a href="%appurl%">Calendar</a> /
				<a href="%appurl%viewdate/'.($year-1).'/"><</a> 
				<a href="%appurl%viewdate/'.($year+1).'/">></a> 
				<a href="%appurl%viewdate/'.$year.'/">'.$year.'</a>
			</div>
			<div id="month-grid">
		';
		for($i = 1; $i <= 12; $i++)
			echo '<a href="%appurl%viewdate/'.$year.'-'.sprintf('%02d', $i).'/">'.date('F', strtotime($year.'-'.sprintf('%02d', $i))).'</a>';
		echo '</div>';
		echo '<div style="clear:both"></div>';
	}
	
	private function showcalendar($date = '') // maybe add function variable for table name. no use ini
	{	
		if($date == '')	$date = date('Y-m', time()); // no date provided? just use this year-month
		$date = date('U', strtotime($date)); // Set $date to provided time of beginning of year-month
		
		$today = explode('/', date('j/n/Y', time()));
		
		// w = day of week (day 1), t = number of days in month, n = month #, Y = year #, F = MonthName, j = day of month
		$calendar = explode('/', date('w/t/n/Y/F', $date));
		
		// Set calendar variables
		$this_month = $calendar[2]; // current month #
		$prev_year = $calendar[3]; // current year #
		$next_year = $prev_year; // initialize to current year, change year only if month rolls over
		$prev_month = $this_month - 1;
		$next_month = $this_month + 1;
		if($prev_month < 1) { $prev_month = 12; $prev_year--; }
		if($next_month > 12) { $next_month = 1; $next_year++; }
		
		// Pull calendar events
		$sql = "
			SELECT * FROM hq_events
			WHERE date BETWEEN '".$calendar[3].'-'.sprintf('%02d', $this_month)."-01 00:00:00' 
				AND '".$calendar[3].'-'.sprintf('%02d', $this_month).'-'.$calendar[1]." 23:59:59'
				AND project = ".intval($this->ini)."
			ORDER BY date
		";
		
		$this->db->query($sql);
		while($row = $this->db->fetch())
			$event[date('j', strtotime($row['date']))][] = $row;
		
		// using last year-month, calculate days, calculate beginning of calendar display
		$last_month_days = date('t', strtotime($prev_year.'-'.$prev_month));
		if($calendar[0] == 0) $first_calendar_day = $last_month_days - 6; // fill top row with last month
		else $first_calendar_day = ($last_month_days - $calendar[0]) + 1; // +1 to offset starting at 0
		
		echo '
			
			<div id="calendar_nav">
				<a class="new_event" href="%appurl%newevent/">New Event</a>
				<a class="view_agenda" href="%appurl%agenda/">Agenda</a>
				<span class="calendar_date_nav">
					<a href="%appurl%viewdate/'.$prev_year.'-'.sprintf('%02d', $prev_month).'/"><</a> 
					<a href="%appurl%viewdate/'.$next_year.'-'.sprintf('%02d', $next_month).'/">></a>
					<a href="%appurl%viewdate/'.$calendar[3].'-'.$this_month.'/">'.$calendar[4].'</a> <a href="%appurl%viewdate/'.$calendar[3].'/">'.$calendar[3].'</a>
				</span>
			</div>
		';
		
		$end = false; // to end display
		$day = $first_calendar_day;
		$scope = 'prev'; // start with previous month
		
		echo '
			<table id="cal">
				<tr><th>Sunday</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th><th>Saturday</th></tr>
		';
		while(!$end) 
		{
			$weekday = 1;
			echo '<tr>';
			while($weekday <= 7) // loop through sunday - saturday
			{
				if($scope == 'prev' && $day > $last_month_days)
				{
					// Finished printing last month, now pick up with start up current month
					$day = 1;
					$scope = 'cur';
				}
				
				if($scope == 'cur' && $day > $calendar[1])
				{
					// Finished current month, finish off with next month
					$day = 1;
					$scope = 'next';
					$end = true;
				}
				
				$class = $scope;
				if($scope == 'cur' && $day == $today[0] && $calendar[2] == $today[1] && $calendar[3] == $today[2])
					$class .= ' today';
				
				echo '<td class="'.$class.'">';
					if($scope == 'cur')
						echo '<a href="%appurl%viewdate/'.$calendar[3].'-'.sprintf('%02d', $this_month).'-'.sprintf('%02d', $day).'/">'.$day.'</a>';
					else
						echo $day;
					
					if($scope == 'cur' && isset($event[$day]))
						foreach($event[$day] as $date => $item)
							echo '<br /><a href="%appurl%view/'.$item['id'].'/">'.date('g:ma', strtotime($item['date'])).' - '.substr($item['title'],0,15).'</a>';
				echo '</td>';
				$day++; $weekday++;
			}
			echo '</tr>';
		}
		echo '</table>';
	}
	
	public function agenda($vars)
	{
		echo '<div id="calendar_nav">
				<a class="new_event" href="%appurl%newevent/">New Event</a>
				<a class="view_agenda" href="%appurl%agenda/">Agenda</a>
				<a class="back2calendar" href="%appurl%">Back to calendar</a>
			</div>';
		$events = $this->db->fetchall('SELECT * FROM hq_events WHERE date > DATE(NOW()) AND project = '.intval($this->ini).' ORDER BY date ASC');
		
		foreach($events as $event)
		{
			echo '<a href="%appurl%view/'.$event['id'].'/">'.$event['title'].'</a> - '.date("F j, Y, g:i a", strtotime($event['date'])).'<br />';
		}
	}
	
	public function newevent($vars)
	{
		$ticket = '';
		if(isset($vars[1])) $ticket = ' / Ticket ID: '.$vars[1];
		
		?>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script> -->
		<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script> 
		<script src="%relbase%lf/lib/jquery-ui-timepicker-addon.js"></script> 
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
		
		<div id="calendar_nav">Calendar / New Event<?php echo $ticket; ?></div>
		
		<form action="%appurl%createevent/" method="post">
			<ul>
				<li>Title: <input type="text" name="title" /></li>
				<li>Note: <textarea name="note" cols="30" rows="10"></textarea></li>
				<li>Date: <input type="text" id="datepicker" name="date" /></li>
				<li>Ticket ID: <input name="ticket_id" type="text" <?php if(isset($vars[1])) echo 'value="'.$vars[1].'"'; ?> /></li>
				<li><input type="submit" value="Create Event" /></li>
			</ul>
		</form>
		
		<script type="text/javascript">
		$("#datepicker").datetimepicker({
			dateFormat: "yy-mm-dd",
			timeFormat: "HH:mm:ss"
		});
		</script>
		<?php
	}
	
	public function createevent($vars)
	{	
		$this->db->query(
			sprintf(
				"INSERT INTO hq_events (`id`, `project`, `owner`, `title`, `note`, `date`, `ticket_id`) 
					VALUES (NULL, %d, %d, '%s', '%s', '%s', %d)",
				intval($this->ini),
				$this->lf->api('getuid'),
				mysql_real_escape_string($_POST['title']),
				mysql_real_escape_string($_POST['note']),
				mysql_real_escape_string($_POST['date']),
				intval($_POST['ticket_id'])
			)
		);
		
		$id = $this->db->last();
		
		redirect302($this->lf->appurl.$this->ini.'/calendar/view/'.$id);
	}
	
	public function edit($vars)
	{
		$edit = $this->db->fetch('SELECT * FROM hq_events WHERE id = '.intval($vars[1]));
		
		readfile(ROOT.'system/lib/tinymce/js.html');
		?>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script> -->
		<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script> 
		<script src="%relbase%lf/lib/jquery-ui-timepicker-addon.js"></script> 
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
		
		<div id="calendar_nav">Calendar / Edit Event [<a onclick="return confirm('Do you really want to delete this event?');" href="%appurl%rm/<?=$vars[1];?>/">delete</a>]</div>
		
		<form action="%appurl%updateevent/<?=$vars[1];?>/" method="post">
			<ul>
				<li>Title: <input type="text" name="title" value="<?=$edit['title'];?>" /></li>
				<li>Note: <textarea name="note" cols="30" rows="10"><?=$edit['note'];?></textarea></li>
				<li>Date: <input type="text" id="datepicker" name="date" value="<?=$edit['date'];?>" /></li>
				<li>Ticket ID: <input name="ticket_id" type="text" value="<?=$edit['ticket_id'];?>" /></li>
				<li><input type="submit" value="Update Event" /></li>
			</ul>
		</form>
		
		<script type="text/javascript">
		$("#datepicker").datetimepicker({
			dateFormat: "yy-mm-dd",
			timeFormat: "HH:mm:ss"
		});
		</script>
		<?php
	}
	
	public function updateevent($vars)
	{	
		$this->db->query("UPDATE hq_events SET
			title = '".mysql_real_escape_string($_POST['title'])."',
			note = '".mysql_real_escape_string($_POST['note'])."',
			date = '".mysql_real_escape_string($_POST['date'])."',
			ticket_id = ".intval($_POST['ticket_id'])."
			WHERE id = ".intval($vars[1])
		);
		
		redirect302($this->lf->appurl.$this->ini.'/calendar/view/'.$vars[1]);
	}
	
	public function rm($vars)
	{
		$this->db->query('DELETE FROM hq_events WHERE id = '.intval($vars[1]));
		redirect302($this->lf->appurl.$this->ini.'/calendar/');
	}
	
	public function like($vars = array(''))
	{
		// $vars[1] == unlike, auditlog
		
		$vars = array_slice($vars, 1);
		//if(!isset($this->comment_id)) $this->comment_id = $_POST['inst'];
		echo $this->lf->extmvc('like', 'like/like', 'likeinst'/*$this->comment_id*/, $vars);
	}
}

?>