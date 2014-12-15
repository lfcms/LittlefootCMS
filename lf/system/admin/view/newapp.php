<h3>App Linker: Step 2</h3>
<p>This tool can be used to link Apps to each Navigation item.</p>

<h4>Link new app: <?=$_POST['app'];?></h4>
<form action="%baseurl%menu/create/link/" method="post">
	<ul>
		<li>Args: <?=$args;?></li>
		<li>Location: 
			<?php if(isset($section_list)) { ?>
				<select name="section">
					<?php foreach($section_list as $section): ?>
					<option value="<?=$section;?>"><?=$section;?></option>
					<?php endforeach; ?>
				</select>
			<?php } else { ?>
				<input type="text" name="section" />
			<?php } ?>

			Recursive? <input type="checkbox" name="recursive"/></li>
		<li>
			
			<input type="hidden" name="include" value="<?php echo $_POST['include'];?>" />
			<input type="hidden" name="app" value="<?php echo $_POST['app'];?>" />
			<input type="submit" value="New Link" /> 
		</li>
	</ul>
</form>