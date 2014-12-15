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
	
?>