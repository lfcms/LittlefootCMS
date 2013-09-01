<?php ob_start(); ?>
		
		<script type="text/javascript" src="http://dev.bioshazard.com/projects/apploader/assets/jquery-latest.min.js"></script>
		<script type="text/javascript" src="http://dev.bioshazard.com/projects/apploader/assets/jquery-ui-1.8.16.custom.min.js"></script>
		<script type="text/javascript" src="http://dev.bioshazard.com/projects/apploader/assets/jquery-ui-timepicker-addon.js"></script>
		<style type="text/css">
			table {width: 100%;}
		</style>
		
		<div id="header">
			<h1>Todo List</h1>
		</div>
		<h2><?php echo $data[0]['type']; ?></h2>
		<table>
		
			<tr>
				<th>rm</th>
				<th>Note</th>
				<th>Due Date</th>
			</tr>
			<?php 
				$type = $data[0]['type'];
				foreach($data as $row) { 
					if($edit == $row['id']) $save = $row;
					if($type != $row['type'])
					{
						$type = $row['type'];
						?>
						</table>
						<h2><?php echo $row['type']; ?></h2>
						<table>
							<tr>
								<th>rm</th>
								<th>Note</th>
								<th>Due Date</th>
							</tr>
			<?php 	} ?>
			<tr>
				<td class="mod">
					[<a onclick="return confirm('Do you really want to delete this?');" href="%appurl%rm/<?php echo $row['id'] ?>/">x</a>]
				</td>
				<td class="note">
					<a href="%appurl%view/<?php echo $row['id'] ?>/">
						<?php echo substr($row['note'], 0, 40); ?>
					</a>
				</td>
				<td class="date"><?php echo $row['date'] ?></td>
			</tr>
			<?php }  ?>
		</table>
		<?php $below = ob_get_clean(); ob_start(); ?>
		<div style="float: left">TEST COLUMN</div>
		<fieldset style="margin-left: 100px;">
			<legend><?php echo isset($save) ? 'Update' : 'Add'; ?> Entry</legend>
			<form action="%appurl%update/<?php echo isset($save) ? $save['id'].'/' : ''; ?>" method="post" id="addnote">
				<ul>
					<?php echo isset($save) ? '<li>[<a href="%appurl%">Create New / Deselect Current</a>]</li>' : ''; ?>
					<li>Type<br /><input type="text" name="type"<?php if(isset($save)) echo ' value="'.$save['type'].'"'; ?> /></li>
					<li>Note<br /><textarea name="content" id="" cols="30" rows="10"><?php if(isset($save)) echo $save['note']; ?></textarea></li>
					<li>Due Date<br /><input type="text" id="datepicker" name="duedate"<?php if(isset($save)) echo ' value="'.$save['date'].'"'; ?> /></li>
					<li><input type="submit" value="<?php echo isset($save) ? 'Update' : 'Add'; ?> Entry" /></li>
				</ul>
			</form>
		</fieldset><?php
	
		// switch em up :)
		echo ob_get_clean().$below; 
/*

just a list of all things that need to be done.

can be "whenever" (due date 0)

*/

?>