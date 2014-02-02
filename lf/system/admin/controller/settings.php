<?php 

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
					VALUES (NULL, '".mysql_real_escape_string($_POST['newvar'])."', '".mysql_real_escape_string($_POST['newval'])."')
				";
				$this->db->query($sql);
			}*/
			
			if(isset($_POST['setting']))
			{
				$sql = "UPDATE lf_settings SET val = CASE var";
			
				foreach($_POST['setting'] as $var => $val)
				{
				
					$sql .= " WHEN '".mysql_real_escape_string($var)."' THEN '".mysql_real_escape_string($val)."'";
					$params[] = mysql_real_escape_string($var);
				}
				
				$sql .= " END WHERE var IN ('".implode("', '", $params)."')";
				
				$this->db->query($sql);
			}
			
			redirect302();
		}
		
		
		// Settings form
		$rewrite = 'URL Rewrite:  <select name="setting[rewrite]" id=""><option value="on">on</option><option value="off">off</option></select>';
		if(!isset($settings['rewrite']) || $settings['rewrite'] == 'off')
			$rewrite = str_replace(' value="off"', ' selected="selected" value="off"', $rewrite);
			
		if(!isset($settings['force_url']) || $settings['force_url'] != '')
			$url = $settings['force_url'];
		else $url = '';
		$force_url = 'Force URL (empty to not force URL): <input type="text" name="setting[force_url]" size="50" value="'.$url.'" />';
		
		if(!isset($settings['nav_class']) || $settings['nav_class'] != '')
			$class = $settings['nav_class'];
		else $class = '';
		$navclass = 'Navigation CSS class: <input type="text" name="setting[nav_class]" value="'.$class.'" />';
		
		$debug = 'Debug:  <select name="setting[debug]" id=""><option value="on">on</option><option value="off">off</option></select>';
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
		$simplecms = 'Simple CMS:  <select name="setting[simple_cms]" id="">'.$simple_options.'</select>';
		
		// Settings form
		$signup = 'Enable Signup:  <select name="setting[signup]" id=""><option value="on">on</option><option value="off">off</option></select>';
		if(!isset($settings['signup']) || $settings['signup'] == 'off')
			$signup = str_replace(' value="off"', ' selected="selected" value="off"', $signup);
		 
		echo '
			<div id="admin_settings">
				<h2>Settings</h2>
				<form action="?" method="post">
					<ul>
						<li>'.$rewrite.'</li>
						<li>'.$force_url.'</li>
						<li>'.$navclass.'</li>
						<li>'.$debug.'</li>
						<li>'.$signup.'</li>
						<li>'.$simplecms.' (works, but no option for ini yet)</li>
						<li><input type="submit" value="submit" /></li>
					</ul>
				</form>
			</div>
		';
		
		/* UPGRADE */
		
		$newest = file_get_contents('http://littlefootcms.com/files/build-release/littlefoot/lf/system/version');
		
		echo '
			<div id="admin_upgrade">
				<h2>Upgrade Littlefoot</h2>
				<div id="current">
				<p>Current version: '.$this->request->api('version').'</p>';
			
		if($newest != $this->request->api('version'))
			echo '<p>Latest version available: '.$newest.'</p>';
		else
			echo '<p>You are up to date!</p>';
			
		echo '
				<p>[ <a href="%appurl%lfup/">Upgrade Littlefoot</a> ]</p>
			</div>
			<div id="restore">
				<h3>Restore to old system</h3>';
			
			if(is_dir(ROOT.'backup'))
			{
				$backups = scandir(ROOT.'backup/');
				foreach($backups as $backup)
				{
					if($backup == '.' || $backup == '..') continue;
					
					if(is_file(ROOT.'backup/'.$backup.'/version'))
						$version = file_get_contents(ROOT.'backup/'.$backup.'/version');
					else
						continue;
					
					echo '[ <a href="%appurl%restore/'.$backup.'/">Restore</a> ] [<a href="%appurl%rm/'.$backup.'/">Delete</a>] '.$version.'<br />';
				}
			} else echo 'No system restore points are available.';
		echo '
				</div>
			</div>';
	}
	
	public function lfup($var)
	{
		downloadFile('http://littlefootcms.com/files/upgrade/littlefoot/system.zip', ROOT.'system.zip');
		unset($_SESSION['upgrade']);
		redirect302();
	}

	public function rm($vars)
	{
		if(!isset($vars[1])) redirect302();
		
		if(is_dir(ROOT.'backup/'.$vars[1]))
			rrmdir(ROOT.'backup/'.$vars[1]);
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
			
			echo 'Littlefoot system/ restored. <a href="'.$_SERVER['HTTP_REFERER'].'">Return to Littlefoot CMS</a>';
			exit();
		}
	}
}

?>