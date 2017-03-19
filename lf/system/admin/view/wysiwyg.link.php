<?php namespace lf\admin;

//if(!isset($thelink)) 
//	$thelink = (new \lf\cms)->getLinks()[$action['id']][0];

// // TODO: pull these save values from the links on this page 
// // (replace into preview() to allow multi app assignment)
// $link['id'] = $thelink['id'];
// $link['app'] = $thelink['app'];
// $link['ini'] = $thelink['ini'];
// $link['section'] = $thelink['section'];

// Default args is an input with the current value. Customizable by app.
$args = '<input type="text" value="'.$link['ini'].'" name="ini" placeholder="app ini" />';

// backward compatibility for args.php
$save = $link;

// Args for app config ini
if(is_file(LF.'apps/'.$link['app'].'/args.php'))
	include LF.'apps/'.$link['app'].'/args.php';
	
?>

<form id="nav_form" action="<?=\lf\requestGet('ActionUrl');?>links/<?=$link['id'];?>" method="post">
	<div class="tile white">
		<div class="tile-header">
			<h5 class="fxlarge">
				<i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>
				<?=$link['app'];?> 
				<a <?=jsprompt();?> href="<?=\lf\requestGet('ActionUrl');?>rmlink/<?=$link['id'];?>" class="pull-right x light_gray_fg"><i class="fa fa-trash-o"></i></a>
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
					Location in Skin:
					<select name="section" id="">
						<option disabled="disabled">-- Select a location --</option>
						<?php foreach($locations as $location): 
						$selected = '';
						if($link['section'] == $location)
						{
							$selected = 'selected="selected"';
						}
						?>
						<option <?=$selected;?>>
							<?=$location;?>
						</option>
						<?php endforeach; ?>
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
		</div>
	</div>
</form>