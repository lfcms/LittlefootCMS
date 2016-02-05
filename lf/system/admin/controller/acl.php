<?php

/**
 * @ignore
 */
class acl
{
	public $default_method = 'user';
	
	public function user()
	{
		$vars = \lf\www('Param'); // backward compatibility
		
		// Pull links from nav cache
		$nav = file_get_contents(ROOT.'cache/nav.cache.html');
		$nav = str_replace('%baseurl%', '', $nav);
		
		// Extract action list
		preg_match_all('/href="([^"]+)\/"/', $nav, $actions);
	
		// List all Users/Groups
		$result = \lf\orm::q('lf_users')->cols('id, display_name, access')->order('display_name, access')->getAll();
		
		$users = array(0 => 'Anonymous');
		$groups = array();
		foreach($result as $user)
		{
			$users[$user['id']] = $user['display_name'];
			$groups[] = $user['access'];
		}
		
		$groups = array_unique($groups);
		
		$acls = \lf\orm::q('lf_acl_user')->getAll();
		
		include 'view/acl.user.php';
	}
	
	public function inherit()
	{
		$vars = \lf\www('Param'); // backward compatibility
		// Pull links from nav cache
		$nav = file_get_contents(ROOT.'cache/nav.cache.html');
		$nav = str_replace('%baseurl%', '', $nav);
		
		// Extract action list
		preg_match_all('/href="([^"]+)\/"/', $nav, $actions);
	
		// List all Users/Groups
		$result = \lf\orm::q('lf_users')
			->cols('id, display_name, access')
			->order('display_name, access')
			->getAll();
		
		$users = array();
		$groups = array();
		foreach($result as $user)
		{
			$users[$user['id']] = $user['display_name'];
			$groups[] = $user['access'];
		}
		
		$groups = array_unique($groups);
		
		$acls = \lf\orm::q('lf_acl_inherit')->getAll();
	
		include 'view/acl.inherit.php';
	}
	
	public function acl_global()
	{
		$vars = \lf\www('Param'); // backward compatibility
		// Pull links from nav cache
		$nav = file_get_contents(ROOT.'cache/nav.cache.html');
		$nav = str_replace('%baseurl%', '', $nav);
		
		// Extract action list
		preg_match_all('/href="([^"]+)\/"/', $nav, $actions);
	
		// List all Users/Groups
		$result = \lf\orm::q('lf_users')->cols('id, display_name, access')->order('display_name, access')->getAll();
		
		$users = array(0 => 'Anonymous');
		$groups = array();
		foreach($result as $user)
		{
			$users[$user['id']] = $user['display_name'];
			$groups[] = $user['access'];
		}
		
		$groups = array_unique($groups);
		
		$acls = \lf\orm::q('lf_acl_global ')->getAll();
	
		include 'view/acl.global.php';
	}
	
	public function edit()
	{
		$vars = \lf\www('Param'); // backward compatibility
		echo '<pre>';
		print_r($vars);
		print_r($_POST);
		echo '</pre>';
	}
	
	public function update()
	{
		$vars = \lf\www('Param'); // backward compatibility
		
		echo '<pre>';
		print_r($vars);
		print_r($_POST);
		echo '</pre>';
		
		return;
		
		header("Location: ".$_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function add()
	{
		$vars = \lf\www('Param'); // backward compatibility
		if($_POST['appurl'] != '') 
			$_POST['action'] = $_POST['action'].'|'.$_POST['appurl'];
		
		unset($_POST['appurl']);
		
		foreach($_POST as $key => $val)
			$_POST[$key] = (new \lf\orm)->escape($val);
			
		(new \lf\orm)->query("
			INSERT INTO lf_acl_".(new \lf\orm)->escape($vars[1])." (`id`, `".implode("`, `", array_keys($_POST))."`)
			VALUES (NULL, '".implode("', '", array_values($_POST))."')
		");
		
		notice('Added ACL rule');
		redirect302();
	}
	
	public function rm()
	{
		$vars = \lf\www('Param'); // backward compatibility
		(new \lf\orm)->query("
			DELETE FROM lf_acl_".(new \lf\orm)->escape($vars[1])."	
			WHERE id = ".intval($vars[2])."
		");
		
		notice('Deleted ACL rule');
		redirect302();
	}
}

?>