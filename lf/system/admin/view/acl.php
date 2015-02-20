<h2>Access Control Lists</h2>

<div class="row">
	<div class="col-12">
		<nav class="hlist">
			<ul>
				<li><a <?=$include=='user'?'class="active"':'';?> href="%appurl%user/">User</a></li>
				<li><a <?=$include=='inherit'?'class="active"':'';?> href="%appurl%inherit/">Inherit</a></li>
				<li><a <?=$include=='global'?'class="active"':'';?> href="%appurl%acl_global/">Global</a></li>
			</ul>
		</nav>
	</div>
</div>

<?=$this->partial('acl.'.$include);?>