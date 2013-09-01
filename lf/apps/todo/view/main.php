<form action="%appurl%newcategory/"><input type="" /></form>
<?php

echo '<ul>';
foreach($categories as $category)
{
	$type = $category['type'];
	
	//echo $type.'<br />';
	echo '<li><a href="%appurl%'.$type.'/">'.$type.'</a></li>';
}
echo '</ul>';

?>