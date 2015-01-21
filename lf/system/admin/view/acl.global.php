<?=$this->partial('acl-partial-header', array('user' => '', 'inherit' => '', 'global' => 'class="dark_bb"'));?>

<form action="%appurl%add/global" method="post">
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
		<li><input type="text" name="appurl" placeholder="(optional) app url" /></li>
		<li><button type="submit">Add New</button></li>
	</ul>
</form>

<table class="table">
	<tr class="light_gray light">
		<th>Action</th>
		<th>Permission</th>
		<th></th>
		<th></th>
	</tr>
	<?php foreach($acls as $acl): ?>
	<tr>
		<td><?=$acl['action'];?></td>
		<td><?=$acl['perm']?'Allow':'Deny';?></td>
		<td>Edit</td>
		<td><a href="" class="x">Delete</a></td>
	</tr>
	<?php endforeach; ?>
</table>