<?php 

if(!isset($this->subalias)) $this->subalias = '';
if(!isset($parent)) $parent = '-1';
if(!isset($this->depth)) $this->depth = 0;
else $this->depth++; // child element

if(!isset($prefix)) $prefix="";

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
			<option value="'.$action['id'].'">
				'.$prefix.$action['position'].' '.$action['label'].'
			</option>
		';
		
		include 'view/dashboard-sharednavitem.php';
		
		if(isset($actions[$action['id']])): ?>
		<!-- <ul> -->
			<?=$this->partial('dashboard-partial-nav', array('actions' => $actions, 'parent' => $action['id'], 'prefix' => $prefix.$action['position'].'.'));?>
		<!-- </ul> -->
		
	<?php endif; ?>
	
	<?php 
	endforeach; 
endif; 

$this->depth--; // end child element