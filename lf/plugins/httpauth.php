<?php

function httpauth($request)
{
	//$request->settings['default_skin'] = 'aios';
	
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
		header('WWW-Authenticate: Basic realm="My Realm"');
		header('HTTP/1.0 401 Unauthorized');
		echo 'Access Denied';
		exit;
	} else {
		echo "<p>Hello {$_SERVER['PHP_AUTH_USER']}.</p>";
		echo "<p>You entered {$_SERVER['PHP_AUTH_PW']} as your password.</p>";
	}
}

//$this->hook_add('plugins_loaded', 'httpauth');

?>