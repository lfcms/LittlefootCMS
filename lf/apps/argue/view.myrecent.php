<h3>Recent Posts</h3>
<ul>
	<?php foreach($result as $row) {?>
	<li><?php echo $row['user'].': '.$row['message']; ?></li>
	<?php } ?>
</ul>