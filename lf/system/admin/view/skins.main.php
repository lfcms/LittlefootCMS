<h2>Skins Manager</h2>

<div class="row">
	<!-- New Skin -->
	<div class="col-3 pull-right">
		<div class="tile rounded">
			<div class="tile-header gray light">
				<h3>New Skin</h3>
			</div>
			<div class="tile-content">
				<div class="row">
					<div class="col-12">
						<h4 class="no_martop">Upload</h4>
						<form id="upload_skin_form" enctype="multipart/form-data" action="%appurl%install/" method="post">
							<input type="hidden" name="MAX_FILE_SIZE" value="55000000" />
							<input id="upload_skin_file" type="file" name="skin" class="marbot"/>
							<?php echo $install; ?>
							<!--<span>(<?php echo ini_get('upload_max_filesize'); ?> Upload Limit)</span>-->
						</form>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h4 class="no_martop">Download</h4>
						<a href="%appurl%download/" class="blue button">Store</a>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h4 class="no_martop">Create</h4>
						<form id="create_skin_form" action="%appurl%blankskin/" method="post">
							<input id="create_skin_namebox" type="text" name="name" placeholder="Name your new skin" />
							<button class="green">Create Skin</button>
						</form> 
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Installed Skins -->
	<div class="col-9">
		<div class="row spaced no_martop">
		<?php foreach($skins as $skin):
					
			$highlight = '';
			if($skin == $request->settings['default_skin'])
				$highlight = 'selected';
				
			if(is_file(ROOT.'skins/'.$skin.'/screenshot.png'))
				$screenshot = '%relbase%lf/skins/'.$skin.'/screenshot.png';
			else
				$screenshot = 'http://placehold.it/350x500';
				// need to replace with local default image
				
			$readme = is_file(ROOT.'skins/'.$skin.'/readme') ? file_get_contents(ROOT.'skins/'.$skin.'/readme') : 'No readme found.';
		
		?>
			<div class="col-6">
				<div class="tile rounded <?=$highlight;?>">
					<!-- Skin Title -->
					<div class="tile-header">
						<h4><?=$skin;?> <a onclick="return confirm('Do you really want to delete this?');" href="%appurl%rm/<?=$skin;?>/" class="x pull-right">x</a></h4>
					</div>
					
					<div class="h250">
						<a href="<?=$screenshot;?>"><img class="fit" src="<?=$screenshot;?>"  /></a>
					</div>
					
					<div class="tile-content">
						<div class="row">
							<div class="col-12">
								<p class="h100 scroll no_mar"><?=$readme;?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-6">
								<a href="%appurl%setdefault/<?=$skin;?>" class="button green">default</a>
							</div>
							<div class="col-3">
								<a href="%appurl%edit/<?=$skin;?>/" class="button blue">edit</a>
							</div>
							<div class="col-3">
								<a href="%appurl%zip/<?=$skin;?>/" class="button">zip</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		</div>
	</div>
</div>
	








