<h2><a href="%appurl%">Users</a> > <?=ucfirst($action).' User'.$link;?></h2>
<form action="%baseurl%users/<?=$action;?>/" method="post">
	<ul>
		<li><input type="text" name="user" value="<?=$values['user'];?>" /> Username</li>
		<li><input type="password" name="pass" /> Password</li>
		<li><input type="password" name="pass2" /> Confirm Password</li>
		<li><input type="text" name="email" value="<?=$values['email'];?>" /> Email</li>
		<li><input type="text" name="nick" value="<?=$values['nick'];?>" /> Nickname</li>
		<li><input type="text" name="group" value="<?=$values['group'];?>" /> Group (user, admin, etc)</li>
		
		<?php if($action != 'update') { ?>
		
		<li><input type="checkbox" name="sendmail" /> Email credentials to user</li>
		<li>Re-enter Admin Password: <input type="password" name="adminpass" /></li>
		
		<?php } ?>
		
		<li><?=$id;?><input type="submit" value="<?=ucfirst($action);?> user" name="action" /></li>
	</ul>
</form>