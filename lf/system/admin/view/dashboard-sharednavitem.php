<?php $edit = \lf\get('edit'); ?>
<a id="nav_<?=$action['id'];?>"></a>
<div class="tile white<?=$edit==$action['id']?' active':'';?>">
	<div class="tile-header gray_fg">
		<div class="row">
			<div class="col-4">	
				<a href="%appurl%main/<?=$action['id'];?>/#nav_<?=$action['id'];?>">
					<i class="fa fa-pencil"></i>
					<?=$action['position'] ? $prefix.$action['position'] : '<i title="hidden" class="fa fa-user-secret"></i>';?>.
					<?=$action['label'];?>
				</a>
			</div>
			<div class="col-4">					
				<a href="%appurl%wysiwyg/<?=$action['id'];?>">WYSIWYG</a>
			</div>
			<div class="col-4">
				<span class="pull-right">
					<?php
					
					$theapp = (new \lf\cms)->getLinks()[$action['id']][0]['app'];
					
				
					echo $theapp;
					
					if( is_file(LF.'apps/'.$theapp.'/admin.php')): ?>
						<a href="<?= \lf\requestGet('AdminUrl');?>apps/<?=$theapp;?>/"><i class="fa fa-cog"></i></a>
					<?php else: ?>
						<span><i class="fa fa-cog"></i></span>
					<?php endif; ?>
				|
					<a class="x" <?=jsprompt('Are you sure?');?> href="<?= \lf\requestGet('AdminUrl');?>dashboard/rm/<?=$action['id'];?>/"><i class="fa fa-trash-o"></i></a>
					
					
				</span>
			</div>
		</div>
	</div>
	
	<?php if(\lf\requestGet('Param')[1] == $action['id']): /* Load form if selected */ ?>
	<div class="tile-content">
		<?=(new \lf\cms)
			->partial(
				'dashboard-partial-editform', 
				array(
					'save' => $action
				));?>
	</div>
	<?php endif; ?>
</div>