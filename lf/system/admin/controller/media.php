<?php 

/**
 * @ignore
 */
class media
{	
	function __construct($request, $dbconn)
	{
		$file = ROOT.'system/lib/tinymce/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php';
		if(!is_file($file))
			echo 'Missing: '.$file;
	}
	
	public function main($var)
	{
		?>
		<h2>File Manager</h2>
		<p>Manage your files. You can add these files to your blog posts and pages if you want.</p>
		<br />
		<iframe width="100%" height="500px" src="%relbase%lf/system/lib/tinymce/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php" frameborder="0"></iframe>
		<?php
	}
}

?>