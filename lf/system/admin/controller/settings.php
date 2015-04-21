<?php 

/**
 * @ignore
 */
class settings extends app
{
	public function main($var)
	{
		/* UPGRADE */
		
		$newest = file_get_contents('http://littlefootcms.com/files/build-release/littlefoot/lf/system/version');
		
		$rewrite['options'] = array('on', 'off');
		$rewrite['value'] = 'on';
		if(!isset($this->lf->settings['rewrite']) || $this->lf->settings['rewrite'] == 'off') 
			$rewrite['value'] = 'off';
		
		
		$debug['options'] = array('on', 'off');
		$debug['value'] = 'on';
		if(!isset($this->lf->settings['debug']) || $this->lf->settings['debug'] == 'off') 
			$debug['value'] = 'off';
			
		$signup['options'] = array('on', 'off');
		$signup['value'] = 'on';
		if(!isset($this->lf->settings['signup']) || $this->lf->settings['signup'] == 'off') 
			$signup['value'] = 'off';
		
		/* SIMPLECMS */
		$apps = scandir(ROOT.'apps'); // get app list
		unset($apps[1], $apps[0]);
		foreach($apps as $app)
		{
			if(is_file(ROOT.'apps/'.$app.'/index.php'))
				$simplecms['options'][] = $app;
		}
		$simplecms['value'] = $this->lf->settings['simple_cms'];
		
		$url = '';
		if(isset($this->lf->settings['force_url']))
			$url = $this->lf->settings['force_url'];
			
		$nav_class = '';
		if(isset($this->lf->settings['nav_class']))
			$nav_class = $this->lf->settings['nav_class'];
			
		// Backup list
		$backups = array();
		if(is_dir(ROOT.'backup')) { // this really needs to get moved to a model
			$result = scandir(ROOT.'backup/');
			
			foreach($result as $backup) {
				if($backup == '.' || $backup == '..') continue;
				
				if(is_file(ROOT.'backup/'.$backup.'/version'))
					$backups[$backup] = file_get_contents(ROOT.'backup/'.$backup.'/version');
				else
					continue;
			}
		}
		
		// Reinstall
		$result = glob(ROOT.'apps/*/install.sql');
		$installs = array();
		foreach($result as $install)
		{
			preg_match('/([^\/]+)\/install.sql$/', $install, $match);
			$installs[] = $match[1];
		}
				
		// include view
		include 'view/settings.main.php';
	}
	
	public function saveoptions($args)
	{
		if(count($_POST))
		{
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
				
				$this->notice('Options saved');
			}
			redirect302();
		}
	}
	
	public function upgradedev($args)
	{
		if($this->request->api('version') == '1-DEV')
			include ROOT.'system/upgrade.dev.php';
			
		redirect302();
	}
	
	public function applyUpgrade()
	{
		include LF.'system/lib/recovery/upgrade.php';
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
			$this->notice(ROOT.'system.zip does not exist');
		}
		else if(!rename(ROOT.'system', ROOT.'backup/system-'.$time)) 
		{
			// if unable to rename...
			$this->notice('Unable to move '.ROOT.'system to '.ROOT.'backup/system-'.$time);
		} 
		else
		{
			// unzip into system/
			$file = 'system.zip';
			$dir = ROOT;
			Unzip($dir,$file);
			
			if(!is_dir(ROOT.'system'))
				$this->notice('Failed to unzip system.zip');
			else
			{
				unlink(ROOT.'system.zip');
				/*echo 'Littlefoot update installed. <a href="'.$_SERVER['HTTP'].'">Click here to return to the previous page.</a>';
				exit();*/
				
				/*if(is_file(ROOT.'system/upgrade.php')) {
					// load the upgrade script if present
					include ROOT.'system/upgrade.php'; 
					unlink(ROOT.'system/upgrade.php'); 
					//exit(); 
				}*/
				
				$this->notice('Upgraded Littlefoot successfully.');
			}
		}
		
		redirect302();
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