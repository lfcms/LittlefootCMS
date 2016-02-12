<div class="row">
	<div class="col-12">
		<h2 class="no_marbot" title="Store: Click an app to install it. Update links will download the latest."><i class="fa fa-shopping-cart"></i> Store</h2>
	</div>
</div>

<?=notice();?>

<div class="row">
	<div class="col-4">
		<h3 class="no_martop">Apps</h3>
		<form action="%appurl%zipfromurl/" method="post">
			<ul class="vlist">
				<li>
					<input type="text" name="url" placeholder="Install .zip from URL" />
				</li>
				<li>
					<input type="text" name="app" placeholder="App Name" />
				</li>
				<li>
					<input type="submit" name="download[app]" value="Download" class="blue" />
				</li>
			</ul>
		</form>
		<!--<h4>Upload</h4>
		<form id="upload_app_form" enctype="multipart/form-data" action="%appurl%install/app/" method="post">
			 <input type="hidden" name="MAX_FILE_SIZE" value="55000000" />
			<input id="upload_skin_file" type="file" name="app" class="marbot"/>
			<?php /*echo $install;*/ ?>
			<!--<span>(<?php echo ini_get('upload_max_filesize'); ?> Upload Limit)</span>
		</form>-->
		<h4>Available Apps</h4>
		<ul class="efvlist white">
		<?php foreach($apps as $app => $ignore): ?> 	
			<li>
			<?php if(!isset($app_files[$app])): ?>
				<a href="%appurl%dlapp/<?=$app;?>/"><?=$app;?></a>
			<?php else: ?>
				<?=$app;?> 
				[<a href="%appurl%dlapp/<?=$app;?>/update/">Update</a>]
			<?php endif; ?>
			</li>
		 <?php endforeach; ?>
		</ul>
	</div>
	<div class="col-4">
		<h3 class="no_martop">Skins</h3>
		<form action="%appurl%zipfromurl/" method="post">
			<ul class="vlist">
				<li>
					<input type="text" name="url" placeholder="Install .zip from URL" />
				</li>
				<li>
					<input type="text" name="app" placeholder="App Name" />
				</li>
				<li>
					<input type="submit" name="download[skin]" value="Download" class="blue" />
				</li>
			</ul>
		</form>
		<!--<h4>Upload</h4>
		<form id="upload_skin_form" enctype="multipart/form-data" action="%appurl%install/skin/" method="post">
			 <input type="hidden" name="MAX_FILE_SIZE" value="55000000" />
			<input id="upload_skin_file" type="file" name="skin" class="marbot"/>
			<?php /*echo $install;*/ ?>
			<!--<span>(<?php echo ini_get('upload_max_filesize'); ?> Upload Limit)</span>
		</form>-->
		<h4>Available Skins</h4>
		<ul class="efvlist white">
		<?php foreach($skins as $skin => $ignore): ?> 	
			<li>
			<?php if(!isset($skin_files[$skin])): ?>
				<a href="%appurl%dlskin/<?=$skin;?>/"><?=$skin;?></a>
			<?php else: ?>
				<?=$skin;?> [<a href="%appurl%dlskin/<?=$skin;?>/update/">Update</a>]
			</li>
			<?php endif; ?>
		 <?php endforeach; ?>
		</ul>
	</div>
	<div class="col-4">
		<h3 class="no_martop">Plugins</h3>
		<form action="%appurl%zipfromurl/" method="post">
			<ul class="vlist">
				<li>
					<input type="text" name="url" placeholder="Install .zip from URL" />
				</li>
				<li>
					<input type="text" name="app" placeholder="App Name" />
				</li>
				<li>
					<input type="submit" name="download[plugin]" value="Download" class="blue" />
				</li>
			</ul>
		</form>
		<!--<h4>Upload</h4>
		<form id="upload_plugin_form" enctype="multipart/form-data" action="%appurl%install/plugin/" method="post">
			 <input type="hidden" name="MAX_FILE_SIZE" value="55000000" />
			<input id="upload_skin_file" type="file" name="plugin" class="marbot"/>
			<?php /*echo $install;*/ ?>
			<span>(<?php echo ini_get('upload_max_filesize'); ?> Upload Limit)</span>
		</form>-->
		<h4>Available Plugins</h4>
		<ul class="efvlist white">
		<?php foreach($plugins as $plugin => $ignore): ?> 	
			<li>
			<?php if(!isset($plugin_files[$plugin])): ?>
				<a href="%appurl%dlplugin/<?=$plugin;?>/"><?=$plugin;?></a>
			<?php else: ?>
				<?=$plugin;?> [<a href="%appurl%dlplugin/<?=$plugin;?>/update/">Update</a>]
			</li>
			<?php endif; ?>
		 <?php endforeach; ?>
		</ul>	
	</div>
</div>






