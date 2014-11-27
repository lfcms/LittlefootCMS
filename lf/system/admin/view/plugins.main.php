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
			<th>Status</th>
		</tr>
		<tr>
			<td><input type="text" name="hook" id="lf_hook" placeholder="hook_name" /></td>
			<td><select name="plugin" id=""><?=$plugins;?></select></td>
			<td><input type="submit" value="Hook It Up!" /></td>
		</tr>
	<?php
	
	foreach($registered_hooks as $id => $row): ?>
		<tr>
			<td><?=$row['hook'];?></td>
			<td><?=$row['plugin'];?></td>
			<td><?=$row['status'];?></td>
		</tr>
	<?php endforeach; ?>
	</table>
</form>
<h3>Available Littlefoot Hooks</h3>
<p>pre_render</p>
<p>pre_auth</p>
<p></p>
<p></p>
<p></p>