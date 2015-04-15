<h2><i class="fa fa-shopping-cart"></i> Store</h2>

<p>Click an app to install it. Update links will download the latest.</p>
				
<div class="row">
	<div class="col-4">
		<h3>Apps</h3>
		<!--<h4>Upload</h4>
		<form id="upload_app_form" enctype="multipart/form-data" action="%appurl%install/app/" method="post">
			 <input type="hidden" name="MAX_FILE_SIZE" value="55000000" />
			<input id="upload_skin_file" type="file" name="app" class="marbot"/>
			<?php /*echo $install;*/ ?>
			<!--<span>(<?php echo ini_get('upload_max_filesize'); ?> Upload Limit)</span>
		</form>
		<h4>Download</h4>-->
		<ul class="efvlist">
		<?php foreach($apps as $app => $ignore): ?> 	
			<li>
			<?php if(!isset($app_files[$app])): ?>
				<a href="%appurl%dlapp/<?=$app;?>/"><?=$app;?></a>
			<?php else: ?>
				<?=$app;?> [<a href="%appurl%dlapp/<?=$app;?>/update/">Update</a>]
			</li>
			<?php endif; ?>
		 <?php endforeach; ?>
		</ul>
	</div>
	<div class="col-4">
		<h3>Skins</h3>
		<!--<h4>Upload</h4>
		<form id="upload_skin_form" enctype="multipart/form-data" action="%appurl%install/skin/" method="post">
			 <input type="hidden" name="MAX_FILE_SIZE" value="55000000" />
			<input id="upload_skin_file" type="file" name="skin" class="marbot"/>
			<?php /*echo $install;*/ ?>
			<!--<span>(<?php echo ini_get('upload_max_filesize'); ?> Upload Limit)</span>
		</form>
		<h4>Download</h4>-->
		<ul class="fvlist">
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
		<h3>Plugins</h3>
		<!--<h4>Upload</h4>
		<form id="upload_plugin_form" enctype="multipart/form-data" action="%appurl%install/plugin/" method="post">
			 <input type="hidden" name="MAX_FILE_SIZE" value="55000000" />
			<input id="upload_skin_file" type="file" name="plugin" class="marbot"/>
			<?php /*echo $install;*/ ?>
			<span>(<?php echo ini_get('upload_max_filesize'); ?> Upload Limit)</span>
		</form>
		<h4>Download</h4>-->
		<ul class="fvlist">
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






