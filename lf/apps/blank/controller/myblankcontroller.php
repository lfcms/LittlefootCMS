<?php

class myblankcontroller 
{
	private $request;
	private $dbconn;
	
	public function __construct($request, $dbconn, $ini = '')
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->ini = $ini;
	}
	
	//default
	public function main($vars)
	{
		?>
		<h2>Welcome to the Example App</h2>
		<a href="%appurl%otherpage/">Click here to get to the other method</a>
		<?php

	}
	
	public function otherpage($vars)
	{
		?><h2>Welcome to the Example App</h2>
                <a href="%appurl%main/">Click here to get back</a> (notice that you can call main() either by name or by default)<?php
		echo '<pre>$vars: ';
		print_r($vars);
		echo '</pre>';
	}

}

?>
