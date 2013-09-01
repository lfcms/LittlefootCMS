<?php 

if($this->request->api('getuid') > 0)
	echo $this->request->apploader('notes'); 
else
	echo 'Please login to use this app.';

?>