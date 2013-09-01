<h3><a href="%appurl%">Shop</a> </h3>
[<a href="%appurl%newitem/">new item</a>]
<ul id="main_items">
<?php foreach($items as $item)
{
	$src = '%relbase%lf/media/shop/'.$this->ini.'/'.$item['img'];
	
	echo '<li>
		<a href="%appurl%browse/'.$item['category_id'].'/'.urlencode($item['category']).'">'.$item['category'].'</a> - 
		<a href="%appurl%view/'.$item['id'].'/">'.$item['name'].'</a> 
		<img height="200px" src="'.$src.'" alt="" />
		@ $'.$item['price'].'
	</li>';
}
?>
</ul>