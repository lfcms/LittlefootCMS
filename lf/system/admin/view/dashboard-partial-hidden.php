<?php

if(count($actions)):
	foreach($actions as $action): 
	
		$apps = $this->links[$action['id']];
		$theapp = $apps[0]['app']; // support multi app linking... not in use atm
		
	?>
	
	<li>
	
		<?php if($this->edit == $action['id']): /* Load form if selected */ ?>
		
		<?=$this->partial('dashboard-partial-editform', array('save' => $action));?>
		
		<?php else: /* Else load normal nav item */ ?>
		
		
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
	
	<?php 
	endforeach; 
else: ?>

Set positions to 0 to hide from main nav

<?php endif; ?>