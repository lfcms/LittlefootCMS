<?php

class skins extends app
{
	function init($args)
	{
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
		
		
		
		$apps = file_get_contents('http://littlefootcms.com/files/download/skins/skins.txt');
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
		
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function install($vars)
	{
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
	
	public function blankskin($vars)
	{
		$name = $_POST['name'];
		if(!preg_match('/^[_\-a-zA-Z0-9]+$/', $name, $match)) redirect302();
		if(!mkdir(ROOT.'skins/'.$match[0])) { return "Failed to make directory at ".ROOT.'skins/'.$match[0]; }
		$data = '<html>
	<head>
		<title>%title%</title>
		<link rel="stylesheet" type="text/css" href="%{skinbase}%/css/styles.css" />
	</head>
	<body>
		<h1>Blank Template</h1>
		%login%<br />
		%nav%<br />
		<div>%content%</div>
	</body>
</html>';
		file_put_contents(ROOT.'skins/'.$match[0].'/index.php', $data);
		redirect302();
	}
	
	public function rm($vars)
	{
		preg_match('/[_\-a-zA-Z0-9]+/', $vars[1], $matches);
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
		preg_match('/[_\-a-zA-Z0-9]+/', $vars[1], $matches);
		$skin = $this->pwd.$matches[0];
		
		if(is_dir($skin))
		{
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
			
			echo '<form action="%appurl%update/'.$vars[1].'/" method="post" id="skinform">
			
					<div id="skin_nav">';	
			echo '<h3><a href="%appurl%">Skins</a> / <a href="%appurl%edit/'.$matches[0].'/">'.$matches[0].'</a></h3>';
			$data = preg_replace('/%([a-z]+)%/', '%{${1}}%', $data);
			ksort($files);
			
			if(!is_file($skin.'/home.php')) echo '<a href="%appurl%makehome/'.$matches[0].'">(create home.php)</a><br />';
			
			foreach($files as $id => $url)
			{
				$select = '';
				if($id == $vars[2]) $select = ' class="selected"';
				
				echo '<a'.$select.' href="%appurl%edit/'.$matches[0].'/'.$id.'/">'.$url.'</a><br />';
			}
			echo '
						<input type="submit" value="Update" /><br /><br />
					</div>
					
					<style type="text/css" media="screen">
						#editor { 
							position: relative;
							top: 0;
							right: 0;
							bottom: 0;
							left: 0;
							height: '.($linecount*16).'px;
							width: 100%;			}
					</style>
					<div id="editor">'.htmlentities($data).'</div><br />
					<input type="submit" value="Update" />
				</form>';
			?>
				<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
				<script src="http://d1n0x3qji82z53.cloudfront.net/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
				<script>
				$(document).ready(function(){
					var editor = ace.edit("editor");
					editor.setShowPrintMargin(false);
					editor.setTheme("ace/theme/textmate");
					editor.getSession().setMode("ace/mode/<?php echo $ext; ?>");
					editor.focus(); //To focus the ace editor
					
					 
					
					$("#skinform").append('<textarea style="display: none" name="file" id="file" cols="30" rows="10"></textarea>');
					
					$("#skinform").submit(function(){
						$("textarea#file").val(editor.getValue());
						
						$("#skinform").append('<input type="hidden" id="hidden_ajax" name="ajax" value="true" />');
						
						//   var dataString = 'name='+ name + '&email=' + email + '&phone=' + phone;
						$.ajax({ 
						  type: "POST", 
						  url: $("#skinform").attr("action"),  
						  data: $("#skinform").serialize(),  
						  success: function(data) {  
							$("#hidden_ajax").remove(); // unset ajax
							$("#skinform input[name=csrf_token]").val(data);
							
							//display message back to user here
							$(".ajax_message").remove();
							
							$('#skin_nav').append('<p class="ajax_message">saved</p>');
							
							
							$(".ajax_message").hide('slow');
							
							
							//$("#ajax_message").remove();
							
							
							/*$token = NoCSRF::generate( 'csrf_token' );
							$out = str_replace($match[0][$i], $match[0][$i].' <input type="hidden" name="csrf_token" value="'.$token.'" />', $out);*/
						  }  
						});  
						return false;  
					});
					
					
					/*$(window).scroll(function(){
					  if($(this).scrollTop() > 400$('#editor').position().top){
						$('#skin_nav').css({position:'fixed',top:10,left:10});
					  }else{
						$('#skin_nav').css({position:'relative'});
					  } 

					});*/
				});
				</script>
			<?php
		}
	}
	
	public function makehome($args)
	{
		preg_match('/[_\-a-zA-Z0-9]+/', $args[1], $matches);
		$skin = $this->pwd.$matches[0];
		
		if(!is_file($skin.'/home.php'))
		file_put_contents($skin.'/home.php', file_get_contents($skin.'/index.php'));
		
		redirect302();
	}
	
	public function update($vars)
	{
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
	
	public function setdefault($vars)
	{	
		if(preg_match('/[_\-a-zA-Z0-9]+/', $vars[1], $matches))
		{
			$this->db->query("UPDATE lf_settings SET val = '".$matches[0]."' WHERE var = 'default_skin'");
			header('HTTP/1.1 302 Moved Temporarily');
			header('Location: '. $_SERVER['HTTP_REFERER']);
			exit();
		}	
		
		echo "Invalid skin supplied";
		$this->main('');
	}
	
	public function zip($vars)
	{
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
