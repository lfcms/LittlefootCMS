<?php

class calendar extends app
{
	public $default_method = 'viewdate';
	
	public function view($vars)
	{
		?>
		<style type="text/css">
			.app-calendar a { color: #000 !important; }
		</style>
		<?php
		
		$event = $this->db->fetch('SELECT * FROM calendar_events WHERE id = '.intval($vars[1]));
		
		echo '<h2>
			<a href="%appurl%">Calendar</a> / 
			<a href="%appurl%viewdate/'.date('Y-m-d', strtotime($event['date'])).'/">'.date('F j, Y', strtotime($event['date'])).'</a> / 
			<a href="%appurl%'.implode('/', $vars).'/">'.$event['title'].'</a>
		</h2>';
		echo '<p><strong>Date: '.date('F j, Y', strtotime($event['date'])).' @ '.date('g:m a', strtotime($event['date'])).'</strong></p>';
		echo '<p>'.$event['description'].'</p>';
	}
	
	public function viewdate($vars)
	{
		?>
		<style type="text/css">
			.app-calendar a { color: #000 !important; }
		</style>
		<?php
		if(!isset($vars[1]) || !preg_match('/^(\d{4})(?:-0?(\d{1,2}))?(?:-0?(\d{1,2}))?$/', $vars[1], $match)) return $this->showcalendar();
		
		if(isset($match[3])) // day requested
		{
			$sql = "
				SELECT * FROM calendar_events
				WHERE YEAR(date) = ".$match[1]." AND MONTH(date) = ".$match[2]." AND DAY(date) = ".$match[3]."
				ORDER BY date
			";
			//echo $sql;
			$event = $this->db->fetchall($sql);
			
			if($event == array())
			{
				echo '<h2><a href="%appurl%">Calendar</a> / '.date('F j, Y', strtotime($vars[1])).'</h2>';
				echo '<p>No events found</p>';
			}
			else
			{
				echo '<h2><a href="%appurl%">Calendar</a> / '.date('F d, Y', strtotime($event[0]['date'])).'</h2>';
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
			<style type="text/css">
				#month-grid a { border: 1px solid #000; display: block; width: 20%; float: left; padding: 15px; margin: 5px; }
				#month-grid a:hover { background: #999 }
			</style>
			<h2>
				<a href="%appurl%">Calendar</a> /
				<a href="%appurl%viewdate/'.($year-1).'/"><</a> 
				<a href="%appurl%viewdate/'.($year+1).'/">></a> 
				<a href="%appurl%viewdate/'.$year.'/">'.$year.'</a>
			</h2>
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
			SELECT * FROM calendar_events
			WHERE date BETWEEN '".$calendar[3].'-'.sprintf('%02d', $this_month)."-01' 
				AND '".$calendar[3].'-'.sprintf('%02d', $this_month).'-'.$calendar[1]."'
			ORDER BY date
		";/*
		$sql = "
			SELECT *, note as title
			FROM todo_task
			WHERE
				owner = ".$this->request->api('getuid')." AND
				date BETWEEN '".$calendar[3].'-'.$this_month."-01' 
					AND '".$calendar[3].'-'.$this_month.'-'.$calendar[1]."'
			ORDER BY date
		";*/
		
		$this->db->query($sql);
		while($row = $this->db->fetch())
			$event[date('j', strtotime($row['date']))][] = $row;
		
		// using last year-month, calculate days, calculate beginning of calendar display
		$last_month_days = date('t', strtotime($prev_year.'-'.$prev_month));
		if($calendar[0] == 0) $first_calendar_day = $last_month_days - 6; // fill top row with last month
		else $first_calendar_day = ($last_month_days - $calendar[0]) + 1; // +1 to offset starting at 0
		
		echo '
			<style type="text/css">
				.prev { background: #987; }
				.cur { background: #897; }
				.next { background: #789; }
				.today { background: #FFF; }
				#cal { border: 1px #000 solid; }
				#cal td { width: 14%; vertical-align:top; }
				#cal th { text-align: center; }
				#cal a { color: #000; }
				/*.mine { background: #CCC; }*/
			</style>
			
			<h2>
				<a href="%appurl%">Calendar</a> / 
				<a href="%appurl%viewdate/'.$prev_year.'-'.sprintf('%02d', $prev_month).'/"><</a> 
				<a href="%appurl%viewdate/'.$next_year.'-'.sprintf('%02d', $next_month).'/">></a>
				<a href="%appurl%viewdate/'.$calendar[3].'-'.$this_month.'/">'.$calendar[4].'</a> <a href="%appurl%viewdate/'.$calendar[3].'/">'.$calendar[3].'</a>
			</h2>
		';
		
		$end = false; // to end display
		$day = $first_calendar_day;
		$scope = 'prev'; // start with previous month
		
		echo '
			<table id="cal" width="100%">
				<tr><th>Sunday</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th><th>Saturday</th></tr>
		';
		while(!$end) 
		{
			$weekday = 1;
			echo '<tr height="125px">';
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
							echo '<br /><a href="%appurl%view/'.$item['id'].'/">'.substr($item['title'],0,15).' @ '.date('g:ma', strtotime($item['date'])).'</a>';
				echo '</td>';
				$day++; $weekday++;
			}
			echo '</tr>';
		}
		echo '</table>';
	}
}

?>