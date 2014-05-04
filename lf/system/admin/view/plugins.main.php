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
		</tr>
		<tr>
			<td><select name="hook" id=""><?=$hooks;?></select></td>
			<td><select name="plugin" id=""><?=$plugins;?></select> <input type="submit" value="Hook It Up!" /></td>
		</tr>
	<?php 
	
	var_dump($registered_hooks);
	
	
	
	foreach($registered_hooks as $hook => $plugins): ?>
		<tr>
			<td colspan="2"><?=$hook;?></td>
		</tr>
		<tr>
			<td><?=$plugin;?></td>
			<td><?=var_dump($hook);?></td>
		</tr>
	<?php endforeach; ?>
	</table>
</form>