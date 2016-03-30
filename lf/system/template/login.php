<div id="auth_lf">
<?php

$user = (new \lf\user)->fromSession();

if($user->getId() != 0)
{
        $link = '';
        if($user->hasAccess('admin'))
                $link .= ' <a href="'.\lf\requestGet('IndexUrl').'admin/">admin</a>';
        $link .= '<a href="'.\lf\requestGet('IndexUrl').'_auth/logout">logout</a>';

        ?><div class="auth_welcome">Hello, <a href="<?=\lf\requestGet('IndexUrl');?>_auth/profile"><?=$user->getDisplay_name();?></a>. <?=$link;?></div>

<?php } else {

?>
	<div class="auth_login">
		<form id="auth_login_form" action="<?=\lf\requestGet('IndexUrl');?>_auth/login" method="post">
				<ul class="vlist">
					<input type="hidden" name="dest" value="<?php $_SERVER['REQUEST_URI']; ?>" />
					<li class="auth_user"><input type="text" name="user" placeholder="username" /></li>
					<li class="auth_pass"><input type="password" name="pass" placeholder="password" /></li>
					<li class="auth_submit"><input class="light_gray button" type="submit" value="Log In" /></li>
					<li class="auth_links">
						<a href="<?=\lf\requestGet('IndexUrl');?>_auth/forgotform">Forgot?</a>
					</li>
					<li class="auth_links">
					<?php if(\lf\getSetting('signup') == 'on') { ?>
						<a href="<?=\lf\requestGet('IndexUrl');?>_auth/signup">Sign Up</a>
					<?php } ?>
					</li>
				</ul>
		</form> 
	</div>
<?php }

//echo $this->note.$this->error;

?>
</div>
