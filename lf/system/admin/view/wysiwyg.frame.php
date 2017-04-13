<h2> <i class="fa fa-compass"></i> Navigation </h2>

<?=notice();?>

<div class="row no_martop">
	<div class="col-12">
		<nav class="light_b main_nav white"><?=(new \lf\cms)->renderNavCache( \lf\requestGet('AdminUrl').'wysiwyg/' );?></nav>
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
		
		<?php include 'view/wysiwyg.action.php'; ?>
		
		<h4 title="Apps Linked to This Nav Item"><i class="fa fa-link"></i> Linked Apps</h4>
		<?php

		if( $links ):
			foreach($links as $link): ?>
			<div class="row">
				<div class="col-12">
				<?php
				// print editor form for each
				//include 'view/wysiwyg.link.php';
				echo (new \lf\cms)->partial('wysiwyg.link', ['link' => $link, 'locations' => $locations]);
				?>
				</div>
			</div>
		<?php endforeach;
		else: 
		?>
			<p>Nothing linked. Link an app with the form at the top right of this page.</p>
		<?php 
		endif; 

		$iframeUrl = \lf\requestGet('AdminUrl').'wysiwyg/preview/'.$action['id'].'/'.implode('/', $param);

?>
	</div>
	<div class="col-3">
		<?php //$include = include 'view/wysiwyg.addlink.php';
		echo (new \lf\cms)->partial('wysiwyg.addlink', ['include' => $action['id'] ]); ?>
	</div>
</div>


<div class="row">
	<div class="col-12">
		<h4 title="Preview Your Site and Make Updates in Realtime" class="no_martop"><i class="fa fa-eye"></i> Preview <a href="<?=$iframeUrl?>" class="pull-right" title="Fullscreen Preview"><i class="fa fa-expand"></i></a></h4>
		<iframe src="<?=$iframeUrl?>"
			class="white light_b" width="100%" height="700px" frameborder="0">
		</iframe>
	
	</div>
</div>