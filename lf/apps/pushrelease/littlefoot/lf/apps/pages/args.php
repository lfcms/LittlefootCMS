<?php

	$sql = 'SELECT id, title FROM lf_pages';
	$this->db->query($sql);
	$pages = $this->db->fetchall();
	
	if(count($pages))
	{
		$args = '<select name="ini" id="">';
		foreach($pages as $page)
			$args .= '<option value="'.$page['id'].'">'.$page['id'].' - '.$page['title'].'</option>';
		$args .= '</select> or ';
	}
	
	$args .= '<a href="%baseurl%apps/manage/pages/new/">Create New Page</a>';
?>