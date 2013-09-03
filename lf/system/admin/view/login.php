<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Login Page | Starlight Premium Admin Template</title>
<link rel="stylesheet" href="%skinbase%css/style.css" type="text/css" />
<script type="text/javascript" src="%skinbase%js/plugins/jquery-1.7.min.js"></script>
<script type="text/javascript" src="%skinbase%js/plugins/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('.loginform button').hover(function(){
		$(this).stop().switchClass('default','hover');
	},function(){
		$(this).stop().switchClass('hover','default');
	});
	
	$('#login').submit(function(){
		var u = jQuery(this).find('#username');
		if(u.val() == '') {
			jQuery('.loginerror').slideDown();
			u.focus();
			return false;	
		}
	});
	
	$('#username').keypress(function(){
		jQuery('.loginerror').slideUp();
	});
});
</script>
<!--[if lt IE 9]>
	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
</head>

<body class="login">

<div class="loginbox radius3">
	<div class="loginboxinner radius3">
    	<div class="loginheader">
    		<h1 class="bebas">Sign In</h1>
        	<div class="logo"><h1 class="bebas" style="color: #DDD">LittleFoot CMS</h1></div>
    	</div><!--loginheader-->
        
        <div class="loginform">
        	<div class="loginerror"><p>Invalid username or password</p></div>
        	<form id="login" action="%baseurl%_auth/login" method="post"> 
            	<p>
                	<label for="username" class="bebas">Username</label>
                    <input type="text" id="username" name="user" class="radius2" />
					<input type="hidden" name="submit" value="Log In" />
                </p>
                <p>
                	<label for="password" class="bebas">Password</label>
                    <input type="password" id="password" name="pass" class="radius2" />
                </p>
                <p>
                	<button class="radius3 bebas">Sign in</button>
                </p>
                <p><a href="" class="whitelink small">Can't access your account?</a></p>
            </form>
        </div><!--loginform-->
    </div><!--loginboxinner-->
</div><!--loginbox-->

</body>
</html>
