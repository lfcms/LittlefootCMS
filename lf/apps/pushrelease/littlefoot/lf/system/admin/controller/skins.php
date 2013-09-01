<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information.

class skins
{
	private $db;
	private $request;
	
	function __construct($request, $dbconn)
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->pwd = ROOT.'skins/';
	}
	
	public function main($vars)
	{
		$pwd = $this->pwd;
		$request = $this->request;
		$install = extension_loaded('zip') ? '<input type="submit" value="Install" />' : "Error: Zip Extension missing.";
		include 'view/skins.main.php';
	}
	
	
	public function download($var)
	{
		echo '<h2><a href="%appurl%">Skins</a> / Download</h2>';
		
		echo '<p>Skins with a link can be installed. Those that are blank are already installed.</p>';
		
		
		
		$apps = file_get_contents('http://lf.bioshazard.com/files/download/skins/skins.txt');
		$apps = array_flip(explode("\n",$apps,-1));
		$files = array_flip(scandir(ROOT.'skins'));
		
		echo '<ul>';
		foreach($apps as $app => $ignore)
		{	
			echo '<li>';
			
			if(!isset($files[$app])) echo '<a href="%appurl%getappfromnet/'.$app.'/">'.$app.'</a>';
			else echo $app. ' [<a href="%appurl%getappfromnet/'.$app.'/update/">Update</a>]';
			echo '</li>';
		}
		echo '</ul>';
	}
	
	public function getappfromnet($vars)
	{
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

		$apps = file_get_contents('http://lf.bioshazard.com/files/download/skins/skins.txt');
		$apps = array_flip(explode("\n",$apps,-1));
		
		if(isset($apps[$vars[1]]))
		{
			$files = array_flip(scandir(ROOT.'skins'));
			if(isset($files[$vars[1]])) return 'app already downloaded: '.$vars[1];
			
			$file = 'http://lf.bioshazard.com/files/download/skins/'.$vars[1].'.zip';
			$dest = ROOT.'skins/'.$vars[1].'.zip';
			echo $file.'<br />';
			echo $dest.'<br />';
			
			// download and unzip into skins/
			downloadFile($file, $dest);
			Unzip( ROOT.'skins/', $vars[1].'.zip' );
			unlink($dest);
			
			
		} else echo "App not found: ".$vars[1];
		
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function install($vars)
	{
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		
		preg_match('/^([_\-a-zA-Z0-9]+)\.(zip|tar\.gz)/', $_FILES['skin']['name'], $match);
		
		if($match[2] != 'zip') return;
		//if($_FILES['skin']['type'] != 'application/zip') return;
		if($_FILES['skin']['size'] > 55000000) return;
				
		$target =  $this->pwd.$match[1];
		
		if(is_dir($target)) return;
		if(!mkdir($target)) return;
		
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
	
	public function rm($vars)
	{
		preg_match('/[a-z]+/', $vars[1], $matches);
		$vars['app'] = $matches[0];
		$app = $this->pwd.$matches[0];
		
		if(is_dir($app))
			$this->deleteAll($app);
		
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function edit($vars)
	{
		preg_match('/[a-z]+/', $vars[1], $matches);
		$skin = $this->pwd.$matches[0];
		
		if(is_dir($skin))
		{
			echo '<h3><a href="%appurl%">Skins</a> / <a href="%appurl%edit/'.$matches[0].'/">'.$matches[0].'</a></h3>
				<style type="text/css">
					#file { padding: 10px; background: #EEE; color: #333; font-family: arial; font-size: 16px; height: 600px; }
				</style>
			';
			
			$template = file_get_contents($skin.'/index.php'); 
			//$data = str_replace('%baseurl%', '%template%', $data);
			
			preg_match_all('/"%baseurl%\/([^".]+\.(?:css|js))"/', $template, $match);
			$files = $match[1];
			$files[-1] = 'index.php';
			
			if(!isset($vars[2])) $vars[2] = -1;
			$vars[2] = intval($vars[2]);
			$file = $skin.'/'.$files[$vars[2]];
			
			$data = '';
			if(is_file($file))
				$data = file_get_contents($file);
				
			$vars[1] .= '/'.$vars[2];
			
			echo '<form action="%appurl%update/'.$vars[1].'/" method="post" id="skinform">
			';	
			$data = preg_replace('/%([a-z]+)%/', '%{${1}}%', $data);
			ksort($files);
			foreach($files as $id => $url)
			{
				echo '<a href="%appurl%edit/'.$matches[0].'/'.$id.'/">'.$url.'</a><br />';
			}
			echo '
					<input type="submit" value="Update" /><br /><br />
					
					<style type="text/css" media="screen">
						#editor { 
							position: relative;
							top: 0;
							right: 0;
							bottom: 0;
							left: 0;
							height: 1000px;
							width: 100%;			}
					</style>
					<div id="editor">'.htmlentities($data).'</div><br />
					<input type="submit" value="Update" />
				</form>';
			
			?>
				<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
				<script src="http://d1n0x3qji82z53.cloudfront.net/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
				<script>
					var editor = ace.edit("editor");
					editor.setTheme("ace/theme/monokai");
					editor.getSession().setMode("ace/mode/html");
					
					$("#skinform").append('<textarea style="display: none" name="file" id="file" cols="30" rows="10"></textarea>');
					
					$("#skinform").submit(function(){
						$("textarea#file").val(editor.getValue());
					});
				</script>
			<?php
		}
	}
	
	public function update($vars)
	{
		preg_match('/[a-z]+/', $vars[1], $matches);
		$skin = $this->pwd.$matches[0];
		
		if(!isset($_POST['file'])) redirect302();
		
		$data = $_POST['file'];
		$data = preg_replace('/%{([a-z]+)}%/', '%${1}%', $data);
		
		$template = file_get_contents($skin.'/index.php'); 
		preg_match_all('/"%baseurl%\/([^".]+\.(?:css|js))"/', $template, $match);
		$files = $match[1];
		$files[-1] = 'index.php';
		
		$file = $skin.'/'.$files[$vars[2]];
		
		if(is_dir($skin))
		{
			if(!is_dir(dirname($file))) mkdir(dirname($file), 0755, true);
			file_put_contents($file, $data); 
		}
		
		redirect302();
	}
	
	public function setdefault($vars)
	{	
		if(preg_match('/[a-z0-9]+/', $vars[1], $matches))
		{
			$this->db->query("UPDATE lf_settings SET val = '".$matches[0]."' WHERE var = 'default_skin'");
			header('HTTP/1.1 302 Moved Temporarily');
			header('Location: '. $_SERVER['HTTP_REFERER']);
			exit();
		}	
		
		echo "Invalid skin supplied";
		$this->main('');
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