<?php 

/**
 * @ignore
 */
class settings extends app
{
	public function main($var)
	{
		/* UPGRADE */
		$master = curl_get_contents('http://littlefootcms.com/files/build-release/master');
		$dev = curl_get_contents('http://littlefootcms.com/files/build-release/dev');
		
		$rewrite['options'] = array('on', 'off');
		$rewrite['value'] = 'on';
		if(!isset($this->lf->settings['rewrite']) || $this->lf->settings['rewrite'] == 'off') 
			$rewrite['value'] = 'off';
		
		
		$debug['options'] = array('on', 'off');
		$debug['value'] = 'on';
		if(!isset($this->lf->settings['debug']) || $this->lf->settings['debug'] == 'off') 
			$debug['value'] = 'off';
			
		$bots['options'] = array('on', 'off');
		$bots['value'] = 'on';
		if(!isset($this->lf->settings['bots']) || $this->lf->settings['bots'] == 'off') 
			$bots['value'] = 'off';
			
		$release['options'] = array('DEV', 'STABLE');
		$release['value'] = 'DEV';
		if(!isset($this->lf->settings['release']) || $this->lf->settings['release'] == 'STABLE') 
			$release['value'] = 'STABLE';
		
		$newest = $release['value']=='DEV'?$dev:$master;
			
		$signup['options'] = array('on', 'off');
		$signup['value'] = 'on';
		if(!isset($this->lf->settings['signup']) || $this->lf->settings['signup'] == 'off') 
			$signup['value'] = 'off';
		
		/* SIMPLECMS */
		$apps = scandir(LF.'apps'); // get app list
		unset($apps[1], $apps[0]);
		foreach($apps as $app)
		{
			if(is_file(LF.'apps/'.$app.'/index.php'))
				$simplecms['options'][] = $app;
		}
		$simplecms['value'] = $this->lf->settings['simple_cms'];
		
		$url = '';
		if(isset($this->lf->settings['force_url']))
			$url = $this->lf->settings['force_url'];
		
		$title = '';
		if(isset($this->lf->settings['title']))
			$title = $this->lf->settings['title'];
			
		$nav_class = '';
		if(isset($this->lf->settings['nav_class']))
			$nav_class = $this->lf->settings['nav_class'];
			
		// Backup list
		$backups = array();
		if(is_dir(LF.'backup')) { // this really needs to get moved to a model
			$result = scandir(LF.'backup/');
			
			foreach($result as $backup) {
				if($backup == '.' || $backup == '..') continue;
				
				if(is_file(LF.'backup/'.$backup.'/version'))
					$backups[$backup] = curl_get_contents(LF.'backup/'.$backup.'/version');
				else
					continue;
			}
		}
		
		// Reinstall
		$result = glob(LF.'apps/*/install.sql');
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
		$oldSettings = $this->lf->settings;
		$newSettings = $_POST['setting'];
		
		foreach($oldSettings as $var => $val)
		{
			if(isset($newSettings[$var]))
			{
				(new LfSettings)
					->byVar($var)
					->setVal($newSettings[$var])
					->debug()
					->save();
					
				unset($newSettings[$var]);
			}
		}
		
		foreach($newSettings as $var => $val)
		{
			(new LfSettings)
				->add()
				->setVar($var)
				->setVal($val)
				->debug()
				->save();
		}
			
		$this->notice('Options saved');
		redirect302();
		
		/* gotta find a way to do this in ORM 
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
		}*/
	}
	
	public function upgradedev($args)
	{
		if($this->request->api('version') == '1-DEV')
			include LF.'system/upgrade.dev.php';
			
		redirect302();
	}
	
	public function applyUpgrade()
	{
		include LF.'system/lib/recovery/upgrade.php';
	}
	
	public function lfup($var)
	{
		if(isset($this->lf->settings['release']) && $this->lf->settings['release'] == 'DEV')
			downloadFile('http://littlefootcms.com/files/upgrade/littlefoot/littlefoot-dev.zip', LF.'system.zip');
		else
			downloadFile('http://littlefootcms.com/files/upgrade/littlefoot/system.zip', LF.'system.zip');
		
		unset($_SESSION['upgrade']);
		//upgrade();
		
		
		$time = time();
		if(!is_dir('backup')) mkdir('backup');
		
		if(!is_file(LF.'system.zip'))
		{
			$this->notice(LF.'system.zip does not exist');
		}
		else if(!rename(LF.'system', LF.'backup/system-'.$time)) 
		{
			// if unable to rename...
			$this->notice('Unable to move '.LF.'system to '.LF.'backup/system-'.$time);
		} 
		else
		{
			// unzip into system/
			$file = 'system.zip';
			$dir = ROOT;
			Unzip($dir,$file);
			
			if(!is_dir(LF.'system'))
				$this->notice('Failed to unzip system.zip');
			else
			{
				unlink(LF.'system.zip');
				/*echo 'Littlefoot update installed. <a href="'.$_SERVER['HTTP'].'">Click here to return to the previous page.</a>';
				exit();*/
				
				/*if(is_file(LF.'system/upgrade.php')) {
					// load the upgrade script if present
					include LF.'system/upgrade.php'; 
					unlink(LF.'system/upgrade.php'); 
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
		
		if(is_dir(LF.'backup/'.$vars[1]))
			rrmdir(LF.'backup/'.$vars[1]);
		redirect302();
	}
	
	public function reinstall($args)
	{
		if(!isset($args[1])) return 'invalid request';
		
		if(!preg_match('/^[a-zA-Z0-9_\.]+$/', $args[1], $match))
			return 'bad app specified';
		
		chdir(LF.'apps/'.$match[0]);
		
		$this->db->import('install.sql');
		
		redirect302();
	}
	
	public function restore($vars)
	{
		if(!isset($vars[1])) redirect302();
		
		$time = time(); 
		if(!is_dir(LF.'backup'))
			mkdir(LF.'backup');
		if(!rename(LF.'system', LF.'backup/system-'.$time)) // if unable to rename...
			echo 'Unable to move '.LF.'system to '.LF.'backup/system-'.$time; 
		else
		{
			rename(LF.'backup/'.$vars[1], LF.'system');
			
			/*echo 'Littlefoot system/ restored. <a href="'.$_SERVER['HTTP_REFERER'].'">Return to Littlefoot CMS</a>';
			exit();*/
			
			redirect302();
		}
	}
}

?>