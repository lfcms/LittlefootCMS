<h2>Access Control Lists</h2>

<div class="row">
	<div class="col-2">
		<a class="<?=$include=='user'?'active':'';?> button" href="%appurl%user/">User</a></li>
	</div>
	<div class="col-2">
		<a class="<?=$include=='inherit'?'active':'';?> button" href="%appurl%inherit/">Inherit</a>
	</div>
	<div class="col-2">
		<a class="<?=$include=='global'?'active':'';?> button" href="%appurl%acl_global/">Global</a>
	</div>
	<div class="col-6"></div>
</div>

<?=$this->partial('acl.'.$include);?>