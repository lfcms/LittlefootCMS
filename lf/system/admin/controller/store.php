<?php

class store extends app
{
	public $repobase = "http://littlefootcms.com/files/download";
	
	public function main()
	{
		//$this->lf->vars[];
		
		$apps = file_get_contents($this->repobase.'/apps/apps.txt');
		$apps = array_flip(explode("\n",$apps,-1));
		$app_files = array_flip(scandir(LF.'apps'));
		
		$skins = file_get_contents($this->repobase.'/skins/skins.txt');
		$skins = array_flip(explode("\n",$skins,-1));
		$skin_files = array_flip(scandir(LF.'skins'));
		
		$plugins = file_get_contents($this->repobase.'/plugins/plugins.txt');
		$plugins = array_flip(explode("\n",$plugins,-1));
		$plugin_files = array_flip(scandir(LF.'plugins'));
		
		include 'view/store.main.php';
	}
	
	public function install($vars)
	{
		$type = $vars[1];
		
		//if($_FILES['skin']['type'] != 'application/zip') return;
		if($_FILES[$type]['size'] > 55000000) return;
				
		$target =  LF.$type.'s';
		
		//if(is_dir($target)) return;
		//if(!mkdir($target)) return;
		
		if(!move_uploaded_file($_FILES[$type]['tmp_name'], $target.'/install.zip')) 
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
		
		redirect302();
	}
	
	public function dlplugin()
	{
		if(!isset($this->lf->vars[1])) return 'Missing Arg 2';
		
		$this->item = $this->lf->vars[1];
		$this->type = 'plugins';
		
		return $this->downloader();
	}
	
	public function dlskin()
	{
		if(!isset($this->lf->vars[1])) return 'Missing Arg 2';
		
		$this->item = $this->lf->vars[1];
		$this->type = 'skins';
		
		return $this->downloader();
	}
	
	public function dlapp()
	{
		if(!isset($this->lf->vars[1])) return 'Missing Arg 2';
		
		$this->item = $this->lf->vars[1];
		$this->type = 'apps';
		
		return $this->downloader();
	}
	
	private function downloader()
	{
		$item = $this->item;
		$type = $this->type;
		
		//echo $this->repobase."/${type}/${type}.txt";
		$list = file_get_contents($this->repobase."/$type/$type.txt");
		$list = array_flip(explode("\n",$list,-1));
		
		//pre($list);
		
		if(isset($list[$item]))
		{
			$files = array_flip(scandir(ROOT.$type));
			
			if(isset($files[$vars[1]]))
				return 'App already downloaded: '.$item;
			
			$file = $this->repobase."/${type}/${item}.zip";
			$dest = LF.$type.'/'.$item.'.zip';
			//echo $file.'<br />';
			//echo $dest.'<br />';
			
			// download and unzip into apps/
			downloadFile($file, $dest);
			Unzip( LF.$type.'/', $item.'.zip' );
			unlink($dest);
			
			if($type == 'apps')
				$this->installsql($item);
			
		} else echo "$type not found: ".$item;
		
		redirect302();
	}
	
	private function installsql($app)
	{
		$sql = ROOT.'apps/'.$app.'/install.sql';
		if(is_file($sql))
		{
			$this->db->import($sql);
			unlink($sql);
		}
	}
	
	/*
	public function upgradesql($app)
	{
		if($this->simple) return;
		
		$sql = ROOT.'apps/'.$app.'/upgrade.sql';
		if(is_file($sql))
		{
			$this->db->import($sql);
			unlink($sql);
		}
	}*/
}