<?php 

$nav = '';
if(isset($vars[1])) $nav = '<a href="'.$this->instbase.$vars[1].'">'.urldecode($vars[1]).'</a> / ';

echo '<h2><a href="%appurl%">Bugtrack</a> / '.$nav.'<a href="'.$this->instbase.'">'.$this->inst.'</a> / New Article</a></h2>';
?>

<div id="categories">
	<h4>Categories</h4>
	<form action="%appurl%addcategory/" method="post"><input type="text" name="category" placeholder="New category" /></form>
	<ul>
	<?php if($cats)
		foreach($cats as $category)
		{
			$category = $category['category'];
			
			echo '<li>[<a href="%appurl%rmcategory/'.urlencode($this->inst).'/'.urlencode($category).'/">x</a>] 
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
	<style type="text/css">
		.app-bugtrack { padding: 5px; }
		.add_thread .title { margin-bottom: 10px; width: 100%; font-size:20px; }
	</style>
	<form action="<?php echo $inst_base; ?>create/" method="post" class="add_thread">
		<input type="submit" class="submit" value="Post" /><br />
		<input type="text" name="title" value="New Title" class="title" />
		Category: <select name="category" id=""><?php echo $cat_options; ?></select> or <input type="text" name="newcat" placeholder="New Category" /><br /><br />
		<textarea name="content"></textarea><br />
		<input type="submit" class="submit" value="Post" />
	</form>
</div>