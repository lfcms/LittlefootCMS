<html>
	<head>
		<title>Admin</title>
		<style type="text/css">
			body { /*background: #AAA;*/ font-family: Arial; }
			#login { background: #EEE; display: block; width: 200px; margin: 130px auto 0; padding: 10px; }
			form { margin: 0; }
			h1 { text-align: center; margin: 0; }
			ul { list-style: none; margin: 0 auto; padding: 0;}
			li { display: block; clear: both; width: 100%; padding-top: 5px; font-size: 14px; }
			label { display: block; font-size: 12px; }
			input { border: #CCC 1px solid; }
			#submit { background: #FFF; padding: 5px; }
			.msg { border: #00A solid 1px; font-size: 12px; padding: 5px 0; color: #009; margin: 0 auto 10px; text-align: center; background: #CCF; font-weight: bold; }
			.error { display: block; font-size: 12px; width: 200px; background: #FAA; border: #900 3px solid; padding: 10px; margin: 10px auto; }
		</style>
	</head>
	<body>
        <div id="login">
			<h1 class="loginheader">Admin Login</h1>
            <form action="?" method="post">
                <ul>
                    <li><label for="user">Username</label> <input type="text" name="user" id="user" class="text" /></li>
                    <li><label for="pass">Password</label> <input type="password" name="pass" id="pass" class="text" /></li>
                    <li><input type="submit" name="submit" id="submit" value="Log In" /></li>
                </ul>
            </form>
        </div>
		<?php if(isset($this)) echo $this->note == '' ? '' : '<span class="msg">'.$this->note.'</span>'; ?>
		<?php if(isset($this)) echo $this->error == '' ? '' : '<span class="error">'.$this->error.'</span>'; ?>
	</body>
</html>