<?php

// Settings form
$rewrite = 'URL Rewrite:<br />  <select name="setting[rewrite]" id=""><option value="on">on</option><option value="off">off</option></select>';
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
	$signup = str_replace(' value="off"', ' selected="selected" value="off"', $signup);
 
echo '
	<div id="admin_settings">
		<h2>Settings</h2>
		<form action="?" method="post">
			<ul>
				<li><input type="submit" value="Save Changes" /></li>
				<li>'.$rewrite.'</li>
				<li>'.$force_url.'</li>
				<li>'.$navclass.'</li>
				<li>'.$debug.'</li>
				<li>'.$signup.'</li>
				<li>'.$simplecms.' (works, but no option for ini yet)</li>
			</ul>
		</form>
	</div>
	
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