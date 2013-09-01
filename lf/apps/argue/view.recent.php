<h3>Messages</h3>
<ul>
	<?php foreach($result as $row) {?>
	<li>
		<?php
			echo $row['user'].': '.$row['message']; 
		?>
	</li>
	<?php } ?>
</ul>
<form action="%baseurl%" method="post" id="argue_add">
	<fieldset>
		<legend>Reply</legend>
		@ <select name="" id="">
			<option value="">test</option>
			<option value="">tasdfest</option>
			<option value="">asdf</option></select>
		<textarea name="message" id=""></textarea>
		<input type="submit" value="Send" />
	</fieldset>
</form>