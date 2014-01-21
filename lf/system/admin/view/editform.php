<br />
		<ul>	
			<li>	
				Config: <?=$args;?>
			</li>
			<li>
				Url: <?php echo $_SERVER['SERVER_NAME'].$this->request->relbase; ?> <select name="parent">
					<optgroup label="Select Base">
						<option value="-1">&lt;base&gt;</option>
						<?=$nav['select'];?>
					</optgroup>
					</select>
				/ <input type="text" name="alias"  style="width: 75px;" value="<?php if(isset($save['alias'])) echo $save['alias']; ?>"/>
			</li>	
			<?php if(isset($vars[2]) && $vars[2] == 'adv'): ?>
			<a href="%appurl%<?php echo $vars[0]; ?>/<?php echo $vars[1]; ?>/">Hide Advanced Options</a><br />
			<li>
				Location: 
					<?php if(isset($section_list)) { ?>
						<select name="section">
							<?php foreach($section_list as $section): ?>
							<option value="<?=$section;?>"<?php if($section == $save['section']) echo  'selected=""'; ?>><?=$section;?></option>
							<?php endforeach; ?>
						</select>
					<?php } else { ?>
						<input type="text" name="section" />
					<?php } ?>
			</li>
			<!-- Recursive? <input type="checkbox" name="recursive"/> -->
			<li>
				Page Title: <input type="text" name="title" value="<?php if(isset($save['title'])) echo $save['title']; ?>" /><br />
				<!-- Nav Label: <input type="text" name="label" value="<?php if(isset($save['label'])) echo $save['label']; ?>" /><br /> -->
			</li>
			<li>
				Template: 
					<select name="template">
						<?=$template_select;?>
					</select>
				App? <input type="checkbox" name="app" <?php if(isset($save['isapp']) && $save['isapp'] == 0) echo ''; else echo 'checked="checked"'; ?> /> 
			</li>
		</ul>
		<br />
		<?php else: ?>
		<a href="%appurl%<?php echo $vars[0]; ?>/<?php echo $vars[1]; ?>/adv/">Show Advanced Options</a><br />
		<?php endif; ?>

		<?php if(isset($save['id'])) echo '<input type="hidden" name="id" value="'.$save['id'].'">'; ?><input type="submit" value="Update" /> <?php if(isset($save['label'])) echo ' ( <a class="deselect_nav_form" href="%appurl%">Deselect</a> )';?>
	<br />
	<br />
	<br />
	[<a id="nav_<?=$save['alias'];?>" onclick="return confirm('Do you really want to delete this?');" href="%baseurl%apps/rm/<?=$save['id'];?>/">Delete Nav Item</a>]