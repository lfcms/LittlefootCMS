<div id="auth_lf">
<?php

if($this->api('me') != 'Anonymous')
{
        $link = '';
        if($this->auth['access'] == 'admin')
                $link .= ' <a href="'.$this->base.'admin/">admin</a>';
        $link .= '<a href="'.$this->base.'_auth/logout">logout</a>';

        ?><div class="auth_welcome">Hello, <?=$this->api('me');?>. <?=$link;?></div>

<?php } else {

?>
	<div class="auth_login">
		<form id="auth_login_form" action="%baseurl%_auth/login" method="post">
				<ul>
					<input type="hidden" name="dest" value="<?php $_SERVER['REQUEST_URI']; ?>" />
					<li class="auth_user"><input type="text" name="user" placeholder="username" /></li>
					<li class="auth_pass"><input type="password" name="pass" placeholder="password" /></li>
					<li class="auth_submit"><input class="light_gray button" type="submit" value="Log In" /></li>
					<li class="auth_links">
						<a href="%baseurl%_auth/forgotform">Forgot?</a>
					</li>
					<li class="auth_links">
						<?php if($this->settings['signup'] == 'on') { ?>
						<a href="%baseurl%_auth/signup">Sign Up</a>
						<?php } ?>
					</li>
				</ul>
		</form> 
	</div>
<?php }

//echo $this->note.$this->error;

?>
</div>
