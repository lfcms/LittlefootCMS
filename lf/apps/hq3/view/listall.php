<?php ob_start(); ?>
<style type="text/css">
	table, textarea {width: 100%;}
</style>

<?php
	$type = '';
	if($data != array()):
		foreach($data as $note): 
			if($edit == $note['id']) $save = $note;
			if($type != $note['type']) 
			{
				if($type != NULL) echo '</table>';
				echo '
					<h2>'.$note['type'].'</h2>
					<table>				
						<tr>
							<th>rm</th>
							<th>Note</th>
							<th>Due Date</th>
						</tr>
				'; 
				$type = $note['type']; 
			} 
?>
	<tr>
		<td class="mod">
			[<a onclick="return confirm('Do you really want to delete this?');" href="%appurl%rm/<?php echo $note['id'] ?>/">x</a>]
		</td>
		<td class="note">
			<a href="%appurl%view/<?php echo $note['id'] ?>/">
				<?php echo htmlentities(substr($note['note'], 0, 60), ENT_QUOTES); ?>
			</a>
		</td>
		<td class="date"><?php echo $note['date'] ?></td>
	</tr>
<?php endforeach; ?>
</table>
<?php endif;

		$below = ob_get_clean(); ob_start(); ?>

		<h2>Todo List</h2>
		<fieldset>
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