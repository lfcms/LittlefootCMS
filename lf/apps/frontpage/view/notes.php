<h2>Notes</h2>
[<a href="%appurl%view/0/">Create new / View All</a>]
<?php

echo '<ul>';
foreach($apps as $app)
{
	$type = $app['type'];
	
	//echo $type.'<br />';
	echo '<li><a href="%appurl%type/'.$type.'/">'.$type.'</a></li>';
}
echo '</ul>';

?>