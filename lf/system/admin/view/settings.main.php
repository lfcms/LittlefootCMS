<h2>Settings</h2>
<div class="row">
	<div class="col-6">	
		<form action="?" method="post">
			<ul class="efvlist rounded">
				<li>
					<label for="rewrite">URL Rewrite:</label>
					<?php foreach($rewrite['options'] as $option):
						$checked = $rewrite['value']==$option?'checked':'';
					?>
					<input id="rewrite" type="radio" <?=$checked;?> name="rewrite" value="<?=$option;?>" /> <?=ucfirst($option);?>
					<?php endforeach; ?>
				</li>
				<li>
					<label for="debug">Debug</label>
					<?php foreach($debug['options'] as $option):
						$checked = $debug['value']==$option?'checked':'';
					?>
					<input id="debug" type="radio" <?=$checked;?> name="debug" value="<?=$option;?>" /> <?=ucfirst($option);?>
					<?php endforeach; ?>
				</li>
				<li>
					<label for="signup">Sign Up</label>
					<?php foreach($signup['options'] as $option):
						$checked = $signup['value']==$option?'checked':'';
					?>
					<input id="signup" type="radio" <?=$checked;?> name="signup" value="<?=$option;?>" /> <?=ucfirst($option);?>
					<?php endforeach; ?>
				</li>
				<li>
					<label for="setting[simple_cms]">Simple CMS: (works, but no option for ini yet)</label> 
					<select id="setting[simple_cms]" name="setting[simple_cms]">
						<option value="_lfcms">Full CMS</option>

						<?php foreach($simplecms['options'] as $option): 
							$selected = $simplecms['value'] == $option ? 'selected="selected"' : '';
						?>
						<option <?=$selected;?> value="<?=$option;?>"><?=$option;?></option>
					<?php endforeach; ?>
					</select> 
				</li>
				<li>
					<label for="setting[force_url]">Force URL (empty to not force URL):</label>
					<input id="setting[force_url]" type="text" name="setting[force_url]" size="50" value="<?=$force_url;?>" />
				</li>
				<li>
					<label for="setting[nav_class]">Navigation CSS class:</label>
					<input id="setting[nav_class]" type="text" name="setting[nav_class]" value="<?=$nav_class;?>" />
				</li>
			</ul>
			<input class="blue button martop" type="submit" value="Save Changes" />
		</form>
	</div>
	<div class="col-6">
		<ul class="efvlist rounded">
			<li>
				<h4>Current version: <?=$this->request->api('version');?></h4>
				
				<?php if($newest != $this->request->api('version')): ?>
					<p>Latest version available: <?=$newest;?></p>
				<?php else: ?>
					<p>You are up to date!</p>
				<?php endif; ?>

				<?php if($this->request->api('version') == '1-DEV'): ?> 
					<p><a href="%baseurl%upgradedev">Run lf/system/upgrade.dev.php</a></p>
				<?php endif; ?>
				
			</li>
			<li>
				<div id="restore">
					<h4>Restore Old Version</h4>
					<div class="old-version-info">
					
					<?php if(is_dir(ROOT.'backup')) {
							$backups = scandir(ROOT.'backup/');
							$backup_count = 0;
							
							foreach($backups as $backup) {
								if($backup == '.' || $backup == '..') continue;
								
								if(is_file(ROOT.'backup/'.$backup.'/version'))
									$version = file_get_contents(ROOT.'backup/'.$backup.'/version');
								else
									continue;
								?>
								
								<p><?=$version;?> - <a href="%baseurl%restore/<?=$backup;?>/">restore</a> -
								<a href="%baseurl%rm/<?=$backup;?>/" class="delete_item">delete</a></p>
								
								<?php
								$backup_count++;
							}
							
							if($backup_count == 0)
								echo '<p>No system restore points are available.</p>';
						}
					?>
					</div>
				</div>
			</li>
			<li>
				<div>
					<h3>Reinstall</h3>
					<?php
					
						$installs = glob(ROOT.'apps/*/install.sql');
						//var_dump($installs);
						foreach($installs as $install)
						{
							preg_match('/([^\/]+)\/install.sql$/', $install, $match);
							//print_r($match);
							
							?>
							
							[<a href="%baseurl%reinstall/<?=$match[1];?>">reinstall</a>] <?=$match[1];?><br />
							
							<?php
						}
						
					?>		
				</div>
			</li>
		</ul>
		<a class="blue button martop" href="%baseurl%lfup/">Upgrade Littlefoot</a>
	</div>
</div>