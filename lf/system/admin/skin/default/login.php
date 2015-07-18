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
		<link href="%relbase%lf/system/lib/lf.css" rel="stylesheet">
		<link rel="stylesheet" href="%skinbase%css/custom.css" type="text/css" />
	</head>

	<body class="gray">
		<div class="lf_login wrapper">	
			<div class="row">
				<div class="col-4"></div>
				<div class="col-4 ">
					<h1 class="no_mar"><span class="hidden">Littlefoot</span><a href="http://littlefootcms.com/"><img src="%relbase%lf/system/template/images/lf-icon-white-transparent.png"/></a></h1>
					<?php if($this->error != '') echo '<p class="error light text-center">'.$this->error.'</p>'; ?>
					<form id="login" action="<?=$this->base;?>_auth/login" method="post">
						<ul class="vlist">
							<li><input type="text" id="username" name="user" placeholder="Username" /></li>
							<li><input type="password" id="password" name="pass" placeholder="Password" /></li>
							<li><button class="green button dark_b" href="" >Sign in</button></li>
							<li><a class="button" id="forgot" href="<?=$this->base;?>_auth/forgotform">Forgot your password?</a></li>
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
