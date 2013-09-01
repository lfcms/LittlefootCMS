<?php

if($auth->vars['user'] != 'anonymous')
{
	
	$sql = "SELECT DISTINCT t.acl FROM io_threads t WHERE t.owner_id = ".$auth->vars['id'];
	$result = $database->query($sql);
	
	if(!mysql_num_rows($result))
		$output .= "No Public Threads";
	else
	{
		$t_id = -1;
		$switch = true;
		while($row = mysql_fetch_assoc($result))
		{
			print_r($row); echo "<br>";
			$cat[] = $row['acl'];
		}
		
		krsort($cat);
		$output .= '<ul id="categories">';
		
		foreach($cat as $val)
		{
			$protocol = 'http://';
			$url = array(
				$conf['domain'],
				$conf['subdir'],
				'categories',
				$val
			);
			$output .= '
				<li>
					<a href="'.$protocol.implode('/',$url).'">'.$val.'</a>
				</li>
			';
		}
		$output .= '</ul>';
	}
}
else
{
	$output = "Please log in to view this page.";
}

?>