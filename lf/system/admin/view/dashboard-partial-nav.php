<?php 

if(!isset($this->subalias)) $this->subalias = '';
if(!isset($parent)) $parent = '-1';
if(!isset($this->depth)) $this->depth = 0;
else $this->depth++; // child element

if(isset($actions[$parent])):
	foreach($actions[$parent] as $action): 
	
		$apps = $this->links[$action['id']];
		$theapp = $apps[0]['app']; // support multi app linking... not in use atm
		
		if($this->edit == $action['id'])
		{
			$this->subalias = str_replace(
				'value="'.$action['parent'].'"', 
				'selected="selected" value="'.$action['parent'].'"', 
				$this->subalias);
		}
		
		$this->subalias .= '
			<option '.$selected.' value="'.$action['id'].'">
				'.str_repeat("/ ", $this->depth).$action['alias'].'
			</option>
		';
	?>
	
	<li>
	
		<?php if($this->edit == $action['id']): /* Load form if selected */ ?>
		
		<?=$this->partial('dashboard-partial-editform', array('save' => $action));?>
		
		<?php else: /* Else load normal nav item */ ?>
		
		<?=$action['position'];?>. 
		
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
		
		<?php if(isset($actions[$action['id']])): ?>
		
		<ul>
			<?=$this->partial('dashboard-partial-nav', array('actions' => $actions, 'parent' => $action['id']));?>
		</ul>
		
		<?php endif; ?>
		
	</li>
	
	<?php 
	endforeach; 
endif; 

$this->depth--; // end child element