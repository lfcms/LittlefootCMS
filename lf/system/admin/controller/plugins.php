<?php

/**
 * @ignore
 */
class plugins extends app
{
	public function main($args)
	{
		//$this->db->query("UPDATE lf_settings SET val = '' WHERE var = 'plugins'");
		//$registered_hooks = $this->lf->settings['plugins'];
		
		$registered_hooks = orm::q('lf_plugins')->getAll();
		
		include 'model/plugins.main.php';
		include 'view/plugins.main.php';
	}
	
	public function rm($args)
	{
		orm::q('lf_plugins')->filterByid($args[1])->delete();
		redirect302($this->lf->appurl);
	}
	
	public function hookup($args)
	{
		
		//$plugin_hooks = $this->db->fetch("SELECT * FROM lf_settings WHERE var = 'plugins'");
		$plugin_hook = orm::q('lf_plugins')->filterByhook($_POST['hook'])->filterByplugin($_POST['plugin'])->first();
		
		$_POST['status'] = 'active';
		if(!$plugin_hook)
		{
			orm::q('lf_plugins')->insertArray($_POST);
		}
		else
		{
			orm::q('lf_plugins')->updateById($plugin_hook['id'], $_POST);
		}
		
		redirect302();
	}
}

?>