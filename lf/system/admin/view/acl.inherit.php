<?=(new \lf\cms)->partial('acl.header', array('active' => 'inherit'));?>
<div class="row">
	<form action="%appurl%add/inherit" method="post">
		<div class="col-2">
			<button type="submit" class="green"><i class="fa fa-plus"></i> Add New</button>
		</div>
		<div class="col-2">
			<select name="group" id="">
				<option value="">Select User/Group</option>
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
		</div>
		<div class="col-2">
			<select name="inherits" id="">
				<option value="">What to Inherit</option>
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
		</div>
	</form>
</div>
<?php if(hasnotice()): ?>
<div class="notice marbot"><?=$this->notice();?></div>
<?php endif; ?>
<table class="table white">
	<tr class="light_gray">
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
		<td><a <?=jsprompt();?> href="%appurl%rm/inherit/<?=$acl['id'];?>" class="x"><i class="fa fa-trash-o"></i></a></td>
	</tr>
	<?php endforeach; ?>
</table>