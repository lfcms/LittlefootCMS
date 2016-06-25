<h2 class="no_marbot"><i class="fa fa-paint-brush"></i> Skins Manager</h2>
	
<div class="row">
	<!-- New Skin -->
	<div class="col-3 pull-right">
		<div class="tile white">
			<div class="tile-header">
				<h4><i class="fa fa-plus"></i> Add New</h4>
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
			$iconColor = 'light_gray_fg';
			$title = 'Activate Skin';
			
			if( $skin == \lf\getSetting('default_skin') ){
				$highlight = 'selected';
				$iconColor = 'green_fg';
				$title = 'This Skin is Active';
			}
			if(is_file(ROOT.'skins/'.$skin.'/screenshot.png'))
				$screenshot = \lf\requestGet('LfUrl').'skins/'.$skin.'/screenshot.png';
			else
				$screenshot = 'http://placehold.it/350x500';
				// need to replace with local default image
				
			$readme = is_file(ROOT.'skins/'.$skin.'/readme') ? file_get_contents(ROOT.'skins/'.$skin.'/readme') : 'No readme found.';
		
		?>
			<div class="col-6">
				<div class="tile white <?=$highlight;?>">
					<!-- Skin Title -->
					<div class="tile-header">
						<h4>
							<?=$skin;?>
							<a href="%appurl%setdefault/<?=$skin;?>" class="pull-right"><i class="<?=$iconColor;?> fa fa-power-off" title="<?=$title;?>"></i></a>
						</h4>
					</div>
					
					<!-- <div class="h250 fit">
						<a href="<?=$screenshot;?>"><img class="fit" src="<?=$screenshot;?>"  /></a>
					</div> -->
					<div class="">
						<input type="checkbox" id="<?=$skin;?>-details" name="<?=$skin;?>-details" class="dropdown" />
						<label for="<?=$skin;?>-details">
							<div class="open-content h200 fit">
								<a href="<?=$screenshot;?>" class="block"><img class="fit" src="<?=$screenshot;?>"  /></a>
							</div>
							<div class="drop-content h200">
								<p class="scroll no_mar close-content pad"><?=$readme;?></p>
							</div>
							<div class="tile-content">
								<div class="row fxlarge">
									<div class="col-4">
										<span class="open-content pull-left blue_fg"><i class="fa fa-list"></i> Details</span>
										<span class="close-content pull-left blue_fg"><i class="fa fa-eye"></i> Preview</span>
									</div>
									<div class="col-2">
										<a href="%appurl%edit/<?=$skin;?>/" class="pull-right" title="Edit Skin"><i class="fa fa-pencil-square-o"></i></a>
									</div>
									<div class="col-2">
										<a href="%appurl%zip/<?=$skin;?>/" class="pull-right" title="Zip Skin"><i class="fa fa-file-archive-o"></i></a>
									</div>
									<div class="col-2">
										<a href="%appurl%fork/<?=$skin;?>" class="pull-right" title="Clone Skin"><i class="fa fa-copy"></i></a>
									</div>
									<div class="col-2">
										<a onclick="return confirm('Do you really want to delete this?');" href="%appurl%rm/<?=$skin;?>/" class="x pull-right" title="Delete Skin"><i class="fa fa-trash-o"></i></a>
									</div>
								</div>
							</div>
						</label>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		</div>
	</div>
</div>