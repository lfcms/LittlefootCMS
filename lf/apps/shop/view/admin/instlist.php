<h3>Shop List</h3>
<ul>
	<?php 
	foreach($list as $inst)
	{
		echo '<li><a href="%appurl%'.urlencode($inst).'">'.$inst.'</a></li>';
	}
	?>
</ul>