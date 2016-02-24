<?php

//check for crucial php settings
if(version_compare(phpversion(), '5.4.0', '<')
&& get_magic_quotes_gpc()) // magic quotes (only affects <5.4)
        $warnings[] = '[<a target="_blank" href="https://www.google.com/search?q=how+to+disable+magic+quotes">Fix it</a>] Magic quotes is ENABLED';

if(version_compare(phpversion(), '5.4.0', '<')
&& ini_get('short_open_tag') == false) // php short tags (only affects <5.4)
        $warnings[] = '[<a target="_blank" href="https://www.google.com/search?q=how+to+enable+php+short+tags">Fix it</a>] Short tags is DISABLED ';

if(is_file('config.php'))
{
	//include 'config.php';
	$dbconn = (new \lf\orm)->initDb();
	
	if($dbconn->error != '') $errors = $dbconn->error;
	else
	{
		if(!$dbconn->fetch("select * from lf_settings limit 1"))
			$msg .= 'I found a config file, but can\'t seem to connect to your database. Please verify the contents of lf/config.php or try and reconfigure the credentials.';
		else
			$msg .= 'The config file exists, but the database seems to be missing crucial data in at least lf_settings.';
	}
}

$host = isset($_POST['host']) ? $_POST['host'] : 'localhost';
$user = isset($_POST['user']) ? $_POST['user'] : get_current_user();
$dbname = isset($_POST['dbname']) ? $_POST['dbname'] : get_current_user().'_lf';

if(isset($msg))
	$msg = '<div class="warning marbot rounded">'.$msg.'</div>';
else
	$msg = '';

$error_msg = '';
if(count($this->errors) > 0)
	foreach($this->errors as $error)
		$error_msg .= '<div class="marbot error rounded">'.$error.'</div>';

?>
<html class="lf">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
		<style type="text/css">
			<?php readfile(ROOT.'system/lib/lf.css'); ?>
		</style>
	</head>
	<body>
		<h1 class="banner dark_gray light text-center">Littlefoot Setup</h1>
		<div class="wide_container">
			<form action="?" method="post">
				<div class="row">
					<div class="col-3">
					</div>
					<div class="col-6">
						<?=isset($msg) ? $msg : '';?>
						<?=isset($error_msg) ? $error_msg : '';?>					
						<p>Enter your database credentials and preferred admin password, then click Install.</p>
						<ul class="fvlist">
							<li>
								How do I <a target="_blank" href="https://www.google.com/#q=how+to+create+user+and+database+in+mysql">create a user and database in mysql?</a>
							</li>
							<li>
								How do I <a target="_blank" href="https://www.google.com/#q=how+to+create+user+and+database+in+mysql+cpanel">create a mysql user and database with cPanel?</a>
							</li>
						</ul>
						<div class="row">
							<div class="col-6">
								<h3>MySQL Access</h3>
								<ul class="vlist">
									<li>
										Host: <input type="text" value="<?=$host;?>" name="host" value="localhost" />
									</li>
										Database Name: <input type="text" value="<?=$dbname;?>" name="dbname" placeholder="eg. cpusername_littlefootdb" />
									<li>
										Database User: <input type="text" value="<?=$user;?>"name="user" placeholder="eg. cpusername_dbuser" />
									</li>
									<li>
										Password: <input type="password" name="pass" placeholder="Database User's Password" />
									</li>
									<li>
										<?php if(is_file('config.php')): ?>
										<label for="">Overwrite Config File:</label> <input class="check" type="checkbox" name="overwrite" checked="checked" />
										
										<label for="">Re-install Base Data:</label> <input class="check" type="checkbox" name="data" />
										<?php else: ?>
										<label for=""><input class="check" type="checkbox" name="data" checked="checked" /> Install Base Data (uncheck this if you are just remaking a lost config)</label>
										<?php endif; ?>
									</li>
								</ul>
								
										
										
										
										
										
							</div>
							<div class="col-6">
								<h3>Site Admin User</h3>
								<ul class="vlist">
									<li>
										<label for="auser">Username</label>
										<input type="text" name="auser" id="auser" value="admin" required/>
									</li>
										<label for="apass">Password</label>
										<input type="password" name="apass" id="apass" placeholder="Sup3rSecr3tP@$$word" required/>
									<li>
									</li>
									<li>
										<button class="green" style="">Install</button>
									</li>
									<li>
										<a class="blue button marbot" target="_blank" href="http://littlefootcms.com/">View User Guide</a>
									</li>
								</ul>
							</div>
						</div>
						
						
						
					</div>
					<div class="col-3">
						
					</div>
				</div>
			</form>
		</div>
	</body>
</html>