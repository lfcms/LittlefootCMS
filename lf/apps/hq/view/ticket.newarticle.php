<?php 

echo '<h3>New Ticket</a></h3>';
?>

<div id="categories">
	<h4>Categories</h4>
	<form action="%appurl%addcategory/" method="post"><input type="text" name="category" placeholder="New category" /></form>
	<ul>
	<?php if($cats)
		foreach($cats as $category)
		{
			$category = $category['id'];
			
			echo '<li><a href="%appurl%cat/'.$category.'/">%category:'.$category.'%</a></li>';
		}
	?>
	</ul>
</div>

<div id="note" class="new_ticket">
	<form action="%appurl%create/" method="post" class="add_thread">
		<input type="submit" class="submit" value="Post" /><br />
		<input type="text" name="title" value="New Title" class="title" />
		
		Category: <select name="category" id=""><?php echo $cat_options; ?></select> or <input type="text" name="newcat" placeholder="New Category" /> 
		Assigned: <select name="assigned" id=""><?=$user_options;?></select>
		Flagged: <select name="flagged" id=""><?php 
			$flags = array('none', 'urgent');
			foreach($flags as $flag)
				echo '<option value="'.$flag.'">'.ucfirst($flag).'</option>';
		?></select>
		Status: <select name="status" id=""><?php 
			$options = array('open', 'closed', 'backburner');
			foreach($options as $option)
				echo '<option value="'.$option.'">'.ucfirst($option).'</option>';
		?></select>
		
		<br /><br />
		<textarea name="content"></textarea><br />
		<input type="submit" class="submit" value="Post" />
	</form>
</div>
