<h3>Shop Instance: <a href="<?=$this->instbase;?>"><?=$this->inst;?></a></h3>
<?php echo $breadcrumb; ?>
[<a href="<?=$this->instbase.'newitem/'.$cat;?>">new item</a>]

<div id="category_list">Categories:
	<ul>
	<li><?php 
	
	echo '<form action="%appurl%addcategory" method="post">
		<input type="hidden" name="category" value="'.$parent.'" />
		<input type="text" name="name" placeholder="New Category" />
	</form>';
	
	//echo '[<a href="%appurl%'.$this->inst.'/addchild/'.$parent.'">add child</a>]'; 
	
	
	?></li>
	<?php 

	if(count($categories))
		foreach($categories as $cat)
		{
			echo '<li>
				<a href="%appurl%'.$this->inst.'/browse/'.$cat['id'].'/'.$breadurl.urlencode($cat['category']).'/">'.$cat['category'].'</a> 
			</li>';
		}
	?>
	</ul>
</div>
<?php 

if(count($items)): 

?>
<div id="item_list">Items:
	<ul>
	<?php 
	
	foreach($items as $item)
	{
		echo '<li><a href="'.$this->instbase.'view/'.$item['id'].'/">'.$item['name'].'</a> - $'.$item['price'].'</li>';
	}
	?>
	</ul>
</div>
<?php endif; ?>