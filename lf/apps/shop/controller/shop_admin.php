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

class shop_admin extends app
{
	public $default_method = '_router';
	
	protected function init($vars)
	{
		if($this->ini == '')
			$this->ini = 'myshop';
	}
	
	public function instlist($vars)
	{
		$instances = $this->db->fetchall('SELECT DISTINCT inst FROM shop_categories ORDER BY inst');
		
		$list = array();
		foreach($instances as $instance)
			$list[] = $instance['inst'];
		
		include 'view/admin/instlist.php';
	}
	
	public function _router($vars)
	{
		if($vars[0] == '') return $this->instlist($vars);
		
		$this->instbase = $this->lf->appurl.$vars[0].'/';
		$this->inst = urldecode($vars[0]);
		
		// Load 
		$vars = array_slice($vars, 1); // move vars over to emulate direct execution
		
		$method = 'browse'; // default method, also acts as router
		if(isset($vars[0])) $method = $vars[0];
		$this->$method($vars);
		
		echo '<div style="clear:both; margin-bottom: 10px;"></div>';
	}
	
	public function browse($vars)
	{
		$parent = -1;
		if(isset($vars[1]))
			$parent = intval($vars[1]);
		$vars = array_slice($vars, 2);
		
		$categories = $this->db->fetchall("
			SELECT id, category
			FROM shop_categories
			WHERE inst = '".$this->inst."' AND parent = ".$parent);
			
		$items = $this->db->fetchall('
			SELECT *
			FROM shop_items
			WHERE category_id = '.$parent);
		
		$breadcrumb = '';
		if(count($vars))
		{
			$breadcrumb = '<p>Breadcrumb: ';
			foreach($vars as $var)
				$breadcrumb .= urldecode($var).' > ';
			$breadcrumb = substr($breadcrumb, 0, -3);
			$breadcrumb .= '</p>';
		}
	
		$breadurl = '';
		if(count($vars))
			$breadurl = implode('/', $vars).'/';
			
		$cat = $parent != -1 ? $parent : '';
			
		include 'view/admin/browse.php';
	}
	
	public function view($vars)
	{
		$item = $this->db->fetch("
			SELECT i.*, c.category FROM shop_items i
			LEFT JOIN shop_categories c
				ON c.id = i.category_id
			WHERE i.id = ".intval($vars[1]));
			
		include 'view/admin/item.php'; 
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
	
	public function newitem($vars)
	{
		include 'view/admin/newform.php';
	}
	
	public function createitem($vars)
	{
		if(!count($_POST)) redirect302();
		if(in_array('', $_POST)) redirect302();
		
		$file = upload(ROOT.'media/shop/'.$this->ini.'/');
		
		$this->db->query("INSERT INTO shop_items (id, name, description, price, stock, category_id, img)
			VALUES (NULL, 
				'".mysql_real_escape_string($_POST['name'])."', 
				'".mysql_real_escape_string($_POST['description'])."', 
				'".intval($_POST['price'])."', 
				'".intval($_POST['stock'])."', 
				1,
				'".mysql_real_escape_string($file['img'])."'
			)
		");
		
		$id = $this->db->last();
		
		redirect302($this->lf->appurl.$this->ini.'/view/'.$id);
	}
	
	public function addcategory($vars)
	{
		if(count($_POST) > 0)
		{
			$cat = $this->db->fetch("SELECT * FROM shop_categories WHERE category = '".intval($_POST['name'])."'");
			
			if(!$cat)
			{
				$this->db->query("
					INSERT INTO shop_categories (`id`, `parent`,  `category`, `inst`)
					VALUES (NULL, '".intval($_POST['category'])."', '".mysql_real_escape_string($_POST['name'])."', '".mysql_real_escape_string($this->ini)."')
				");
			}
		}
		
		redirect302(); //$this->lf->appurl.$this->ini.'/browse/'.intval($_POST['category']));
	}
	
	public function addchild($vars)
	{
		include 'view/admin/addchild.php';
	}
}