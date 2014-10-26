<?php
	$admin_apps = str_replace(
		array(ROOT.'apps/', '/admin.php'), '', 
		glob(ROOT.'apps/*/admin.php')
	);
	
	// $this->settings
?>
<ul>
	<?php /*<li><a href="<?=$this->base;?>dashboard/">Dashboard</a></li>*/ ?>
	<li><a class="dashboard" href="<?=$this->base;?>dashboard/"><span>Dashboard</span></a>
	<?php if($this->settings['simple_cms'] == '_lfcms'): ?>
		<ul>
		<?php foreach($admin_apps as $shortcut): 
			if(isset($this->vars[1]) && $shortcut == $this->vars[1]) $highlight = ' class="current"';
			else $highlight = '';
		?>
			<li<?=$highlight;?>><a class="elements" href="<?=$this->base;?>apps/manage/<?php echo $shortcut; ?>/"><span><?php echo ucfirst($shortcut); ?></span></a></li>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	</li>
	<li><a class="tables" href="<?=$this->base;?>skins/"><span>Skins</span></a></li>
	
	
	<!-- <li><a class="tables" href="<?=$this->base;?>plugins/"><span>Plugins</span></a></li> -->
	
	
	<!--<li><a class="media" href="<?=$this->base;?>media/"><span>Media</span></a></li>-->
	<li><a class="users" href="<?=$this->base;?>users/"><span>Users</span></a></li>
	<li><a class="editor" href="<?=$this->base;?>acl/"><span>ACL</span></a></li>
    <!-- <li><a class="buttons" href="<?=$this->base;?>upgrade/"><span>Upgrade</span></a></li> -->
	<li><a class="buttons" href="<?=$this->base;?>settings/"><span>Settings</span></a></li>
	<li><a class="elements" href="<?=$this->relbase;?>" target="_blank"><span>Preview Site</span></a></li>
</ul>
