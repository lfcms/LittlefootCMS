<h2 title="WYSIWYG Website Editor"><i class="fa fa-dashboard"></i> Dashboard</h2>

<?php

// load in nav cache
$previewNav = (new \lf\cms)->getNavCache();
$replace = [
	 '%baseurl%' => \lf\requestGet('AdminUrl').'wysiwyg/'
	// '<a ' => '<a target="_parent"'
 ];
$previewNav = str_replace(array_keys($replace), array_values($replace), $previewNav);

?>


<div class="row">
	<div class="col-9">
		<div class="row no_martop">
			<div class="col-12">
				<nav class="light_b main_nav white"><?=$previewNav;?></nav>
			</div>
		</div>
		<?php include 'view/wysiwyg.action.php'; ?>
	</div>
	<div class="col-3">
		<?php include 'view/wysiwyg.addlink.php'; ?>
	</div>
</div>

<h3 title="Apps Linked to This Nav Item"><i class="fa fa-link"></i> Linked Apps</h3>
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

?>

<div class="row">
	<div class="col-9">
		<h3 title="Preview Your Site and Make Updates in Realtime"><i class="fa fa-eye"></i> Preview <a href="<?=\lf\requestGet('AdminUrl');?>wysiwyg/preview/<?=$action['id'];?>" class="pull-right" title="Fullscreen Preview"><i class="fa fa-arrows-alt"></i></a></h3>
		<iframe src="<?=\lf\requestGet('AdminUrl');?>wysiwyg/preview/<?=$action['id'];?>"
			class="white light_b" width="100%" height="700px" frameborder="0">
		</iframe>
	
	</div>
</div>