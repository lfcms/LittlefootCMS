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

<h2 class="no_marbot">Dashboard</h2>
<div class="row no_martop">
	<div class="col-6">
		<h3>Navigation</h3>
        <!-- <p>Manage your website's nav menu. Click on the nav item title to edit it, click [x] to delete it, and click (Admin) to manage the associated app.</p> -->
        <div class="row">
        	<div class="col-12 spaced">
				<!-- <ul class="efvlist"> -->
				<?=$this->partial('dashboard-partial-nav', array('actions' => $actions));?>
				<!-- </ul> -->
			</div>
        </div>
        <h3>Hidden Navigation</h3>
        <!-- <p>This works just like the nav menu manager above, but these nav items will be hidden from nav menu of your website. This feature is useful for hiding apps like /signup, /secret-blog</p> -->
        <div class="row">
			<div class="col-12">
				<ul class="efvlist">
					<?=$this->partial('dashboard-partial-hidden', array('actions' => $hidden));?>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-6">
		<h3>App Gallery</h3>
		<!-- <p>Install apps packaged as .zip files or download apps from the store. Click on the name of an app to attach it to the website.</p> -->
		<div id="appgallery-container">
			<div id="new-app">
				<!-- <form enctype="multipart/form-data" action="%appurl%install/" method="post">
					<input type="hidden" name="MAX_FILE_SIZE" value="55000000" />
					<h3>Click an app to add it to the navigation. <a href="%appurl%download/">Download more apps from the Store</a></h3>
					<div><input type="file" name="app" value="Upload" /></div>
					<div><?=$install;?></div>
				</form> -->
				<p>Click an app to add it to the nav. <a href="%appurl%download/">Download more apps from the Store</a></p>
			</div>
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