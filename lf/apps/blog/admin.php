<?php echo $this->request->apploader('blog_admin');

if(is_file(ROOT.'system/lib/tinymce/js.html'))
	readfile(ROOT.'system/lib/tinymce/js.html');
else
	echo 'No "TinyMCE" package found at '.ROOT.'system/lib/tinymce/';