




<h3>
	<?php if($save == '') { $action = 'create'; ?>
		Create a new Link
	<?php } else { $action = 'update'; ?>
		Update Link
	<?php } ?>
</h3>
<p>This tool can be used to create new linked apps. If the position of the item is set to 0, it is registered as url link, but does not show on the main nav menu</p>

<h4>Link new app: <?=$var[1];?></h4>

<form action="%appurl%create/" method="post">
	<ul>
		<li>Args: <?=$args;?></li>
			<input type="hidden" name="app" value="<?php echo $var[1];?>" />
		</li>
	
		<li>
			Url: <?php echo $_SERVER['SERVER_NAME'].$this->request->relbase; ?> <select name="parent">
				<optgroup label="Select Base">
					<option value="-1">&lt;base&gt;</option>
					<?=$nav['select'];?>
				</optgroup>
				</select>
			/ <input type="text" name="alias"  style="width: 75px;" value="<?php if(isset($save['alias'])) echo $save['alias']; else echo $var[1]; ?>"/>
		</li>
		<li>
		<?php if(!$adv): ?>
<a href="%appurl%<?php echo $var[0]; ?>/<?php echo $var[1]; ?>/adv/">Show Advanced Options</a>
		<?php else: ?>
<a href="%appurl%<?php echo $var[0]; ?>/<?php echo $var[1]; ?>/">Hide Advanced Options</a>
		<?php endif; ?>
		</li>
		<?php if($adv): ?>
		<li>Location: 
			<?php if(isset($section_list)) { ?>
				<select name="section">
					<option value="content">content</option>
					<?php 
					
					$section_list = array_flip($section_list);
					unset($section_list['content']);
					$section_list = array_flip($section_list);
					
					foreach($section_list as $section): ?>
					<option value="<?=$section;?>"><?=$section;?></option>
					<?php endforeach; ?>
				</select>
			<?php } else { ?>
				<input type="text" name="section" />
			<?php } ?>

			<!-- Recursive? <input type="checkbox" name="recursive"/> -->
			Order: <input type="text" name="position"  style="width: 25px;" value="<?php if(isset($save['position'])) echo $save['position']; else echo 1; ?>" /> 
			</li>
		<li>Page Title: <input type="text" name="title" value="<?php if(isset($save['title'])) echo $save['title']; ?>" /></li>
		<li>Nav Label: <input type="text" name="label" value="<?php if(isset($save['label'])) echo $save['label']; ?>" /></li>
		<li>
			Template: 
				<select name="template">
					<?=$template_select;?>
				</select>
			App? <input type="checkbox" name="isapp" <?php if(isset($save['isapp']) && $save['isapp'] == 0) echo ''; else echo 'checked="checked"'; ?> /> 
			
		</li>
		<?php endif; ?>

		<li><?php if(isset($save['id'])) echo '<input type="hidden" name="id" value="'.$save['id'].'">'; ?><input type="submit" value="<?=ucfirst($action);?>" /> <?php if(isset($save['label'])) echo '( <a href="%baseurl%menu/view/">Deselect</a> )';?> </li>
	</ul>
	
</form>