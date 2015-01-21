<style type="text/css">
	#hook_form ul { list-style: none; }
	#hook_form ul li { display: inline }
	#plugin_library { width: 500px; }
	#plugin_library th { text-align: left; }
</style>
	
	
	
<form id="hook_form" action="%appurl%hookup" method="post">
	<table id="plugin_library">
		<tr>
			<th>Hooks</th>
			<th>Plugins</th>
			<th>Config</th>
			<th>Status</th>
		</tr>
		<tr>
			<td><input type="text" name="hook" id="lf_hook" placeholder="hook_name" /></td>
			<td><select name="plugin" id=""><?=$plugins;?></select></td>
			<td><input type="text" name="config" placeholder="my-secret-id" /></td>
			<td><input type="submit" value="Hook It Up!" /></td>
		</tr>
	<?php
	
	foreach($registered_hooks as $row): ?>
		<tr>
			<td><?=$row['hook'];?></td>
			<td><?=$row['plugin'];?></td>
			<td><?=$row['config'];?></td>
			<td><?=$row['status'];?> <a href="%appurl%rm/<?=$row['id'];?>" class="nav_delete_item">Delete</a></td>
		</tr>
	<?php endforeach; ?>
	</table>
</form>
<h3>Available Littlefoot Hooks</h3>
<p>pre lf render</p>
<p>post app blog view</p>
<p>pre app pages</p>
<p>post app wiki view</p>