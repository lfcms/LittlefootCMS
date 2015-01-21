<?php

/**
 * @ignore
 */
class upgrade extends app
{
	public function main($var)
	{
		echo '<h1>!!! DEPRECATED !!!</h1>';
		
		echo '<h2>Upgrade Littlefoot</h2>';
		
		echo '<div id="current">';
			echo '<p>Current version: '.$this->request->api('version').'</p>';

			$newest = file_get_contents('http://littlefootcms.com/files/build-release/littlefoot/lf/system/version');
			if($newest != $this->request->api('version'))
				echo '<p>Latest version available: '.$newest.'</p>';
			else
				echo '<p>You are up to date!</p>';
				
			echo '<p>[ <a href="%appurl%lfup/">Upgrade Littlefoot</a> ]</p>';
			echo '</div>';
			echo '<div id="restore">';
			echo '<h3>Restore to old system</h3>';
			
			//if(is_dir(ROOT.'backup/'))
			//{
				$backups = scandir(ROOT.'backup/');
				foreach($backups as $backup)
				{
					if($backup == '.' || $backup == '..') continue;
					
					if(is_file(ROOT.'backup/'.$backup.'/version'))
						$version = file_get_contents(ROOT.'backup/'.$backup.'/version');
					else
						$version = $backup;
					
					echo '[ <a href="%appurl%restore/'.$backup.'/">Restore</a> ] [<a href="%appurl%rm/'.$backup.'/">Delete</a>] '.$version.'<br />';
				}
			//} else echo 'No system restore points are available.';
		echo '</div>';
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
