<?php ob_start(); ?>
		
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
		
		<script type="text/javascript" src="http://trentrichardson.com/examples/timepicker/jquery-ui-timepicker-addon.js"></script>
		
		<script type="text/javascript">
			$(".date").datetimepicker();
		</script>
		
		<style type="text/css">
			table {width: 100%;}
			fieldset {width: 100%;margin:0;}
			#addnote { padding-left: 20px; }
			#addnote textarea { background: #DDD }
			#addnote input { background: #DDD }
		</style>
		
		
		
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
		<?php $below = ob_get_clean(); ?>
		<h2>Events</h2>
		<?php echo '[<a href="%appurl%">Back to type list</a> or <a href="%appurl%view/0/">View All</a>]'; ?>
		<fieldset>
			<legend><?php echo isset($save) ? 'Update' : 'Add'; ?> Entry</legend>
			<style type="text/css">
				#addnote textarea, #addnote input { width: 98%; font-size: 18px;}
			</style>
			<form action="%appurl%update/<?php echo isset($save) ? $save['id'].'/' : ''; ?>" method="post" id="addnote">
				<ul>
					<?php echo isset($save) ? '<li>[<a href="%appurl%type/'.$save['type'].'/">Close this note</a>]</li>' : ''; ?>
					<li>Type<br /><input type="text" name="type"<?php if(isset($save)) echo ' value="'.$save['type'].'"'; else echo ' value="'.$var[1].'"'; ?> /></li>
					<li>Note<br /><textarea name="content" id="" width="100%" rows="10"><?php if(isset($save)) echo $save['note']; ?></textarea></li>
					<li>Due Date<br /><input type="text" id="datepicker" name="duedate"<?php if(isset($save)) echo ' value="'.$save['date'].'"'; ?> /></li>
					<li><br /><input type="submit" value="<?php echo isset($save) ? 'Update' : 'Add'; ?> Entry" /></li>
				</ul>
			</form>
		</fieldset><?php
	
		if(is_dir(ROOT.'system/lib/tinymce/'))
			readfile(ROOT.'system/lib/tinymce/js.html');
		else
			echo 'No "TinyMCE" package found at '.$this->request->absbase.'system/lib/tinymce/';
		// switch em up :)
		echo $below; 
/*

just a list of all things that need to be done.

can be "whenever" (due date 0)

*/

?>