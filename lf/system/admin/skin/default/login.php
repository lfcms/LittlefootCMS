<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information.

if(isset($_SESSION['_lf_login_error']))
{
	$this->error = $_SESSION['_lf_login_error'];
	unset($_SESSION['_lf_login_error']);
}

$get = array();
$action = '&';
if(count($_GET))
{
	foreach($_GET as $var => $val)
		$get[] = $var.'='.$val;
	$action .= implode('&', $get);
}

?>
<!DOCTYPE html>
<html class="lf">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Login Page | <?php echo $_SERVER['HTTP_HOST']; ?></title>
		<link href="<?=\lf\requestGet('LfUrl');?>system/lib/lf.css" rel="stylesheet">
		<link rel="stylesheet" href="<?=(new \lf\template)->getSkinBase();?>css/custom.css" type="text/css" />
	</head>

	<body class="blue">
		<div class="lf_login container">	
			<div class="row">
				<div class="col-4"></div>
				<div class="col-4 ">
					<h1 class="no_mar"><span class="hidden">Littlefoot</span><a href="http://littlefootcms.com/" target="_blank"><img src="<?=\lf\requestGet('LfUrl');?>system/template/images/lf-banner.png"/></a></h1>
					<?php echo notice(); ?>
					
					<?php if( !(new \lf\user)->isAdmin() && (new \lf\user)->idFromSession() != 0 ): ?>
					
					
					
					<div class="row">
						<div class="col-12 light">
							<h4 class="no_martop">Logged In</h4>
							But this page is for admin access. 
						</div>
					</div>
					<p>
					<a href="<?=\lf\requestGet('IndexUrl');?>" class="green button">Visit Home Page</a>
					</p>
					<p>
					<a href="<?=\lf\requestGet('IndexUrl');?>/_auth/logout" class="red button">Logout (<?=(new \lf\user)->fromSession()->getDisplay_name();?>)</a>
					</p>
					
					
					
					<div class="row">
						<div class="col-12 light">
							<h4>Log In to Admin</h4>
							Login as admin continue to admin interface.
						</div>
					</div>
					
					<?php endif; ?>
					
					<form id="login" action="<?=\lf\requestGet('IndexUrl');?>_auth/login" method="post">
						<ul class="vlist">
							<li><input type="text" id="username" name="user" placeholder="Username" /></li>
							<li><input type="password" id="password" name="pass" placeholder="Password" /></li>
							<li><button class="light_blue button" href="" >Sign in</button></li>
							<li><a class="transparent button" id="forgot" href="<?=$this->base;?>_auth/forgotform">Forgot your password?</a></li>
						</ul>
						<!-- <p>
							<?php echo $recaptcha; ?>
						</p> -->
					</form>
				</div>
				<div class="col-4"></div>
			</div>
		</div>
	</body>
</html>
