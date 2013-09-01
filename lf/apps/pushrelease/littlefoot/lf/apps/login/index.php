<div id="login">
<?php

if($this->auth['user'] != 'anonymous')
{
	$link = '';
	$url = $this->base."?logout=true";
		
	if($this->auth['access'] == 'admin')
		$link .= ' <a href="'.$this->base.'admin/">Admin</a> | ';
		
	$link .= '<a href="'.$url.'">Logout</a>';
		
	?>
	
	Hello, <?=$this->auth['display_name'];?> ( <?=$link;?> )
	
<?php } else { ?>

		<form action="<?=$_SERVER['REQUEST_URI'];?>" method="post">
			U: <input type="text" name="user" />
			P: <input type="password" name="pass" />
			<input style="padding: 2px; background: white; border: 1px;" type="submit" name="submit" value="Log In" /> or 
			<a href="%baseurl%signup/">Sign Up</a>
		</form>
<?php }

//echo $this->note.$this->error;

?>
</div>