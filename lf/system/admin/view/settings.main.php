<h2><i class="fa fa-cog"></i> Settings</h2>

<?=notice();?>
<div class="row">
	<div class="col-7">
		<div class="tile white">
			<div class="tile-header">
				<h3><i class="fa fa-cogs"></i> Options</h3>
			</div>
			<div class="tile-content">
				<form action="%appurl%saveoptions" method="post">
					<div class="row">
						<div class="col-8">
							<label for="setting[title]">Site Title:</label>
							
							<input id="setting[title]" type="text" name="setting[title]" size="50" value="<?=isset($title)?$title:'';?>" />
						</div>
						<div class="col-4">
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
					<hr />
					<div class="row">
						<div class="col-8">
							<label for="setting[force_url]" title="empty to not force URL">Force URL:</label>
							<input id="setting[force_url]" type="text" name="setting[force_url]" size="50" value="<?=isset($force_url)?$force_url:'';?>" />
						</div>
						<div class="col-4">
							<label for="setting[nav_class]">Navigation CSS class:</label>
							<input id="setting[nav_class]" type="text" name="setting[nav_class]" value="<?=$nav_class;?>" />
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-4">
							<label for="rewrite">URL Rewrite:</label>
							<?php foreach($rewrite['options'] as $option):
								$checked = $rewrite['value']==$option?'checked':'';
							?>
							<input id="rewrite" type="radio" <?=$checked;?> name="setting[rewrite]" value="<?=$option;?>" /> <?=ucfirst($option);?>
							<?php endforeach; ?>
						</div>
						<div class="col-4">
							<label for="debug">Debug:</label>
							<?php foreach($debug['options'] as $option):
								$checked = $debug['value']==$option?'checked':'';
							?>
							<input id="debug" type="radio" <?=$checked;?> name="setting[debug]" value="<?=$option;?>" /> <?=ucfirst($option);?>
							<?php endforeach; ?>
						</div>
						<div class="col-4">
							<label for="signup">Sign Up:</label>
							<?php foreach($signup['options'] as $option):
								$checked = $signup['value']==$option?'checked':'';
							?>
							<input id="signup" type="radio" <?=$checked;?> name="setting[signup]" value="<?=$option;?>" /> <?=ucfirst($option);?>
							<?php endforeach; ?>
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-4">
							<label for="setting[bots]">Block Search Engines:</label>
							
							<?php foreach($bots['options'] as $option):
								$checked = $bots['value']==$option?'checked':'';
							?>
							<input id="signup" type="radio" <?=$checked;?> name="setting[bots]" value="<?=$option;?>" /> <?=ucfirst($option);?>
							<?php endforeach; ?>
						</div>
					</div>
					<hr />
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
		<div class="tile white">
			<div class="tile-header">
				<h3><i class="fa fa-leaf"></i> Version</h3>
			</div>
			<div class="tile-content">
				<?php 
				
				// modify LF/version to prevent this
				if( $version == 'DEV'): 
				
				?>
				<p>You are running a development environment. Your version is managed by Git.</p>
				<?php else: ?>
				<div class="row">
					<div class="col-12">
						<a class="blue button martop marbot" href="%appurl%lfup/"><i class="fa fa-arrow-up"></i> Upgrade Littlefoot</a>
					</div>
				</div>
				<hr />
				
				<div class="row">
					<div class="col-12">
						<?php $lfVersion = (new \lf\cms)->getVersion(); ?>
						<h4>
							<i class="fa fa-clock-o"></i> 
							Current version: <?=$lfVersion;?>
						</h4>
						
						<?php if($newest != $lfVersion ): ?>
							<p>
								Latest version available: <?=$newest;?>
							</p>
						<?php else: ?>
							<p>You are up to date!</p>
						<?php endif; ?>
						
						<p><a class="green button" href="%appurl%applyUpgrade">Check Database</a> (run this after upgrading if something isnt working)</p>
							
					</div>
				</div>
				<hr /> 
				
				<div class="row">
					<div class="col-12">
						<h4><i class="fa fa-history"></i> Restore Old Version</h4>
						<div class="old-version-info">
							<ul class="efvlist rounded">
								<?php if(count($backups)): 
									foreach($backups as $backup => $bkversion): ?>
									<li>
										<?=$bkversion;?> - 
										<a href="<?=\lf\requestGet('ActionUrl');?>restore/<?=$backup;?>/">restore</a> -
										<a href="<?=\lf\requestGet('ActionUrl');?>rm/<?=$backup;?>/" class="x">delete</a>
									</li>
								<?php endforeach; else: ?>
									<li>No system restore points are available.</li>
								<?php endif; ?>
							</ul>
						</div>
					</div>
				</div>
				<hr />
				<?php endif; ?>
				
				
				<div class="row">
					<div class="col-12">
						<h4><i class="fa fa-refresh"></i> Reinstall Apps</h4>
						<?php if(count($installs)): ?>
						<ul class="efvlist rounded">
							<?php foreach($installs as $install): ?>	
								<li>[<a href="<?=\lf\requestGet('ActionUrl');?>reinstall/<?=$install;?>">reinstall</a>] <?=$install;?><br /></li>
							<?php endforeach; ?>
						</ul>					
						<?php else: ?>
							<p>No apps to reinstall</p>
						<?php endif; ?>
					</div>
				</div>
				<hr />
				
				<div class="row">
					<div class="col-12">
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
			</div>
		</div>
	</div>
</div>