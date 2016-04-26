<?php

/**

<div class="row">
	<div class="col-3">
		<?php include 'view/dashboard-partial-editform.php'; ?>
	</div>
	<div class="col-9">
		<iframe src="<?=\lf\requestGet('AdminUrl');?>dashboard/preview/<?=$vars[1];?>"
			class="light_b" width="100%" height="800px" frameborder="0">
		</iframe>';
	</div>
</div>

*/

echo '<h3>Action</h3>';
	
// print action editor form
include 'view/wysiwyg.action.php';

?>
<div class="row">
	<div class="col-2">
<?php
// loop through linked apps
$links = (new \LfLinks)->getAllByInclude($action['id']);
foreach($links as $link)
{
	echo '<h3>Links</h3>';
	// print editor form for each
	include 'view/wysiwyg.link.php';
}

// print "add link" form
include 'view/wysiwyg.addlink.php';

?>
	</div>
	<div class="col-10">
		<h3>Preview</h3>
		<iframe src="<?=\lf\requestGet('AdminUrl');?>wysiwyg/preview/<?=$action['id'];?>"
			class="light_b" width="100%" height="800px" frameborder="0">
		</iframe>
	</div>
</div>