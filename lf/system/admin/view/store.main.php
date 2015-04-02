<h2>Store</h2>

<p>Items with a link can be installed. They may otherwise be updated.</p>

<div class="row">
	<div class="col-4">
		<h3>Apps</h3>
		<ul class="efvlist">
		<?php foreach($apps as $app => $ignore): ?> 	
			<li>
			<?php if(!isset($app_files[$app])): ?>
				<a href="%appurl%dlapp/<?=$app;?>/"><?=$app;?></a>
			<?php else: ?>
				<?=$app;?> [<a href="%appurl%dlapp/<?=$app;?>/update/">Update</a>]
			</li>
			<?php endif; ?>
		 <?php endforeach; ?>
		</ul>
	</div>
	<div class="col-4">
		<h3>Skins</h3>
		<ul class="fvlist">
		<?php foreach($skins as $skin => $ignore): ?> 	
			<li>
			<?php if(!isset($skin_files[$skin])): ?>
				<a href="%appurl%dlskin/<?=$skin;?>/"><?=$skin;?></a>
			<?php else: ?>
				<?=$skin;?> [<a href="%appurl%dlskin/<?=$skin;?>/update/">Update</a>]
			</li>
			<?php endif; ?>
		 <?php endforeach; ?>
		</ul>
	</div>
	<div class="col-4">
		<h3>Plugins</h3>
		<ul class="fvlist">
		<?php foreach($plugins as $plugin => $ignore): ?> 	
			<li>
			<?php if(!isset($plugin_files[$plugin])): ?>
				<a href="%appurl%dlplugin/<?=$plugin;?>/"><?=$plugin;?></a>
			<?php else: ?>
				<?=$plugin;?> [<a href="%appurl%dlplugin/<?=$plugin;?>/update/">Update</a>]
			</li>
			<?php endif; ?>
		 <?php endforeach; ?>
		</ul>	
	</div>
</div>






