<div id="_auth">
<?php

if($this->auth['user'] != 'anonymous')
{
        $link = '';
        $url = $this->base."?_auth=logout";
        if($this->auth['access'] == 'admin')
                $link .= ' <a href="'.$this->base.'admin/">Admin</a> | ';
        $link .= '<a href="'.$url.'">Logout</a>';

        ?><div class="_auth_loggedin">Hello, <?=$this->auth['display_name'];?> ( <?=$link;?> )</div>

<?php } else { 

$get = array();
$action = '&';
if(count($_GET))
{
	foreach($_GET as $var => $val)
		$get[] = $var.'='.$val;	
	$action .= implode('&', $get);
}

?>

	<form id="_auth_login_form" action="?_auth=login<?=$action;?>" method="post">
			<ul>
				<li class="_auth_user">User: <input type="text" name="user" /></li>
				<li class="_auth_pass">Pass: <input type="password" name="pass" /></li>
				<li class="_auth_submit"><input type="submit" value="Log In" /></li>
				<?php if($this->settings['signup'] == 'enabled') { ?><li class="_auth_signup">or <a href="%baseurl%signup/">Sign Up</a></li><?php } ?>
			</ul>
	</form> 

<?php }

//echo $this->note.$this->error;

?>
</div>
