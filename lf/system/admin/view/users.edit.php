<h2><a href="%appurl%">Users</a> Edit</h2>
<?=notice();?>
<div class="row">
	<div class="col-6">
		<form class="dashboard_manage" action="<?=\lf\requestGet('AdminUrl');?>users/update/<?=$user['id'];?>" method="post">
			<ul class="vlist">
				<li><input type="text" name="user" value="<?=$user['user'];?>" placeholder="Username" required></li>
				<li><input type="password" name="pass" placeholder="Password"></li>
				<li><input type="password" name="pass2" placeholder="Confirm Password"></li>
				<li><input type="email" name="email" value="<?=$user['email'];?>" placeholder="Email Address" required></li>
				<li><input type="text" name="display_name" value="<?=$user['display_name'];?>" placeholder="Display Name" required></li>
				<li>Access <input type="text" name="access" value="<?=$user['access'];?>" placeholder=" Group (user, admin, etc)" required></li>
		
				<li><button class="blue" type="submit" value="Update user">Submit</button></li>
			</ul>
		</form>
	</div>
	<div class="col-6">
	
	</div>
</div>