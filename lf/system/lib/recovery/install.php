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
	public function noconfig()
	{
		if(count($_POST))
			install::installpost();
			
		$msg = 'No configuration file found at lf/config.php (ignore this if installing for the first time)';
		include ROOT.'system/lib/recovery/install.form.php';
		
		exit();
	}

	public function testinstall()
	{
		if(orm::q('lf_settings')->first() == NULL)
		{
			if(count($_POST))
				install::installpost();
			else
				install::nodb();
			
			exit();
		}
	}
	
	private function nodb()
	{
		if($this->db->error != '') $errors = $this->db->error;
		$msg = 'Unable to query database.';
		include ROOT.'system/lib/recovery/install.form.php';
	}
	
	private function installpost()
	{
		// validate input
		if($_POST['host'] == '') $errors[] = "Missing 'Hostname' information";
		if($_POST['user'] == '') $errors[] = "Missing 'Username' information";
		if($_POST['pass'] == '') $errors[] = "Missing 'Password' information";
		if($_POST['dbname'] == '') $errors[] = "Missing 'Database Name' information";

		if(isset($warnings) && !isset($_POST['warning_check']))
		{
			$errors[] = 'Warnings have been detected. Fix them or check the box to ignore them.';
		}

		if(isset($errors))
		{
			include ROOT.'system/lib/recovery/install.form.php';
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
			if($to == '') $err = true;

			$conf = str_replace($from, $to, $conf);
		}

		if(!is_file('config.php') || (isset($_POST['overwrite']) && $_POST['overwrite'] == 'on'))
			file_put_contents('config.php', $conf);

		if(isset($_POST['data']) && $_POST['data'] == 'on' && is_file('config.php'))
		{
			include 'config.php';
			$dbconn = new Database($db);

			if($dbconn->error != '')
					$errors = $dbconn->error;
			else
			{
					echo $dbconn->import(ROOT.'system/lib/recovery/lf.sql', false);

					/*if($dbconn->fetch("select * from lf_settings limit 1"))
							echo 'Data imported. You can <a href="?install=delete">remove the install folder</a>, then login as admin with: <br />
							u: admin<br />
							p: pass<br />
							Make sure you change the password so its more secure';
					else
							echo 'Data import error';*/
			}
		}
		
		redirect302('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'/admin');
	}
}
