<?php

// 
$match_file = 'default';
if(isset($action['template']))
	$match_file = $action['template'];
	
$pwd = ROOT.'skins';


// Build template option
$template_select = '<option';
		
if($match_file == 'default')
{
	$template_select .= ' selected="selected"';
	
	$skin = LF.'skins/'.\lf\getSetting('default_skin').'/index.php';
	
	// Get all %replace% keywords for selected template (remove system variables)
	if(!is_file($skin))
	{
		echo 'Currently selected skin does not exist. Please see the Skins tab to correct this.';
		$section_list = array('none');
	}
	else
	{
		$template = file_get_contents($skin);
		preg_match_all("/%([a-z]+)%/", str_replace(array('%baseurl%', '%skinbase%', '%nav%', '%title%'), '', $template), $tokens);
		$section_list = $tokens[1];
	}
}

$template_select .= ' value="default">-- Default Skin ('.\lf\getSetting('default_skin').') --</option>';

foreach(scandir($pwd) as $file)
{
	if($file == '.' || $file == '..') continue;

	$skin = $pwd.'/'.$file.'/index.php';
	if(is_file($skin))
	{
		$template_select .= '<option';
		
		if($match_file == $file)
		{
			$template_select .= ' selected="selected"';
		}
		
		$template_name = /*$conf['skin'] == $file ? "Default" :*/ ucfirst($file);
		
		$template_select .= ' value="'.$file.'">'.$template_name.'</option>';
	}
}

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
		<h3>Edit Navigation Item</h3>
	</div>
	<div class="tile-content">
		<div class="row">
			<div class="col-1">
				Position: <input type="number" name="position" value="<?=$action['position'];?>" />
			</div>
			<div class="col-8">
				Title: <input type="text" name="title" value="<?php if(isset($action['title'])) echo $action['title']; ?>" />
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
						%subalias% <!-- this is on the nav partial at the top -->
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
					<?=$template_select;?>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-2">
				&nbsp;
				<button class="green">Update</button>
			</div>
			<div class="col-2">
				&nbsp;
				<a class="button" href="%appurl%">Cancel</a>
			</div>
		</div>
	</div>
</div>