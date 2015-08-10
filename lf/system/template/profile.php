<?php

//(new User)->fromSession();

$user = (new LfUsers)
			->findById( 
				(new User)
					->fromSession()
					->getId() 
			);

//pre($user);

?>

<h2>My Profile</h2>

<form action="%appurl%updateprofile/" method="post">
	<ul class="vlist">
		<li>Display Name <input type="text" name="display_name" value="<?=$user->display_name;?>" placeholder="Display Name" /></li>
		<li>User Name <input type="text" name="user" value="<?=$user->user;?>" placeholder="User Name" /></li>
		<li>Email <input type="text" name="email" value="<?=$user->email;?>" placeholder="Email" /></li>
		<li>Password <input type="text" name="pass" placeholder="******" /></li>
		<li><input type="hidden" name="id" value="<?=$user->id;?>" /><input type="submit" value="Update"/></li>
	</ul>
	
</form>

