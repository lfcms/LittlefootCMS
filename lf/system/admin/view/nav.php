<span class="block light_gray_fg martop marbot pad fxlarge"><i class="fa fa-sliders"></i> Control <i class="fa fa-caret-down pull-right gray_fg"></i></span>
<div class="row">
	<div class="col-12">
		<ul class="efvlist flarge light_a">
			<?php (new \lf\plugin)->run('pre admin nav'); ?>

			<!-- needs to start with `<li><a class="controls"` so it will match during replacement at index.php -->
			<?php if($_SESSION['upgrade']): ?>
			<li><a class="green" href="<?=\lf\requestGet('AdminUrl');?>settings/" title="Upgrade Your Littlefoot Installation">
<i class="fa fa-arrow-circle-up"></i> Upgrade Now!</a></li>
			<?php endif; ?>
			<li><a class="controls" href="<?=\lf\requestGet('IndexUrl');?>admin/dashboard/"><i class="fa fa-dashboard"></i><span><?=
				\lf\getSetting('simple_cms')=='_lfcms'
					?' Legacy Dash'
					:ucfirst(\lf\getSetting('simple_cms')).' Admin';
				?></span></a></li>
			<!--<li><a class="media" href="<?=\lf\requestGet('IndexUrl');?>admin/media/"><span>Media</span></a></li>-->
			<li><a class="controls" href="<?=\lf\requestGet('IndexUrl');?>admin/wysiwyg/"><i class="fa fa-dashboard"></i><span> Dashboard</span></a></li>
			<li><a class="controls" href="<?=\lf\requestGet('IndexUrl');?>admin/skins/"><i class="fa fa-paint-brush"></i><span> Skins</span></a></li>
			<li><a class="controls" href="<?=\lf\requestGet('IndexUrl');?>admin/plugins/"><i class="fa fa-plug"></i><span> Plugins</span></a></li>
			<li><a class="controls" href="<?=\lf\requestGet('IndexUrl');?>admin/users/"><i class="fa fa-users"></i><span> Users</span></a></li>
			<li><a class="controls" href="<?=\lf\requestGet('IndexUrl');?>admin/acl/"><i class="fa fa-key"></i><span> Access</span></a></li>
			<li><a class="controls" href="<?=\lf\requestGet('IndexUrl');?>admin/settings/"><i class="fa fa-cog"></i><span> Settings</span></a></li>
			<li><a class="controls" href="<?=\lf\requestGet('IndexUrl');?>admin/store/"><i class="fa fa-shopping-cart"></i><span> Store</span></a></li>
			<li><a class="controls" target="_blank" href="http://littlefootcms.com/manual/Admin+Documentation" title="Hover over headings for tips!"><i class="fa fa-question"></i><span> Help</span></a></li>
			<li><a class="controls" target="_blank" href="https://github.com/eflip/littlefootcms/issues/"><i class="fa fa-bug"></i><span> Report Bug</span></a></li>
			<!--<li><a class="" href="<?=$this->relbase;?>" target="_blank"><span>Preview Site</span></a></li>-->
			
			<?php (new \lf\plugin)->run('post admin nav'); ?>
		</ul>
	</div>
</div>

<?php if( \lf\getSetting('simple_cms') == '_lfcms'): ?>

<span class="block light_gray_fg martop marbot pad fxlarge"><i class="fa fa-th"></i> App Admin <i class="fa fa-caret-down pull-right gray_fg"></i></span>
<div class="row no_marbot">
	<div class="col-12">
		<ul class="efvlist flarge light_a">
			<?php
			
			$admin_apps = str_replace(
				array(ROOT.'apps/', '/admin.php'), '', 
				glob(ROOT.'apps/*/admin.php')
			);
			
			
			foreach($admin_apps as $shortcut):
				
				if(\lf\requestGet('Action')[0] = 'apps' 
					&& ( isset(\lf\requestGet('Action')[1]) 
						&& 'apps' == \lf\requestGet('Action')[0] 
						&& $shortcut == \lf\requestGet('Action')[1] 
					)
				)
					$highlight = ' class="active blue light_a"';
				else 
					$highlight = '';
				
			?>
				<li<?=$highlight;?>><a class="elements" href="<?=\lf\requestGet('AdminUrl');?>apps/<?php echo $shortcut; ?>/">
						<span><?php echo ucfirst($shortcut); ?></span>
				</a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

<?php endif; 