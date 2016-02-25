<?php namespace lf\admin;
// 
$match_file = 'default';
if(isset($save['template']))
	$match_file = $save['template'];
	
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

if(!isset($thelink)) 
	$thelink = (new \lf\cms)->getLinks()[$save['id']][0];

// TODO: pull these save values from the links on this page 
// (replace into preview() to allow multi app assignment)
$save['app'] = $thelink['app'];
$save['ini'] = $thelink['ini'];
$save['section'] = $thelink['section'];

// Default args is an input with the current value. Customizable by app.
$args = '<input type="text" value="'.$save['ini'].'" name="ini" placeholder="app ini" />';

// Args for app config ini
if(is_file(LF.'apps/'.$save['app'].'/args.php'))
	include LF.'apps/'.$save['app'].'/args.php';
	
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
				<option value="-1"><?=\lf\requestGet('SubdirUrl');?></option>
				<optgroup label="Or select other parent">
					<?=(new dashboard)->navParentSelect($save['parent']);?>
					
					<!-- <?=\lf\get('subalias');?> <!-- this is on the nav partial at the top -->
				</optgroup>
			</select>
		</div>
		<div class="col-6">
			Alias:
			<input type="text" name="alias" value="<?php if(isset($save['alias'])) echo $save['alias']; ?>" />
		</div>
	</div>
	<div class="row">
		<div class="col-6">
			Theme: 
			<select name="template">
				<?=$template_select;?>
			</select>
		</div>
		<div class="col-6">
			Location (unsure? put "content"): <input type="text" name="section" placeholder="content" value="<?=isset($save['section'])?$save['section']:'';?>" />
		</div>
	</div>
	<div class="row">
		<div class="col-6">
			App: 
			<input type="text" name="app" value="<?=$save['app'];?>" />
		</div>
		<div class="col-6">
			Config: <?=$args;?>
		</div>
	</div>
	<!-- 
	<div class="row">
		<div class="col-12">
			Greedy App? (experimental, dont uncheck this) <input type="checkbox" name="isapp" <?=isset($save['isapp'])&&$save['isapp']==0?'':'checked="checked"'; ?> />
		</div>
	</div> -->
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