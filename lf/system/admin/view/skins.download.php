<h2><a href="%appurl%">Skins</a> / Download</h2>
<div id="store-wrapper">
	<p>Skins with a link can be installed. Those that are blank are already installed.</p>
	
	<ul class="fvlist">
	<?php foreach($apps as $app => $ignore): ?> 	
		<li>
		<?php if(!isset($files[$app])): ?>
			<a href="%appurl%getappfromnet/<?=$app;?>/"><?=$app;?></a>
		<?php else: ?>
			<?=$app;?> [<a href="%appurl%getappfromnet/<?=$app;?>/update/">Update</a>]
		</li>
		<?php endif; ?>
	 <?php endforeach; ?>
	</ul>
</div>