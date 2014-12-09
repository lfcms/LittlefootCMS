<?php


		
echo '
	<div id="admin_upgrade">
		<div id="current">
		<div class="upgrade-button">
			<a href="%appurl%lfup/">Upgrade Littlefoot</a>
		</div>
		<div class="upgrade-info">
			<h4>Current version: '.$this->request->api('version').'</h4>';
	
if($newest != $this->request->api('version'))
	echo '<p>Latest version available: '.$newest.'</p>';
else
	echo '<p>You are up to date!</p>';

if($this->request->api('version') == '1-DEV')
{
	echo '<p><a href="%appurl%upgradedev">Run lf/system/upgrade.dev.php</a></p>';
}

echo '
		</div>
	</div>
	<div id="restore">
		<h4>Restore Old Version</h4>
		<div class="old-version-info">';
		if(is_dir(ROOT.'backup'))
		{
			$backups = scandir(ROOT.'backup/');
			$backup_count = 0;
			foreach($backups as $backup)
			{
				if($backup == '.' || $backup == '..') continue;
				
				if(is_file(ROOT.'backup/'.$backup.'/version'))
					$version = file_get_contents(ROOT.'backup/'.$backup.'/version');
				else
					continue;
				
				echo '<p>'.$version.' - <a href="%appurl%restore/'.$backup.'/">restore</a> -
				<a href="%appurl%rm/'.$backup.'/" class="delete_item">delete</a></p>';
				
				$backup_count++;
			} 
			
			if($backup_count == 0)
				echo '<p>No system restore points are available.</p>';
		}
		echo '
		</div>
	</div>
	<div>
		<h3>APP MANAGEMENT</h3>
	</div>
	
</div>';
		
?>