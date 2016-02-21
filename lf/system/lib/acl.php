<?php

namespace lf;

// recurse through inheritance, get list of children.
function get_inherited($inherit, $process)
{
	if(!isset($inherit[$process])) return array(); // anon will trigger this
	
	$groups = $inherit[$process]; // $groups = an array of groups inherited by the $process group
	foreach($groups as $group)
		if(isset($inherit[$group]))
			$groups = array_merge( $groups, get_inherited($inherit, $group) ); 
	return array_unique($groups);
}

/**
 * Load and test ACL access
 */
class acl
{
	private $defaultAccess = true;
	
	public function load()
	{
		(new \lf\cache)->startTimer(__METHOD__);
		
		// inherit 
		$inherit = array();
		foreach( (new \LfAclInherit)->getAll() as $row )
			$inherit[$row['group']][] = $row['inherits']; // sort output as $group => array($inherit1, $inherit2)
		
		// whoever we are, anon, admin, whatever
		$user = (new User)->fromSession();
		
		// get a list of groups from inheritance
		$groups = get_inherited($inherit, $user->getAccess()); 
		$groups[] = $user->getAccess();
		$groups[] = $user->getUid();
//		$groupsql = "'".implode("', '", $groups)."'"; // and get them ready for SQL
		
		// Build user ACL from above group list and individual rules
		$useracl = array();
		foreach( (new \LfAclUser)->getAllByAffects($groups) as $row )
			$useracl[$row['action']] = $row['perm'];
		
		// Build Global ACL from global rules. These apply, then user rules apply on top of it.
		$baseacl = array();
		foreach( (new \LfAclGlobal)->cols('action, perm')->getAll() as $row)
			$baseacl[$row['action']] = $row['perm'];
		
		$this->base = $baseacl;
		$this->user = $useracl;
		
		(new \lf\cache)->endTimer(__METHOD__); 
		
		return $this;
	}
	
	public function toSession()
	{
		(new \lf\cache)->sessSet('acl', $this);
		return $this;
	}
	
	// $access can be true or false;
	public function setDefaultAccess($access)
	{
		$this->defaultAccess = $access;
		return $this;
	}
	
	public function test($action)
	{	// action = 'action/app|var1/var2'
		
		$sessAcl = (new \lf\cache)->sessGet('acl');
		if( is_null($sessAcl) )
		{
			$this->load();
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
}