<?php

include 'config.php';

// TESTING only, remove when finally using
/*chdir('..');
define('ROOT', getcwd().'/');
include 'system/db.class.php';
$oldversion = '1.13.5-r114';

*/
///////////////////////////////////////

if(!class_exists('Database')) 
	include ROOT.'system/db.class.php'; // load database wrapper

$db = new Database($db);

function older($oldversion, $newversion)
{
	//echo $oldversion.' | '.$newversion.'<br />';
	preg_match('/^(\d+).(\d+).(\d+)-r(\d+)$/', $oldversion, $oldversion);
	preg_match('/^(\d+).(\d+).(\d+)-r(\d+)$/', $newversion, $newversion);
	
	
	for($i = 1; $i <= 4; $i++)
	{
		if($oldversion[$i] < $newversion[$i])
			return true;
	}
	
	if($oldversion[4] == $newversion[4]) return false;
	
	return false;
}

echo '<h1>Littlefoot CMS Upgrade</h1>';

if(!is_file(ROOT.'system/version'))
{
	echo '<p>Too old for upgrade</p>';
}
else
{
	$newversion = file_get_contents(ROOT.'system/version');
	
	if($newversion == '1-DEV') die('No upgrade on DEV'); 
	
	if(older($oldversion, '1.13.5-r129'))
	{
		$index = "<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information.

// The lf/ directory is the ROOT of the application
define('ROOT', dirname(__FILE__).'/lf/');
if(!chdir(ROOT)) die('Access Denied to '.ROOT); // if unable to cd there, kill script

include 'system/init.php';
";
		file_put_contents(ROOT.'../index.php', $index);
		
		$acl = array();
		$acl_user = $db->fetchall("SHOW COLUMNS FROM `lf_acl_user`");
		foreach($acl_user as $user)
			$acl[] = $user['Field'];
		if(in_array('nav_id', $acl))
			$db->query('ALTER TABLE lf_acl_user DROP COLUMN nav_id');
			
		$acl = array();
		$acl_user = $db->fetchall("SHOW COLUMNS FROM `lf_acl_global`");
		foreach($acl_user as $user)
			$acl[] = $user['Field'];
		if(in_array('nav_id', $acl))
			$db->query('ALTER TABLE lf_acl_global DROP COLUMN nav_id');
		
		$db->query("UPDATE lf_users SET status = 'valid' WHERE status = 'online'");
			
		echo '<h2>upgrade to 1.13.5-r129</h2>
			<p>please update your skin theme to use %{skinbase}% instead of %{baseurl}%</p>';
		$oldversion = '1.13.5-r129';
	}
	
	if(older($oldversion, '1.13.5-r130'))
	{
		$config = ROOT.'pacakges/tinymce/jscripts/tiny_mce/plugins/ajaxfilemanager/inc/config.php';
		$filemanager = file_get_contents($config);
		
		$filemanager = preg_replace("/-=- Littlefoot integration[^}]+}/", '-=- Littlefoot integration -=- */
	if(!isset($_SESSION))
	{
		 // Apply same session name as CMS
		$root = str_replace("system/lib/tinymce/jscripts/tiny_mce/plugins/ajaxfilemanager/inc", "", dirname(__FILE__));
		$sess_name = md5($root.$_SERVER["SERVER_NAME"]); // Needs to be alphanumeric, remove other characters
		session_name($sess_name);
		session_start();
	} 
', $filemanager);
		
		file_put_contents($config, $filemanager);
		
		$db->query('ALTER TABLE lf_users ADD loginfailcnt INT NOT NULL');
		
		// update user table
		$columns = array();
		$cols = $db->fetchall("SHOW COLUMNS FROM lf_users");
		foreach($cols as $col)
			$columns[] = $col['Field'];
		if(in_array('salt', $columns))
			$db->query('ALTER TABLE lf_users DROP COLUMN salt');
		if(!in_array('hash', $columns))
			$db->query('ALTER TABLE lf_users ADD hash VARCHAR( 40 ) NOT NULL');
		
		echo '<h2>upgrade to 1.13.5-r130</h2>
			<p>Fixed ajaxfilemanager</p>
			<p>No more reCaptcha, only login limits</p>';
		$oldversion = '1.13.5-r130';
	}
}
