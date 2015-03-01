<h2>Users (<?=$usercount;?>)</h2>
<div class="row">
	<div class="col-4">
		<a class="blue button" href="%appurl%newuser/">Create New User</a>
	</div>
	<div class="col-4">
		<? if($this->hasnotice()): ?>
		<span class="button light_gray"><?=$this->notice();?></span>
		<? endif; ?>
	</div>
	<div class="col-4">
	</div>
</div>
<table class="table rounded">
	<tr class="gray light">
		<th>User</th>
		<th>eMail</th>
		<th>Display Name</th>
		<th>Access</th>
		<th>Status</th>
		<th>Edit</th>
		<th>Delete</th>
	</tr>
	<?php foreach($users as $user): ?>
	<tr class="text-center">
		<td><?=$user['user'];?></td>
		<td><a href="mailto:<?=$user['email'];?>"><?=$user['email'];?></a></td>
		<td><?=$user['display_name'];?></td>
		<td><?=$user['access'];?></td>
		<td><?=$user['status'];?></td>
		<td><a href="%appurl%edit/<?=$user['id'];?>">edit</a></td>
		<td>
		<?php if($user['id'] == $this->lf->api('getuid')): ?>
		<a title="You can't delete yourself!">you</a>
		<?php else: ?>
		<a <?=jsprompt();?> href="%appurl%rm/<?=$user['id'];?>" class="x">x</a>
		<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>