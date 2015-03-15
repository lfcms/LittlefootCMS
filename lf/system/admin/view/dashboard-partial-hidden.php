<?php

if(count($actions)):
	foreach($actions as $action): 
	
		$apps = $this->links[$action['id']];
		$theapp = $apps[0]['app']; // support multi app linking... not in use atm
	
		include 'view/dashboard-sharednavitem.php';
	/* ?>
	
	<li>
	
		<?php if($this->edit == $action['id']): ?>
		
		<?=$this->partial('dashboard-partial-editform', array('save' => $action));?>
		
		<?php else: ?>
		
		
		<a href="%appurl%main/<?=$action['id'];?>/#nav_<?=$action['alias'];?>">
			<?=$action['alias'];?>
		</a>
		
		<?php if( is_file(ROOT.'apps/'.$theapp.'/admin.php')): ?>
		<a href="%baseurl%apps/<?=$theapp;?>/"  class="nav_manage_link">admin</a>
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