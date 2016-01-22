<?php

namespace lf;

// recurse through inheritance, get list of children.
function get_acl_groups($inherit, $process)
{
	if(!isset($inherit[$process])) return array(); // anon will trigger this
	
	$groups = $inherit[$process]; // $groups = an array of groups inherited by the $process group
	foreach($groups as $group)
		if(isset($inherit[$group]))
			$groups = array_merge( $groups, get_acl_groups($inherit, $group) ); 
	return array_unique($groups);
}

class acl
{
	private $defaultAccess = true;
	
	public function loadAcl()
	{
		(new \lf\cache)->startTimer(__METHOD__);
		
		// inherit
		$inherit = array();
		foreach( (new LfAclInherit)->getAll() as $row )
			$inherit[$row['group']][] = $row['inherits']; // sort output as $group => array($inherit1, $inherit2)
		
		//$user = new \User();
		
		// get a list of groups from inheritance
		$groups = get_acl_groups($inherit, (new \User)->getAccess()); // I think I am getting an anonymous user here, idk why I did that :\
		$groups[] = $user->getAccess();
		$groups[] = $this->api('getuid');
//		$groupsql = "'".implode("', '", $groups)."'"; // and get them ready for SQL
		
		// Build user ACL from above group list and individual rules
		$useracl = array();
		$baseacl = array();
		
/*
		// old query
		$rows = $this->db->fetchall("
			SELECT action, perm FROM lf_acl_user 
			WHERE affects IN ('".$this->api('getuid')."', ".$groupsql.")
		"); // ) AND action = '".implode('/', $this->action)."'
		foreach($rows as $row)
			$userAcl[$row['action']] = $row['perm'];
	*/	
		// new query
		foreach( (new LfAclUser)->->getAllByAffects($groups) as $row )
			$useracl[$row['action']] = $row['perm'];
		
		// build base acl
		$rows = $this->db->fetchall("SELECT action, perm FROM lf_acl_global"); // WHERE action = '".implode('/', $this->action)."'
		foreach( (new LfAclGlobal)->cols('action, perm')->getAll() as $row)
			$baseacl[$row['action']] = $row['perm'];
		
		//$this->baseacl = $baseAcl;
		//$this->auth['acl'] = $userAcl;
		
		$this->base = $baseacl;
		$this->user = $useracl;
		
		(new \lf\cache)->sessSet('acl', $this);
		
		// should make this into magic __call per http://stackoverflow.com/a/3716750
		(new \lf\cache)->endTimer(__METHOD__); 
		
		return $this;
	}
	
	// $access can be true or false;
	public function setDefaultAccess($access)
	{
		$this->defaultAccess = $access;
		return $this;
	}
	
	public function aclTest($action)
	{	// action = 'action/app|var1/var2'
		
		$sessAcl = (new \lf\cache)->sessGet('acl');
		if( is_null($sessAcl) )
		{
			$this->loadAcl();
			$sessAcl = $this;
		}
		
		// pull both ACLs for upcoming comparison
		$baseacl = $sessAcl->base;
		$useracl = $sessAcl->user;
		
		//foreach($actions // TODO: recursive permission search
		
		// if the user has an ACL denying from current action, deny access.
		if(isset($useracl[$action]) && $useracl[$action] == 0)
			return false;
		
		// If a base acl rule says that an action is restricted
		if(isset($baseacl[$action]) && $baseacl[$action] == 0)
			// if user has acl to override the base acl
			if(isset($useracl[$action]) && $useracl[$action] == 1)
				return true;
			else // otherwise, deny per base acl
				return false;
		
		// access is granted by default
		return $this->defaultAccess;
	}