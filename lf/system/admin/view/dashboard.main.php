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

<h2 class="no_marbot"><i class="fa fa-tachometer"></i> Dashboard</h2>
<div class="row no_martop">
	<div class="col-7">
		<h3><i class="fa fa-bars"></i> Navigation</h3>
        <!-- <p>Manage your website's nav menu. Click on the nav item title to edit it, click [x] to delete it, and click (Admin) to manage the associated app.</p> -->
        <div class="row">
        	<div class="col-12 spaced">
				<?=$this->partial('dashboard-partial-hidden', array('actions' => $hidden));?>
				<?=$this->partial('dashboard-partial-nav', array('actions' => $actions));?>
			</div>
		</div>
	</div>
	<div class="col-5">
		<h3><i class="fa fa-cubes"></i> App Gallery</h3>
		<!-- <p>Install apps packaged as .zip files or download apps from the store. Click on the name of an app to attach it to the website.</p> -->
		<div id="appgallery-container">
			<p>Click an app below to add it to the navigation.</p>
			<ul class="efvlist rounded">
				<?php foreach($apps as $app): ?>
				<li>
					<div class="pull-right">
						<a onclick="return confirm('Do you really want to delete this?');" href="%appurl%delapp/<?=$app;?>/" class="x">x</a>
					</div>
					
						<a href="%appurl%linkapp/<?=$app;?>/"><?=$app;?></a>
					
					<div style="clear:both"></div>
				</li>			
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>