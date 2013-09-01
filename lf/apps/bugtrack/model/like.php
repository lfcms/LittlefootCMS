<?php

if(count($like) > 0 && $this->request->api('me') != 'anonymous')
{
	// Get list of likes
	$sql = "
		SELECT * FROM io_like 
		WHERE user_id = ".$this->request->api('getuid')." 
		AND link IN (
	";
	foreach($like as $replace => $ignore)
		$sql .= "'".$replace."', ";
	$sql = substr($sql, 0, strlen($sql)-2);
	$sql .= " )";
	$this->db->query($sql);
	
	// replace all like markers where a like is already in place
	$replace = ''; $with = '';
	while($row = $this->db->fetch())
	{	
		$replace[] = '%'.$row['link'].'%';
		$with[] = '<a class="unlike hrefapi" href="%appurl%unlike/'.$row['link'].'/">Unlike</a>';
		unset($like[$row['link']]);
	}
	$out = str_replace($replace, $with, $out);
	
	// replace other like markers that have not been clicked
	$replace = ''; $with = '';	
	foreach($like as $var => $val)
	{
		$replace[] = '%'.$var.'%'; 
		$with[] = '<a class="like hrefapi" href="%appurl%like/'.$var.'/">Like</a>';
	}
	$out = str_replace($replace, $with, $out);
}

?>