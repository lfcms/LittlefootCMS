<html class="lf">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="<?=\lf\requestGet('LfUrl');?>system/lib/lf.css" />
		<link rel="stylesheet" href="<?=\lf\requestGet('LfUrl');?>system/lib/3rdparty/icons.css" />
	</head>
	<body>
		<h1 class="banner blue light text-center">Littlefoot Setup</h1>
		<div class="wide_container">
			<form action="?" method="post">
				<div class="row">
					<div class="col-3">
					</div>
					<div class="col-6">
						<?=implode($this->error);?>					
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
										<label for="">Overwrite Config File: <input class="check" type="checkbox" name="overwrite" checked="checked" /></label>
										</li><li>
										<label for="">Re-install Base Data: <input class="check" type="checkbox" name="data" /></label>
										<?php else: ?>
										<label for=""><input class="check" type="checkbox" name="data" checked="checked" /> Install Base Data (not needed if recovering a config)</label>
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