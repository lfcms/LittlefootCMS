<?php

class apps extends app
{
	public function main($args)
	{
		$var = $args;
		
		if(isset($this->lf->simple) && $this->lf->simple != '_lfcms') return;
		
		// $var[0] = 'manage'
		$app_name = $var[0];
		
		echo '<h2><a href="%appurl%'.$app_name.'/">'.ucfirst($app_name).'</a> / Admin</h2>
			<div class="dashboard_manage">';
		$var = array_slice($var, 1); // pass the rest of the vars to the admin.php script
		
		$oldvars = $this->request->vars;
		
		$this->request->vars = $var;
		
		// manage
		preg_match('/[A-Za-z0-9_]+/', $args[0], $matches);		
		$app_path = ROOT.'apps/'.$matches[0];
		
		
		$preview = 'admin';
		$admin = true;
		$urlpreview = '';
		if(isset($var[0]) && $var[0] == 'preview') 
		{
			$preview = 'index';
			$admin = false;
			$var = array_slice($var, 1);
			$urlpreview = 'preview/';
		}
		
		ob_start();
		//if(is_file($app_path.'/'.$preview.'.php'))
		//{ 
			$old = getcwd(); chdir($app_path);
			#$database = $this->dbconn;
			$this->request->appurl = $this->request->base.'apps/'.$app_name.'/'.$urlpreview;
			
			echo $this->request->loadapp($app_name, $admin, NULL, $var);
			
			//include($preview.'.php');
			chdir($old);
		//}
		
		echo '</div>';
		
		$this->request->vars = $oldvars;
		return str_replace('%appurl%', '%appurl%'.$app_name.'/'.$urlpreview, ob_get_clean());
	}
	
	public function manage($var)
	{
		// backward compatible
		redirect302($this->lf->base.'apps/'.$var[1]);
	}
}