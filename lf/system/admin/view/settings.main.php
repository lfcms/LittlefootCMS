<h2><i class="fa fa-cog"></i> Settings</h2>
<?=$this->notice();?>
<div class="row">
	<div class="col-7">
		<div class="tile rounded">
			<div class="tile-header light_gray">
				<h3><i class="fa fa-cogs"></i> Options</h3>
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
							<label for="setting[bots]">Block Search Engines:</label>
							
							<?php foreach($signup['options'] as $option):
								$checked = $setting['value']==$option?'checked':'';
							?>
							<input id="signup" type="radio" <?=$checked;?> name="setting[bots]" value="<?=$option;?>" /> <?=ucfirst($option);?>
							<?php endforeach; ?>
							
						</div>
						<div class="col-6">
							<label for="setting[title]">Site Title:</label>
							
							<input id="setting[title]" type="text" name="setting[title]" size="50" value="<?=isset($force_url)?$force_url:'';?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-6">
							<label for="setting[force_url]" title="empty to not force URL">Force URL:</label>
							<input id="setting[force_url]" type="text" name="setting[force_url]" size="50" value="<?=isset($force_url)?$force_url:'';?>" />
						</div>
						<div class="col-6">
							<label for="setting[nav_class]">Navigation CSS class:</label>
							<input id="setting[nav_class]" type="text" name="setting[nav_class]" value="<?=$nav_class;?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<button class="blue button"><i class="fa fa-floppy-o"></i> Save Changes</button>
						</div>
					</div>		
				</form>
			</div>
		</div>
		
	</div>
	<div class="col-5 spaced">
		<div class="tile rounded">
			<div class="tile-header light_gray">
				<h3><i class="fa fa-leaf"></i> Version</h3>
			</div>
			<div class="tile-content">
				<a class="blue button martop marbot" href="%appurl%lfup/"><i class="fa fa-arrow-up"></i> Upgrade Littlefoot</a>
				
				<h4><i class="fa fa-clock-o"></i> Current version: <?=$this->request->api('version');?></h4>
				
				<?php if($newest != $this->request->api('version')): ?>
					<p>
						Latest version available: <?=$newest;?>
					</p>
				<?php else: ?>
					<p>You are up to date!</p>
				<?php endif; ?>

				<?php if($this->request->api('version') == '1-DEV'): ?> 
					<p><a href="%appurl%upgradedev">Run lf/system/upgrade.dev.php</a></p>
				<?php else: ?> 
					<p><a href="%appurl%applyUpgrade">Run lf/system/lib/recovery/upgrade.php</a></p>
				<?php endif; ?>
				
				<h4><i class="fa fa-history"></i> Restore Old Version</h4>
				<div class="old-version-info">
				<?php if(count($backups)): foreach($backups as $backup => $version): ?>
					<p>
						<?=$version;?> - 
						<a href="%baseurl%restore/<?=$backup;?>/">restore</a> -
						<a href="%baseurl%rm/<?=$backup;?>/" class="x">delete</a>
					</p>
				<?php endforeach; else: ?>
					<p>No system restore points are available.</p>
				<?php endif; ?>
				</div>
				<h4>
					<i class="fa fa-link"></i> Links:
					<a title="Littlefoot Home" href="http://littlefootcms.com/"><i class="fa fa-home"></i></a>
					<a title="Download" href="https://github.com/eflip/LittlefootCMS/archive/master.zip"><i class="fa fa-download"></i></a>
					<a title="Dev Docs" href="http://littlefootcms.com/files/docs/index.html"><i class="fa fa-book"></i></a>
					<a title="GitHub" href="https://github.com/eflip/LittlefootCMS"><i class="fa fa-github"></i></a>
					<a title="Facebook" href="https://www.facebook.com/littlefootcms"><i class="fa fa-facebook"></i></a>
					<a title="Community" href="http://reddit.com/r/littlefoot"><i class="fa fa-reddit"></i></a>
				</h4>
			</div>
		</div>
		<div class="tile rounded">
			<div class="tile-header light_gray">
				<h3><i class="fa fa-refresh"></i> Reinstall</h3>
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