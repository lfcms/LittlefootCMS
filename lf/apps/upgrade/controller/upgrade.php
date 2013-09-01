<?php

/*

public functions are the controllers
private functions are the models
view loads at the end

*/

class upgrade
{
	/*private $dbconn;
	public $html;
	private $model;
	
	function __construct($dbconn = NULL)
	{
		$this->db = $dbconn;
	}*/
	
	private $request;
	private $html;
	private $pwd;
	private $dbconn;
	
	public function __construct($request, $dbconn, $ini = '')
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->pwd = $request->absbase.'/apps';
		$this->ini = $ini;
	}
	
	public function main($var)
	{
		echo '<p>Current version: '.$this->request->api('version').'</p>';
		echo '<br />To update LittleFoot, just run [<a href="%appurl%lfup/">LFup</a>]';
		
		
	}
	
	public function lfup($var)
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
	
		downloadFile('http://littlefootcms.com/files/upgrade/system-latest.zip', ROOT.'system.zip');
		
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		exit();
	}
}

?>
