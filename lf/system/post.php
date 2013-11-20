<?php

$vars = $this->vars;
$output = '';

if($this->db->query('SELECT * FROM lf_links WHERE id = '.intval($link)))
	$_app = $this->db->fetch();
else
	die('invalid request');

$path = ROOT.'apps/'.$_app['app'].'/index.php';
if(is_file($path))
{
	ob_start();
	include($path);
	$output = ob_get_clean();
	
	$output = str_replace(
		array( 
			'%baseurl%',
			'%appurl%',
			'%post%'
		),
		array(
			$this->base,
			$this->base,
			$this->base.'post/'.$_app['id'].'/'
		),
		$output
	);
}

// by default, return to referer
header('HTTP/1.1 302 Moved Temporarily');
header('Location: '. $_SERVER['HTTP_REFERER']);
//echo $output;

>