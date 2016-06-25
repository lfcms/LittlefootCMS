<div class="tile white">
	<div class="tile-header">
		<h4 title="Link a New Application."><i class="fa fa-plus"></i> Add New</h4>
	</div>
	<div class="tile-content">
		<form method="post" action="<?=\lf\requestGet('ActionUrl');?>links">
			<ul class="vlist">
				<li>App Name: 
					<select name="app" id="">
						<option disabled="disabled" selected="selected" value="">-- Select an App --</option>
						<?=(new \lf\cms)->appSelect();?>
					</select></li>
				<li>Config: <input type="text" name="ini" placeholder="Config" /></li>
				<li>Location: <input type="text" name="section" placeholder="Location" /></li>
				<li>Create New Nav Item? <input name="newnav" type="checkbox" /></li>
				<li><input type="hidden" name="include" value="<?=$include;?>" /><button class="green">Create</button></li>
			</ul>
		</form>
	</div>
</div>