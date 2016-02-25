<h2><a href="%appurl%">Users</a> Create</h2>
<? if(hasnotice()): ?>
		<span class="button light_gray"><?=notice();?></span>
		<? endif; ?>
<div class="row">
	<div class="col-6">
		<form class="dashboard_manage" action="<?=\lf\requestGet('AdminUrl');?>users/create/" method="post">
			<ul class="vlist">
				<li><input type="text" name="user" placeholder="Username" required></li>
				<li><input type="password" name="pass" placeholder="Password" required></li>
				<li><input type="password" name="pass2" placeholder="Confirm Password" required></li>
				<li><input type="email" name="email" placeholder="Email Address" required></li>
				<li><input type="text" name="nick" placeholder="Display Name" required></li>
				<li><input type="text" name="group" placeholder=" Group (user, admin, etc)" required></li>		
		
				<li><input type="checkbox" name="sendmail">Email credentials to user</li>
				<li><input type="password" name="adminpass" placeholder="Re-enter Admin Password" required></li>
				
				<li><button class="blue" type="submit">Submit</button></li>
			</ul>
		</form>
	</div>
	<div class="col-6">
	
	</div>
</div>