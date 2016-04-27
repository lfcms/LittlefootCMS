<?php namespace lf\admin;
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

if(!isset($thelink)) 
	$thelink = (new \lf\cms)->getLinks()[$action['id']][0];

// TODO: pull these save values from the links on this page 
// (replace into preview() to allow multi app assignment)
$link['id'] = $thelink['id'];
$link['app'] = $thelink['app'];
$link['ini'] = $thelink['ini'];
$link['section'] = $thelink['section'];

// Default args is an input with the current value. Customizable by app.
$args = '<input type="text" value="'.$link['ini'].'" name="ini" placeholder="app ini" />';

// backward compat for args.php
$save = $link;

// Args for app config ini
if(is_file(LF.'apps/'.$link['app'].'/args.php'))
	include LF.'apps/'.$link['app'].'/args.php';
	
?>

<form id="nav_form" action="%appurl%updatelink/<?=$link['id'];?>" method="post">
	<div class="tile white">
		<div class="tile-header">
			<h5 class="fxlarge">
				<i class="fa fa-arrow-right" aria-hidden="true"></i>
				<?=$link['app'];?> 
				<a href="" class="pull-right x light_gray_fg"><i class="fa fa-trash-o"></i></a>
			</h5>
		</div>
		<div class="tile-content">
			<div class="row">
				<div class="col-2">
					App: <input type="text" name="app" value="<?=$link['app'];?>" />
				</div>
				<div class="col-2">
					Config: <?=$args;?>
				</div>
				<div class="col-4">
					Location (unsure? put "content"): <input type="text" name="section" placeholder="content" value="<?=isset($link['section'])?$link['section']:'';?>" />
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
			<ul class="hlist">
				<li>
				</li>
				<li>
				</li>
				<li>
				</li>
				</li>
				<li>
				</li>
			</ul>
		</div>
	</div>
</form>