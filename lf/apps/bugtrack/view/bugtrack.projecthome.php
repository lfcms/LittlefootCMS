

<?php 

$cat = '';
if($category != '') $cat = ' / <a href="'.$this->instbase.'cat/'.$category.'/">'.$category.'</a>';

echo '<h2><a href="%appurl%">Bugtrack</a> / <a href="'.$this->instbase.'">'.$this->inst.'</a>'.$cat.' / '.ucfirst($status).'</h2>'; 

?>
	
<div id="categories">
	<h4>Categories</h4>
	<form action="<?php echo $this->instbase; ?>addcategory/" method="post"><input type="text" name="category" placeholder="New category" /></form>
	<?php if($categories)
		foreach($categories as $cat)
		{
			$cat = $cat['category'];
			echo '[<a href="%appurl%rmcategory/'.urlencode($this->inst).'/'.urlencode($cat).'/">x</a>] 
				<a href="%appurl%'.urlencode($this->inst).'/cat/'.urlencode($cat).'/">'.$cat.'</a><br />';
		}
		
		if($status == 'closed') $clickstatus = 'open';
		else $clickstatus = 'closed';
	?>
</div>

<div id="note">
	<h4>Tickets 
		[<a href="<?php echo $this->instbase; ?>newarticle/<?php echo urlencode($category); ?>">new</a>] 
		[<a href="<?php echo $this->instbase; if($category != '') echo 'cat/'.urlencode($category).'/'; echo $clickstatus; ?>"><?php echo $clickstatus; ?></a>]
	</h4>
	<ul>
	<?php

	$cat = '';
	foreach($posts as $post)
	{
		if($cat != $post['category'] && $category == '')
		{
			$cat = $post['category'];
			echo '<h4><a href="'.$this->instbase.'cat/'.$cat.'/">'.$cat.'</a></h4>';
		}
		
		echo '<li><a href="%appurl%'.urlencode($post['project']).'/view/'.$post['id'].'/">'.$post['title'].'</a></li>';
	}
	?>
	</ul>
</div>


	