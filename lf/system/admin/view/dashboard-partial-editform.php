<?php include 'model/templateselect.php'; 

$thelink = $this->links[$save['id']][0];

$save['app'] = $thelink['app'];
$save['ini'] = $thelink['ini'];

// Default args is an input with the current value. Customizable by app.
$args = '<input type="text" value="'.$save['ini'].'" name="ini" placeholder="app ini" />';

// Args for app config ini
if(is_file(ROOT.'apps/'.$save['app'].'/args.php'))
	include ROOT.'apps/'.$save['app'].'/args.php';

?>

<a id="nav_<?=$save['alias'];?>"></a>
<form id="nav_form" action="%appurl%update/" method="post">


<ul>
	<li class="blue">
		Label: <input type="text" name="label" value="<?=$save['label'];?>" />	
	</li>
	<li>
		Position: <input type="text" name="position" value="<?=$save['position'];?>" />
	</li>
	<li>
		Page &lt;title /&gt;: <input type="text" name="title" value="<?php if(isset($save['title'])) echo $save['title']; ?>" /><br />
		<!-- Nav Label: <input type="text" name="label" value="<?php if(isset($save['label'])) echo $save['label']; ?>" /><br /> -->
	</li>
	<li>
		Url: <?php echo $_SERVER['SERVER_NAME'].$this->request->relbase; ?> <select name="parent">
			<option value="-1">&lt;no subdir&gt;</option>
			<optgroup label="Select Subdir">
				%subalias%
			</optgroup>
			</select>
		/ <input type="text" name="alias" value="<?php if(isset($save['alias'])) echo $save['alias']; ?>"/>
	</li>
	<li>Config: <?=$args;?></li>
	<li>
		Template: 
			<select name="template">
				<?=$template_select;?>
			</select>
		
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
	<li>
		Capture URL variables? (experimental) <input type="checkbox" name="app" <?php if(isset($save['isapp']) && $save['isapp'] == 0) echo ''; else echo 'checked="checked"'; ?> />
	</li>
</ul>

<?php if(isset($save['id'])) echo '<input type="hidden" name="id" value="'.$save['id'].'">'; ?><input type="submit" value="Update" /> <?php if(isset($save['label'])) echo '<a class="cancel_edit" href="%appurl%">cancel</a>';?>

</form>
