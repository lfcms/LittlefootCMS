<?php

$files = scandir(ROOT.'plugins/');
foreach ($files as $file) {
	if ($file === '.' or $file === '..') continue;

	if (is_file(ROOT . 'plugins/' . $file . '/index.php')) {
		$plugin_list[] = $file;
	}
}

if(isset($plugin_list))
	$plugins = '<option value="">-- Select a plugin --</option><option>'.implode('</option><option>', $plugin_list).'</option>'; 
else
	$plugins = '<option value="">-- No plugins available --</option>';

$hook_list = array('postcontent', 'pre-auth-create');

if(isset($hook_list))
	$hooks = '<option value="">-- Select a hook --</option><option>'.implode('</option><option>', $hook_list).'</option>'; 
else
	$hooks = '<option value="">-- No hooks available --</option>';
	
?>