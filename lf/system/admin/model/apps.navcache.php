<?php

function build_nav_cache($menu, $parent = -1, $prefix = '')
{
	$items = $menu[$parent];
	
	$html = '<ul>';
	if($items)
	foreach($items as $item) // loop through the items
	{
		$newprefix = $prefix;
		$newprefix[] = $item['alias'];
		
		// Generate printable request in/this/form
		$link = implode('/',$newprefix);
		if(strlen($link) != 0) 
			$link .= '/';
		
		$icon = '';
		if(isset($menu[$item['id']]))
			$icon = '<i class="fa fa-caret-down fsmall"></i>';
		
		// and generate the <li></li> element content
		$html .= '<li><a href="%baseurl%'.$link.'" title="'.$item['title'].'">'.$item['label'].' '.$icon.'</a>';
		
		// Process any submenus before closing <li>
		if(isset($menu[$item['id']]))
			$html .= build_nav_cache($menu, $item['id'], $newprefix);
			
		$html .= '</li>';
	}
	$html .= '</ul>';
	
	return $html;
}