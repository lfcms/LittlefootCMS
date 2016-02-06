<?php

if( is_null( \lf\get('subalias') ) )
	\lf\set('subalias', '');

if(!isset($parent)) $parent = '-1';
if(!isset($prefix)) $prefix="";
if(isset($actions[$parent])):
	foreach($actions[$parent] as $action): 
	
		$apps = $this->links[$action['id']];
		$theapp = $apps[0]['app']; // support multi app linking... not in use atm
		
		if(\lf\get('edit') == $action['id'])
		{
			\lf\set('subalias', 
				str_replace( 
					'value="'.$action['parent'].'"', 
					'selected="selected" value="'.$action['parent'].'"', 
					\lf\get('subalias')
				)
			);
		}
		
		\lf\set('subalias', \lf\get('subalias').'<option value="'.$action['id'].'"> '.$prefix.$action['position'].' '.$action['label'].'</option>');
		
		include 'view/dashboard-sharednavitem.php';
		
		if(isset($actions[$action['id']])): ?>
		<!-- <ul> uncomment this to cascade -->
			<?=(new \lf\cms)
					->partial( 
						'dashboard-partial-nav', 
						array(
							'actions' => $actions, 
							'parent' => $action['id'], 
							'prefix' => $prefix.$action['position'].'.'
						));?>
		<!-- </ul> uncomment this to cascade -->
		
	<?php endif; ?>
	
	<?php 
	endforeach; 
endif; 