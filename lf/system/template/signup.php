<?php if($this->lf->settings['signup'] != 'on') die('Signup is disabled.');
		
		if(isset($_GET['dest']))
			$_SESSION['dest_url'] = urldecode($_GET['dest']);
		
		?>
		<div id="signup-login" style="width: 50%; float: left;">
			<h2>Login</h2>
			<form action="%baseurl%_auth/login" method="post">
				<p>Username: <input type="text" name="user" /></p>
				<p>Password: <input type="password" name="pass" /></p>
				<p><a href="%appurl%forgotform/">Forgot your password?</a></p>
				<input style="padding: 5px; background: white; border: 1px;" type="submit" name="submit" value="Log In" />
				
			</form>
		</div>
		
		<script type="text/javascript">
			$(document).ready(function(){
				$('#signup-form form').submit(function() {
					var error = '';
					
					if($('#signup-form form input[name=user]').val() == '') { error = 'Please provide a username.'; }
					else if($('#signup-form form input[name=pass]').val() == '') { error = 'Please provide a password.'; } 
					else if($('#signup-form form input[name=email]').val() == '') { error = 'Please provide an email.'; } 
					else if(!$('#signup-form form input[name=terms]').is(':checked')) { error = 'Please accept the terms and conditions.'; } 
					
					if(error == '') { return true; }
					
					$('#error').remove();
					$('#signup-form form').prepend('<span id="error" style="color: #F00">' + error + '</span>');
					return false;
				});
			});
		</script>
		
		<div id="signup-form" style="margin-left: 50%;">
			<h2>Sign up!</h2>
			<form action="%appurl%create/" method="post">
				<ul>
					<li>User:<br /><input type="text" name="user" /></li>
					<li>Pass:<br /><input type="password" name="pass"/></li>
					<li>Email:<br /><input type="text" name="email" /></li>
					<!-- <li><input type="checkbox" name="terms" /> I accept the <a href="%baseurl%terms/" target="_blank">terms and conditions</a>.</li> -->
					<li><input style="padding: 5px; background: white; border: 1px;" type="submit" value="Sign Up!"/></li>
				</ul>
			</form>
		</div>