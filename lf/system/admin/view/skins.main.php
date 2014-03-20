<div id="avail_skins" class="skin_page_container">
	<h2>Skins Manager</h2>
	<!-- <p>Click a skin to set it as the default. [x] to remove a skin. Zip to download a skin.</p> -->
	<ul class="applist">
	<li id="new_skins">
		<h3>New Skin <a href="%appurl%download/">Store</a></h3>
		<h4>Upload</h4>
		<form id="upload_skin_form" enctype="multipart/form-data" action="%appurl%install/" method="post">
			<input type="hidden" name="MAX_FILE_SIZE" value="55000000" />
			<input id="upload_skin_file" type="file" name="skin" />
			<?php echo $install; ?> <span>(<?php echo ini_get('post_max_size'); ?>/<?php echo ini_get('upload_max_filesize'); ?> Upload Limit)</span>
		</form>
		<h4>Create</h4>
		<form id="create_skin_form" action="%appurl%blankskin/" method="post">
			<input id="create_skin_namebox" type="text" name="name" placeholder="Name your new skin" />
			<input type="submit" value="Create it" />
		</form> 
	</li>
	<?php
	foreach(scandir($pwd) as $file)
	{
		if($file == '.' || $file == '..') continue;
		
		$skin = $pwd.'/'.$file;	
		
		$highlight = 'available_skin';
		if($file == $request->settings['default_skin'])
			$highlight = 'current_skin';
			
		if(is_file($skin.'/index.php') || is_file($skin.'/index.html'))
		{
			?>
			<li class="<?php echo $highlight; ?>">
						<div class="delete">
							<a onclick="return confirm('Do you really want to delete this?');" href="%appurl%rm/<?=$file;?>/">x</a>
						</div>
						<div class="skin-name">
							<a href="%appurl%setdefault/<?php echo $file; ?>"><?=$file;?></a>
						</div>
						<div class="control">
							<a href="%appurl%edit/<?php echo $file; ?>/">edit</a> | 
							<a href="%appurl%zip/<?php echo $file; ?>/">zip</a>
						</div>
						<?php
							if(is_file($skin.'/screenshot.png'))
							{
								echo '<div class="screenshot">
									<a href="%relbase%lf/skins/'.$file.'/screenshot.png" target="_blank">
									<img src="%relbase%lf/skins/'.$file.'/screenshot.png" alt="screenshot" />
									</a>
									</div>';
							}
							else
							{
								echo '<div class="screenshot">
									<span>no screenshot available</span>
									</div>';
							}
						?>
					</li>
			<?php
		}
	}
	?>
	</ul>
</div>
