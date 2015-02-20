<form action="%appurl%add/inherit" method="post">
	<ul>
		<li>
			<select name="group" id="">
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
		<li>
			<select name="inherits" id="">
				<option value="">-- What to Inherit --</option>
				<optgroup label="Groups">
					<?php foreach($groups as $group): ?>
					<option value="<?=$group;?>"><?=$group;?></option>
					<?php endforeach; ?>
				</optgroup>
				<!-- I dont think users work just yet...
				<optgroup label="Users">
					<?php foreach($users as $id => $user): ?>
					<option value="<?=$id;?>"><?=$user;?></option>
					<?php endforeach; ?>
				</optgroup>
				-->
			</select>
		</li>
		<li><button type="submit">Add New</button></li>
	</ul>
</form>

<table class="table">
	<tr class="light_gray light">
		<th>User or Group</th>
		<th>Inherits</th>
		<th>Edit</th>
		<th>Delete</th>
	</tr>
	<?php if($acls) foreach($acls as $acl):
		
	if(isset($users[$acl['group']]))
		$acl['group'] = 'User / '.$users[$acl['group']];
	else
		$acl['group'] = 'Group / '.$acl['group'];
	
	?>
	<tr class="text-center">
		<td><?=$acl['group'];?></td>
		<td>Group / <?=$acl['inherits'];?></td>
		<td>Edit</td>
		<td><a href="%appurl%rm/inherit/<?=$acl['id'];?>" class="x">Delete</a></td>
	</tr>
	<?php endforeach; ?>
</table>