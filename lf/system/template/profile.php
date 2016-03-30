<?php

$myId = (new \lf\user)->idFromSession();
if($myId == 0)
{
	echo 'Anonymous users don\'t have profiles.';	
	(new \lf\template)->printLogin();
	return;
}

$user = (new LfUsers)->findById($myId);

//pre($user);

?>

<h2>My Profile</h2>

<?=notice();?>

<form action="<?=\lf\requestGet('ActionUrl');?>updateprofile/" method="post">
	<ul class="vlist">
		<li>Display Name <input type="text" name="display_name" value="<?=$user->display_name;?>" placeholder="Display Name" /></li>
		<li>User Name <input type="text" name="user" value="<?=$user->user;?>" placeholder="User Name" /></li>
		<li>Email <input type="text" name="email" value="<?=$user->email;?>" placeholder="Email" /></li>
		<li>Password <input type="password" name="pass" placeholder="******" /></li>
		<li><input type="hidden" name="id" value="<?=$user->id;?>" /><input type="submit" value="Update"/></li>
	</ul>
</form>

