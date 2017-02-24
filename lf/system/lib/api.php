<?php

namespace lf;

// automated resources
class api
{
	public function put($resource, $payload)
	{
		
	}
}

// resources?
class resource_users
{
	public function get($payload = null)
	{
		if( ! isnull($payload) ) { }
		
		return (new LfUsers)->cols('user,email')->json();
	}
}


// lf namespace api. eg,
// '/lf/user/idFromSession'
// '/lf/db/CheckoutLock/setArray'
// /api/user/idFromSession