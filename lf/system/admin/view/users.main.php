<div class="row">
	<div class="col-12">
		<h2 class="no_marbot"><i class="fa fa-users no_marbot"></i> Users (<?=$usercount;?>)</h2>
	</div>
</div>
<div class="row">
	<div class="col-3 pull-right">
		<div class="row no_martop">
			<div class="col-12">
				<div class="tile white">
					<div class="tile-header">
						<h4><i class="fa fa-plus"></i> Add New</h4>
					</div>
					<div class="tile-content">
						<? if(hasnotice()): ?>
						<div class="row">
							<div class="col-12">
								<span class="button light_gray"><?=notice();?></span>
							</div>
						</div>
						<? endif; ?>
						<div class="row">
							<div class="col-12">
								<a class="green button" href="%appurl%newuser/">Create User</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				LDAP <?=extension_loaded('ldap')?'':'(PHP LDAP Module not installed)';?>:
				<form action="%appurl%saveldap" method="post">
					<input <?= ! is_null( \lf\getSetting('ldap') ) ? 'value="'.\lf\getSetting('ldap').'"' : '' ;?>  <?=extension_loaded('ldap')?'':'disabled';?> type="text" name="ldap" placeholder="{'port':636,'basedn':'ou=People,dc=mydomain','host':'ldaps://ldap.mydomain.com'}" />
				</form>
			</div>
		</div>
	</div>
	<div class="col-9">
		<table class="table white">
			<tr class="light_gray">
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
				<td><a href="%appurl%edit/<?=$user['id'];?>"><i class="fa fa-edit"></i></a></td>
				<td>
				<?php if($user['id'] == (new \lf\user)->fromSession()->getId() ): ?>
				<span title="You can't delete yourself!"><i class="fa fa-lock"></i></span>
				<?php else: ?>
				<a <?=jsprompt();?> href="%appurl%rm/<?=$user['id'];?>" class="x"><i class="fa fa-trash-o"></i></a>
				<?php endif; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div>