<?php

/**
 * @ignore
 */
class plugins extends app
{
	public function main($args)
	{
		//$this->db->query("UPDATE lf_settings SET val = '' WHERE var = 'plugins'");
		$registered_hooks = $this->lf->settings['plugins'];
		
			
		include 'model/plugins.main.php';
		include 'view/plugins.main.php';
	}
	
	public function hookup($args)
	{
		
		$plugin_hooks = $this->db->fetch("SELECT * FROM lf_settings WHERE var = 'plugins'");
		
		if(!$plugin_hooks)
		{
			echo 'insert';
			$hooks = json_encode(array($_POST['hook'][$_POST['plugin']] => true));
			$this->db->query("INSERT INTO lf_settings VALUES (NULL, 'plugins', '".$this->db->escape($hooks)."')");
		}
		else
		{
			//echo 'update';
			$plugin_hooks = json_decode($plugin_hooks['val'], 1);
			
			//echo '<pre>';
			//print_r($plugin_hooks);
			
			$plugin_hooks[$_POST['hook']][$_POST['plugin']] = true;
			
			//print_r($plugin_hooks);
			
			$hooks = json_encode($plugin_hooks);
			
			
			//var_dump($hooks);
			
			
			//echo '</pre>';
			
			
			$this->db->query("UPDATE lf_settings SET val = '".$this->db->escape($hooks)."' WHERE var = 'plugins'");
		}
		
		redirect302();
	}
}

?>