<?php 

if($this->api('getuid') == 0)
	echo 'Access Denied';
else
	echo $this->mvc('bugtrack', $_app['ini']);
/*
if(is_file(ROOT.'system/lib/tinymce/js.html'))
	readfile(ROOT.'system/lib/tinymce/js.html');
else
	echo 'No "TinyMCE" package found at '.ROOT.'system/lib/tinymce/';*/

//echo $this->apploader('blog', $_app['ini']); 

?>