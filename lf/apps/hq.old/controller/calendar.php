<?php

/*

public functions are the controllers
private functions are the models
view loads at the end

*/

class calendar
{
	private $request;
	private $html;
	private $pwd;
	private $dbconn;
	
	public function __construct($request, $dbconn, $ini)
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->pwd = $request->absbase.'/apps';
		$this->project = $ini;
	}
	
	public function main($vars)
	{
		
		/*

		[][][][][][][]
		[][][][][][][]
		[][][][][][][]
		[][][][][][][]

		%7 : 1=1,2=2,...,7=0

		20th - 4th weekday (from 0)
		20th % 7 = 6

		6 - 1 = 5
		4th weekday - 5 = lastweekday of last week.
		4th weekday - 5 % 7 = lastweekday of last week.

		*/
		$days = 0;
		if(isset($vars[0])) $days = $vars[0];

		$today = time() + $days*(24 * 60 * 60);

		$now = explode('/', date('w/n/j/Y/t/F', $today));
		$last_month = time() - (($now[2]) * 24 * 60 * 60);
		$last_month_days = date('t', $last_month);
		// weekday/month/day/year

		$baseday = $now[2] % 7; // get base day (19 = 5, 22 = 1) lowest day in same column
		$diff = $baseday - 1; // get distance to day 1

		$dayonepos = (7 - ($diff - $now[0])) % 7; // first day of current month (from weekday 0)
		$first_cal_day = $last_month_days + 1 - $dayonepos;

		$sql = "
			SELECT * 
			FROM hq_events
			WHERE
				project = ".$this->project." AND
				date BETWEEN '".date('Y-m', $today)."-01' 
					AND '".date('Y-m-t', $today)."'
			ORDER BY date
		";
		$this->db->query($sql);
		while($row = $this->db->fetch())
			$event[date('j', strtotime($row['date']))][] = $row;
			
		//print_r($event);
		
		$sql = "
			SELECT * 
			FROM hq_notes
			WHERE
				project = ".$this->project." AND
				date BETWEEN '".date('Y-m', $today)."-01' 
					AND '".date('Y-m-t', $today)."'
			ORDER BY date
		";
		$this->db->query($sql);
		while($row = $this->db->fetch())
			$notes[date('j', strtotime($row['date']))][] = $row;

			
		?>
		<style type="text/css">
			.prev { background: #987; }
			.cur { background: #897; }
			.next { background: #789; }
			.today { background: #FFF; }
			#cal td { width: 14%; vertical-align:top; }
			/*.mine { background: #CCC; }*/
			td { padding-left: 5px; }
		</style>
		<h2><a href="%appurl%main/"><?php echo $now[5].' '.$now[3]; ?></a></h2>
		<table id="cal" width="100%">
			<?php 
				$end = false; $day = $first_cal_day; $scope = 'prev';
				while(!$end):
					$weekday = 1; 
			?>
			<tr height="80px">
				<?php
					while($weekday <= 7 && $weekday >= 1): 
						if($scope == 'prev' && $day > $last_month_days)
						{
							$day = 1;
							$scope = 'cur';
						}
						
						if($scope == 'cur' && $day > $now[4])
						{
							$day = 1;
							$scope = 'next';
							$end = true;
						}
				?>
				<td class="<?php 
					echo $scope; 
					if($scope == 'cur' && $day == $now[2]) 
						echo ' today'; 
				?>"><?php 
					echo '<div style="float: right; margin-right: 5px;"><a href="%appurl%day/'.$day.'/">'.$day.'</a></div>';
					if($scope == 'cur' && isset($event[$day]))
					{
						foreach($event[$day] as $date => $item)
						{
							$class = '';
							if($this->request->api('getuid') == $item['owner'])
								$class = ' class="mine"';
							echo '<br />'.$item['type'].' - <a'.$class.' href="%appurl%../events/type/'.$item['type'].'/'.$item['id'].'/">'.substr($item['note'], 0, 15).'</a>';
						}
					}
					if($scope == 'cur' && isset($notes[$day]))
					{
						foreach($notes[$day] as $date => $item)
						{
							$class = '';
							if($this->request->api('getuid') == $item['owner'])
								$class = ' class="mine"';
							echo '<br />'.$item['type'].' - <a'.$class.' href="%appurl%../notes/type/'.$item['type'].'/'.$item['id'].'/">'.substr($item['note'], 0, 15).'</a>';
						}
					}
				?></td>
				<?php $day++; $weekday++; endwhile; ?>
			</tr>
			<?php endwhile; ?>
		</table><?php
	}
	
	public function day($vars)
	{
		echo '<h3>'.date('Y-m-d').'</h3>';
		
		$events = $this->db->fetchall("
			SELECT * FROM hq_events 
			WHERE date 
				BETWEEN '".date('Y-m-').$vars[1]." 00:00:01'
					AND '".date('Y-m-').$vars[1]." 23:59:59'
				AND project = ".$this->project
		);
		$notes = $this->db->fetchall("
			SELECT * FROM hq_notes
			WHERE date 
				BETWEEN '".date('Y-m-').$vars[1]." 00:00:01'
					AND '".date('Y-m-').$vars[1]." 23:59:59'
				AND project = ".$this->project
		);
		
		$vars = array('notes', 'events');
		foreach($vars as $var)
		{
			echo '<h4>'.$var.'</h4>';
			foreach($$var as $row)
			{
				echo '<a href="%appurl%../'.$var.'/view/'.$row['id'].'/">'.$row['note'].'</a>';
				echo '<br />';
			}
		}
	}
}

?>