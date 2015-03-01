<?php

if(count($actions)):
	foreach($actions as $action): 
	
		$apps = $this->links[$action['id']];
		$theapp = $apps[0]['app']; // support multi app linking... not in use atm
	
	?>
	
	<a id="nav_<?=$action['id'];?>"></a>
	<div class="tile rounded<?=$this->edit==$action['id']?' active':'';?>">
		<div class="tile-header gray_fg">
			<div class="row">
				<div class="col-9">
					<!-- <?=$action['position'];?>. -->
					(hidden)
					<a href="%appurl%main/<?=$action['id'];?>/#nav_<?=$action['id'];?>">
						<?=$action['label'];?>
					</a> 
				</div>
				<div class="col-2">
					<?php if( is_file(ROOT.'apps/'.$theapp.'/admin.php')): ?>
					
					<a href="%baseurl%dashboard/manage/<?=$theapp;?>/"  class="pull-right">admin</a>
								
					<?php else: ?>
							
					<span class="pull-right">admin</span>
								
					<?php endif; ?>
				</div>
				<div class="col-1">
					<a class="x pull-right" <?=jsprompt('Are you sure?');?> href="%baseurl%apps/rm/<?=$action['id'];?>/">x</a>
				</div>
			</div>
		</div>
		<?php if($this->edit == $action['id']): /* Load form if selected */ ?>
		<div class="tile-content">
			
				<?=$this->partial('dashboard-partial-editform', array('save' => $action));?>
				
			
			
		</div>
		<?php endif; ?>
	</div>	
	
	<?php
	
	/* ?>
	
	<li>
	
		<?php if($this->edit == $action['id']): ?>
		
		<?=$this->partial('dashboard-partial-editform', array('save' => $action));?>
		
		<?php else: ?>
		
		
		<a href="%appurl%main/<?=$action['id'];?>/#nav_<?=$action['alias'];?>">
			<?=$action['alias'];?>
		</a>
		
		<?php if( is_file(ROOT.'apps/'.$theapp.'/admin.php')): ?>
		<a href="%baseurl%dashboard/manage/<?=$theapp;?>/"  class="nav_manage_link">admin</a>
		<?php else: ?>
		admin
		<?php endif; ?>
		
		<a class="x" <?=jsprompt('Are you sure?');?> href="%baseurl%apps/rm/<?=$action['id'];?>/">x</a>
		
		<?php endif; ?>
		
	</li>
	
	<?php */
	endforeach; 
else: ?>

Set positions to 0 to hide from main nav

<?php endif; ?>