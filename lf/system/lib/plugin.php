<?php

namespace lf;

// maybe I should call this "plugin" or "\lf\admin\plugin" should be pluginController
class plugin
{
	/** array of active plugins for each hook */
	private $active = NULL;
	
	public function __construct()
	{
		// it must always be loaded, there is no plain instance of this class. its like orm with mysqli
		$this->init();
	}
	
	private function init()
	{
		$active = get('activePlugins');
		
		if( is_null( $active ) )
		{
			$active = array();
			$result = (new \LfPlugins)->getAll();
			if($result)
				foreach($result as $hook)
					$active[ $hook['hook'] ][ $hook['plugin'] ] = $hook['config'];
					
			set('activePlugins', $active);
		}
		
		$this->active = $active;
	}
	
	public function run($hook)
	{
			// pre($hook);
			// pre($this->active);
			// pre(LF.'plugins/'.$hook.'/index.php');
			
			//lfbacktrace();
			//pre('');
			
		// if the provided hook name has no active plugin, return $this
		if(!isset($this->active[$hook])) 
			return $this;
		
		foreach($this->active[$hook] as $plugin => $config)
		{
			$hookDetails = ' / '.$plugin.' @ '.$hook.' / Config: '.$config;
			
			startTimer(__METHOD__.$hookDetails);
			include LF.'plugins/'.$plugin.'/index.php';
			endTimer(__METHOD__.$hookDetails);
		}
		
		return $this;
	}
}