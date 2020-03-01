<?php

namespace lf;

if(!extension_loaded('mysqli'))
{
  // TODO: Better logging management
	echo '<h1>mysqli PHP extension is missing! Install it to use littlefoot.</h1>';
	phpinfo();
	exit();
}

class install {

    public $log = [];

    public function __construct() {
        // Can we connect to the database?
        $orm = (new orm);
        // If not, execute installer
        if(!$orm->connected()) {
            if(count($_POST)) {
                $this->postInstaller();
            } else {
                $this->run();
            }
            // If we weren't connected to the database, the above will print install form or 302 to admin.
            exit();
        }
    }

    public function run()
	{
		// guess form field contents
    // TODO: Guess from ENV first
		$host = 'localhost';
		$dbname = get_current_user().'_lf';
		$user = get_current_user();

		include LF.'system/lib/recovery/install.form.php';
		exit;
	}

  private function postInstaller()
  {
    $this->writeConfig();

    if( isset($_POST['data'])
        && $_POST['data'] == 'on'
        && is_file('config.php') )
      $this->importRecoveryData();

    // consolidate into import operation? test DB connection post-config, pre-import?
    redirect302( requestGet('AdminUrl') );
  }

  public function postValidate()
  {
    if($_POST['db']['host'] == '')   $this->log['err'][] = "Missing 'Database Hostname' information";
    if($_POST['db']['user'] == '')   $this->log['err'][] = "Missing 'Database Username' information";
    //if($_POST['pass'] == '')   $this->log['err'][] = "Missing 'Database Password' information";
    if($_POST['db']['dbname'] == '') $this->log['err'][] = "Missing 'Database Name' information";
    if($_POST['admin']['user'] == '')  $this->log['err'][] = "Missing 'Admin Username' information";
    if($_POST['admin']['pass'] == '')  $this->log['err'][] = "Missing 'Admin Password' information";

    if(isset($this->log['err']))
    {
      $_POST = array();
      return $this->run();
    }

    return $this;
  }

	private function writeConfig()
	{
		$this->postValidate();

		// Take config.php template, replace credentials with $_POST data
		$dbConfigFile = file_get_contents(LF.'config-dist.php');
		$dbCredentials = array(
			'localhost' 		=> $_POST['db']['host'],
			'mysql_user'		=> $_POST['db']['user'],
			'mysql_passwd' 		=> $_POST['db']['pass'],
			'mysql_database' 	=> $_POST['db']['dbname'],
		);

		// Replace keys with values
		$dbConfigFile = str_replace(
			array_keys($dbCredentials),
			array_values($dbCredentials),
			$dbConfigFile);

		// If the config.php is not already there, write it
		if( !is_file(LF.'config.php') || ( isset($_POST['overwrite']) && $_POST['overwrite'] == 'on' ) )
		{
			if(!file_put_contents(LF.'config.php', $dbConfigFile))
			{
				$this->log['err'][] = 'Unable to write to "'.LF.'config.php"';

				// Get permissions and owner of LF folder
				$perms = substr(sprintf('%o', fileperms(LF)), -4);
				$ownerUID = fileowner(LF);

				// Print current ownership
				$this->log['err'][] = '"'.LF.'" Owner: "'.$ownerUID.'", Perms: '.$perms;

				// Print how to fix
				if(extension_loaded('posix'))
				{
					$processUser = posix_getpwuid(posix_geteuid());
					$processUserName = $processUser['name'];
					$this->log['err'][] = "POSIX detected user '$processUserName' needs write access to the lf/ folder.";
				}
				else
				{
					$this->log['err'][] = "PHP module 'POSIX' is not loaded, so I can't auto-detect which user needs write permissions<br />"
						.'"'.LF.'" needs to be writable by the user running this PHP script. Check the system processes to see who owns the process as it runs or find a System Administrator.';
				}
			}
		}

		// Verify that we wound up with a config.php
		if(!is_file(LF.'config.php'))
			$this->log['err'][] = 'Config file missing after write attempt.';

    if(isset($this->log['err']))
		{
			$_POST = array(); // this is so bad... but its all private, so no one should depend on this feature
			return $this->run();
		}
	}

	/**
	 * If we are to import the MySQL data...
	 */
	private function importRecoveryData()
	{
		$admin = $_POST['admin'];
		$_POST = array(); // doing this because in ->runInstaller, there is a ->post() that will loop if this is enabled since I am using ORM again. BAAAAD :C
		// the (new orm) will try to do an import, if it is unable to, the installform will trigger

    // Make sure we can connect now
    $orm = (new orm);
    if(!$orm->connected()) {
      $this->log['err'][] = 'Unable to connect to DB before import';
      echo $this->run();
      exit();
    }

    // Run the default lf.sql
		(new orm)->import(ROOT.'system/lib/recovery/lf.sql', false);

    // Creat new admin user object
		(new user)
			->setDisplay_name(ucfirst($admin['user']))
			->setEmail($admin['email'])
			->setUser($admin['user'])
			->setPass($admin['pass'])
			->setStatus('valid')
			->setAccess('admin')
			->save()       // save to database for future use
			->toSession(); // and auto login as that new user

		$_SESSION['upgrade'] = false;

		return $this;
	}

}
