<?php

namespace lf;

if(!extension_loaded('mysqli'))
{
	echo '<h1>mysqli PHP extension is missing! Install it to use littlefoot.</h1>';
	phpinfo();
	exit();
}

/**
 * provide installform
 */
class install
{
	private $errors = array();
	
	public function __construct() { }

	public function test()
	{
		if( (new \LfSettings)->first() == NULL )
		{
			if(count($_POST) > 0)
				$this->post();
			else
				$this->nodb();
			
			exit();
		}
	}
	
	public function noconfig()
	{
		
		include LF.'system/lib/recovery/install.form.php';
		
		exit();
	}
	
	// we tried to db, but couldn't... so nodb
	private function nodb()
	{
		notice('<div class="error">Unable to query database.</div>');
		$this->printInstallForm();
	}
	
	public function postValidate()
	{
		if($_POST['host'] == '')   $this->errors[] = "Missing 'Database Hostname' information";
		if($_POST['user'] == '')   $this->errors[] = "Missing 'Database Username' information";
		//if($_POST['pass'] == '')   $this->errors[] = "Missing 'Database Password' information";
		if($_POST['dbname'] == '') $this->errors[] = "Missing 'Database Name' information";
		if($_POST['auser'] == '')  $this->errors[] = "Missing 'Admin Username' information";
		if($_POST['apass'] == '')  $this->errors[] = "Missing 'Admin Password' information";
		
		return $this;
	}
	
	public function configCheck()
	{
		// does a config exist?
		if( is_file( LF.'config.php' ) )
		{
			include LF.'config.php'; // load $db config
			notice('<div class="notice"><i class="fa fa-check"></i> Found config.php</div>');
		}
		else
		{
			notice('<div class="error">First time installation? Ignore this message. Otherwise: config.php not found.</div>');
			(new install)->printInstallForm();
		}
		
		return $this;
	}
	
	public function printInstallForm()
	{
		$host = isset($_POST['host']) ? $_POST['host'] : 'localhost';
		$user = isset($_POST['user']) ? $_POST['user'] : get_current_user();
		$dbname = isset($_POST['dbname']) ? $_POST['dbname'] : get_current_user().'_lf';

		// manage it within the call to install form.
		if(count($_POST) > 0)
			$this->post();

		include LF.'system/lib/recovery/install.form.php';
		return $this;
	}
	
	private function post()
	{
		$this->postValidate();

		if(count($this->errors) > 0) 
			return $this->printInstallForm();
		
		// Take config.php template, replace credentials with $_POST data
		$dbConfigFile = file_get_contents(LF.'config-dist.php');
		$dbCredentials = array(
			'localhost' 		=> $_POST['host'],
			'mysql_user'		=> $_POST['user'],
			'mysql_passwd' 		=> $_POST['pass'],
			'mysql_database' 	=> $_POST['dbname'],
		);
		
		// Loop through database credentials provided, applying them to the configuration template
		foreach($dbCredentials as $variable => $value)
		{
			if($value == '') 
				$this->error[] = "Submitted database value for '$variable' is blank.";

			$dbConfigFile = str_replace($variable, $value, $dbConfigFile);
		}
		
		if(count($this->errors) > 0) 
			redirect302();//return $this->printInstallForm();
		
		// If the config.php is not already there, write it
		if(!is_file(LF.'config.php') || (isset($_POST['overwrite']) && $_POST['overwrite'] == 'on'))
		{
			if(!file_put_contents(LF.'config.php', $dbConfigFile))
			{
				$this->errors[] = 'Unable to write to "'.LF.'config.php"';
				
				// Get permissions and owner of LF folder
				$perms = substr(sprintf('%o', fileperms(LF)), -4);
				$ownerUID = fileowner(LF);
				
				// Print current ownership
				$this->errors[] = '"'.LF.'" Owner: "'.$ownerUID.'", Perms: '.$perms;
				
				// Print how to fix
				if(extension_loaded('posix'))
				{
					$processUser = posix_getpwuid(posix_geteuid());
					$processUserName = $processUser['name'];
					$this->errors[] = "POSIX detected user '$processUserName' needs write access to the lf/ folder.";
				}
				else
				{
					$this->errors[] = "PHP module 'POSIX' is not loaded, so I can't auto-detect which user needs write permissions<br />"
														.'"'.LF.'" needs to be writable by the user running this PHP script. Check the system processes to see who owns the process as it runs.';
				}
			}
		}	
		
		// Verify that we wound up with a config.php
		if(!is_file(LF.'config.php'))
			$this->errors[] = 'Config file missing after write attempt.';

		
// 		pre($_POST);
// 		pre($this->errors);
// 		pre(LF);
// 		pre($dbConfigFile,'var_dump');
// 		pre($dbCredentials);
// 		exit;
		
		if(count($this->errors) > 0) return $this->printInstallForm();
		
		
		
		
		
		
		// If we are to import the MySQL data
		if( isset($_POST['data']) && $_POST['data'] == 'on' && is_file('config.php') )
		{
			// Initialize ORM. This attempts to make a database connection, testing it.
			$orm = new orm();

			// If we had trouble?
			if($orm->error != array())
			{
				// Save Errors in the array
				$this->errors = array_merge($this->errors, $orm->error);
				
				if(count($this->errors) > 0) return $this->printInstallForm();
			}
			else
			{
				// Run the default lf.sql
				echo $orm->import(ROOT.'system/lib/recovery/lf.sql', false);
				
				// Add admin user
				$aUser = $_POST['auser'];	
				$aPass = $_POST['apass'];
				(new User)
					->setAccess('admin')
					->setUser($aUser)
					->setDisplay_name(ucfirst($aUser))
					->setPass($aPass)
					->setStatus('valid')
					->save()
					->toSession(); // and auto login as that user
			}
		}
		
		$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		
		if($url[strlen($url) - 1] != '/')
			$url .= '/'; // ensure trailing slash
		
		redirect302($url.'admin');
	}
}