<?php

$admin_skin = 'default'; // this needs to be an option instead of hard coded
			
// maybe you are an admin, but I need you to login first
//if($this->auth['access'] != 'admin' && strpos($this->auth['access'], 'app_') === false)

$user = new User();	

if( ! $user->hasaccess('admin') ) 
	/*&& strpos($this->auth['access'], 'app_') === false*/
{
	
	//$publickey = '6LffguESAAAAAKaa8ZrGpyzUNi-zNlQbKlcq8piD'; // littlefootcms public key
	$recaptcha = '';//recaptcha_get_html($publickey);
	
	//pre($_SESSION);
	//exit();
	
	ob_start();
	include('skin/'.$admin_skin.'/login.php'); 

	$out = ob_get_clean();

	$out = str_replace('%skinbase%', $this->relbase.'lf/system/admin/skin/'.$admin_skin.'/', $out);
        $out = str_replace('%baseurl%', $this->base.'admin/', $out);
        $out = str_replace('%relbase%', $this->relbase, $out);
	$out = str_replace('%skinbase%', $this->relbase.'lf/system/admin/skin/'.$admin_skin.'/', $out);

	echo $out;
} 


if($user->hasaccess('admin'))
{
	include('loader.php');
	$this->function_timer['admin'] = microtime(true) - $funcstart;
	$this->app_timer['no apps, just admin'] = 0;
} 
/*
else if(strpos($this->auth['access'], 'app_') !== false)
{
	$admin_skin = 'fresh';
	$app = explode('_', $this->auth['access']);
	$app_name = $app[1];
	$app = $this->loadapp($app_name, true, '', $this->action);
	
	$app = str_replace('%appurl%', $this->base.'admin/', $app);
	
	ob_start();
	include('skin/'.$admin_skin.'/index.php');
	$out = str_replace('%skinbase%', $this->relbase.'lf/system/admin/skin/'.$admin_skin.'/', ob_get_clean());
	$out = str_replace('%baseurl%', $this->base.'admin/', $out);
	$out = str_replace('%relbase%', $this->relbase, $out);
	$out = str_replace('Littlefoot CMS', ucfirst($app_name).' Admin', $out);
	$out = str_replace(array('<nav>', '</nav>'), '', $out);
	$out = str_replace('class="content"', 'class="content" style="margin: 10px;"', $out);
	
	echo $out;
}*/

exit; 