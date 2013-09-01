<?php 

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

$today = time() - $days*(24 * 60 * 60);

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
	FROM todo_task
	WHERE
		owner = ".$this->api('getuid')." AND
		date BETWEEN '".date('Y-m', $today)."-01' 
			AND '".date('Y-m-t', $today)."'
	ORDER BY date
";
$this->db->query($sql);
while($row = $this->db->fetch())
	$event[date('j', strtotime($row['date']))][] = $row;
		
?>
<style type="text/css">
	.prev { background: #987; }
	.cur { background: #897; }
	.next { background: #789; }
	.today { background: #FFF; }
	#cal td { width: 14%; vertical-align:top; }
	/*.mine { background: #CCC; }*/
</style>
<h2><?php echo $now[5].' '.$now[3]; ?></h2>
<table id="cal" width="100%">
	<?php 
		$end = false; $day = $first_cal_day; $scope = 'prev';
		while(!$end):
			$weekday = 1; 
	?>
	<tr height="125px">
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
			echo $day;
			if($scope == 'cur' && isset($event[$day]))
				foreach($event[$day] as $date => $item)
				{
					$class = '';
					if($this->api('getuid') == $item['owner'])
						$class = ' class="mine"';
					echo '<br />'.$item['type'].' - <a'.$class.' href="%baseurl%notes/task/type/'.$item['type'].'/'.$item['id'].'/">'.substr($item['note'], 0, 15).'</a>';
				}
		?></td>
		<?php $day++; $weekday++; endwhile; ?>
	</tr>
	<?php endwhile; ?>
</table>