<h2>Settings</h2>
<?=$this->notice();?>
<div class="row">
	<div class="col-7">
		<div class="tile rounded">
			<div class="tile-header light_gray">
				<h3>Options</h3>
			</div>
			<div class="tile-content">
				<form action="%appurl%saveoptions" method="post">
					<div class="row">
						<div class="col-3">
							<label for="rewrite">URL Rewrite:</label>
							<?php foreach($rewrite['options'] as $option):
								$checked = $rewrite['value']==$option?'checked':'';
							?>
							<input id="rewrite" type="radio" <?=$checked;?> name="setting[rewrite]" value="<?=$option;?>" /> <?=ucfirst($option);?>
							<?php endforeach; ?>
						</div>
						<div class="col-3">
							<label for="debug">Debug:</label>
							<?php foreach($debug['options'] as $option):
								$checked = $debug['value']==$option?'checked':'';
							?>
							<input id="debug" type="radio" <?=$checked;?> name="setting[debug]" value="<?=$option;?>" /> <?=ucfirst($option);?>
							<?php endforeach; ?>
						</div>
						<div class="col-3">
							<label for="signup">Sign Up:</label>
							<?php foreach($signup['options'] as $option):
								$checked = $signup['value']==$option?'checked':'';
							?>
							<input id="signup" type="radio" <?=$checked;?> name="setting[signup]" value="<?=$option;?>" /> <?=ucfirst($option);?>
							<?php endforeach; ?>
						</div>
						<div class="col-3">
							<label for="setting[simple_cms]">Simple CMS:</label> 
							<select id="setting[simple_cms]" name="setting[simple_cms]">
								<option value="_lfcms">Full CMS</option>

								<?php foreach($simplecms['options'] as $option): 
									$selected = $simplecms['value'] == $option ? 'selected="selected"' : '';
								?>
								<option <?=$selected;?> value="<?=$option;?>"><?=$option;?></option>
							<?php endforeach; ?>
							</select> 
						</div>
					</div>
					<div class="row">
						<div class="col-6">
							<label for="setting[force_url]">Force URL (empty to not force URL):</label>
							<input id="setting[force_url]" type="text" name="setting[force_url]" size="50" value="<?=isset($force_url)?$force_url:'';?>" />
						</div>
						<div class="col-6">
							<label for="setting[nav_class]">Navigation CSS class:</label>
							<input id="setting[nav_class]" type="text" name="setting[nav_class]" value="<?=$nav_class;?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<input class="blue button" type="submit" value="Save Changes" />
						</div>
					</div>		
				</form>
			</div>
		</div>
		
	</div>
	<div class="col-5 spaced">
		<div class="tile rounded">
			<div class="tile-header light_gray">
				<h3>Version</h3>
			</div>
			<div class="tile-content">
			
				<a class="blue button martop marbot" href="%appurl%lfup/">Upgrade Littlefoot</a>
				
				<h4>Current version: <?=$this->request->api('version');?></h4>
				
				<?php if($newest != $this->request->api('version')): ?>
					<p>Latest version available: <?=$newest;?></p>
				<?php else: ?>
					<p>You are up to date!</p>
				<?php endif; ?>

				<?php if($this->request->api('version') == '1-DEV'): ?> 
					<p><a href="%baseurl%upgradedev">Run lf/system/upgrade.dev.php</a></p>
				<?php endif; ?>
				
				<h4>Restore Old Version</h4>
				<div class="old-version-info">
				<?php if(count($backups)): foreach($backups as $backup => $version): ?>
					<p>
						<?=$version;?> - 
						<a href="%baseurl%restore/<?=$backup;?>/">restore</a> -
						<a href="%baseurl%rm/<?=$backup;?>/" class="delete_item">delete</a>
					</p>
				<?php endforeach; else: ?>
					<p>No system restore points are available.</p>
				<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="tile rounded">
			<div class="tile-header light_gray">
				<h3>Reinstall</h3>
			</div>
			<div class="tile-content">
				<?php if(count($installs)): ?>
				<ul class="efvlist martop marbot">
					<?php foreach($installs as $install): ?>
					
						<li>[<a href="%baseurl%reinstall/<?=$install;?>">reinstall</a>] <?=$install;?><br /></li>
					<?php endforeach; ?>
				</ul>					
				<?php else: ?>
					<p>No apps to reinstall</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>