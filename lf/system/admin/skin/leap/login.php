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

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title>Login Page | <?php echo $_SERVER['HTTP_HOST']; ?></title>
                <link rel="stylesheet" href="%skinbase%css/styles.css" type="text/css" />
        </head>

        <body class="login">
                <div class="loginbox radius3">
                        <div class="loginboxinner radius3">
                                <div class="loginheader">
                                        <div class="logo"><h1 class="bebas" style="color: #DDD">Littlefoot CMS</h1></div>
                                        <div style="clear: both"></div>
                                </div><!--loginheader-->
                                <div class="loginform">
                                        <form id="login" action="<?=$this->base;?>_auth/login" method="post">
                                                <p>
                                                        <label for="username" class="bebas">Username</label>
                                                        <input type="text" id="username" name="user" class="radius2" />
                                                        <input type="hidden" value="Log In" />
                                                        <input type="hidden" name="adminlogin" value="admin" />
                                                </p>
                                                <p>
                                                        <label for="password" class="bebas">Password</label>
                                                        <input type="password" id="password" name="pass" class="radius2" />
                                                </p>
                                                <p><?php echo $recaptcha; ?></p>
                                                <p><button class="radius3 bebas">Sign in</button></p>
												
												<a id="forgot" href="<?=$this->base;?>_auth/forgotform">Forgot your password?</a>
												
                                                <?php if($this->error != '') echo '<p class="error">'.$this->error.'</p>'; ?>
                                        </form>
                                </div><!--loginform-->
                        </div><!--loginboxinner-->
                </div><!--loginbox-->
        </body>
</html>