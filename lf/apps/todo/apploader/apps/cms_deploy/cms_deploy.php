<?php

session_start();

/*

__construct is the controller

private functions are the models, loads view at the end.

*/

class cms_deploy
{
	private $dbconn;
	public $html;
	private $model;
	
	function __construct($dbconn = NULL)
	{
		$this->dbconn = $dbconn;
	}
	
	public function session($var)
	{
		if($var[1] == 'logout')
		{
			echo 'Logged out.';
			$_SESSION = array();
		}
		
		// check for post of form
		if(isset($_POST['user']) && isset($_POST['pass']))
		{
			$_SESSION['user'] = $_POST['user'];
			$_SESSION['pass'] = $_POST['pass'];
		}
		
		// check for set session
		if(isset($_SESSION['pass']))
			include('session.logged.php'); // if session set, display user
		else
			include('session.form.php'); // if not set, offer login
	}
	
	public function deploy($var)
	{
		if(!isset($_SESSION['user']))
			return $this->session(0);
		
		switch($var[1])
		{
			case 'add':
				if(count($_POST))
				{
					$this->add_domain();
					echo 'domain added';
				}
		
			default:
				
				include('deploy.listdomains.php');
				include('deploy.form.php');
				break;
		}
	}
	
	
	public function rm($var)
	{
		$db = $this->dbconn;
		
		// Sanatize Due Date entry
		$success = preg_match('/^\d+$/', $var[1], $match);
		
		if($success)
		{
			$sql = "DELETE FROM	ideas_notes WHERE id = '".$match[0]."'";
			$db->query($sql);
		}
		redirect301('http://dev.bioshazard.com/projects/apploader/index.php/cms_deploy/listitems/');
	}
	
	public function update($var)
	{
		$db = $this->dbconn;
		
		$id = is_numeric($var[1]) ? $var[1] : 'NULL';
		
		if($_POST['duedate'] == '')
		{
			$datetime = 'NOW()';
		}
		else
		{
		
			// Sanatize Due Date entry
			$success = preg_match('/^(\d{2}\/\d{2})\/(\d{4})\s(\d{2}\:\d{2})/', $_POST['duedate'], $match);
			$match[1] = str_replace('/', '-', $match[1]);
			$datetime = "'".$match[2].'-'.$match[1].' '.$match[3].":00'";
		}
		
		if($success)
		{
			echo $id;
			if($id == 'NULL')
				$sql = "
					INSERT INTO ideas_notes ( `id`, `note`, `date`, `type` ) 
					VALUES ( NULL, '".mysql_real_escape_string($_POST['content'])."',".$datetime.",'".mysql_real_escape_string($_POST['type'])."')
				";
			else
				$sql = "
					UPDATE ideas_notes SET
						note = '".mysql_real_escape_string($_POST['content'])."',
						date = ".$datetime.",
						type = '".mysql_real_escape_string($_POST['type'])."'
					WHERE
						id = ".$id."
				";
			
			$db->query($sql);
		}
		
		redirect301('http://dev.bioshazard.com/projects/apploader/index.php/cms_deploy/listitems/');
	}
	
	public function app_list($var)
	{
		// Print existing items.
		$data = $this->get_apps('all');
		
		//Check for number
		$edit = is_numeric($var[1]) ? $var[1] : 'all';
		
		// Print to screen
		include 'view.applist.php';
	}
	
	private function add_domain()
	{
		$req = curl_init();                                      # Create Curl Object
		curl_setopt($req, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($req, CURLOPT_RETURNTRANSFER,1);             # Return contents of transfer on curl_exec
		curl_setopt($req, CURLOPT_SSL_VERIFYHOST,0);
		
		if(!preg_match('/^[a-z0-9]+$/', $_POST['user'], $match))
			return 1;
		
		$user = $match[0];
		
		$domain = $user.'.howtofixservers.com';
		
		$dir = 'instantcms/sites/'.$domain;
		
		$query = 'https://'.rawurlencode($_SESSION['user']).':'.rawurlencode($_SESSION['pass']).'@localhost:2083/xml-api/cpanel?user='.rawurlencode($_SESSION['user']).'&xmlin=';
		$xmlin = '<cpanelaction><module>AddonDomain</module><func>addaddondomain</func><args><dir>'.$dir.'</dir><newdomain>'.$domain.'</newdomain><subdomain>'.$user.'</subdomain></args></cpanelaction>';
		
		$query .= rawurlencode($xmlin);
		
		curl_setopt($req, CURLOPT_URL, $query);
        $result = curl_exec($req);
		
		return $result;
	}
	
	private function get_apps($var = 'all')
	{
		$db = $this->dbconn;
		$sql = 'SELECT * FROM ideas_notes ORDER BY type, date ASC';
		if(is_numeric($var))
			$sql .= ' WHERE id = '.$var;
		
		//echo $sql;
		
		$result = $db->query($sql);
		
		$rows = array();
		
		while($row = mysql_fetch_assoc($result))
		{
			$success = preg_match('/^(\d{4})\-(\d{2}\-\d{2})\s(\d{2}\:\d{2})\:\d{2}/', $row['date'], $match);
			$match[2] = str_replace('-', '/', $match[2]);
			$row['date'] = $match[2].'/'.$match[1].' '.$match[3];
			
			$rows[] = $row;
		}
		
		return $rows;
	}
}

?>
