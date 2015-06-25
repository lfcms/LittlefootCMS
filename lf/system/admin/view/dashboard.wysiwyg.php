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