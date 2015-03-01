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

		<?php $counter = 0; 

		/*$imglist = array(
			'http://cdn.tripwiremagazine.com/wp-content/uploads/2012/11/church-website-templates1.jpg',
			'http://www.webdesignme.com/wp-content/uploads/revolution-theme-blue.jpg',
			'http://blog.tmimgcdn.com/wp-content/uploads/2010/12/Free-Website-Template.jpg?7e70d4',
			'http://blog.tmimgcdn.com/wp-content/uploads/2011/09/Free-Website-Template2.jpg?7e70d4',
			'http://www.enjin.com/images/games/ffxiv/ffxiv-website-themes.jpg',
			'http://www.opendesigns.org/od/wp-content/designs/34/34854/thumbnail.jpg'
		);*/


			foreach($skins as $skin): 
			
				
		if($counter > 2 && $counter % 2 == 0): ?>

		<?php endif;
				
				
			$highlight = '';
			if($skin == $request->settings['default_skin'])
				$highlight = 'selected';
				
			if(is_file(ROOT.'skins/'.$skin.'/screenshot.png'))
				$screenshot = '%relbase%lf/skins/'.$skin.'/screenshot.png';
			else
				$screenshot = 'http://placehold.it/350x500';
		?>
			<div class="col-6">
				<div class="tile rounded <?=$highlight;?>">
					<!-- Skin Title -->
					<div class="tile-header">
						<h4><?=$skin;?> <a onclick="return confirm('Do you really want to delete this?');" href="%appurl%rm/<?=$skin;?>/" class="x pull-right">x</a></h4>
					</div>
					
					<!-- Screenshot Example 1-->
					<div class="h250">
						<a href="<?=$screenshot;?>"><img class="fit" src="<?=$screenshot;?>"  /></a>
					</div>
					
					<!-- Screenshot Example 2-->
						
					<!--
					
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
					
					-->
					
					<div class="tile-content">
						<div class="row">
							<div class="col-12">
								<p class="h100 scroll no_mar">Details and stuff I know you like these skins yo check it out. Details and stuff I know you like these skins yo check it out. Details and stuff I know you like these skins yo check it out. Details and stuff I know you like these skins yo check it out. Details and stuff I know you like these skins yo check it out. Details and stuff I know you like these skins yo check it out. Details and stuff I know you like these skins yo check it out. Details and stuff I know you like these skins yo check it out. </p>
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
	








