<h2>Skins Manager</h2>

<div class="tile rounded">
	<div class="tile-header light_gray">
		<h3>New Skin <a href="%appurl%download/">Store</a></h3>
	</div>
	<div class="tile-content">
		<div class="row">
			<div class="col-6">
				<h4>Upload</h4>
				<form id="upload_skin_form" enctype="multipart/form-data" action="%appurl%install/" method="post">
					<input type="hidden" name="MAX_FILE_SIZE" value="55000000" />
					<input id="upload_skin_file" type="file" name="skin" />
					<?php echo $install; ?> <span>(<?php echo ini_get('upload_max_filesize'); ?> Upload Limit)</span>
				</form>
			</div>
			<div class="col-6">
				<h4>Create</h4>
				<form id="create_skin_form" action="%appurl%blankskin/" method="post">
					<input id="create_skin_namebox" type="text" name="name" placeholder="Name your new skin" />
					<input type="submit" value="Create Skin" />
				</form> 
			</div>
		</div>
	</div>
</div>




<div class="row spaced">
	<?php $counter = 0; 

	/*echo '<pre>';
	for($i = 250; $i < 750; $i += 50)
	{
		echo '.lf .h'.$i.',
';
	}
	
	for($i = 250; $i < 750; $i += 50)
	{
		echo '.lf .h'.$i.' { height: '.$i.'px }
';
	}
	echo '</pre>';*/
	
	
	
$imglist = array(
	'http://cdn.tripwiremagazine.com/wp-content/uploads/2012/11/church-website-templates1.jpg',
	'http://www.webdesignme.com/wp-content/uploads/revolution-theme-blue.jpg',
	'http://blog.tmimgcdn.com/wp-content/uploads/2010/12/Free-Website-Template.jpg?7e70d4',
	'http://blog.tmimgcdn.com/wp-content/uploads/2011/09/Free-Website-Template2.jpg?7e70d4',
	'http://www.enjin.com/images/games/ffxiv/ffxiv-website-themes.jpg',
	'http://www.opendesigns.org/od/wp-content/designs/34/34854/thumbnail.jpg'
);


	foreach($skins as $skin): 
	
		
if($counter > 3 && $counter % 3 == 0): ?>
</div>
<div class="row spaced">
<?php endif;
		
		
		$highlight = '';
		if($skin == $request->settings['default_skin'])
			$highlight = 'selected';
			
		$counter++;
	?>
	<div class="col-4">
		<div class="tile rounded <?=$highlight;?>">
			<div class="tile-header light_gray">
				<h4><?=$skin;?> <a onclick="return confirm('Do you really want to delete this?');" href="%appurl%rm/<?=$skin;?>/" class="x pull-right">x</a></h4>
			</div>
			<div class="h350">
				<a href="<?=$imglist[$counter%6];?>"><img class="fit" src="<?=$imglist[$counter%6];?>"  /></a>
			</div>
			
			<div class="tile-content">
				<div class="delete">
					<a onclick="return confirm('Do you really want to delete this?');" href="%appurl%rm/<?=$skin;?>/" class="delete_item">x</a>
				</div>
				<div class="skin-zip">
					<a href="%appurl%zip/<?=$skin;?>/" class="zip_item">zip</a>
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
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>





