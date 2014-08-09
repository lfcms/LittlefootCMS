<?php

function build_menu($menu, $edit, $parent = -1, $depth = -1, $prefix = '', $snip = 0)
{
	//echo '<pre>';
	$variable1 = NULL;
	$nav['select'] = '';
	
	static $release = 0;
	$items = $menu[$parent];
	
	$html = '<ol>';
	if(count($items) > 0)
	foreach($items as $item)
	{
		$newprefix = $prefix . '/'. $item['alias'];
		
		if(isset($edit['id']) && $item['id'] == $edit['id'])
		{
			$snip = 1; // dont take these nav items as options since we have them currently selected
			$release = $depth;
			$variable1 = $item['id'];
		}
		
		if(!$snip)
		{	
			$select = '';
			if(isset($edit['parent']) && $item['id'] == $edit['parent'])
				$select = ' selected="selected"';
			
			$word = str_repeat("/ ", substr_count($prefix, '/')).$item['alias'];
			$nav['select'] .= '<option value="'.$item['id'].'" '.$select.'>'.$word.'</option>';
		}
		
		$html .= '<li';
		if($variable1 == $item['id'])
			$html .= ' class="selected"';
		$html .= ' id="nav_item_'.$item['id'].'">';
			
		$apphtml = $item['app'];
		
		//$apphtml .= ' (<a href="%appurl%main/'.$item['id'].'/">Edit</a>)';
		
		$html .= '<a class="nav_delete_item" '.jsprompt('Are you sure?').' href="%baseurl%apps/rm/'.$item['id'].'/">x</a>';
		
		if(is_file(ROOT.'apps/'.$item['app'].'/admin.php'))
		{
			$apphtml .= ' <a href="%baseurl%dashboard/manage/'.$item['app'].'/"  class="nav_manage_link">admin</a>';
		}
		else
		{
			$apphtml .= ' <span class="no_admin">admin</span>';
		}	
		// set postion for nav item
		if($variable1 == $item['id'])
		{
			$pos = 'Position: <input type="text" name="position"  style="width: 40px;" value="';
			//if(isset($save['position'])) 
				$pos .= $edit['position']; 
			//else $pos .= 1;
			$pos .= '" /> ';
			
			$label = 'Label: <input type="text" name="label" value="'.$item['label'].'" />';
		}
		else 
		{
			$pos = $item['position'].'. ';
			$label = '<a class="nav_selector" href="%appurl%main/'.$item['id'].'/#nav_'.$item['alias'].'">'.$item['label'].'</a>';
		}
			
			
		if($variable1 == $item['id'])
			$html .= '<form id="nav_form" action="%appurl%update/" method="post">';
			
		$html .= $pos.' '.$label.' - App: '.$apphtml;
		
		if($variable1 == $item['id'])
			$html .= '%editform%</form>';
		
		// if a parent id is set in the array, print the child objects
		if(isset($menu[$item['id']]))
		{
			$output = build_menu($menu, $edit, $item['id'], $depth+1, $newprefix, $snip);
			$html .= $output['html'];
			$nav['select'] .= $output['select'];
		}
		
		$html .= '</li>';
		if($release == $depth)
			$snip = 0;
	}

	$html .= '</ol>';
	
	$nav['html'] = $html;
	return $nav;
}

function build_hidden($items, $edit)
{
	//echo '<pre>';
	$variable1 = NULL;
	$hooks['select'] = '';
	
	static $release = 0;
	
	$html = '<ul>';
	if(count($items) > 0)
		foreach($items as $item)
		{
			$selected = (isset($edit['id']) && $edit['id'] == $item['id']);
			$select = '';
			if($selected)
			{
				if(isset($edit['parent']) && $item['id'] == $edit['parent'])
					$select = ' selected="selected"';
			}
			
			$word = $item['alias'];
			$hooks['select'] .= '<option value="'.$item['id'].'" '.$select.'>'.$word.'</option>';
			
			$html .= '<li';
			if($selected)
				$html .= ' class="selected"';
			$html .= '>';			
				
			$apphtml = $item['app'];
			
			
			
			$html .= '<a class="nav_delete_item" '.jsprompt('Are you sure?').' href="%baseurl%apps/rm/'.$item['id'].'/">x</a>';
			
			if(is_file(ROOT.'apps/'.$item['app'].'/admin.php'))
			{
				$apphtml .= ' <a href="%baseurl%dashboard/manage/'.$item['app'].'/" class="nav_manage_link">admin</a>';
			}
			else
			{
				$apphtml .= ' <span class="no_admin">admin</span>';
			}
			// set postion for nav item
			if($selected)
			{
				$pos = '<input type="text" name="position"  style="width: 20px;" value="';
				//if(isset($save['position'])) 
					$pos .= $edit['position']; 
				//else $pos .= 1;
				$pos .= '" /> ';
				
				$label = 'Label: <input type="text" name="label" value="'.$item['label'].'" />';
			}
			else 
			{
				$pos = '';
				$label = '<a href="%appurl%main/'.$item['id'].'/#nav_'.$item['alias'].'">'.$item['label'].'</a>';
			}
			
			if($selected)
				$html .= '<form action="%appurl%update/" method="post">';
				
			$html .= $pos.'	'.$label.' - '.$apphtml;

			if($selected)
				$html .= '%editform%</form>';
			
			$html .= '</li>';
		}

	$html .= '</ul>';
	$hidden['html'] = $html;
	return $hidden;
}

?>