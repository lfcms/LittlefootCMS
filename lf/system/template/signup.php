<?php if(\lf\getSetting('signup') != 'on') die('Signup is disabled.');
		
		if(isset($_GET['dest']))
			$_SESSION['dest_url'] = urldecode($_GET['dest']);
		
		?>
		<div class="row">
			<div id="signup-login" class="col-6">
				<h2>Login</h2>
				<form action="<?=\lf\requestGet('ActionUrl');?>login" method="post">
					<ul class="vlist">
						<li><input type="text" name="user" placeholder="Username" /></li>
						<li><input type="password" name="pass" placeholder="Password" /></li>
						<li><a href="%appurl%forgotform/">Forgot your password?</a></li>
						<input class="light_gray button" type="submit" name="submit" value="Log In" />
					</ul>
				</form>
			</div>
		
			<script type="text/javascript">
				$(document).ready(function(){
					$('#signup-form form').submit(function() {
						var error = '';
						
						if($('#signup-form form input[name=user]').val() == '') { error = 'Please provide a username.'; }
						else if($('#signup-form form input[name=pass]').val() == '') { error = 'Please provide a password.'; } 
						else if($('#signup-form form input[name=email]').val() == '') { error = 'Please provide an email.'; } 
						//else if(!$('#signup-form form input[name=terms]').is(':checked')) { error = 'Please accept the terms and conditions.'; } 
						
						if(error == '') { return true; }
						
						$('#error').remove();
						$('#signup-form form').prepend('<span id="error" style="color: #F00">' + error + '</span>');
						return false;
					});
				});
			</script>
		
			<div id="signup-form" class="col-6">
				<h2>Sign up!</h2>
				<form action="<?=\lf\requestGet('ActionUrl');?>create/" method="post">
					<ul class="vlist">
						<li><input type="text" name="user" placeholder="Username"/></li>
						<li><input type="password" name="pass" placeholder="Password"/></li>
						<li><input type="text" name="email" placeholder="Email Address"/></li>
						<!-- <li><input type="checkbox" name="terms" /> I accept the <a href="<?=\lf\requestGet('ActionUrl');?>terms/" target="_blank">terms and conditions</a>.</li> -->
						<li><input class="light_gray button" type="submit" value="Sign Up!"/></li>
					</ul>
				</form>
			</div>
		</div>