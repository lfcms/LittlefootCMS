<?php

namespace lf;

class ldap
{
	public function login($server, $user, $pass)
	{
		$server = json_decode(str_replace("'", '"', $server), true);

		$host = $server['host'];
		$port = $server['port'];
		$basedn = $server['basedn'];

		## Connect to the LDAP server.
		$ds=ldap_connect($host, $port);
		if(!$ds)
		{
			echo "Unable to connect to LDAP server";
			return false;
		}

		## Make it work, because otherwise it won't.
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

		### Bind anonymously
		$r=ldap_bind($ds);     // this is an "anonymous" bind, typically
							   // read-only access

		// Search email entry
		$sr=ldap_search($ds, $basedn, 'cn='.$user);

		if( ldap_count_entries($ds, $sr) == 0 )
		{
			//echo "No entry found for ".$user.'.';
			return false;
		}

		//echo '<img src="data:image/jpeg;base64,'.base64_encode($info['jpegphoto'][0]).'" /><br />';

		$info = ldap_get_entries($ds, $sr);
		$dn = $info[0]['dn']; // get dn of first

		## Bind with credentials for found DN
		$r = ldap_bind($ds, $dn, $_POST['pass']);

		ldap_close($ds);
		
		//// OLD RETURN RESULT
		//if($r)
		//	return $info;
		//
		//return $r;
		
		if($r)
			$ldapResults = $info;
		else
			$ldapResults = $r;
			
		
	
	
	
		// If login succeeds,
		if(!$ldapResults)
		{
			$this->debug[] = 'not in ldap';
			$this->debug[] = $username;
			
			$this->authenticate( $username, $password );
		}
		else
		{
			$this->debug[] = 'ldap login success';
			
			// is he in our database yet? (in case we need to assign access)
			$login = (new \LfUsers)
				->cols('id, user, email, display_name, access')
				->byUser($username)
				->first();

			### If the LDAP login is valid, but we dont have a local user
			if(!$login)
			{
				$this->debug[] = 'ldap valid, adding new user';
				$this->addLdapUser($ldapResults);
			}
		}
	}

	public function addLdapUser($ldapResults)
	{
		$ldapResults = $ldapResults[0];
		(new LfUsers)->add()
			->setUser($ldapResults['cn'][0])
			->setEmail($ldapResults['cn'][0])
			->setStatus('valid')
			->setAccess('user')
			->setDisplay_name($ldapResults['displayname'][0])
			->save();

		$login = (new LfUsers)
			->cols('id, user, email, display_name, access')
			->byUser($ldapResults['cn'][0])
			->first();
	}	
}