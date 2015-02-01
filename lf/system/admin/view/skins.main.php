<h2>Skins Manager</h2>
<!-- <p>Click a skin to set it as the default. [x] to remove a skin. Zip to download a skin.</p> -->
<ul class="tile">
	<li id="new_skins">
		<h3>New Skin <a href="%appurl%download/">Store</a></h3>
		<h4>Upload</h4>
		<form id="upload_skin_form" enctype="multipart/form-data" action="%appurl%install/" method="post">
			<input type="hidden" name="MAX_FILE_SIZE" value="55000000" />
			<input id="upload_skin_file" type="file" name="skin" />
			<?php echo $install; ?> <span>(<?php echo ini_get('upload_max_filesize'); ?> Upload Limit)</span>
		</form>
		<h4>Create</h4>
		<form id="create_skin_form" action="%appurl%blankskin/" method="post">
			<input id="create_skin_namebox" type="text" name="name" placeholder="Name your new skin" />
			<input type="submit" value="Create Skin" />
		</form> 
	</li>
	<?php foreach($skins as $skin): 
	
		$highlight = 'available_skin';
		if($file == $request->settings['default_skin'])
			$highlight = 'current_skin';
	?>
	<li class="<?=$highlight;?>">
		<div class="delete">
			<a onclick="return confirm('Do you really want to delete this?');" href="%appurl%rm/<?=$skin;?>/" class="delete_item">x</a>
		</div>
		<div class="skin-zip">
			<a href="%appurl%zip/<?=$skin;?>/" class="zip_item">zip</a>
		</div>
		<div class="skin-name">
			<?=$skin;?>
		</div>
		<div class="default">
			<a href="%appurl%setdefault/<?=$skin;?>">Set as Default</a>
		</div>
		<div class="edit">
			<a href="%appurl%edit/<?=$skin;?>/">Edit Skin</a>
		</div>
		<?php if(is_file($skin.'/screenshot.png')): ?>
			<div class="screenshot">
				<a href="%relbase%lf/skins/<?=$skin;?>/screenshot.png" target="_blank">
				<img src="%relbase%lf/skins/<?=$skin;?>/screenshot.png" alt="screenshot" />
				</a>
			</div>
		<?php else: ?>
			<div class="screenshot">
				<span>no screenshot available</span>
			</div>
		<?php endif; ?>
	</li>
	<?php endforeach; ?>
</ul>
