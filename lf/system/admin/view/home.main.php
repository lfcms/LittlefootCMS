<h2><i class="fa fa-dashboard"></i> Dashboard</h2>
<div class="row">
	<div class="col-12">
		<div class="no_martop row">
			<?php foreach($widgets as $app => $widget): ?>
			<div class="col-6">
				<div class="tile white">
				<div class="tile-header">
					<h3><a href="<?=\lf\requestGet('AdminUrl');?>apps/<?=$app;?>"><?=ucfirst($app);?></a></h3>
				</div>
				<div class="tile-content">
					<div class="row">
						<div class="col-12">
							
							
							
				<?=$widget;?>
							
							
							
							
							
							
						</div>
					</div>
				</div>
			</div>
				
				
				
				
			</div>
			<?php endforeach; ?> 
		</div>
	</div>
</div>