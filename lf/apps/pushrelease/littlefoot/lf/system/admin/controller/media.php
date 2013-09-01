<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information. 

class media
{	
	function __construct($request, $dbconn)
	{
		$file = $request->absbase.'system/lib/tinymce/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php';
		if(!is_file($file))
			echo $file;
	}
	
	public function main($var)
	{
		?>
		<p>Manage your files. You can add these files to your blog posts and pages if you want.</p>
		<iframe width="100%" height="500px" src="%relbase%lf/system/lib/tinymce/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php" frameborder="0"></iframe>
		<?php
	}
}

?>