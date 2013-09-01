<h3>Shop / Add Item</h3>
<form id="createitem_form" action="<?php echo $this->instbase; ?>createitem/" method="post" enctype="multipart/form-data">
	<ul>
		<li><input class="form_title" type="text" name="name" placeholder="Item Name" /></li>
		<li>Product Image: <input type="file" name="img" /></li>
		<li><label for="form_desc">Item Description</label><textarea id="form_desc" name="description" id="" cols="30" rows="10"></textarea></form></li>
		<li>Price: <input type="text" name="price" /></li>
		<li>Stock: <input type="text" name="stock" /></li>
		<li><input type="submit" value="Create Item" /></li>
	</ul>
</form>