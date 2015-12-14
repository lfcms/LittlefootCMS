<a id="nav_<?=$action['id'];?>"></a>
<div class="tile rounded<?=$this->edit==$action['id']?' active':'';?>">
	<div class="tile-header gray_fg">
		<div class="row">
			<div class="col-4">					
				<?=$action['position'] ? $prefix.$action['position'] : '<i title="hidden" class="fa fa-user-secret"></i>';?>
		
				<a href="%appurl%main/<?=$action['id'];?>/#nav_<?=$action['id'];?>">
					<?=$action['label'];?>
				</a>
			</div>
			<div class="col-8">
				<span class="pull-right">
					(<?=$linkedApp['ini'];?>)
					<?=$linkedApp['app'];?> |
					<?php if( is_file(LF.'apps/'.$theapp.'/admin.php')): ?>
						<a href="%baseurl%apps/<?=$theapp;?>/"><i class="fa fa-keyboard-o"></i></a>
					<?php else: ?>
						<span><i class="fa fa-keyboard-o"></i></span>
					<?php endif; ?>
					
					<a href="%appurl%wysiwyg/<?=$action['id'];?>">
						<i class="fa fa-paint-brush"></i>
					</a>
					
					<a class="x" <?=jsprompt('Are you sure?');?> href="%baseurl%dashboard/rm/<?=$action['id'];?>/"><i class="fa fa-trash"></i></a>
				</span>
			</div>
		</div>
	</div>
	<?php if($this->edit == $action['id']): /* Load form if selected */ ?>
	<div class="tile-content">
		<?=$this->partial('dashboard-partial-editform', array('save' => $action));?>
	</div>
	<?php endif; ?>
</div>