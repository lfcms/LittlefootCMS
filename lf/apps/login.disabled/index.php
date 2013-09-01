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
	
	Hello, <?=$this->auth['display_name'];?> ( <?=$link;?> ) <?php echo $this->extmvc('notify', 'notification/notification'); ?>
	
<?php } else { ?>
	
		<form action="?" method="post">
			U: <input type="text" name="user" /> P: <input type="password" name="pass" /> 
			<input style="padding: 5px; background: white; border: 1px;" type="submit" name="submit" value="Log In" />
		</form> or <a href="%baseurl%signup/">Sign Up</a>
<?php }

//echo $this->note.$this->error;

?>
</div>