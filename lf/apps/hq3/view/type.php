<?php ob_start(); ?>
		
		<script type="text/javascript" src="%relbase%apps/todo/apploader/assets/jquery-latest.min.js"></script>
		<script type="text/javascript" src="%relbase%apps/todo/apploader/assets/jquery-ui-1.8.16.custom.min.js"></script>
		<script type="text/javascript" src="%relbase%apps/todo/assets/jquery-ui-timepicker-addon.js"></script>
		<style type="text/css">
			table {width: 100%;}
		</style>
		<?php if(isset($data[0]['type'])): ?>
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
					<a href="%appurl%type/<?php echo $row['type']; ?>/<?php echo $row['id'] ?>/">
						<?php echo substr(htmlentities($row['note']), 0, 40); ?>
					</a>
				</td>
				<td class="date"><?php echo $row['date'] ?></td>
			</tr>
			<?php }  ?>
		</table>
		<?php endif; ?>
		
		<?php $below = ob_get_clean(); ?>
		<div style="float: left">
		<form action="%appurl%newcategory/"><input type="text" name="newcategory" /></form>
		<?php
			echo '<ul>';
			foreach($apps as $app)
			{
				$type = $app['type'];
				
				//echo $type.'<br />';
				echo '<li><a href="%appurl%type/'.$type.'/">'.$type.'</a></li>';
			}
			echo '</ul>';
		?>
		</div>
		<fieldset style="margin-left: 150px; padding: 10px; ">
			<?php /*echo '[<a href="%appurl%">Back to type list</a> or <a href="%appurl%view/0/">View All</a>]';*/ ?>
			<style type="text/css">
				#addnote textarea, #addnote input { width: 98%; font-size: 18px;}
			</style>
			<div style="float: right; width: 50%">
				<form action="%appurl%update/<?php echo isset($save) ? $save['id'].'/' : ''; ?>" method="post" id="addnote">
					<ul>
						<?php echo isset($save) ? '<li>[<a href="%appurl%type/'.$save['type'].'/">Close this note</a>]</li>' : ''; ?>
						<li>Type<br /><input type="text" name="type"<?php if(isset($save)) echo ' value="'.$save['type'].'"'; else echo ' value="'.$var[1].'"'; ?> /></li>
						<li>Note<br /><textarea name="content" id="" width="100%" rows="10"><?php if(isset($save)) echo $save['note']; ?></textarea></li>
						<li>Due Date<br /><input type="text" id="datepicker" name="duedate"<?php if(isset($save)) echo ' value="'.$save['date'].'"'; ?> /></li>
						<li><br /><input type="submit" value="<?php echo isset($save) ? 'Update' : 'Add'; ?> Entry" /></li>
					</ul>
				</form>
			</div>
			<div style="float: left; width: 50%">
				<?php echo $below; ?>
			</div>
		</fieldset><?php
	
		// switch em up :)
		
/*

just a list of all things that need to be done.

can be "whenever" (due date 0)

*/

?>