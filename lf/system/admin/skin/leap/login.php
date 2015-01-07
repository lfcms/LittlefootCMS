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
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Login Page | <?php echo $_SERVER['HTTP_HOST']; ?></title>
		<link href="%relbase%lf/system/lib/littlefoot.css" rel="stylesheet">
		<link rel="stylesheet" href="%skinbase%css/styles.css" type="text/css" />
	</head>

	<body class="light_gray">
		<div class="wrapper">	
			<div class="row">
				<div class="col-5"></div>
				<div class="col-2 dark_gray rounded">
					<h4 class="text-center light">Littlefoot CMS</h4>
					<form id="login" action="<?=$this->base;?>_auth/login" method="post">
						<input type="text" id="username" name="user" placeholder="Username" />
						<input type="password" id="password" name="pass" placeholder="Password" />
						<!-- <p>
							<?php echo $recaptcha; ?>
						</p> -->
						<button class="green button" href="" >Sign in</button>
						<a class="button light_gray_fg" id="forgot" href="<?=$this->base;?>_auth/forgotform">Forgot your password?</a>

						<?php if($this->error != '') echo '<p class="error light text-center">'.$this->error.'</p>'; ?>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>
