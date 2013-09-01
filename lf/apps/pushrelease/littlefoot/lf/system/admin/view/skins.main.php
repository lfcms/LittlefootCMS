<h2>Skin Manager</h2>
<h3>Install Skins</h3>	
<p>Upload a skin in .zip format</p>
<form enctype="multipart/form-data" action="%appurl%install/" method="post">
	<ul>
		<li>
			<input type="hidden" name="MAX_FILE_SIZE" value="55000000" />
			Source: <input type="file" name="skin" />
			(<?php echo ini_get('post_max_size'); ?>/<?php echo ini_get('upload_max_filesize'); ?> Upload Limit)
			<?php echo $install; ?>
		</li>
	</ul>
</form>
<h3>Download Skins</h3> 
<p><a href="%appurl%download/">Download Skins</a></p>
<h3>Available Skins:</h3>
<p>Click a skin to set it as the default. Click the [x] to remove the skin.</p>

	<style type="text/css">	
		.left_header { float: left; }
		.right_header { float: right; }
		.left_header, .right_header { padding: 0 5px; }
		.left_header a, .right_header a { font-size: small; }
	</style>
	
<ul class="applist">
<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information.
foreach(scandir($pwd) as $file)
{
	if($file == '.' || $file == '..') continue;
	
	$skin = $pwd.'/'.$file;	
	
	$highlight = '';
	if($file == $request->settings['default_skin'])
		$highlight = 'border: 1px solid #999; background: #DDD;';
		
	if(is_file($skin.'/index.php'))
	{
		?>
		<li style="padding: 5px; <?php echo $highlight; ?>"> 
					<div class="left_header">
						<a onclick="return confirm('Do you really want to delete this?');" href="%appurl%rm/<?=$file;?>/">x</a>
					</div>
						
					<div class="right_header">
						
						<a href="%appurl%edit/<?php echo $file; ?>/">Edit</a>
					</div>
					<div style="clear:both; padding: 5px;">
						<a href="%appurl%setdefault/<?php echo $file; ?>"><?=$file;?></a>
					<div>
				</li>
		<?php
	}
}
?>
</ul>