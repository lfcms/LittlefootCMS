<?=$this->partial('acl-partial-header', array('user' => 'class="dark_bb"', 'inherit' => '', 'global' => ''));?>

<form action="%appurl%add/user" method="post">
	<ul>
		<li>
			<select name="action" id="">
				<option value="">-- Select Nav --</option>
				<?php foreach($actions[1] as $action): ?>
				<option value="<?=$action;?>">/<?=$action;?></option>
				<?php endforeach; ?>
			</select>
		</li>
		<li>
			<input type="radio" name="perm" value="1" checked /> Allow
			<input type="radio" name="perm" value="0" /> Deny
		</li>
		<li>
			<select name="affects" id="">
				<option value="">-- Select User/Group --</option>
				<optgroup label="Groups">
					<?php foreach($groups as $group): ?>
					<option value="<?=$group;?>"><?=$group;?></option>
					<?php endforeach; ?>
				</optgroup>
				<optgroup label="Users">
					<?php foreach($users as $id => $user): ?>
					<option value="<?=$id;?>"><?=$user;?></option>
					<?php endforeach; ?>
				</optgroup>
			</select>
		</li>
		<li><input type="text" name="appurl" placeholder="(optional) app url" /></li>
		<li><button type="submit">Add New</button></li>
	</ul>
</form>

<table class="table">
	<tr class="light_gray light">
		<th>Action</th>
		<th>Permission</th>
		<th>Affects</th>
		<th></th>
		<th></th>
	</tr>
	<?php foreach($acls as $acl): 
	
		if(isset($users[$acl['affects']]))
			$acl['affects'] = 'User / '.$users[$acl['affects']];
		else
			$acl['affects'] = 'Group / '.$acl['affects'];
	?>
	<tr>
		<td><?=$acl['action'];?></td>
		<td><?=$acl['perm']?'Allow':'Deny';?></td>
		<td><?=$acl['affects'];?></td>
		<td>Edit</td>
		<td><a href="%appurl%rm/user/<?=$acl['id'];?>" class="x">Delete</a></td>
	</tr>
	<?php endforeach; ?>
</table>