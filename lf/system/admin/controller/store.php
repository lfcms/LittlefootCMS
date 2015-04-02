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
	
	public function dlapps()
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