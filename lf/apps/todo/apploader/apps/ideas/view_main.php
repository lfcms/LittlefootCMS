<html>
	<head>
		<title>Ideas</title>
		<link rel="stylesheet" href="%baseurl%assets/css/smoothness/jquery-ui-1.8.16.custom.css" />
		<style type="text/css">
			ul { list-style: none; padding: 0; margin: 0; }
			li { margin-bottom: 10px; }
			
			/* css for timepicker */
			.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
			.ui-timepicker-div dl { text-align: left; }
			.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
			.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
			.ui-timepicker-div td { font-size: 90%; }
			.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
			
			table { margin-left: 20px; width: 600px; }
			table th { text-align: left; }
			
			body { margin: 0; padding: 0; font-family: Arial; background: #CDF;}
			
			#header { background: #2CF; color: #333; }
			#header h1 { margin: 0; padding: 10px; }
			
			h2, form, table { margin-left: 10px; }
			
			td.mod { width: 50px; }
			td.date { width: 150px; }
			
			fieldset { width: 600px; margin: 10px 0 10px 10px; border: 1px solid #000; }
			legend { border: 1px solid #000; padding: 5px; }
		</style>
		<?php include dirname(__FILE__).'/js.html'; ?>
		<script type="text/javascript" src="%baseurl%assets/jquery-latest.min.js"></script>
		<script type="text/javascript" src="%baseurl%assets/jquery-ui-1.8.16.custom.min.js"></script>
		<script type="text/javascript" src="%baseurl%assets/jquery-ui-timepicker-addon.js"></script>
		<script type="text/javascript">
		
			Date.firstDayOfWeek = 0;
			Date.format = 'yyyy-mm-dd';
			$(function() {
				$( "#datepicker" ).datetimepicker();
			});
			
			
			Date.firstDayOfWeek = 0;
			Date.format = 'yyyy-mm-dd';
		
			console.debug('Compiled OK');
			
		</script>
	</head>
	<body>
		<div id="header">
			<h1>Ideas</h1>
		</div>
		<h2><?php echo $data[0]['type']; ?></h2>
		<table>
		
			<tr>
				<th>Mod</th>
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
								<th>Mod</th>
								<th>Note</th>
								<th>Due Date</th>
							</tr>
			<?php 	} ?>
			<tr>
				<td class="mod">
					[<a href="%baseurl%index.php/ideas/rm/<?php echo $row['id'] ?>/">x</a>]
				</td>
				<td class="note">
					<a href="%baseurl%index.php/ideas/listitems/<?php echo $row['id'] ?>/">
						<?php echo substr(strip_tags($row['note']), 0, 40); ?>
					</a>
				</td>
				<td class="date"><?php echo $row['date'] ?></td>
			</tr>
			<?php }  ?>
		</table>
		<fieldset>
			<legend><?php echo isset($save) ? 'Update' : 'Add'; ?> Entry</legend>
			<form action="%baseurl%index.php/ideas/update/<?php echo isset($save) ? $save['id'].'/' : ''; ?>" method="post" id="addnote">
				<ul>
					<?php echo isset($save) ? '<li>[<a href="%baseurl%index.php/ideas/listitems/">Create New / Deselect Current</a>]</li>' : ''; ?>
					<li>Tag<br /><input type="text" name="type"<?php if(isset($save)) echo ' value="'.$save['type'].'"'; ?> /></li>
					<li>Note<br /><textarea name="content" id="" cols="30" rows="10"><?php if(isset($save)) echo $save['note']; ?></textarea></li>
					<li>Due Date<br /><input type="text" id="datepicker" name="duedate"<?php if(isset($save)) echo ' value="'.$save['date'].'"'; ?> /></li>
					<li><input type="submit" value="<?php echo isset($save) ? 'Update' : 'Add'; ?> Entry" /></li>
				</ul>
			</form>
		</fieldset>
	</body>
</html><?php

/*

just a list of all things that need to be done.

can be "whenever" (due date 0)

*/

?>