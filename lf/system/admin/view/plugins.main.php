<h2>Plugins Manager</h2>

<div class="row">
	<div class="col-9">
		<form id="hook_form" action="%appurl%hookup" method="post">
			<table class="table text-center" id="plugin_library">
				<tr class="gray light">
					<th>Hooks</th>
					<th>Plugins</th>
					<th>Config</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
				<tr>
					<td><input type="text" name="hook" id="lf_hook" placeholder="hook_name" /></td>
					<td><select name="plugin" id=""><?=$plugins;?></select></td>
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
					<td><a href="%appurl%rm/<?=$row['id'];?>" class="button red">Delete</a></td>
				</tr>
			<?php endforeach; ?>
			</table>
		</form>
	</div>
	<div class="col-3">
		<div class="tile rounded">
			<div class="tile-header gray light">
				<h3>Hooks</h3>
			</div>
			<div class="tile-content">
				<p>pre lf render</p>
				<p>post app blog view</p>
				<p>pre app pages</p>
				<p>post app wiki view</p>
			</div>
		</div>
	</div>
</div>
