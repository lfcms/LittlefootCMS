<h2>WYSIWYG Dashboard</h2>

<?php

// load in nav cache
$previewNav = (new \lf\cms)->getNavCache();
$replace = [
	 '%baseurl%' => \lf\requestGet('AdminUrl').'wysiwyg/'
	// '<a ' => '<a target="_parent"'
 ];
$previewNav = str_replace(array_keys($replace), array_values($replace), $previewNav);

?>

<h3>
	<i class="fa fa-compass"></i> Navigation
</h3>
<div class="row">
	<div class="col-9">
		<nav class="light_b main_nav white"><?=$previewNav;?></nav>
		<?php include 'view/wysiwyg.action.php'; ?>
	</div>
	<div class="col-3">
		<?php include 'view/wysiwyg.addlink.php'; ?>
	</div>
</div>

<h4 title="Apps Linked to This Nav Item"><i class="fa fa-link"></i> Linked Apps</h4>

<?php

// loop through linked apps
$links = (new \LfLinks)->getAllByInclude($action['id']);
echo '<div class="row">';
foreach($links as $link)
{
	echo '<div class="col-9">';
	// print editor form for each
	include 'view/wysiwyg.link.php';
	echo '</div>';
}
	
echo '</div>';

$iframeUrl = \lf\requestGet('AdminUrl').'wysiwyg/preview/'.$action['id'].'/'.implode('/', $param);

?>

<div class="row">
	<div class="col-9">
		<h4 title="Preview Your Site and Make Updates in Realtime" class="no_martop"><i class="fa fa-eye"></i> Preview <a href="<?=$iframeUrl?>" class="pull-right" title="Fullscreen Preview"><i class="fa fa-arrows-alt"></i></a></h4>
		<iframe src="<?=$iframeUrl?>"
			class="white light_b" width="100%" height="700px" frameborder="0">
		</iframe>
	
	</div>
</div>