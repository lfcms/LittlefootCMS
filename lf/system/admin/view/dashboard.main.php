<script type="text/javascript">
/*
$(document).ready(function() {

	// Expand / Collapse
	$('#actions li ol').parent().prepend('<a href="#" class="toggle">+</a> ');
	$('#actions .toggle').click(function() {
		$(this).parent().find('>ol').toggle('fast');
	});

	$('#actions li ol').hide();
	
	$.each($('#actions li ol'), function ( key, value ) {
		if($(value).find('.selected').length > 0)
		{
			$(this).show();
		}
	});

});*/
</script>
<div class="row no_martop">
	<div class="col-7">
		<h2 class="no_marbot" title="Manage your navigation and applications. Setting the position to 0 will hide a navigation item."><i class="fa fa-compass"></i> Menu</h2>
        <!-- <p>Manage your website's nav menu. Click on the nav item title to edit it, click [x] to delete it, and click (Admin) to manage the associated app.</p> -->
        <div class="row">
        	<div class="col-12 spaced">
				<?=$this->partial('dashboard-partial-hidden', array('actions' => $hidden));?>
				<?=$this->partial('dashboard-partial-nav', array('actions' => $actions));?>
			</div>
		</div>
	</div>
	<div class="col-5">
		<h4 title="Click an app below to add it to the navigation.">
			<i class="fa fa-plus"></i> Add to Menu
		</h4>
		<!-- <p>Install apps packaged as .zip files or download apps from the store. Click on the name of an app to attach it to the website.</p> -->
		<div id="appgallery-container">
			<ul class="efvlist rounded">
				<?php foreach($apps as $app): ?>
				<li>
					<div class="pull-right">
						<a onclick="return confirm('Do you really want to delete this?');" href="%appurl%delapp/<?=$app;?>/" class="x"><i class="fa fa-trash"></i></a>
					</div>
					
						<a href="%appurl%linkapp/<?=$app;?>/"><?=$app;?></a>
					
					<div style="clear:both"></div>
				</li>			
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>