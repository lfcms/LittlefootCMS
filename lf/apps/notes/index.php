<?php

/*

//speed test
time a curl call to the website

//ftp manager in php (store user variables in session)

*/

	
//$output .= '<p>'.print_r($variables, true).'</p>';
//$output .= '<p>'.print_r($variables, true).'</p>';

//$sql = "SELECT * FROM notes_entry";

if(is_numeric($variables[0]))
{
	$sql = "
		SELECT 
			e.id, e.note,
			a.subject as parent
		FROM notes_entry e
			LEFT JOIN notes_assoc a ON a.child = e.id
		WHERE e.id = ".$variables[0]."
	";
	
	$result = $database->query($sql);

	$row = mysql_fetch_assoc($result);
	
	//print_r($row);
	
	$output .= '<p><a href="/notes/'.$row['parent'].'">..</a> | <a href="/notes/'.$row['id'].'">'.$row['note'].'</a></p>';

	$sql = "
		SELECT
			e.id, e.note,
			a.assoc
		FROM notes_entry e
			LEFT JOIN notes_assoc a ON a.child = e.id
		WHERE
			a.subject = ".$variables[0]."
	";

	$result = $database->query($sql);

	$output .= "<dl>";
	while($row = mysql_fetch_assoc($result))
		$output .= '
			<dt><a href="'.$row['id'].'">'.$row['assoc'].'</a></dt>
			<dd>'.$row['note'].'</dd>
		';
	$output .= "</dl>";
	$output .= '
		<form action="/notes/new" method="post">
			<ul>
				<li>Type: <input type="text" name="type" /></li>
				<li><textarea type="text" name="note" /></textarea></li>
				<li><input type="submit" value="Add Note" /></li>
			</ul>
		</form>
	';
}

?>