<h2><i class="fa fa-paint-brush"></i> Skins Manager</h2>

<div class="row">
	<!-- New Skin -->
	<div class="col-3 pull-right">
		<div class="tile white">
			<div class="tile-header">
				<h3><i class="fa fa-plus"></i> Add New</h3>
			</div>
			<div class="tile-content">
				<div class="row">
					<div class="col-12">
						<form id="create_skin_form" action="%appurl%blankskin/" method="post">
							<div class="row no_martop">
								<div class="col-12">
									<input id="create_skin_namebox" type="text" name="name" placeholder="Skin Name" />
								
								</div>
							</div>
							<div class="row no_marbot">
								<div class="col-12">
									<button class="green">Create Skin</button>
								</div>
							</div>
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
			$icon = '';
			
			if($skin == $request->settings['default_skin']){
				$highlight = 'selected';
				$icon = 'fa fa-check';
			}
			if(is_file(ROOT.'skins/'.$skin.'/screenshot.png'))
				$screenshot = '%relbase%lf/skins/'.$skin.'/screenshot.png';
			else
				$screenshot = 'http://placehold.it/350x500';
				// need to replace with local default image
				
			$readme = is_file(ROOT.'skins/'.$skin.'/readme') ? file_get_contents(ROOT.'skins/'.$skin.'/readme') : 'No readme found.';
		
		?>
			<div class="col-6">
				<div class="tile white <?=$highlight;?>">
					<!-- Skin Title -->
					<div class="tile-header">
						<h4><?=$skin;?> <i class="<?=$icon;?> green_fg" title="This skin is set as DEFAULT."></i> <a onclick="return confirm('Do you really want to delete this?');" href="%appurl%rm/<?=$skin;?>/" class="x pull-right"><i class="fa fa-trash-o"></i></a></h4>
					</div>
					
					<div class="h250 fit">
						<a href="<?=$screenshot;?>"><img class="fit" src="<?=$screenshot;?>"  /></a>
					</div>
					
					<div class="tile-content">
						<div class="row">
							<div class="col-12">
								<p class="h100 scroll no_mar"><?=$readme;?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-5">
								<a href="%appurl%setdefault/<?=$skin;?>" class="button green"><i class="fa fa-power-off"></i> Default</a>
							</div>
							<div class="col-4">
								<a href="%appurl%edit/<?=$skin;?>/" class="button blue"><i class="fa fa-pencil-square-o"></i> Edit</a>
							</div>
							<div class="col-3">
								<a href="%appurl%zip/<?=$skin;?>/" class="button"><i class="fa fa-file-archive-o"></i> Zip</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		</div>
	</div>
</div>