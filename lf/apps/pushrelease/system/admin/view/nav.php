<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information.
	$admin_apps = str_replace(
		array(ROOT.'apps/', '/admin.php'), '', 
		glob(ROOT.'apps/*/admin.php')
	);
?>
<ul>
	<?php /*<li><a href="<?=$this->base;?>dashboard/">Dashboard</a></li>*/ ?>
	<li><a class="dashboard" href="<?=$this->base;?>dashboard/"><span>Dashboard</span></a></li>
	<li><a class="grid" href="<?=$this->base;?>apps/"><span>Apps</span></a>
		<ul>
		<?php foreach($admin_apps as $shortcut): ?>
			<li><a class="elements" href="<?=$this->base;?>apps/manage/<?php echo $shortcut; ?>/"><span><?php echo ucfirst($shortcut); ?></span></a></li>
		<?php endforeach; ?>
		</ul>
	</li>
	<li><a class="tables" href="<?=$this->base;?>skins/"><span>Skins</span></a></li>
	<li><a class="media" href="<?=$this->base;?>media/"><span>Media</span></a></li>
	<li><a class="users" href="<?=$this->base;?>users/"><span>Users</span></a></li>
	<li><a class="editor" href="<?=$this->base;?>acl/"><span>ACL</span></a></li>
	<li><a class="buttons" href="<?=$this->base;?>settings/"><span>Settings</span></a></li>
	<li><a class="editor" href="<?=$this->base;?>help/"><span>Help</span></a></li>
	<li><a class="elements" href="<?=$this->relbase;?>" target="_blank"><span>Preview</span></a></li>
</ul>