<h2> <i class="fa fa-compass"></i> Navigation </h2>

<?=notice();?>

<div class="row no_martop">
	<div class="col-12">
		<nav class="light_b main_nav white">
		<?=(new \lf\nav)->buildAdminHtml( (new \LfActions)
											->byPosition('!=', 0) // not hidden
											->order('position + 0','ASC') // sort by position
											->matrix(['parent', 'id']) // matrix on parent so it can loop through and handle child assignment
		);?>
		</nav>
	</div>
</div>

<input type="checkbox" id="hidden-nav" name="hidden-nav" class="dropdown" />
<label for="hidden-nav">
	<div class="drop-content">
		<div class="row no_martop">
			<div class="col-12">
				<h4 class="no_martop"><i title="hidden" class="fa fa-eye-slash"></i> Hidden Navigation</h4>
				<nav class="light_b main_nav white"><?=(new \lf\cms)->hiddenList();?></nav>
			</div>
		</div>
	</div>
	<span class="open-content pull-right blue_fg marbot"><i class="fa fa-chevron-down"></i> Show Hidden</span>
	<span class="close-content pull-right red_fg marbot"><i class="fa fa-chevron-up"></i> Hide</span>
</label>


<div class="row">
	<div class="col-9">
		
		<?php include 'view/navigation.action.php'; ?>
		
		<h4 title="Apps Linked to This Nav Item"><i class="fa fa-link"></i> Linked Apps</h4>
		<?php

		if( $links ):
			foreach($links as $link): ?>
			<div class="row">
				<div class="col-12">
				<?php
				// print editor form for each
				//include 'view/navigation.link.php';
				echo (new \lf\cms)->partial('navigation.link', ['link' => $link, 'locations' => $locations]);
				?>
				</div>
			</div>
		<?php endforeach;
		else: 
		?>
			<p>Nothing linked. Link an app with the form at the top right of this page.</p>
		<?php 
		endif; 

		
?>
	</div>
	<div class="col-3">
		<?php //$include = include 'view/navigation.addlink.php';
		echo (new \lf\cms)->partial('navigation.addlink', ['include' => $action['id'] ]); ?>
	</div>
</div>