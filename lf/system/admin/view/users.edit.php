<h2>Edit User</h2>
<span><a href="%appurl%" title="Back to Users Manager"><i class="fa fa-arrow-left" aria-hidden="true"></i> Users Manager</a></span>
<?=notice();?>
<div class="row">
	<div class="col-6">
		<form action="<?=\lf\requestGet('AdminUrl');?>users/update/<?=$user['id'];?>" method="post">
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