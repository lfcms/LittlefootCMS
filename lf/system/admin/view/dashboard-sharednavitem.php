<?php $edit = \lf\get('edit'); ?>
<a id="nav_<?=$action['id'];?>"></a>
<div class="tile white<?=$edit==$action['id']?' active':'';?>">
	<div class="tile-header gray_fg fxlarge">
		<div class="row">
			<div class="col-6">	
				<a href="%appurl%main/<?=$action['id'];?>/#nav_<?=$action['id'];?>">
					<?=$action['position'] ? $prefix.$action['position'] : '<i title="hidden" class="fa fa-user-secret"></i>';?>.
					<?=$action['label'];?>
				</a>
			</div>
			<div class="col-4">
				<span class="pull-right">
				<?php
					$theapp = (new \lf\cms)->getLinks()[$action['id']][0]['app'];
					echo $theapp;
					if( is_file(LF.'apps/'.$theapp.'/admin.php')):?>
						<a href="<?= \lf\requestGet('AdminUrl');?>apps/<?=$theapp;?>/"><i class="fa fa-arrow-right"></i></a>
					<? else: ?>
						<span><i class="fa fa-arrow-right"></i></span>
					<? endif; ?>
				</span>
			</div>
			<div class="col-1">
				<a class="pull-right" href="%appurl%wysiwyg/<?=$action['id'];?>" title="WYSIWYG Editor"><i class="fa fa-pencil-square-o"></i></a>
				</span>
			</div>
			<div class="col-1">
				<a class="x pull-right" <?=jsprompt('Are you sure?');?> href="<?= \lf\requestGet('AdminUrl');?>dashboard/rm/<?=$action['id'];?>/"><i class="fa fa-trash-o"></i></a>
			</div>
		</div>
	</div>
	
	<?php if(isset(\lf\requestGet('Param')[1]) && \lf\requestGet('Param')[1] == $action['id']): /* Load form if selected */ ?>
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