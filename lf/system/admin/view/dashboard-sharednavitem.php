<a id="nav_<?=$action['id'];?>"></a>
<div class="tile rounded<?=$this->edit==$action['id']?' active':'';?>">
	<div class="tile-header gray_fg">
		<div class="row">
			<div class="col-8">					
				<?=$action['position'] ? $prefix.$action['position'] : '(hidden)';?>
		
				<a href="%appurl%main/<?=$action['id'];?>/#nav_<?=$action['id'];?>">
					<?=$action['label'];?>
				</a>
			</div>
			<div class="col-3">
				<span class="pull-right">
					<?=$theapp;?> |
					<?php if( is_file(ROOT.'apps/'.$theapp.'/admin.php')): ?>
						<a href="%baseurl%dashboard/apps/<?=$theapp;?>/">admin</a>
					<?php else: ?>
						<strike>admin</strike>
					<?php endif; ?>
				</span>
			</div>
			<div class="col-1">
				<a class="x pull-right" <?=jsprompt('Are you sure?');?> href="%baseurl%dashboard/rm/<?=$action['id'];?>/">x</a>
			</div>
		</div>
	</div>
	<?php if($this->edit == $action['id']): /* Load form if selected */ ?>
	<div class="tile-content">
		<?=$this->partial('dashboard-partial-editform', array('save' => $action));?>
	</div>
	<?php endif; ?>
</div>