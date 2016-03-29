<?php

namespace lf\admin;

/**
 * # ACL Manager
 * 
 * **Action**: The navigation item
 * 
 * **Permission**: The access rule (allow or deny)
 * 
 * **Affects**: The user or group that is affected by this rule
 * 
 * ### Global
 * 
 * Global ACL rules are applied first. They affect all users (including anonymous). 
 * 
 * ### Inherit
 * 
 * You can have one group's or user's ACL ruleset apply to another through inheritance. This allows multilevel permission to be set more efficiently.
 * 
 * ### User
 * 
 * These rules are run last and can override the global ruling. If a page is denied for all users, and I make a rule so `myuser` is granted access to that page, `myuser` will be able to view it without a problem, while the rest remain blocked.
 */
class acl
{
	public $default_method = 'acl_global';
	
	public function user()
	{
		$vars = \lf\requestGet('Param'); // backward compatibility
		
		// Pull links from nav cache
		$nav = file_get_contents(ROOT.'cache/nav.cache.html');
		$nav = str_replace('%baseurl%', '', $nav);
		
		// Extract action list
		preg_match_all('/href="([^"]+)\/"/', $nav, $actions);
	
		// List all Users/Groups
		$result = (new \db\lf_users)
			->cols('id, display_name, access')
			->order('display_name, access')
			->getAll();
		
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
		$vars = \lf\requestGet('Param'); // backward compatibility
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
	
	/**
	 * Needs the prefix because `global()` by itself is a reserved word.
	 */
	public function acl_global()
	{
		$vars = \lf\requestGet('Param'); // backward compatibility
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
		$vars = \lf\requestGet('Param'); // backward compatibility
		echo '<pre>';
		print_r($vars);
		print_r($_POST);
		echo '</pre>';
	}
	
	public function update()
	{
		$vars = \lf\requestGet('Param'); // backward compatibility
		
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
		$vars = \lf\requestGet('Param'); // backward compatibility
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
		$vars = \lf\requestGet('Param'); // backward compatibility
		(new \lf\orm)->query("
			DELETE FROM lf_acl_".(new \lf\orm)->escape($vars[1])."	
			WHERE id = ".intval($vars[2])."
		");
		
		notice('Deleted ACL rule');
		redirect302();
	}
}

?>