<div class="row">
	<div class="col-1">
		Position: <input type="number" name="position" value="<?=$save['position'];?>" />
	</div>
	<div class="col-2">
		Label: <input type="text" name="label" value="<?=$save['label'];?>" />	
	</div>
	<div class="col-3">
		Title: <input type="text" name="title" value="<?php if(isset($save['title'])) echo $save['title']; ?>" />
	</div>
	<div class="col-4">
		Parent:
		<select name="parent">
			<option value="-1"><?=$_SERVER['SERVER_NAME'].$this->request->relbase;?></option>
			<optgroup label="Select Parent">
				%subalias% <!-- this is on the nav partial at the top -->
			</optgroup>
		</select>
	</div>
	<div class="col-2">
		Alias:
		<input type="text" name="alias" value="<?php if(isset($save['alias'])) echo $save['alias']; ?>" />
	</div>
</div>
<div class="row">
	<div class="col-2">
		
	</div>
	<div class="col-2">
		Template: 
		<select name="template">
			<?=$template_select;?>
		</select>
	</div>
	<div class="col-2">
		&nbsp;
		<button class="green">Update</button>
	</div>
	<div class="col-2">
		&nbsp;
		<a class="button" href="%appurl%">Cancel</a>
	</div>
</div>


<div class="row">
	<div class="col-3">
		<?php include 'view/dashboard-partial-editform.php'; ?>
	</div>
	<div class="col-9">
		<iframe src="<?=$this->lf->wwwAdmin;?>dashboard/preview/<?=$vars[1];?>"
			class="light_b" width="100%" height="800px" frameborder="0">
		</iframe>';
	</div>
</div>