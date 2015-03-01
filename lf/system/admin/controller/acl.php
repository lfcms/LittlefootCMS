<?php

/**
 * @ignore
 */
class acl extends app
{
	public $default_method = 'user';
	
	public function user($vars)
	{
		// Pull links from nav cache
		$nav = file_get_contents(ROOT.'cache/nav.cache.html');
		$nav = str_replace('%baseurl%', '', $nav);
		
		// Extract action list
		preg_match_all('/href="([^"]+)\/"/', $nav, $actions);
	
		// List all Users/Groups
		$result = orm::q('lf_users')->cols('id, display_name, access')->order('display_name, access')->get();
		
		$users = array();
		$groups = array();
		foreach($result as $user)
		{
			$users[$user['id']] = $user['display_name'];
			$groups[] = $user['access'];
		}
		
		$groups = array_unique($groups);
		
		$acls = orm::q('lf_acl_user')->get();
		
		include 'view/acl.user.php';
	}
	
	public function inherit($vars)
	{
		// Pull links from nav cache
		$nav = file_get_contents(ROOT.'cache/nav.cache.html');
		$nav = str_replace('%baseurl%', '', $nav);
		
		// Extract action list
		preg_match_all('/href="([^"]+)\/"/', $nav, $actions);
	
		// List all Users/Groups
		$result = orm::q('lf_users')->cols('id, display_name, access')->order('display_name, access')->get();
		
		$users = array();
		$groups = array();
		foreach($result as $user)
		{
			$users[$user['id']] = $user['display_name'];
			$groups[] = $user['access'];
		}
		
		$groups = array_unique($groups);
		
		$acls = orm::q('lf_acl_inherit')->get();
	
		include 'view/acl.inherit.php';
	}
	
	public function acl_global($vars)
	{
		// Pull links from nav cache
		$nav = file_get_contents(ROOT.'cache/nav.cache.html');
		$nav = str_replace('%baseurl%', '', $nav);
		
		// Extract action list
		preg_match_all('/href="([^"]+)\/"/', $nav, $actions);
	
		// List all Users/Groups
		$result = orm::q('lf_users')->cols('id, display_name, access')->order('display_name, access')->get();
		
		$users = array();
		$groups = array();
		foreach($result as $user)
		{
			$users[$user['id']] = $user['display_name'];
			$groups[] = $user['access'];
		}
		
		$groups = array_unique($groups);
		
		$acls = orm::q('lf_acl_global ')->get();
	
		include 'view/acl.global.php';
	}
	
	public function edit($vars)
	{
		echo '<pre>';
		print_r($vars);
		print_r($_POST);
		echo '</pre>';
	}
	
	public function update($vars)
	{
		
		echo '<pre>';
		print_r($vars);
		print_r($_POST);
		echo '</pre>';
		
		return;
		
		header("Location: ".$_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function add($vars)
	{
		if($_POST['appurl'] != '') 
			$_POST['action'] = $_POST['action'].'|'.$_POST['appurl'];
		
		unset($_POST['appurl']);
		
		foreach($_POST as $key => $val)
			$_POST[$key] = $this->db->escape($val);
			
		$this->db->query("
			INSERT INTO lf_acl_".$this->db->escape($vars[1])." (`id`, `".implode("`, `", array_keys($_POST))."`)
			VALUES (NULL, '".implode("', '", array_values($_POST))."')
		");
		
		$this->notice('Added ACL rule');
		redirect302();
	}
	
	public function rm($vars)
	{
		$this->db->query("
			DELETE FROM lf_acl_".$this->db->escape($vars[1])."	
			WHERE id = ".intval($vars[2])."
		");
		
		$this->notice('Deleted ACL rule');
		redirect302();
	}
}

?>