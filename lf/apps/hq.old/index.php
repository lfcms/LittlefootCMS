<?php 

if($this->api('getuid') > 0)
{
	$wiki = $this->apploader('wiki'); 
	$cal = $this->apploader('calendar'); 
	$notes = $this->apploader('notes', 'task'); 
	$events = $this->apploader('events', 'task');
	
	//echo $this->request->apploader('wiki'); 
	?>
	<div style="float:left; width: 50%"><?php echo $wiki; ?></div>
	<div style="float:right; width: 50%"><?php echo $cal; ?></div>
	<div style="clear:both;"></div>
	<div style="float:left; width: 50%"><?php echo $notes; ?></div>
	<div style="float:right; width: 50%"><?php echo $events; ?></div>
	<?php
}
else
	echo 'Please login to use this app.';

?>