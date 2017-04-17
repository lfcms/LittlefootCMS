<?php

namespace lf\admin;

class media
{
	public function init()
	{
		$this->initSubdir();
		
	}
	
	// display file browser
	public function main()
	{
		// session array of chdir on base
		
		$filesWithDotFolders = scandir( $this->getFullDir() );
		$files = array_slice($filesWithDotFolders,2);
		
		include 'view/media.main.php';
	}
	
	public function open()
	{
		$file = \lf\requestGet('Param')[1];
		$sanatizeFilename = '/^[a-zA-Z0-9_\-]+\.(jpg|png|gif)$/i';
		if(!preg_match($sanatizeFilename, $file, $match))
		{
			notice('<div class="error">Filename does not pass sanatize filter</div>');
			redirect302();
		}
		
		$filename = $match[0];
		$imgurl = \lf\requestGet('Subdir').'lf/media/'.$this->getSubDir().'/'.$filename;
		
		include 'view/media.open.php';
	}
	
	public function chdir()
	{
		$args = \lf\requestGet('Param');
		$directoryValidate = '/^[a-zA-Z_\-]+$/';
		if(!preg_match($directoryValidate, $args[1], $match))
		{
			notice('<div class="error">Directory does not pass validation filter.</div>');
			redirect302();
		}
		$this->appendSubdir($match[0]);
		//notice('<div class="success">Changed directory</div>');		
		redirect302(\lf\requestGet('ActionUrl'));
	}
	
	// the public method available for direct passthrough call
	public function cdparent()
	{
		if( isset( \lf\requestGet('Param')[1] ) )
			$count = \lf\requestGet('Param')[1];
		else
			$count = 1;
		
		$this->chdirparent($count);
		
		redirect302(\lf\requestGet('ActionUrl'));
	}
	
	public function upload()
	{
		pre($_FILES);
		upload( $this->getFullDir() );
		notice('<div class="notice">Upload complete</div>');
		redirect302();
	}
	
	private function appendSubdir($subdir)
	{
		$_SESSION['lf_admin_media_subdir'][] = $subdir;
		return $this;
	}
	
	private function getSubDir()
	{
		return implode('/', $_SESSION['lf_admin_media_subdir']);
	}
	
	private function getFullDir()
	{
		return LF.'media/'.$this->getSubDir();
	}
	
	private function getSubDirDepth()
	{
		return count($_SESSION['lf_admin_media_subdir']);
	}
	
	private function initSubdir()
	{
		if(!isset($_SESSION['lf_admin_media_subdir']))
			$_SESSION['lf_admin_media_subdir'] = [];
		
		return $this;
	}
	
	private function getSubDirParts()
	{
		return $_SESSION['lf_admin_media_subdir'];
	}
	
	private function chdirparent($count = 1)
	{
		while($count > 0)
		{
			if( count( $_SESSION['lf_admin_media_subdir'] ) >= 1 )
				array_pop($_SESSION['lf_admin_media_subdir']);
			$count--;
		}
	}
}

?>