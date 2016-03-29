<?php

namespace lf\admin;

/**
 * # Plugin manager controller
 * 
 * ## Definitions
 * 
 * **Hooks**: When a hook is executed it looks like this:
 * 
 * `$this->hook_run('pre lf render');`
 * 
 * Anything assigned to this hook, will execute at that part of the code before anything else happens.
 * 
 * **Plugin**: Name of plugin (installed to `lf/plugins/myplugin`).
 * 
 * **Config**: All plugins are provided with a $config variable to set up whatever data is needed to customize the plugin. For example, the Google Analytics plugin uses your Google Analytics ID. Same thing with the DisQus app.
 * 
 * **Hook it Up!**: Add the hook
 * 
 * ### Hooks
 * 
 * A (so far incomplete) list of the available hooks that are called at page load. Many more can be inferred, but I hope to update this list in future releases. 
 */
class plugins
{
	public function main()
	{
		$args = \lf\requestGet('Param'); // backward compatibility
		//$this->db->query("UPDATE lf_settings SET val = '' WHERE var = 'plugins'");
		//$registered_hooks = $this->lf->settings['plugins'];
		
		$registered_hooks = \lf\orm::q('lf_plugins')->getAll();
		
		include 'model/plugins.main.php';
		include 'view/plugins.main.php';
	}
	
	public function rm()
	{
		$args = \lf\requestGet('Param'); // backward compatibility
		\lf\orm::q('lf_plugins')->filterByid($args[1])->delete();
		redirect302();
	}
	
	public function hookup()
	{
		$args = \lf\requestGet('Param'); // backward compatibility
		
		//$plugin_hooks = $this->db->fetch("SELECT * FROM lf_settings WHERE var = 'plugins'");
		$plugin_hook = \lf\orm::q('lf_plugins')->filterByhook($_POST['hook'])->filterByplugin($_POST['plugin'])->first();
		
		$_POST['status'] = 'active';
		if(!$plugin_hook)
		{
			\lf\orm::q('lf_plugins')->insertArray($_POST);
		}
		else
		{
			\lf\orm::q('lf_plugins')->updateById($plugin_hook['id'], $_POST);
		}
		
		redirect302();
	}
}

?>