<?php

namespace lf\admin;

class apps
{
	public function main()
	{
		$args = \lf\www('Param');
		$var = $args;
		
		if(isset($this->lf->simple) && $this->lf->simple != '_lfcms') return;
		
		// $var[0] = 'manage'
		$app_name = $var[0];
		echo '<h2 class="no_marbot">
				<a href="'.\lf\wwwAppUrl().$app_name.'/">
					'.ucfirst($app_name).'
				</a> Admin</h2>
			<div class="dashboard_manage">';
		
		\lf\get('request')->actionDrop(); // drop the 'apps' action in front
		\lf\get('request')->actionPush(); // make '$app' the new root action
		
		// manage
		preg_match('/[A-Za-z0-9_]+/', $args[0], $matches);		
		$app_path = ROOT.'apps/'.$matches[0];
		
		//$preview = 'admin';
		$admin = true;
		/*$urlpreview = '';
		if(isset($var[0]) && $var[0] == 'preview') 
		{
			$preview = 'index';
			$admin = false;
			\lf\get('request')->actionPop();
			$urlpreview = 'preview/';
		}*/
		
		ob_start();
		//if(is_file($app_path.'/'.$preview.'.php'))
		//{ 
			$old = getcwd(); chdir($app_path);
			#$database = $this->dbconn;
			
			include LF.'apps/'.$app_name.'/admin.php';
			
			//echo $this->request->loadapp($app_name, $admin, NULL, $var);
			
			//include($preview.'.php');
			chdir($old);
		//}
		
		echo '</div>';
		
		return \lf\resolveAppUrl( ob_get_clean() );
	}
	
	public function manage($var)
	{
		$var = \lf\www('param');
		// backward compatible
		redirect302(\lf\www('Admin').'apps/'.$var[1]);
	}
}