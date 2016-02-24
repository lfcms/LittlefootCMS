<?php

namespace lf;

// maybe I should call this "plugin" or "\lf\admin\plugin" should be pluginController
class hook
{
	private $hooks = NULL;
	
	public function __construct()
	{
		$this->initHooks();
	}
	
	private function initHooks()
	{
		$hooks = get('hooks');
		
		if( is_null( $hooks ) )
		{
		
			$hooks = array();
			$result = (new \LfPlugins)->getAll();
			if($result)
				foreach($result as $plugin)
					$hooks[ $plugin['hook'] ][ $plugin['plugin'] ] = $plugin['config'];
					
			set('hooks', $hooks);
		}
		
		$this->hooks = $hooks;
	}
	
	public function run($hook)
	{
		if(!isset($this->hooks[$hook])) 
			return $this;
		
		foreach($this->hooks[$hook] as $plugin => $config)
		{
			$hookDetails = ' / '.$plugin.' @ '.$hook.' / Config: '.$config;
			
			(new \lf\cache)->startTimer(__METHOD__.$hookDetails);
			include ROOT.'plugins/'.$plugin.'/index.php';
			(new \lf\cache)->endTimer(__METHOD__.$hookDetails);
		}
		
		return $this;
	}
}