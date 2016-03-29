<?php // Littlefoot CMS - Copyright (c) 2016, EFLIP LLC. All rights reserved. See license.txt for product license information.
 

namespace lf\admin;

/**
 * # LF Admin Settings class
 * 
 * ### Options
 * 
 * **URL Rewrite**: *Off* will use `/index.php/` in the title and avoid the use of mod_rewrite. *On* will utilize mod_rewrite to work without the `/index.php/` at the start of all requests making the URL much prettier.
 * 
 * **Debug**: Display run times of all systems at the bottom of the source HTML output.
 * 
 * **Sign Up**: Enable Sign Up through a "Sign Up" button to the right of the login form. By default, email confirmation is required for signups.
 * 
 * **SimpleCMS**: By default, the full CMS is loaded as described in [[Dashboard]] Navigation. However, if a specific app is selected, the Navigation Bar will not be displayed and the selected app will load as if it had an alias of `/`. The Admin Dashboard would be nothing but the admin for that app and the other apps admin would become unavaiable and no longer display in the admin nav menu.
 * 
 * **Force URL**: If a URL is specified, it will be forced as the preferred base URL of your littlefoot installation. Ideal for forcing `https://` and `www`.
 * 
 * **Navigation CSS Class**: This will give the root `<ul>` of your Navigation Bar a class as specified here.
 * 
 * ### Version
 * 
 * **Upgrade Littlefoot**: This will download the latest published version of Littlefoot from http://littlefootcms.com/files/upgrade/system-latest.zip
 * 
 * #### Restore Old Version
 * 
 * When you upgrade Littlefoot, the old `lf/system` folder is moved to `lf/backup`.
 * 
 * ### Reinstall
 * 
 * Each app comes with an `install.sql` that is run when it is first installed. If your database runs into any problems, or you pulled the app from git, you can run it via this tool.
 */
class settings
{
	public function main()
	{
		$var = \lf\requestGet('Param');
		
		/* UPGRADE */
		$master = curl_get_contents('http://littlefootcms.com/files/build-release/master');
		$dev = curl_get_contents('http://littlefootcms.com/files/build-release/dev');
		
		$rewrite['options'] = array('on', 'off');
		$rewrite['value'] = 'on';
		if(\lf\getSetting('rewrite') == 'off') 
			$rewrite['value'] = 'off';
		
		$debug['options'] = array('on', 'off');
		$debug['value'] = 'on';
		if(\lf\getSetting('debug') == 'off') 
			$debug['value'] = 'off';
		
		// Load Version
		$version = (new \lf\cms)->getVersion();
		
		$bots['options'] = array('on', 'off');
		$bots['value'] = 'on';
		if(\lf\getSetting('bots') == 'off') 
			$bots['value'] = 'off';
			
		// $release['options'] = array('DEV', 'STABLE');
		// $release['value'] = 'DEV';
		// if(\lf\getSetting('release') == 'STABLE') 
		$release['value'] = 'STABLE';
		
		$newest = $release['value']=='DEV'?$dev:$master;
			
		$signup['options'] = array('on', 'off');
		$signup['value'] = 'on';
		if(\lf\getSetting('signup') == 'off') 
			$signup['value'] = 'off';
		
		/* SIMPLECMS */
		$apps = scandir(LF.'apps'); // get app list
		unset($apps[1], $apps[0]);
		foreach($apps as $app)
		{
			if(is_file(LF.'apps/'.$app.'/index.php'))
				$simplecms['options'][] = $app;
		}
		$simplecms['value'] = \lf\getSetting('simple_cms');
		
		$url = '';
		if(!is_null(\lf\getSetting('force_url')))
			$url = \lf\getSetting('force_url');
		
		$title = '';
		if(!is_null(\lf\getSetting('title')))
			$title = \lf\getSetting('title');
			
		$nav_class = '';
		if(!is_null(\lf\getSetting('nav_class')))
			$nav_class = \lf\getSetting('nav_class');
			 
		// Backup list
		$backups = array();
		if(is_dir(LF.'backup')) { // this really needs to get moved to a model
			$result = scandir(LF.'backup/');
			
			foreach($result as $backup) {
				if($backup == '.' || $backup == '..') continue;
				
				if(is_file(LF.'backup/'.$backup.'/version'))
					$backups[$backup] = file_get_contents(LF.'backup/'.$backup.'/version');
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
	
	public function saveoptions()
	{
		$args = \lf\requestGet('Param');
		
		$oldSettings = (new \lf\cms)->getSettings();
		$newSettings = $_POST['setting'];
		
		foreach($oldSettings as $var => $val)
		{
			if(isset($newSettings[$var]))
			{
				$result = (new \LfSettings)
					->byVar($var)
					->setVal($newSettings[$var])
					//->debug()
					->save();
					
				unset($newSettings[$var]);
			}
		}
		
		
		foreach($newSettings as $var => $val)
		{
			(new \LfSettings)
				->add()
				->setVar($var)
				->setVal($val)
				//->debug()
				->save();
		}
		
		notice('<div class="notice">Options saved</div>');
		redirect302();
		
		/* gotta find a way to do this in ORM 
		if(count($_POST))
		{
			if(isset($_POST['setting']))
			{
				$sql = "UPDATE lf_settings SET val = CASE var";
			
				foreach($_POST['setting'] as $var => $val)
				{
					$sql .= " WHEN '".(new orm)->escape($var)."' THEN '".(new orm)->escape($val)."'";
					$params[] = (new orm)->escape($var);
				}
				
				$sql .= " END WHERE var IN ('".implode("', '", $params)."')";
				(new orm)->query($sql);
				
				notice('Options saved');
			}
		}*/
	}
	
	public function upgradedev()
	{
		$args = \lf\requestGet('Param');
		
		if( (new \lf\cms)->getVersion() == '1-DEV' )
			include LF.'system/upgrade.dev.php';
		
		redirect302();
	}
	
	public function applyUpgrade()
	{
		include LF.'system/lib/recovery/upgrade.php';
	}
	
	public function lfup()
	{
		$var = \lf\requestGet('Param');
		
		if(\lf\getSetting('release') == 'DEV')
			downloadFile('http://littlefootcms.com/files/upgrade/littlefoot/system-dev.zip', LF.'system.zip');
		else
			downloadFile('http://littlefootcms.com/files/upgrade/littlefoot/system.zip', LF.'system.zip');
		
		unset($_SESSION['upgrade']);
		//upgrade();
		
		$time = time();
		if(!is_dir('backup')) mkdir('backup');
		
		if(!is_file(LF.'system.zip'))
		{
			notice(LF.'system.zip does not exist');
		}
		else if(!rename(LF.'system', LF.'backup/system-'.$time)) 
		{
			// if unable to rename...
			notice('Unable to move '.LF.'system to '.LF.'backup/system-'.$time);
		} 
		else
		{
			// unzip into system/
			$file = 'system.zip';
			$dir = ROOT;
			Unzip($dir,$file);
			
			if(!is_dir(LF.'system'))
				notice('Failed to unzip system.zip');
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
				
				notice('Upgraded Littlefoot successfully.');
			}
		}
		
		redirect302();
	}

	public function rm()
	{
		$vars = \lf\requestGet('Param');
		if(!isset($vars[1])) redirect302();
		
		if(is_dir(LF.'backup/'.$vars[1]))
			rrmdir(LF.'backup/'.$vars[1]);
		redirect302();
	}
	
	public function reinstall()
	{
		$args = \lf\requestGet('Param');
		if(!isset($args[1])) return 'invalid request';
		
		if(!preg_match('/^[a-zA-Z0-9_\.]+$/', $args[1], $match))
			return 'bad app specified';
		
		chdir(LF.'apps/'.$match[0]);
		
		(new orm)->import('install.sql');
		
		redirect302();
	}
	
	public function restore($vars)
	{
		$vars = \lf\requestGet('Param');
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