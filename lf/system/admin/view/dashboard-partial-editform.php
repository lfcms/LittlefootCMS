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
<form id="nav_form" action="%appurl%update/" method="post">

<div class="row">
	<div class="col-3">
		Position: <input type="number" name="position" value="<?=$save['position'];?>" />
	</div>
	<div class="col-9">
		Label: <input type="text" name="label" value="<?=$save['label'];?>" />	
	</div>
</div>
<div class="row">
	<div class="col-12">
		Title: <input type="text" name="title" value="<?php if(isset($save['title'])) echo $save['title']; ?>" />
	</div>
</div>
<div class="row">
	<div class="col-6">
		Parent:
		<select name="parent">
			<option value="-1"><?=$_SERVER['SERVER_NAME'].$this->request->relbase;?></option>
			<optgroup label="Select Parent">
				%subalias% <!-- this is on the nav partial at the top -->
			</optgroup>
		</select>
	</div>
	<div class="col-6">
		Alias:
		<input type="text" name="alias" value="<?php if(isset($save['alias'])) echo $save['alias']; ?>" />
	</div>
</div>
<div class="row">
	<div class="col-12">
		Config: <?=$args;?>
	</div>
</div>
<div class="row">
	<div class="col-6">
		Template: 
		<select name="template">
			<?=$template_select;?>
		</select>
	</div>
	<div class="col-6">
		Location: 
		<?php if(isset($section_list)) { ?>
			<select name="section">
				<?php foreach($section_list as $section): ?>
				<option value="<?=$section;?>"<?php if(isset($save['section']) && $section == $save['section']) echo  'selected=""'; ?>><?=$section;?></option>
				<?php endforeach; ?>
			</select>
		<?php } else { ?>
			<input type="text" name="section" />
		<?php } ?>
	</div>
</div>
<div class="row">
	<div class="col-12">
		Capture URL variables? (experimental) <input type="checkbox" name="app" <?=isset($save['isapp'])&&$save['isapp']==0?'':'checked="checked"'; ?> />
	</div>
</div>
<div class="row">
	<div class="col-8">
		<button class="green">Update</button>
	</div>
	<div class="col-4">
		<a class="button" href="%appurl%">Cancel</a>
	</div>
</div>


<?php /*if(isset($save['id'])) echo '<input type="hidden" name="id" value="'.$save['id'].'">'; */?>
<?php /*if(isset($save['label'])) echo '<a class="cancel_edit" href="%appurl%">cancel</a>';*/?>

<input type="hidden" name="id" value="<?=$save['id'];?>">

</form>
