<?php 

/**
 * @ignore
 */
class settings extends app
{
	public function main($var)
	{
		// Query for settings, save as array[var] = val)
		$sql = 'SELECT * FROM lf_settings ORDER BY var';
		$this->db->query($sql);
		$result = $this->db->fetchall();
		foreach($result as $setting)
			$settings[$setting['var']] = $setting['val'];
			
		if(count($_POST))
		{
			/*if(isset($_POST['newvar']) && isset($_POST['newval']) && $_POST['newval'] != '' && !array_key_exists($_POST['newvar'], $_POST['setting']))
			{
				$sql = "
					INSERT INTO lf_settings (id, var, val)
					VALUES (NULL, '".$this->db->escape($_POST['newvar'])."', '".$this->db->escape($_POST['newval'])."')
				";
				$this->db->query($sql);
			}*/
			
			if(isset($_POST['setting']))
			{
				$sql = "UPDATE lf_settings SET val = CASE var";
			
				foreach($_POST['setting'] as $var => $val)
				{
				
					$sql .= " WHEN '".$this->db->escape($var)."' THEN '".$this->db->escape($val)."'";
					$params[] = $this->db->escape($var);
				}
				
				$sql .= " END WHERE var IN ('".implode("', '", $params)."')";
				
				$this->db->query($sql);
			}
			
			redirect302();
		}
		
		
		/* UPGRADE */
		
		$newest = file_get_contents('http://littlefootcms.com/files/build-release/littlefoot/lf/system/version');
		
		include 'view/settings.main.php';
	}
	
	public function upgradedev($args)
	{
		if($this->request->api('version') == '1-DEV')
			include ROOT.'system/upgrade.dev.php';
			
		redirect302();
	}
	
	public function lfup($var)
	{
		downloadFile('http://littlefootcms.com/files/upgrade/littlefoot/system.zip', ROOT.'system.zip');
		unset($_SESSION['upgrade']);
		//upgrade();
		
		
		$time = time();
		if(!is_dir('backup')) mkdir('backup');
		
		if(!is_file(ROOT.'system.zip'))
		{
			echo ROOT.'system.zip does not exist';
		}
		else if(!rename(ROOT.'system', ROOT.'backup/system-'.$time)) 
		{
			// if unable to rename...
			echo 'Unable to move '.ROOT.'system to '.ROOT.'backup/system-'.$time; 
		} 
		else
		{
			// unzip into system/
			$file = 'system.zip';
			$dir = ROOT;
			Unzip($dir,$file);
			
			if(!is_dir(ROOT.'system'))
				echo 'Failed to unzip system.zip';
			else
			{
				unlink(ROOT.'system.zip');
				/*echo 'Littlefoot update installed. <a href="'.$_SERVER['HTTP'].'">Click here to return to the previous page.</a>';
				exit();*/
				
				if(is_file(ROOT.'system/upgrade.php')) {
					// load the upgrade script if present
					include ROOT.'system/upgrade.php'; 
					unlink(ROOT.'system/upgrade.php'); 
					//exit(); 
				}
				
				redirect302();
			}
		}
		
		
		/*
		//redirect302();
			
		echo 'Littlefoot system/ restored. <a href="'.$_SERVER['HTTP_REFERER'].'">Return to Littlefoot CMS</a>';
		exit();*/
	}

	public function rm($vars)
	{
		if(!isset($vars[1])) redirect302();
		
		if(is_dir(ROOT.'backup/'.$vars[1]))
			rrmdir(ROOT.'backup/'.$vars[1]);
		redirect302();
	}
	
	public function reinstall($args)
	{
		if(!isset($args[1])) return 'invalid request';
		
		if(!preg_match('/^[a-zA-Z0-9_\.]+$/', $args[1], $match))
			return 'bad app specified';
		
		chdir(ROOT.'apps/'.$match[0]);
		
		$this->db->import('install.sql');
		
		redirect302();
	}
	
	public function restore($vars)
	{
		if(!isset($vars[1])) redirect302();
		
		$time = time(); 
		if(!is_dir(ROOT.'backup'))
			mkdir(ROOT.'backup');
		if(!rename(ROOT.'system', ROOT.'backup/system-'.$time)) // if unable to rename...
			echo 'Unable to move '.ROOT.'system to '.ROOT.'backup/system-'.$time; 
		else
		{
			rename(ROOT.'backup/'.$vars[1], ROOT.'system');
			
			/*echo 'Littlefoot system/ restored. <a href="'.$_SERVER['HTTP_REFERER'].'">Return to Littlefoot CMS</a>';
			exit();*/
			
			redirect302();
		}
	}
}

?>