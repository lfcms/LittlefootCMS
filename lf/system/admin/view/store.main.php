<h2 title="Store: Click an app to install it. Update links will download the latest version."><i class="fa fa-shopping-cart"></i> Store</h2>

<?=notice();?>

<h3 class="no_martop">Download</h3>
<form action="%appurl%dlFromZipUrl/" method="post">
	<ul class="vlist">
		<li>
			<input type="text" name="download[url]" placeholder="Install .zip from URL" />
		</li>
		<li>
			Type:
			<input type="radio" name="download[type]" value="apps" /> App
			<input type="radio" name="download[type]" value="skins" /> Skin
			<input type="radio" name="download[type]" value="plugins" /> Plugin
		</li>
		<li>
			<input type="submit" value="Download" class="blue" />
		</li>
	</ul>
</form>


<h3 class="no_marbot" title="Repo located at <?=$this->repobase;?>">From Repo</h3>
<div class="row no_martop">
	<div class="col-4">
		<!-- <form action="%appurl%zipfromurl/" method="post">
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
		</form> -->
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
				<a class="pull-right" href="%appurl%dlapp/<?=$app;?>/update/" title="Update"><i class="fa fa-refresh"></i></a>
			<?php endif; ?>
			</li>
		 <?php endforeach; ?>
		</ul>
	</div>
	<div class="col-4">
		<!-- <form action="%appurl%zipfromurl/" method="post">
			<ul class="vlist">
				<li>
					<input type="text" name="url" placeholder="Install .zip from URL" />
				</li>
				<li>
					<input type="text" name="app" placeholder="Skin Name" />
				</li>
				<li>
					<input type="submit" name="download[skin]" value="Download" class="blue" />
				</li>
			</ul>
		</form> -->
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
				<?=$skin;?> <a class="pull-right" href="%appurl%dlskin/<?=$skin;?>/update/" title="Update"><i class="fa fa-refresh"></i></a>
			</li>
			<?php endif; ?>
		 <?php endforeach; ?>
		</ul>
	</div>
	<div class="col-4">
		<!-- <form action="%appurl%zipfromurl/" method="post">
			<ul class="vlist">
				<li>
					<input type="text" name="url" placeholder="Install .zip from URL" />
				</li>
				<li>
					<input type="text" name="app" placeholder="Plugin Name" />
				</li>
				<li>
					<input type="submit" name="download[plugin]" value="Download" class="blue" />
				</li>
			</ul>
		</form> -->
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
				<?=$plugin;?> <a class="pull-right" href="%appurl%dlplugin/<?=$plugin;?>/update/" title="Update"><i class="fa fa-refresh"></a>
			</li>
			<?php endif; ?>
		 <?php endforeach; ?>
		</ul>
	</div>
</div>
