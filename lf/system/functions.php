<?php

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
			$count++;
		}

		zip_close($zip);
	}
}

function upgrade()
{
	$time = time();
	if(!is_dir('backup')) mkdir('backup');
	$oldversion = file_get_contents(ROOT.'system/version');
	
	if(!rename(ROOT.'system', ROOT.'backup/system-'.$time)) // if unable to rename...
		echo 'Unable to move '.ROOT.'system to '.ROOT.'backup/system-'.$time; 
	else if(!is_file(ROOT.'system.zip'))
	{
		echo ROOT.'system.zip does not exist';
	} else
	{
		// unzip into system/
		$file = 'system.zip';
		$dir = ROOT;
		Unzip($dir,$file);
		
		if(!is_dir(ROOT.'system'))
			echo 'Failed to unzip system.zip';
		else
		{
			unlink(ROOT.'system.zip');
			echo 'Littlefoot update installed. <a href="?">Click here to return to the previous page.</a>';
			exit();
		}
		
	}
}

function revert() 
{
	if(!is_dir('backup')) redirect302();
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

// ty xaav from [http://stackoverflow.com/questions/3938534/download-file-to-server-from-url]
function downloadFile ($url, $path) {

	$newfname = $path;
	$file = fopen ($url, "rb");
	if ($file) {
		$newf = fopen ($newfname, "wb");

		if ($newf)
		while(!feof($file)) {
			fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
		}
	}

	if ($file) {
		fclose($file);
	}

	if ($newf) {
		fclose($newf);
	}
}

// process submitted $_FILES, allow only images by default
function upload($destfolder, $allowedExts = array("jpg", "jpeg", "gif", "png"), $allowedTypes = array("image/gif", "image/jpeg", "image/pjpeg", "image/png"), $limit = 5)
{
	if($destfolder[strlen($destfolder) - 1] != '/') $destfolder .= '/';
	$return = array();
	foreach($_FILES as $key => $file)
	{
		if($limit < 1) break;
		
		$filedata = explode(".", $file["name"]);
		$extension = strtolower($filedata[1]);
		
		// If either filter array has elements, return invalid if mismatch
		if((count($allowedExts) && !in_array($extension, $allowedExts)) 
		|| (count($allowedTypes) && !in_array($file["type"], $allowedTypes)))
		{
			$return[$key] = "Error: Invalid file ext/type";
			continue;
		}
		
		if ($file["error"] > 0) {
			$return[$key] = "Error: Code " . $file["error"];
		} else {
			if(!is_dir($destfolder)) {
				if(!mkdir($destfolder, 0755, true))
				{
					$return[$key] = "Error: Unable to create upload dir '".$destfolder."'";
					continue;
				}
			}
			
			// handle duplicate filename
			if (file_exists($destfolder.$file["name"]))
			{
				$count = 1;
				while(file_exists($destfolder.$count.'-'.$file["name"]))
					$count++;
					
				$file["name"] = $count.'-'.$file["name"];
			}
			
			//$file["name"] = 'dupe_'.date('U').$file["name"];
			
			$success = move_uploaded_file(
				$file["tmp_name"],
				$destfolder.$file["name"]
			);
			
			if($success)
				$return[$key] = $file["name"];
			else
				$return[$key] = "Error: Failed at move_uploaded_file(".$file["tmp_name"].', '.$destfolder.$file["name"].')';
		}
		
		$limit--; // to prevent spoofed multifile. this used to be unprotected :\
	}
	
	$_FILES = array();
	return $return;
}

function lfbacktrace()
{
	$bt = debug_backtrace(); 

	foreach($bt as $t)
	{
		$class = '';
		if(isset($t['class'])) $class = $t['class'].'->';
		
		$args = '';
		if(isset($t['args']))
		{
			foreach($t['args'] as $arg)
				$args .= print_r($arg, true).', ';
			$args = substr($args, 0, -2);
		}
		
		if(isset($t['file']))
			echo '<br />'.$t['file'].' line '.$t['line'].' '.$class.$t['function'].'('.$args.')';
	}
}

// ty Yuriy [http://stackoverflow.com/questions/1296681/php-simplest-way-to-delete-a-folder-including-its-contents]
function rrmdir($dir) { 
  foreach(glob($dir . '/*') as $file) { 
    if(is_dir($file)) rrmdir($file); else unlink($file); 
  } rmdir($dir); 
}

// process HTML to produce thumbnails from larger images. it scrapes and replaces the image URLs
function thumbnail($html, $dimensions = '200x200')
{
	if(!preg_match_all('/src="([^"]+\.(png|jpe?g))"/', $html, $match)) return $html;
	
	$hw = explode('x', $dimensions);
			
	$tw = $hw[0];
	$th = $hw[1];
	
	for($i = 0; $i < count($match[0]); $i++)
	{
		$img = $match[1][$i];
		
		$thumb = ROOT.'media/_lfthumb/thumb_'.$dimensions.'_'.md5($img).'.'.$match[2][$i];
		if(!is_file($thumb))
		{
			if(!is_dir(ROOT.'media/_lfthumb/')) mkdir(ROOT.'media/_lfthumb/');
			
			if(strpos($img, 'lf/media/'))
				$img = preg_replace('/(.*)lf\/media\//', ROOT.'media/', urldecode($img));
				
			$fs = getimagesize($img);
			$w = $fs[0];
			$h = $fs[1];
			
			$tn = imagecreatetruecolor($tw, $th);
			
			/*echo '<pre>';
			var_dump($tn, $image, 0, 0, 0, 0, $tw, $th, $w, $h);
			echo '</pre>';*/
			
			switch($fs['mime'])
			{
				case 'image/jpeg':
					$image = imagecreatefromjpeg($img);
					imagecopyresampled($tn, $image, 0, 0, 0, 0, $tw, $th, $w, $h); 
					imagejpeg($tn, $thumb, 100);	
					
					break;
					
				case 'image/png':
					$image = imagecreatefrompng($img);
					
					// for png conversion
					imagecopyresampled($tn, $image, 0, 0, 0, 0, $tw, $th, $w, $h);
					
					imagealphablending($image, true);
					imagesavealpha($image, true);
					
					imagepng($tn, $thumb);
			}

			// Here we are saving the .jpg, you can make this gif or png if you want
			//the file name is set above, and the quality is set to 100%
		}
		
		$html = str_replace($match[1][$i], '%relbase%lf/media/_lfthumb/thumb_'.$dimensions.'_'.md5($match[1][$i]).'.'.$match[2][$i], $html);
	}
	
	return $html;
}

function jsprompt($msg = 'Are you sure?')
{
	return 'onclick="return confirm(\''.$msg.'\');"';
}

?>