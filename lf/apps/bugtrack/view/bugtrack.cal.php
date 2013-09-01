<?php 

$nav = '';
if($inst != '') {
	$inst = substr($inst, 0, -1);
	$nav = '<a href="'.$this->instbase.'">'.$inst.'</a> / ';
}

echo '<h2>
	<a href="%appurl%">Bugtrack</a> / '.$nav.'Calendar</a>
</h2>';
?>

<div id="categories">
	<h4>Categories</h4>
	<form action="%appurl%addcategory/" method="post"><input type="text" name="category" placeholder="New category" /></form>
	<ul>
	<?php if($categories)
		foreach($categories as $category)
		{
			$category = $category['category'];
			
			$active = '';
			if($category == $post['category']) $active = ' class="active"';
			
			echo '<li'.$active.'>[<a href="%appurl%rmcategory/'.urlencode($this->inst).'/'.urlencode($category).'/">x</a>] 
				<a href="%appurl%'.urlencode($this->inst).'/cat/'.urlencode($category).'/">'.$category.'</a></li>';
		}
	?>
	</ul>
</div>

<style type="text/css">
	#note { padding-right: 20px; }
	#note .title { padding: 5px; font-size: 22px; width: 100%; margin-top: 10px; }
	#note .content { border: 1px solid #000; margin: 10px 0; padding: 10px; }
	#note .edit { border: 1px solid #000; margin: 10px 0; padding: 10px; width: 100%; }
	#postlist ul { margin: 0; padding: 0; }
	#postlist ul li { border-left: dotted 1px #000; padding: 5px 10px; margin: 5px 0;}
	#postlist > ul > li { border: none; padding-left: 0; }
</style>

<div id="note">
	<?php echo $calendar; ?>
</div>