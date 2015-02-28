<div class="row">
	<form action="%appurl%add/global" method="post">
		<div class="col-2">
			<select name="action" id="">
				<option value="">Select Nav</option>
				<?php foreach($actions[1] as $action): ?>
				<option value="<?=$action;?>">/<?=$action;?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="col-2">
			<input type="text" name="appurl" placeholder="(optional) app url" />
		</div>
		<div class="col-2">
			<input type="radio" name="perm" value="1" checked />Allow
			<input type="radio" name="perm" value="0" />Deny
		</div>
		<div class="col-2">
			<button class="green" type="submit">Add New</button>
		</div>
	</form>
</div>



<table class="table">
	<tr class="gray light">
		<th>Action</th>
		<th>Permission</th>
		<th>Edit</th>
		<th>Delete</th>
	</tr>
	<?php if($acls) foreach($acls as $acl): ?>
	<tr class="text-center">
		<td><?=$acl['action'];?></td>
		<td><?=$acl['perm']?'Allow':'Deny';?></td>
		<td>Edit</td>
		<td><a href="" class="x">Delete</a></td>
	</tr>
	<?php endforeach; ?>
</table>