<?php

if(!extension_loaded('mysqli'))
{
	echo '<h1>mysqli PHP extension is missing! Install it to use littlefoot.</h1>';
	phpinfo();
	exit();
}

/**
 * @ignore
 */
class install
{
	public function __construct()
	{
		
	}
	
	public function noconfig()
	{
		if(count($_POST))
			$this->post();
		
		$msg = 'No configuration file found at lf/config.php (ignore this if installing for the first time)';
		include LF.'system/lib/recovery/install.form.php';
		
		exit();
	}

	public function test()
	{
		if( (new LfPages)->first() == NULL )
		{
			if(count($_POST))
				$this->post();
			else
				$this->nodb();
			
			exit();
		}
	}
	
	// we tried to db, but couldn't... so nodb
	private function nodb()
	{
		$msg = 'Unable to query database.';
		include ROOT.'system/lib/recovery/install.form.php';
	}
	
	private function post()
	{
		// validate input
		if($_POST['host'] == '') $errors[] = "Missing 'Hostname' information";
		if($_POST['user'] == '') $errors[] = "Missing 'Username' information";
		if($_POST['pass'] == '') $errors[] = "Missing 'Password' information";
		if($_POST['dbname'] == '') $errors[] = "Missing 'Database Name' information";
		if($_POST['auser'] == '') $errors[] = "Missing 'Admin Username' information";
		if($_POST['apass'] == '') $errors[] = "Missing 'Admin Password' information";

		if(isset($warnings) && !isset($_POST['warning_check']))
		{
			$errors[] = 'Warnings have been detected. Fix them or check the box to ignore them.';
		}

		if(isset($errors))
		{
			include LF.'system/lib/recovery/install.form.php';
			exit();
		}

		$conf = file_get_contents('config-dist.php');
		$replace = array(
			'localhost' => $_POST['host'],
			'mysql_user' => $_POST['user'],
			'mysql_passwd' => $_POST['pass'],
			'mysql_database' => $_POST['dbname'],
		);
		
		foreach($replace as $from => $to)
		{
			if($to == '') 
				$err = true;

			$conf = str_replace($from, $to, $conf);
		}

		if(!is_file('config.php') || (isset($_POST['overwrite']) && $_POST['overwrite'] == 'on'))
			file_put_contents('config.php', $conf);

		if(isset($_POST['data']) && $_POST['data'] == 'on' && is_file('config.php'))
		{
			$dbconn = db::init();

			if($dbconn->error != '')
					$errors = $dbconn->error;
			else
			{
				// run import script
				echo $dbconn->import(ROOT.'system/lib/recovery/lf.sql', false);
				
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
