<h2 class="no_martop"><i class="fa fa-dashboard"></i> Dashboard</h2>
<div class="row">
	<div class="col-10">
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
	<div class="col-2">

		Navigation Links
		
		<?php foreach($links as $app => $linkArray): ?>
		<h4><?=$app;?></h4>
		<ul class="marbot">
		
		<?php foreach($linkArray as $link): ?>
			<li>
				<a href="<?=\lf\requestGet('AdminUrl').'navigation/id/'.$link['include'];?>"><?=$link['title'];?></a>
			</li>
		<?php	endforeach;  ?>
		</ul>
		<?php	endforeach;  ?>
	</div>
</div>