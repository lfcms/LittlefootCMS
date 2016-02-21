<?php

namespace lf\admin;

/**
 * LF Admin Users Manager controller
 */
class users
{	
	public function main()
	{
		$args = \lf\www('Param'); // backward compatibility
		$users = orm::q('lf_users')->order()->getAll();
		$usercount = count($users); 
		include 'view/users.main.php';
	}
	
	public function edit()
	{
		$args = \lf\www('Param'); // backward compatibility
		$user = orm::q('lf_users')->filterByid($args[1])->first();
		include 'view/users.edit.php'; 
	}
	
	## NEW
	public function saveldap()
	{
		$args = \lf\www('Param'); // backward compatibility
			// move this to upgrade
		(new orm)->query('ALTER TABLE lf_settings MODIFY val VARCHAR(128)');
		
		if(!isset($this->lf->settings['ldap']))
		{
			$ldap = (new LfSettings)->add()
				->setVar('ldap')
				->setVal($_POST['ldap'])
				->save();
				
			$this->lf->settings['ldap'] = $ldap->val;
		}
		else
		{
			(new LfSettings)
				->byVar('ldap')
				->setVal($_POST['ldap'])
				->save();
		}
		
		notice('LDAP Set');
		
		redirect302();
	}
	
	public function update()
	{
		$args = \lf\www('Param'); // backward compatibility
		// If a password was provided, apply it.
		if($_POST['pass'] != '')
		{
			if($_POST['pass'] != $_POST['pass2'])
			{
				notice('User Saved');
				redirect302($this->lf->appurl);
			}
			
			$_POST['pass'] = sha1($_POST['pass']);
		}
		// Else, discard
		else unset($_POST['pass']);
		
		unset($_POST['pass2']);
		orm::q('lf_users')->debug()->updateById($args[1], $_POST);
		
		notice('User Saved');
		
		redirect302($this->lf->appurl);
	}
	
	public function newuser()
	{
		$args = \lf\www('Param'); // backward compatibility
		include 'view/users.create.php';
	}
	
	public function create()
	{
		$args = \lf\www('Param'); // backward compatibility
		$postnames = array(
			'user' => "Username",
			'pass' => "Password",
			'pass2' => "Confirm Password",
			'email' => "Email",
			'nick' => "Nickname",
			'group' => "Group",
			'adminpass' => "Admin password"
		);
		
		foreach($postnames as $name => $text)
			if(!isset($_POST[$name]) || $_POST[$name] == '')
				$error[] = "'$text' is empty";
		
		if($_POST['pass'] != $_POST['pass2'])
		{
			$error[] = '"Confirm Password" field does not match "Password"';
		}
		
		if(isset($error))
		{
			notice('Unable to create user:<br/>* '.implode('<br />* ', $error));
			//redirect302();
			echo "ERROR";
		}
		
		// Verify that an admin requested this action
		$sql = "SELECT id FROM lf_users WHERE id = ".(new \lf\user)->fromSession()->getId()." AND pass = '".sha1($_POST['adminpass'])."'";
		$result = (new \lf\orm)->fetch($sql);
		if($result['id'] != (new \lf\user)->fromSession()->getId() )
		{
			notice('Admin password rejected.');
			redirect302();
		}
		
		
		
		
		
		$vars = $_POST;
		$insert = array(
			'user' 			=> (new \lf\orm)->escape($vars['user']),
			'pass' 			=> sha1($vars['pass']),
			'email' 		=> (new \lf\orm)->escape($vars['email']),
			'display_name' 	=> (new \lf\orm)->escape($vars['nick']),
			'hash'			=> '',
			'status'		=> 'valid',
			'access'		=> (new \lf\orm)->escape($vars['group']),
		);
		
		$sql = "
			INSERT INTO 
				lf_users 	( `id`, `last_request`, `".implode('`, `',array_keys($insert))."`)
				VALUES	( NULL, NOW(), '".implode("', '",array_values($insert))."')
		";
		
		(new \lf\orm)->query($sql);
		
		if(isset($_POST['sendmail']))
			mail(
				$_POST['email'], 
				'You have a new account at '.$_SERVER['SERVER_NAME'], 
/*outdented to not break email*/				
'Hello,

You can log in to your new account with the following credentials:

Host: http://'.$_SERVER['SERVER_NAME'].$this->request->relbase.'
User: '.$_POST['user'].'
Pass: '.$_POST['pass'].'

Do not reply to this email. It was generated automatically.', 
'From: noreply@'.$_SERVER['SERVER_NAME']);
		
		notice('User "'.$_POST['user'].'" created');
		
		redirect302($this->lf->appurl);
	}
	
	public function rm()
	{
		$vars = \lf\www('Param');
		$sql = "DELETE FROM lf_users WHERE id = ".intval($vars[1]);
		(new \lf\orm)->query($sql);
		
		redirect302($this->request->appurl);
	}
}

?>