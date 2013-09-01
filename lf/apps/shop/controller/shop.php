<?php

/*

-categories (like lf_actions heirarchy)
id, parent, category

-items
id, name, desc, price, stock, category_id

SELECT DISTINCT c.category, i.id, i.name, i.price 
FROM c.categories LEFT JOIN i.items ON i.category_id = c.id
WHERE i.stock = 0;


add instance system like blog

*/

class shop extends app
{
	protected function init($vars)
	{
		if($this->ini == '')
			$this->ini = 'myshop';
	}
	
	public function main($vars)
	{
		$items = $this->db->fetchall('
			SELECT DISTINCT c.category, i.*
			FROM shop_categories c
			LEFT JOIN shop_items i
				ON i.category_id = c.id
			WHERE i.stock != 0');
		
		//ob_start();
		include 'view/main.php';
		//thumbnail(ob_get_clean(), '200x200');
	}
	
	public function all($vars)
	{
		echo '<h2>Shop / All</h2>';
		
		$items = $this->db->fetchall('
			SELECT *
			FROM shop_items
			WHERE stock != 0');
		
		print_r($items);
	}
	
	public function view($vars)
	{
		$item = $this->db->fetch("
			SELECT i.*, c.category FROM shop_items i
			LEFT JOIN shop_categories c
				ON c.id = i.category_id
			WHERE i.id = ".intval($vars[1]));
			
		include 'view/item.php';
	}
	
	public function browse($vars)
	{
		$items = $this->db->fetchall("
			SELECT i.*, c.category FROM shop_items i
			LEFT JOIN shop_categories c
				ON c.id = i.category_id
			WHERE c.id = ".intval($vars[1]));
			
		include 'view/browse.php';
	}
}