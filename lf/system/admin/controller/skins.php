<?php

namespace lf\admin;

/**
 * LF Admin Skins Manager controller
 */
class skins
{
	public function init()
	{
		$this->pwd = ROOT.'skins/';
	}
	
	public function main()
	{
		$vars = \lf\requestGet('Param'); // backward compatibility
		
		$pwd = $this->pwd;
		$request = $this->request;
		$install = extension_loaded('zip') ? '<input type="submit" value="Install" />' : "Error: Zip Extension missing.";
		
		$skins = array();
		foreach(scandir($pwd) as $file)
		{
			if($file == '.' || $file == '..') continue;
			
			$skin = $pwd.'/'.$file;	
			
			if(is_file($skin.'/index.php') || is_file($skin.'/index.html'))
				$skins[] = $file;
		}
		
		include 'view/skins.main.php';
	}
	
	public function edit()
	{
		$vars = \lf\requestGet('Param'); // backward compatibility
		
		preg_match('/[_\-a-zA-Z0-9]+/', $vars[1], $matches);
		$skin = $this->pwd.$matches[0];
		
		if(!is_dir($skin)) return 'Skin not found!';
		
	
		$template = file_get_contents($skin.'/index.php'); 
		//$data = str_replace('%baseurl%', '%template%', $data);
		
		
		preg_match_all('/"%skinbase%\/([^".]+\.(css|js))"/', $template, $match);
		$files = $match[1];
		$files[-1] = 'index.php';
		
		if(is_file($skin.'/home.php'))
			$files[-2] = 'home.php';
		
		if(!isset($vars[2])) $vars[2] = -1;
		$vars[2] = intval($vars[2]);
		$file = $skin.'/'.$files[$vars[2]];
		$ext = 'html';
		if($vars[2] != -1 && $vars[2] != -2) $ext = $match[2][$vars[2]];
		
		$data = '';
		if(is_file($file))
			$data = file_get_contents($file);
		
		$linecount = substr_count( $data, "\n" ) + 1 + 10;
		
		$vars[1] .= '/'.$vars[2];
		
		$data = preg_replace('/%([a-z]+)%/', '%{${1}}%', $data);
		
		ksort($files);
		
		include 'view/skins.edit.php';
	}
	
	public function download()
	{
		$vars = \lf\requestGet('Param'); // backward compatibility
		
		$apps = file_get_contents('http://littlefootcms.com/files/download/skins/skins.txt');
		$apps = array_flip(explode("\n",$apps,-1));
		$files = array_flip(scandir(ROOT.'skins'));
		
		include 'view/skins.download.php';
	}
	
	public function getappfromnet()
	{
		$vars = \lf\requestGet('Param'); // backward compatibility
		
		$apps = file_get_contents('http://littlefootcms.com/files/download/skins/skins.txt');
		$apps = array_flip(explode("\n",$apps,-1));
		
		if(isset($apps[$vars[1]]))
		{
			$files = array_flip(scandir(ROOT.'skins'));
			if(isset($files[$vars[1]])) return 'app already downloaded: '.$vars[1];
			
			$file = 'http://littlefootcms.com/files/download/skins/'.$vars[1].'.zip';
			$dest = ROOT.'skins/'.$vars[1].'.zip';
			echo $file.'<br />';
			echo $dest.'<br />';
			
			// download and unzip into skins/
			downloadFile($file, $dest);
			Unzip( ROOT.'skins/', $vars[1].'.zip' );
			unlink($dest);
			
			
		} else echo "App not found: ".$vars[1];
		
		redirect302();
	}
	
	public function install()
	{
		$vars = \lf\requestGet('Param'); // backward compatibility
		
		// lol this was way before `redirect302()`
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		
		/*1preg_match('/^([_\-a-zA-Z0-9]+)\.(zip|tar\.gz)/', $_FILES['skin']['name'], $match);
		
		if($match[2] != 'zip') return;*/
		//if($_FILES['skin']['type'] != 'application/zip') return;
		if($_FILES['skin']['size'] > 55000000) return;
				
		$target =  $this->pwd/*1.$match[1]*/;
		
		//if(is_dir($target)) return;
		//if(!mkdir($target)) return;
		
		if(!move_uploaded_file($_FILES['skin']['tmp_name'], $target.'/install.zip')) 
		{ 
			echo "Sorry, there was a problem uploading your file."; 
			return; 
		}
		else
		{
			//echo "The file ". $match[0]. " has been uploaded";
			$zip = zip_open($target.'/install.zip');
			if($zip)
			{
				while ($zip_entry = zip_read($zip)) { 
				
					if(preg_match('/^(.+)\/$/', zip_entry_name($zip_entry), $file))
					{
						if(!mkdir($target.'/'.$file[1]))
						{
							echo "fail";
						}
					}		
					else if(zip_entry_open($zip, $zip_entry, "r"))
					{
						$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
						
						$fp = fopen($target.'/'.zip_entry_name($zip_entry), "w");
						fwrite($fp,"$buf");
						zip_entry_close($zip_entry);
						fclose($fp);
					} 
				}
				zip_close($zip);
				unlink($target.'/install.zip');
			}
		}
		
		exit();
	}
	
	public function blankskin()
	{
		$vars = \lf\requestGet('Param'); // backward compatibility
		
		$name = $_POST['name'];
		if(!preg_match('/^[_\-a-zA-Z0-9]+$/', $name, $match)) 
			redirect302();
		
		if(!mkdir(ROOT.'skins/'.$match[0])) 
			return "Failed to make directory at ".ROOT.'skins/'.$match[0];
		
		$data = file_get_contents(LF.'system/template/blanktheme.php');
		
		file_put_contents(ROOT.'skins/'.$match[0].'/index.php', $data);
		
		redirect302();
	}
	
	public function rm()
	{
		$vars = \lf\requestGet('Param'); // backward compatibility
		preg_match('/[_\-a-zA-Z0-9]+/', $vars[1], $matches);
		$vars['app'] = $matches[0];
		$app = $this->pwd.$matches[0];
		
		if(is_dir($app))
			$this->deleteAll($app);
		
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function makehome()
	{
		$vars = \lf\requestGet('Param'); // backward compatibility
		preg_match('/[_\-a-zA-Z0-9]+/', $args[1], $matches);
		$skin = $this->pwd.$matches[0];
		
		if(!is_file($skin.'/home.php'))
		file_put_contents($skin.'/home.php', file_get_contents($skin.'/index.php'));
		
		redirect302();
	}
	
	public function update()
	{
		$vars = \lf\requestGet('Param'); // backward compatibility
		preg_match('/[_\-a-zA-Z0-9]+/', $vars[1], $matches);
		$skin = $this->pwd.$matches[0];
		
		if(!isset($_POST['file'])) redirect302();
		
		$data = $_POST['file'];
		$data = preg_replace('/%{([a-z]+)}%/', '%${1}%', $data);
		
		$template = file_get_contents($skin.'/index.php'); 
		preg_match_all('/"%skinbase%\/([^".]+\.(?:css|js))"/', $template, $match);
		$files = $match[1];
		$files[-1] = 'index.php';
		$files[-2] = 'home.php';
		
		$file = $skin.'/'.$files[$vars[2]];
		
		if(is_dir($skin))
		{
			if(!is_dir(dirname($file))) mkdir(dirname($file), 0755, true);
			file_put_contents($file, $data); 
		}
		
		
		// return csrf for next form submission
		if(isset($_POST['ajax']) && $_POST['ajax'] == 'true')
		{			
			echo NoCSRF::generate( 'csrf_token' ); 
			$this->request->settings['debug'] = false;
			exit(); 
		} else redirect302();
	}
	
	public function setdefault()
	{	
		$vars = \lf\requestGet('Param'); // backward compatibility
		if(preg_match('/[_\-a-zA-Z0-9]+/', $vars[1], $matches))
		{
			(new \lf\orm)->query("UPDATE lf_settings SET val = '".$matches[0]."' WHERE var = 'default_skin'");
			header('HTTP/1.1 302 Moved Temporarily');
			header('Location: '. $_SERVER['HTTP_REFERER']);
			exit();
		}	
		
		echo "Invalid skin supplied";
		$this->main('');
	}
	
	public function zip()
	{
		$vars = \lf\requestGet('Param'); // backward compatibility
		if(!preg_match('/^[_\-a-zA-Z0-9]+$/', $vars[1], $match))
			redirect302();
		else
		{
			$file = $match[0];
			$zip = $file.".zip";
			
			$cwd = getcwd();
			chdir(ROOT.'skins');
			$skins = scandir('.');
			if(!in_array($file, $skins)) redirect302();
			
			$var = exec('zip -r '.$file.' '.$file, $out, $returncode);
			if($returncode != 0) redirect302();

			header("Content-Type: application/zip");
			header("Content-Transfer-Encoding: Binary");
			header("Content-Length: ".filesize($zip));
			header("Content-Disposition: attachment; filename=\"".$zip."\"");
			readfile($zip);
			unlink($file.".zip");
			exit();
		}
		
	}
	
	private function deleteAll($directory, $empty = false) {
		if(substr($directory,-1) == "/") {
			$directory = substr($directory,0,-1);
		}

		if(!file_exists($directory) || !is_dir($directory)) {
			return false;
		} elseif(!is_readable($directory)) {
			return false;
		} else {
			$directoryHandle = opendir($directory);
		   
			while ($contents = readdir($directoryHandle)) {
				if($contents != '.' && $contents != '..') {
					$path = $directory . "/" . $contents;
					
					if(is_dir($path)) {
						$this->deleteAll($path);
					} else {
						unlink($path);
					}
				}
			}
		   
			closedir($directoryHandle);

			if($empty == false) {
				if(!rmdir($directory)) {
					return false;
				}
			}
		   
			return true;
		}
	}
}

?>
