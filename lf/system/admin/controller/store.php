<?php

namespace lf\admin;

/**
 * LF Admin store Manager controller
 */
class store
{
	// public $repobase = "http://littlefootcms.com/files/download";
	public $github_api = 'https://api.github.com/search/';

	public function main()
	{

		// TODO: Caching and single query rather than 3

		// Get Apps
		$app_q = curl_get_contents($this->github_api.'repositories?q=topic:littlefoot-app+org:lfcms');
		$app_json = json_decode($app_q, TRUE);
		if(isset($app_json["message"])) {
			pre($app_json);
			return;
		}
		foreach($app_json["items"] as $app) {
			$apps[$app["name"]] = $app["html_url"].'/archive/development.zip';
		}
		$app_files = array_flip(scandir(LF.'apps'));

		// Get Skins
		$skin_q = curl_get_contents($this->github_api.'repositories?q=topic:littlefoot-skin+org:lfcms');
		$skin_json = json_decode($skin_q, TRUE);
		if(isset($skin_json["message"])) {
			pre($skin_json);
			return;
		}
		foreach($skin_json["items"] as $skin) {
			$skins[$skin["name"]] = $skin["html_url"].'/archive/development.zip';
		}
		$skin_files = array_flip(scandir(LF.'skins'));



		// Get Plugins
		$plugin_q = curl_get_contents($this->github_api.'repositories?q=topic:littlefoot-plugin+org:lfcms');
		$plugin_json = json_decode($plugin_q, TRUE);
		if(isset($plugin_json["message"])) {
			pre($plugin_json);
			return;
		}
		foreach($plugin_json["items"] as $plugin) {
			$plugins[$plugin["name"]] = $plugin["html_url"].'/archive/development.zip';
		}
		$plugin_files = array_flip(scandir(LF.'plugins'));



		include 'view/store.main.php';
	}

	public function installfromurl()
	{
		/*

		pre($_POST);
		exit();

		$type = \lf\requestGet('Param')[1];

		switch($type)
		{
			//case 'app':
			//	$this->
		}

		*/

		notice('Feature not yet implemented');

		redirect302();
	}
	/* .zip upload - disabled for now
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
	}*/

	public function dlplugin()
	{
		if(!isset(\lf\requestGet('Param')[1])) return 'Missing Arg 2';

		$this->item = \lf\requestGet('Param')[1];
		$this->type = 'plugin';

		return $this->downloader();
	}

	public function dlskin()
	{
		if(!isset(\lf\requestGet('Param')[1])) return 'Missing Arg 2';

		$this->item = \lf\requestGet('Param')[1];
		$this->type = 'skin';

		return $this->downloader();
	}

	public function dlapp()
	{
		if(!isset(\lf\requestGet('Param')[1])) return 'Missing Arg 2';

		$this->item = \lf\requestGet('Param')[1];
		$this->type = 'app';

		return $this->downloader();
	}

	private function downloader()
	{
		$item = $this->item;
		$type = $this->type;

		//echo $this->repobase."/${type}/${type}.txt";

		// $list = curl_get_contents($this->repobase."/$type/$type.txt");
		// $list = array_flip(explode("\n",$list,-1));

		// Get Skins
		$q = curl_get_contents($this->github_api.'repositories?q=topic:littlefoot-'.$this->type.'+org:lfcms');
		$qjson = json_decode($q, TRUE);
		foreach($qjson["items"] as $repo) {
			$list[$repo["name"]] = $repo["html_url"].'/archive/development.zip';
		}

		pre($item);
		pre($type);
		pre($list);

		// exit();







		if(isset($list[$item]))
		{
			$files = array_flip(scandir(LF.$type.'s'));

			if(isset($files[$item]))
				return 'Already downloaded: '.$item;

			// $file = $this->repobase."/${type}/${item}.zip";
			$file = $list[$item];
			pre($file);

			$dest = LF.$type.'s/'.$item.'.zip';
			pre($dest);
			// exit();

			//echo $file.'<br />';
			//echo $dest.'<br />';

			// download and unzip into apps/
			downloadFile($file, $dest);
			Unzip( LF.$type.'s/', $item.'.zip' );
			unlink($dest);

			// TODO: Do not rely on the -development suffix...
			rename(LF.$type.'s/'.$item.'-development',LF.$type.'s/'.strtolower($item));

			if($type == 'app')
				$this->installsql($item);

		} else echo "$type not found: ".$item;

		redirect302();
	}

	/**
	 * Used to install .zips from URL in store
	 */
	public function dlFromZipUrl()
	{
		if( !isset( $_POST['download'] ) )
		{
			notice('Missing post data');
			redirect302();
		}

		$url = $_POST['download']['url'];
		$type = $_POST['download']['type'];

		if( ! preg_match('/^(apps|skins|plugins)$/', $type, $match) )
		{
			notice('Invalid type used: '.$type);
			redirect302();
		}

		$type = $match[0]; // or [1], whatever

		// get just the end part: domain.com/folder/theendpart.zip
		$fileName = end(explode('/', $url));
		$fileParts = explode('.', $fileName);

		// if loading a .git https address (maybe github?)
		if(end($fileParts) == 'git')
		{
			// ill do this later
		}

		// build download location
		$dest = LF.$type.'/'.$fileName;

		// download .zip into LF/type/.
		downloadFile($url, $dest);

		// unzip into LF/$type/
		Unzip( LF.$type.'/', $fileName );

		// delete .zip file
		unlink($dest);

		// disabled cuz you can reinstall in settings
		// // install .sql if app
		// if($type == 'apps')
			// $this->installsql($rename);

		notice('<div class="notice">Installed successfully</div>');
		redirect302();
	}

	/**
	 * offer download from littlefootcms.com repo, or your own
	 */
	public function fromRepo()
	{
		if($_POST['app'] == '')
			redirect302();

		$url = $_POST['url'];
		$item = $_POST['app'];
		$type = key($_POST['download']);

		//echo $this->repobase."/${type}/${type}.txt";
		$list = curl_get_contents($this->repobase."/$type/$type.txt");
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

	public function zipfromurl()
	{
		if($_POST['app'] == '')
			redirect302();

		$url = $_POST['url'];
		$item = $_POST['app'];
		$type = key($_POST['download']);

		//echo $this->repobase."/${type}/${type}.txt";
		$list = curl_get_contents($this->repobase."/$type/$type.txt");
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
			(new \lf\orm)->import($sql);
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
			(new \lf\orm)->import($sql);
			unlink($sql);
		}
	}*/
}
