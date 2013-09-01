<h3>Tickets <?php if($category != '') echo ' / '.$category; echo ' / '.ucfirst($status); ?></h3>
<div id="categories">
	<h4>Categories</h4>
	<form action="%appurl%addcategory/" method="post"><input type="text" name="category" placeholder="New category" /></form>
	<?php if($categories)
		foreach($categories as $cat)
		{
			$cat = $cat['category'];
			echo '[<a href="%appurl%rmproject/'.urlencode($cat).'/">x</a>] <a href="%appurl%cat/'.urlencode($cat).'/">'.$cat.'</a><br />';
		}
	?>
</div>

<?php

$caturl = '';
if($category != '') $caturl = 'cat/'.urlencode($category).'/';

$status_options = array('open', 'closed', 'backburner', 'critical');
$nav = array();
foreach($status_options as $option)
{
	
	if($option == $status)
		$nav[] = ucfirst($option);
	else
		$nav[] = '<a href="%appurl%'.$caturl.$option.'/">'.ucfirst($option).'</a>';
	/*
	
	
		- <a href="%appurl%newarticle/<?php if($category != '') echo $category.'/'; ?>">New</a> 
		| <a href="%appurl%newarticle/<?php if($category != '') echo $category.'/'; ?>">Closed</a> 
		| <a href="%appurl%newarticle/<?php if($category != '') echo $category.'/'; ?>">Backburner</a>
	
	*/
}

?>

<div id="note">
	<h4>Tickets [<a href="%appurl%newarticle/<?php if($category != '') echo urlencode($category); ?>">New</a>] - <?php echo implode(' | ', $nav); ?></h4>
	<ul>
	<?php

	$cat = '';
	foreach($posts as $post)
		echo '<li><a href="%appurl%cat/'.urlencode($post['category']).'">'.$post['category'].'</a> / <a href="%appurl%view/'.$post['id'].'/">'.$post['title'].'</a></li>';
	?>
	</ul>
</div>