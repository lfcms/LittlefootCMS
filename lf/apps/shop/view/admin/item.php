<h2>
	<a href="%appurl%">Shop</a> / 
	<a href="%appurl%browse/<?=$item['id'].'/';?>"><?=$item['category'];?></a> / 
	<a href="%appurl%view/<?=$item['id'].'/'.urlencode($item['name']);?>"><?=$item['name'];?></a>
</h2>
<div>
	<h3><?=$item['name'];?> - $<?=$item['price'];?></h3>
	<img src="<?='%relbase%lf/media/shop/'.$this->ini.'/'.$item['img'];?>" alt="" />
</div>