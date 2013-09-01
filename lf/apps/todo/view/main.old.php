<h2>Todo List</h2>
[<a href="%appurl%view/0/">Create new / View All</a>]
<?php

echo '<ul>';
foreach($categories as $category)
{
	$type = $category['type'];
	
	//echo $type.'<br />';
	echo '<li><a href="%appurl%type/'.$type.'/">'.$type.'</a></li>';
}
echo '</ul>';

?>