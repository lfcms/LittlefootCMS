<?php 

function parse_tab($tab, $root)
{
	echo '<ul>';
	
	foreach($tab[$root] as $id => $note)
	{
		echo '<li>';
		echo '<input type="text" name="input['.$root.']['.$id.']" value="'.$note.'" />';
		if(isset($tab[$id]))
			parse_tab($tab, $id);
		echo '</li>';
	}
	
	if($root == 0)
		echo '<li><input type="submit" value="submit" /></li>';
		
	echo '</ul>';
}

?>
<style type="text/css">
	#tabform {  }
	#tabform input { border: none; border-bottom: 1px solid #000; border-left: 1px solid #000; }
	#tabform li { margin-bottom: 2px; }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		// test
		
		$('input').focus(
			function()
			{
				$(this).css('background', '#000000');
				$(this).css('color', '#FFFFFF');
				//alert('asdf');
			}
		);
		
		$('input').blur(
			function()
			{
				$(this).css('background', '#FFFFFF');
				$(this).css('color', '#000000');
				//alert('asdf');
			}
		);
		
		$('#tabform input').keypress(function(e) {
			if(e.which == 0) // tab
			{
				alert('tab' + $(this).html());
				return false;
			}
			if(e.which == 8) // backspace
			{
				alert('backspace');				
				return false;
			}
		});
	});
	
	
</script>
<h2>tab</h2>
<form action="?" method="post" id="tabform">
	<ul>
		<li><input type="text" name="input[0][1]" />
			<ul>
				<li><input type="text" name="input[1][2]" /></li>
				<li><input type="text" name="input[1][3]" /></li>
				<li><input type="text" name="input[1][4]" />	
					<ul>
						<li><input type="text" name="input[4][10]" /></li>
						<li><input type="text" name="input[4][11]" /></li>
						<li><input type="text" name="input[4][12]" /></li>
					</ul>
				</li>
			</ul>
		</li><input type="text" name="input[0][5]" />
			<ul>
				<li><input type="text" name="input[5][6]" /></li>
				<li><input type="text" name="input[5][7]" /></li>
				<li><input type="text" name="input[5][8]" /></li>
			</ul>
		<li><input type="text" name="input[0][9]" /></li>
		<li><input type="submit" value="submit" /></li>
	</ul>
</form>
<pre>
ROOT <- ITEMS 
<form action="?" method="post" id="asdf">
<?php if(count($_POST)) parse_tab($_POST['input'], 0); ?>
</form>
</pre>