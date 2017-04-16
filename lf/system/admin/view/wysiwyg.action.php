<?php


// if(!isset($thelink)) 
	// $thelink = (new \lf\cms)->getLinks()[$save['id']][0];

// // TODO: pull these save values from the links on this page 
// // (replace into preview() to allow multi app assignment)
// $save['app'] = $thelink['app'];
// $save['ini'] = $thelink['ini'];
// $save['section'] = $thelink['section'];

// // Default args is an input with the current value. Customizable by app.
// $args = '<input type="text" value="'.$save['ini'].'" name="ini" placeholder="app ini" />';

// // Args for app config ini
// if(is_file(LF.'apps/'.$save['app'].'/args.php'))
	// include LF.'apps/'.$save['app'].'/args.php';

?>
<div class="white tile">
	<div class="tile-header">
		<h4 class="fxlarge" title="Edit the Selected Nav Item">
			<i class="fa fa-edit"></i> <?=$action['label'];?> 
			<a <?=jsprompt();?> href="<?=\lf\requestGet('ActionUrl');?>rmaction/<?=$action['id'];?>" class="x pull-right light_gray_fg" title="Delete Nav Item"><i class="fa fa-trash-o"></i></a>
		</h4>
	</div>
	<div class="tile-content">
		<form action="<?=\lf\requestGet('ActionUrl');?>postNavEdit/<?=$action['id'];?>" method="post">
			<div class="row">
				<div class="col-2">
					Position: <input type="number" name="position" value="<?=$action['position'];?>" />
				</div>
				<div class="col-7">
					Title: <input type="text" name="title" value="<?php 
						if(isset($action['title'])) 
							echo $action['title']; 
						?>" />
				</div>
				<div class="col-3">
					Label: <input type="text" name="label" value="<?=$action['label'];?>" />	
				</div>
			</div>
			<div class="row">
				<div class="col-6">
					Parent:
					<select name="parent">
						<option value="-1"><?=\lf\requestGet('SubdirUrl');?></option>
						<optgroup label="Select Parent">
							<?=(new \lf\nav)->parentOptions($action['parent']);?>
						</optgroup>
					</select>
				</div>
				<div class="col-2">
					Alias:
					<input type="text" name="alias" value="<?php if(isset($action['alias'])) echo $action['alias']; ?>" />
				</div>
				<div class="col-4">
					Template: 
					<select name="template">
						<?=(new \lf\cms)->templateSelect($action['template']);?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-4">
					<button class="green">Update</button>
				</div>
				<div class="col-4">
					<a class="button" href="%appurl%">Cancel</a>
				</div>
				<div class="col-4">
				</div>
			</div>
		</form>
	</div>
</div>