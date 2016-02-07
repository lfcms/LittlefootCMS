<div class="row">
	<div class="col-12">
		<h2 class="no_marbot"><i class="fa fa-plug"></i> Plugins Manager</h2>
	</div>
</div>

<div class="row">
	<div class="col-9">
		<form id="hook_form" action="%appurl%hookup" method="post">
			<table class="table text-center white" id="plugin_library">
				<tr class="light_gray">
					<th>Hooks</th>
					<th>Plugins</th>
					<th>Config</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
				<tr>
					<td><input type="text" name="hook" id="lf_hook" placeholder="hook_name" /></td>
					<td><select name="plugin" id=""><?=$pluginselect;?></select></td>
					<td><input type="text" name="config" placeholder="my-secret-id" /></td>
					<td></td>
					<td><button class="green">Hook It Up!</button></td>
				</tr>
			<?php
			
			foreach($registered_hooks as $row): ?>
				<tr>
					<td><?=$row['hook'];?></td>
					<td><?=$row['plugin'];?></td>
					<td><?=$row['config'];?></td>
					<td><?=$row['status'];?></td>
					<td><a href="%appurl%rm/<?=$row['id'];?>" class="x"><i class="fa fa-trash-o"></i></a></td>
				</tr>
			<?php endforeach; ?>
			</table>
		</form>
	</div>
	<div class="col-3 spaced">
		<div class="tile white">
			<div class="tile-header">
				<h4><i class="fa fa-plus"></i> Add New</h4>
			</div>
			<div class="tile-content">
				<h4>Plugins</h4>
				<ul class="vlist">
					<li>
					<?=isset($plugin_list)?implode('</li><li>', $plugin_list):'No plugins found';?>
					</li>
				</ul>
				<h4>Hooks</h4>
				<p>pre lf render</p>
				<p>post app blog view</p>
				<p>pre app pages</p>
				<p>post app wiki view</p>
			</div>
		</div>
	</div>
</div>
