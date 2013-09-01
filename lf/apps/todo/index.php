<?php 

if($this->api('getuid') > 0) // if not anonymous
	echo $this->mvc('todo', $_app['ini']); 
else
	echo 'Please login to use this app.';

?>