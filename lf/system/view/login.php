<div id="_auth">
<?php

if($this->auth['user'] != 'anonymous')
{
        $link = '';
        if($this->auth['access'] == 'admin')
                $link .= ' <a href="'.$this->base.'admin/">Admin</a> | ';
        $link .= '<a href="'.$this->base.'_auth/logout">Logout</a>';

        ?><div class="_auth_loggedin">Hello, <?=$this->auth['display_name'];?> ( <?=$link;?> )</div>

<?php } else {

?>

	<form id="_auth_login_form" action="%baseurl%_auth/login" method="post">
			<ul>
				<input type="hidden" name="dest" value="<?php $_SERVER['REQUEST_URI']; ?>" />
				<li class="_auth_user">User: <input type="text" name="user" /></li>
				<li class="_auth_pass">Pass: <input type="password" name="pass" /></li>
				<li class="_auth_submit"><input type="submit" value="Log In" /></li>
				<li><a href="%baseurl%_auth/forgotform">Reset?</a></li>
				<?php if($this->settings['signup'] == 'on') { ?><li class="_auth_signup">or <a href="%baseurl%_auth/main">Sign Up</a></li><?php } ?>
			</ul>
	</form> 

<?php }

//echo $this->note.$this->error;

?>
</div>
