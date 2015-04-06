<?php
	$admin_apps = str_replace(
		array(ROOT.'apps/', '/admin.php'), '', 
		glob(ROOT.'apps/*/admin.php')
	);
	
	// $this->settings
?>
<h4 class="no_martop">Control</h4>
<div class="row">
	<div class="col-12">
		<ul class="efvlist rounded">
			<?php $this->lf->hook_run('pre lf admin nav'); ?>

			<li>
				<a class="dashboard" href="<?=$this->base;?>dashboard/">
					<i class="fa fa-tachometer"></i>
					<span>
						<?=$this->settings['simple_cms']=='_lfcms'
							?' Dashboard'
							:ucfirst($this->settings['simple_cms']).' Admin';
						?>
					</span>
				</a>
			</li>
			<li><a class="tables" href="<?=$this->base;?>skins/"><i class="fa fa-paint-brush"></i><span> Skins</span></a></li>
			<li><a class="tables" href="<?=$this->base;?>plugins/"><i class="fa fa-plug"></i><span> Plugins</span></a></li>
			<!--<li><a class="media" href="<?=$this->base;?>media/"><span>Media</span></a></li>-->
			<li><a class="users" href="<?=$this->base;?>users/"><i class="fa fa-users"></i><span> Users</span></a></li>
			<li><a class="editor" href="<?=$this->base;?>acl/"><i class="fa fa-shield"></i><span> ACL</span></a></li>
			<!-- <li><a class="" href="<?=$this->base;?>upgrade/"><span>Upgrade</span></a></li> -->
			<li><a class="" href="<?=$this->base;?>settings/"><i class="fa fa-cog"></i><span> Settings</span></a></li>
			<li><a class="editor" href="<?=$this->base;?>store/"><i class="fa fa-shopping-cart"></i><span> Store</span></a></li>
			<li><a class="" target="_blank" href="http://littlefootcms.com/byname/Admin+Documentation"><i class="fa fa-question"></i><span> Help</span></a></li>
			<li><a class="" target="_blank" href="https://github.com/eflip/littlefootcms/issues/"><i class="fa fa-bug"></i><span> Report Bug</span></a></li>
			<!--<li><a class="" href="<?=$this->relbase;?>" target="_blank"><span>Preview Site</span></a></li>-->
			
			<?php $this->lf->hook_run('post lf admin nav'); ?>
		</ul>
	</div>
</div>

<?php if($this->settings['simple_cms'] == '_lfcms'): ?>

<h4>Apps</h4>
<div class="row">
	<div class="col-12">
		<ul class="efvlist rounded">
			<?php foreach($admin_apps as $shortcut): 
				if(isset($this->vars[1]) && $shortcut == $this->vars[1]) $highlight = ' class="active"';
				else $highlight = '';
			?>
				<li <?=$highlight;?>>
					<a class="elements" href="<?=$this->base;?>apps/<?php echo $shortcut; ?>/">
						<span><?php echo ucfirst($shortcut); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

<?php endif; ?>
