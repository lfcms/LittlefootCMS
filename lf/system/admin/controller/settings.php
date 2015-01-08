<?php 

/**
 * @ignore
 */
class settings extends app
{
	public function main($var)
	{
		// Query for settings, save as array[var] = val)
		/*$sql = 'SELECT * FROM lf_settings ORDER BY var';
		$this->db->query($sql);
		$result = $this->db->fetchall();
		foreach($result as $setting)
			$settings[$setting['var']] = $setting['val'];*/
			
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
			
		/*
		$rewrote = '';
		
		// Setup settings (should really be in a model
		if(!isset($settings['rewrite']) || $settings['rewrite'] == 'off')
		{
			$rewrote = ' (if you <a target="_blank" href="'.str_replace('/index.php', '', $this->lf->base.'settings/').'">click here</a> and get 404, this should remain off)';
		}


		/*$rewrite = 'URL Rewrite:'.$rewrote.'<br />  <select name="setting[rewrite]" id=""><option value="on">on</option><option value="off">off</option></select>';
		if(!isset($settings['rewrite']) || $settings['rewrite'] == 'off')
			$rewrite = str_replace(' value="off"', ' selected="selected" value="off"', $rewrite);
			
		if(!isset($settings['force_url']) || $settings['force_url'] != '')
			$url = $settings['force_url'];
		else $url = '';
		$force_url = 'Force URL (empty to not force URL):<br /> <input type="text" name="setting[force_url]" size="50" value="'.$url.'" />';

		if(!isset($settings['nav_class']) || $settings['nav_class'] != '')
			$class = $settings['nav_class'];
		else $class = '';
		$navclass = 'Navigation CSS class:<br /> <input type="text" name="setting[nav_class]" value="'.$class.'" />';

		$debug = 'Debug:<br />  <select name="setting[debug]" id=""><option value="on">on</option><option value="off">off</option></select>';
		if(!isset($settings['debug']) || $settings['debug'] == 'off')
			$debug = str_replace(' value="off"', ' selected="selected" value="off"', $debug);

		$apps = scandir(ROOT.'apps'); // get app list
		unset($apps[1], $apps[0]);
		$simple_options = '<option value="_lfcms">Full CMS</option>';
		foreach($apps as $app)
		{
			if(is_file(ROOT.'apps/'.$app.'/index.php'))
				$simple_options .= '<option value="'.$app.'">'.$app.'</option>';
		}
		$simple_options = str_replace(' value="'.$settings['simple_cms'].'"', ' selected="selected" value="'.$settings['simple_cms'].'"', $simple_options);
		$simplecms = 'Simple CMS: <br /> <select name="setting[simple_cms]" id="">'.$simple_options.'</select>';

		// Settings form
		$signup = 'Enable Signup:<br />  <select name="setting[signup]" id=""><option value="on">on</option><option value="off">off</option></select>';
		if(!isset($settings['signup']) || $settings['signup'] == 'off')
			$signup = str_replace(' value="off"', ' selected="selected" value="off"', $signup);*/
		
		// include view
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