<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information. // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information.
// Unzip.
function Unzip($dir, $file, $destiny="")
{
	$dir .= DIRECTORY_SEPARATOR;
	$path_file = $dir . $file;
	$zip = zip_open($path_file);
	$_tmp = array();
	$count=0;
	if ($zip)
	{
		while ($zip_entry = zip_read($zip))
		{
			$_tmp[$count]["filename"] = zip_entry_name($zip_entry);
			$_tmp[$count]["stored_filename"] = zip_entry_name($zip_entry);
			$_tmp[$count]["size"] = zip_entry_filesize($zip_entry);
			$_tmp[$count]["compressed_size"] = zip_entry_compressedsize($zip_entry);
			$_tmp[$count]["mtime"] = "";
			$_tmp[$count]["comment"] = "";
			$_tmp[$count]["folder"] = dirname(zip_entry_name($zip_entry));
			$_tmp[$count]["index"] = $count;
			$_tmp[$count]["status"] = "ok";
			$_tmp[$count]["method"] = zip_entry_compressionmethod($zip_entry);

			if (zip_entry_open($zip, $zip_entry, "r"))
			{
				$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
				if($destiny)
				{
					$path_file = str_replace("/",DIRECTORY_SEPARATOR, $destiny . zip_entry_name($zip_entry));
				}
				else
				{
					$path_file = str_replace("/",DIRECTORY_SEPARATOR, $dir . zip_entry_name($zip_entry));
				}
				$new_dir = dirname($path_file);

				// Create Recursive Directory
				@mkdir($new_dir);

				$fp = @fopen($dir . zip_entry_name($zip_entry), "w");
				@fwrite($fp, $buf);
				@fclose($fp);

				zip_entry_close($zip_entry);
			}
			echo "\n</pre>";
			$count++;
		}

		zip_close($zip);
	}
}

function install()
{
	include 'install/install.php';
	exit();
}

function upgrade() 
{
	$time = time();
	if(!is_dir('backup'))
		mkdir('backup');
	if(!rename(ROOT.'system', ROOT.'backup/system-'.$time)) // if unable to rename...
		echo 'Unable to move '.ROOT.'system to '.ROOT.'backup/system-'.$time; 
	else
	{
		// unzip into system/
		$file = 'system.zip';
		$dir = ROOT;
		Unzip($dir,$file);
		unlink(ROOT.'system.zip');
		
		echo 'Latest Littlefoot system/ installed. <a href="'.$_SERVER['HTTP_REFERER'].'">Return to Littlefoot CMS</a>';
		exit();
	}
}

function redirect302($url = '')
{		
	if($url == '') $url = $_SERVER['HTTP_REFERER'];
	
	header('HTTP/1.1 302 Moved Temporarily');
	header('Location: '.$url);
	exit();
}

function get_include($path)
{
	if(!is_file($path)) return false;
	
	ob_start();
	include $path; 
	return ob_get_clean();
}

?>