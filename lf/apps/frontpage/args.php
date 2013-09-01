<?php

$pages = $this->db->fetchall('SELECT id, title FROM lf_frontpage');

$args = '';
if(count($pages))
{
	$args .= '<select name="ini" id="">';
	foreach($pages as $page)
		$args .= '<option value="'.$page['id'].'">'.$page['id'].' - '.$page['title'].'</option>';
	$args .= '</select> or ';
}

$args .= '<a href="%appurl%new/">Create New Frame</a>';

?>