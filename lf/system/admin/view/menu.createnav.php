
<h3>Create a new navigation item</h3>
<p>Simplify this.</p>
<form action="%appurl%new/" method="post">
	<ul>
		<li>Path: <select name="parent"><optgroup label="Select Base"><option value="-1" selected="selected">domain.com</option><option value="147">domain.com/</option><option value="109">domain.com/identity</option><option value="148">domain.com/argue</option><option value="150">domain.com/test</option></optgroup></select> / <input type="text" name="alias"  style="width: 75px;"/></li>
			<li>Title: <input type="text" name="title" /></li>
		<li>Label: <input type="text" name="label" /></li>
		<li>Position: <input type="text" name="position"  style="width: 25px;"/> Template: <select name="template"><option value="aios">Aios</option><option value="argue">Argue</option><option value="treetops">Treetops</option></select> App? <input type="checkbox" name="app" /> (ADD ACL)</li>
			<li><input type="hidden" name="id" value=""><input type="submit" value="Submit" /> </li>
	</ul>
</form>