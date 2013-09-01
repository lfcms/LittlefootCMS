<h2><a href="%appurl%">Shop</a> / Browse</h2>
<ul id="main_items">
	<?php foreach($items as $item)
	{
		echo '<li>'.$item['category'].' - <a href="%appurl%view/'.$item['id'].'/">'.$item['name'].'</a> @ $'.$item['price'].'</li>';
	}
	?>
</ul>