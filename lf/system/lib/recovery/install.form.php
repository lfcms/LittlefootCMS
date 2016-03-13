<html class="lf">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="<?=\lf\requestGet('LfUrl');?>system/lib/lf.css" />
		<link rel="stylesheet" href="<?=\lf\requestGet('LfUrl');?>system/lib/3rdparty/icons.css" />
	</head>
	<body>
		<h1 class="banner blue light text-center">Setup</h1>
		<div class="wide_container">
			<form action="?" method="post">
				<div class="row">
					<div class="col-3">
					</div>
					<div class="col-6">
						<?php
							$this->error[] = '';
							echo implode($this->error);
						?>					
						<h3>What?</h3>
						<p>Littlefoot uses an ORM to communicate with the database. It relies on a configuration file at <code><?=LF;?>config.php</code>. If this file is missing, or it does not have valid configuration information, this Setup page will pop up and allow you to set a new config. This is normal for new installations.</p>
						<h4>Frequently Asked Questions</h4>
						<ul class="efvlist">
							<li>
								How do I <a target="_blank" href="https://www.google.com/#q=how+to+create+user+and+database+in+mysql">create a user and database in mysql?</a>
							</li>
							<li>
								How do I <a target="_blank" href="https://www.google.com/#q=how+to+create+user+and+database+in+mysql+cpanel">create a mysql user and database with cPanel?</a>
							</li>
						</ul>
						<div class="row no_martop">
							<div class="col-6">
								<h3>MySQL Access</h3>
								<p><i class="fa fa-info-circle"></i> Enter your database credentials</p>
								<ul class="vlist">
									<li>
										Host: <input type="text" value="<?=$host;?>" name="db[host]" value="localhost" />
									</li>
										Database Name: <input type="text" value="<?=$dbname;?>" name="db[dbname]" placeholder="eg. cpusername_littlefootdb" />
									<li>
										Database User: <input type="text" value="<?=$user;?>"name="db[user]" placeholder="eg. cpusername_dbuser" />
									</li>
									<li>
										Password: <input type="password" name="db[pass]" placeholder="Database User's Password" />
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
								<p><i class="fa fa-info-circle"></i> and preferred admin password,</p>
								<ul class="vlist">
									<li>
										<label for="auser">Username</label>
										<input type="text" name="admin[user]" id="auser" value="admin" required/>
									</li>
									<li>
										<label for="apass">Password</label>
										<input type="password" name="admin[pass]" id="apass" placeholder="Sup3rSecr3tP@$$word" required/>
									</li>
									<li>
										<p><i class="fa fa-info-circle"></i> then click Install.</p>
										<button class="green" style="">Install</button>
									</li>
									<li>
										<a class="blue button marbot" target="_blank" href="http://littlefootcms.com/manual">View Littlefoot Manual</a>
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