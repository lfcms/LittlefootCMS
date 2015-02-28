<h2>Password Reset</h2>
<form action="%appurl%forgotresult/" method="post">
	Email: <input type="text" name="email" /> <input type="submit" value="Reset Password" />
</form>

<?php /*
<div id="_auth">
	<h3>Forget your password?</h3>
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

	<form id="_auth_login_form" action="%baseurl%_auth/remember" method="post">
		<input type="hidden" name="dest" value="<?php $_SERVER['REQUEST_URI']; ?>" />
		Email: <input type="text" name="email" /> <input type="submit" value="Reset password" /></li>
		<?php if($this->settings['signup'] == 'enabled') { ?><li class="_auth_signup">or <a href="%baseurl%signup/">Sign Up</a></li><?php } ?>
	</form> 

<?php }

//echo $this->note.$this->error;

?>
</div>

*/

?>