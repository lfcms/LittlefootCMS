<?php

/*
$html = '
	<h2>Navigation</h2>
	<p>Navigation items are used to anchor apps to user requests in the URL</p>
	<div class="left short border">
		<h3>Nav Items</h3>
		<h5>(dont delete nodes with children yet)</h5>
		<p>Click on any item in the list below to edit its settings</p>
		<div id="actions">%navitems%</div>
		
		<h3>%form_menu_h%</h3>
		<p>Dont forget to add a "simple" version to allow basic users to enter the same info for all fields /idea</p>
		%form_menu%
	</div>
	<div class="right">
		<h2>App Linker: %edit%</h2>
		<p>This tool can be used to link "Apps" to each Navigation item.<br />Select a navigation item on the left to attach apps to it.</p>
		<h3>Linked Apps</h3>
		<p>Below is a list of currently linked apps.</p>
		<table>
			<tr>
				<th>App</th>
				<th>ini</th>
				<th>Section</th>
				<th>Recursive</th>
			</tr>
			%apps_list%
		</table>
		<h3>%apps_form_h%</h3>
		%apps_form%
	</div>
	<div class="clear"></div>
';

/* -=-=-=-=-=-=-=-=-=- END TEMPLATE SECTION -=-=-=-=-=-=-=-=-=- */

// Look into saving all replacements in array $replace [ %var% ] = with

class menu3
{
	var $select;
	var $release;
	var $domain;
	var $variable1 = 0;
	
	function __construct($domain)
	{
		$this->domain = 'domain.com';
		$this->select = '';
	}
	
	public function recurse($menu, $parent, $edit, $depth = -1, $prefix = '', $snip = 0)
	{
		$items = $menu[$parent];
		
		$html = '<ul>';
		if(count($items) > 0)
		foreach($items as $item)
		{
			if($item['id'] == $edit['id'])
			{
				$snip = 1;
				$this->release = $depth;
				$this->variable1 = $item['id'];
			}
			
			$newprefix = $prefix . '/'. $item['alias'];
			
			if($snip != 1)
			{
				$this->select .= '<option value="'.$item['id'].'"';
				
				if($item['id'] == $edit['parent'])
					$this->select .= ' selected="selected"';
				
				$this->select .= '>'.$this->domain.$newprefix.'</option>';
			}
			else 
			{
				$this->current = array(
					'link' => $this->domain.$newprefix,
					'id' => $item['id']
				);
			}
			
			$html .= '<li';
			if($this->variable1 == $item['id'])
				$html .= ' class="selected"';
			$html .= '
					>'.$item['position'].'. 
					[<a onclick="return confirm(\'Do you really want to delete this?\');" href="%baseurl%/rm/'.$item['id'].'">x</a>]
					<a href="%baseurl%/edit/'.$item['id'].'">'.$item['label'].'</a>
			';
			
			// if a parent id is set in the array, print the child objects
			if(isset($menu[$item['id']]))
				$html .= $this->recurse($menu, $item['id'], $edit, $depth+1, $newprefix, $snip);
			
			
			$html .= '</li>';
			if($this->release == $depth)
				$snip = 0;
		}

		$html .= '</ul>';
		return $html;
	}
}

$pwd = $xms_php.'/apps';


if($auth['access'] != 'admin') die("lol no");

$msg = '';

switch($request->action[1])
{
	case 'newlink':
		
		foreach(scandir($pwd) as $file)
		{
			if($file == '.' || $file == '..') 
				continue;

			if(is_file($pwd.'/'.$file.'/index.php'))
				$app_filter[$file] = $file;
		}
		
		if(isset($app_filter[$vars['app']]))
			$app = $app_filter[$vars['app']];
		else
			break;
			
		
		$recurse = $vars['recursive'] == 'on' ? 1 : 0;
		$insert = array(
			"include"	=> mysql_real_escape_string($vars['include']),
			"app"		=> $app,
			"ini"		=> mysql_real_escape_string($vars['ini']),
			"section"	=> mysql_real_escape_string($vars['section']),
			"recursive"	=> $recurse,
		);
		
		$sql = "
			INSERT INTO 
				links	( `id`, `".implode('`, `',array_keys($insert))."`)
				VALUES	( NULL, '".implode("', '",array_values($insert))."')
		";
		$database->query($sql);
		
		$href = isset($_SERVER['HTTP_REFERER'])
			? $_SERVER['HTTP_REFERER']
			: $baseurl
		;
		
		header( "HTTP/1.1 302 Found" );
		header('location: '.$href);
		exit;
		
		break;
		
	case 'edit':
		if(isset($vars['parent']))
		{
			$id = mysql_real_escape_string($vars['id']);
			
			//select current children id's and positions
			$sql = 'SELECT position, parent FROM actions2 WHERE id = '.$id;
			$result = $database->query($sql);
			$from = mysql_fetch_assoc($result);
			$pos = mysql_real_escape_string($vars['position']);
			
			
			if($vars['parent'] != $from['parent'])
			{
				// change in parent node
				$sql = 'SELECT COUNT(*) FROM actions2 WHERE parent = '.mysql_real_escape_string($vars['parent']);
				$result = $database->query($sql);
				$row = mysql_fetch_array($result);
		
				if($row[0] >= $pos)
					$database->query('UPDATE actions2 SET position = position + 1 WHERE parent = '.mysql_real_escape_string($vars['parent']).' AND position >= '.$pos);
				else 
					$pos = $row[0] + 1;
				
				// deal with gap
				$database->query('UPDATE actions2 SET position = position - 1 WHERE parent = '.$from['parent'].' AND position > '.$from['position']);
			} 
			else if($vars['position'] < $from['position'])
			{
				$database->query('UPDATE actions2 SET position = position + 1 WHERE parent = '.mysql_real_escape_string($vars['parent']).' AND position >= '.$pos.' AND position < '.$from['position']);
			}
			else if($vars['position'] > $from['position'])
			{
				$database->query('UPDATE actions2 SET position = position - 1 WHERE parent = '.mysql_real_escape_string($vars['parent']).' AND position <= '.$pos.' AND position > '.$from['position']);
			}
			
			$app = $vars['app'] == 'on' ? '1' : '0';
			$update = array(
				"parent = 	'".mysql_real_escape_string($vars['parent'])."'",
				"position = '".$pos."'",
				"alias = 	'".mysql_real_escape_string($vars['alias'])."'",
				"title = 	'".mysql_real_escape_string($vars['title'])."'",
				"label = 	'".mysql_real_escape_string($vars['label'])."'",
				"app = 		'".$app."'",
				"template = '".mysql_real_escape_string($vars['template'])."'"
			);
			
			// Move the item
			$sql = "UPDATE actions2 SET ".implode(', ', $update)." WHERE id = ".$id;
			//echo $sql;
			$database->query($sql);
			$msg = 'Saved!';
		}
		break;
		
	case 'rmlink':
		
		$sql = 'DELETE FROM links WHERE id = '.mysql_real_escape_string($request->action[2]);
		$result = $database->query($sql);
		
		$href = isset($_SERVER['HTTP_REFERER'])
			? $_SERVER['HTTP_REFERER']
			: $baseurl
		;
		header( "HTTP/1.1 302 Found" );
		header('location: '.$href);
		
		break;	
		
	case 'rm':
		// be sure to have all positions update when an item is rm'd
		$result = $database->query('SELECT position, parent FROM actions2 WHERE id = '.mysql_real_escape_string($request->action[2]));
		$row = mysql_fetch_array($result);
		
		$sql = 'DELETE FROM actions2 WHERE id = '.mysql_real_escape_string($request->action[2]);
		$result = $database->query($sql);
		
		$database->query('UPDATE actions2 SET position = position - 1 WHERE parent = '.$row['parent'].' AND position > '.$row['position']);
		
		$href = isset($_SERVER['HTTP_REFERER'])
			? $_SERVER['HTTP_REFERER']
			: $baseurl
		;
		header( "HTTP/1.1 302 Found" );
		header('location: '.$href);
		
		break;
		
	case 'new':	
	
		$sql = 'SELECT COUNT(*) FROM actions2 WHERE parent = '.mysql_real_escape_string($vars['parent']);
		$result = $database->query($sql);
		$row = mysql_fetch_array($result);
		
		$pos = mysql_real_escape_string($vars['position']);
		
		if($row[0] >= $pos)
			$database->query('UPDATE actions2 SET position = position + 1 WHERE parent = '.mysql_real_escape_string($vars['parent']).' AND position >= '.$pos);
		else 
			$pos = $row[0] + 1;
		
		$id = 'NULL';
		$app = $vars['app'] == 'on' ? '1' : '0';
		$insert = array(
			"parent"	=> mysql_real_escape_string($vars['parent']),
			"position"	=> $pos,
			"alias"		=> mysql_real_escape_string($vars['alias']),
			"title"		=> mysql_real_escape_string($vars['title']),
			"label"		=> mysql_real_escape_string($vars['label']),
			"app"		=> $app,
			"template"	=> mysql_real_escape_string($vars['template'])
		);
		
		$sql = "
			INSERT INTO 
				actions2	( `id`, `".implode('`, `',array_keys($insert))."`)
				VALUES	( ".$id.", '".implode("', '",array_values($insert))."')
		";
		$database->query($sql);
		break;
	case 'child':
		
		break;
	default:
		break;
}

$result = $database->query("SELECT * FROM lf_actions ORDER BY position");

$save = '';
while($row = mysql_fetch_assoc($result))
{
	if($row['id'] == $request->action[2] && $request->action[1] == 'edit')
		$save = $row;
		
	$menu[$row['parent']][$row['position']] = $row;
}

if(isset($request->action[2]))
	$edit = $request->action[2];
else
	$edit = 0;

$menu_obj = new menu3($baseurl);

$html = '<div id="actions">'.$menu_obj->recurse($menu, -1, $save).'</div>';

$match_file = $request->skin;
if(isset($save['template']))
	$match_file = $save['template'];
	
$pwd = $xms_php.'/skins';

$template_select = '<select name="template">';
foreach(scandir($pwd) as $file)
{
	if($file == '.' || $file == '..') continue;

	$skin = $pwd.'/'.$file.'/index.php';
	if(is_file($skin))
	{
		$template_select .= '<option';
		
		if($match_file == $file)
		{
			$template_select .= ' selected="selected"';
			
			// Get all %replace% keywords for selected template
			$template = file_get_contents($skin);
			preg_match_all("/%([a-z]+)%/", $template, $tokens);
			$section_list = $tokens[1];
				
			//$save = $skin;
		}
		
		$template_name = $conf['skin'] == $file ? "Default" : ucfirst($file);
		
		$template_select .= ' value="'.$file.'">'.$template_name.'</option>';
	}
}

$template_select .= '</select>';

if(count($section_list) > 0)
{
	$section = '<select name="section">';
	foreach($section_list as $value)
	{
		$section .= '<option value="'.$value.'">'.$value.'</option>';
	}
	$section .= '</select>';
}
else
	$section = '<input type="text" name="section" />';

if(isset($menu_obj->current))
{
	$menuselect = '<input type="hidden" name="include" value="'.$menu_obj->current['id'].'" />';
	$linksh = $save['label'];
}
else
{
	$menuselect = '<input type="hidden" name="include" value="%" />';
	$linksh = 'All Pages';
}
	
$select = '';
$cmd = 'new';
if($save != '')
{
	$cmd = 'edit/'.$request->action[2];
	
	$select = $save['app'] == 1 ? 'checked="checked" ' : '';
	
	$eitch .= '<h3>Update Item: '.$save['label'].' ( <a href="%baseurl%">Deselect</a> )</h3>';
	
	foreach($save as $var => $val)
		$save[$var] = 'value="'.$val.'"';
		
} else 
{
	$id = '';
	$eitch .= '<h3>Create a new navigation item</h3>';
}


$html = '
	<h2>Navigation</h2>
	<p>Navigation items are used to anchor apps to user requests in the URL</p>
	<div class="left short">
		<h3>Nav Items</h3>
		<h5>(dont delete nodes with children yet)</h5>
		<p>Click on any item in the list below to edit its settings</p>
		'.$html.'
	</div>
	<div class="right">
		'.$eitch.'
		<p>Simplify this</p>
		<form action="%baseurl%/'.$cmd.'" method="post">
			<ul>
				<li>Path: <select name="parent"><optgroup label="Select Base"><option value="-1" %root%>domain.com</option>'.$menu_obj->select.'</optgroup></select> / <input type="text" name="alias" '.$save['alias'].' style="width: 75px;"/></li>
				<li>Title: <input type="text" name="title" '.$save['title'].'/></li>
				<li>Label: <input type="text" name="label" '.$save['label'].'/></li>
				<li>Position: <input type="text" name="position" '.$save['position'].' style="width: 25px;"/> Template: '.$template_select.' App? <input type="checkbox" name="app" '.$select.'/> (ADD ACL)</li>
				<li><input type="hidden" name="id" value="'.$request->action[2].'"><input type="submit" value="Submit" /> '.$msg.'</li>
			</ul>
		</form>
	</div>
';

$selected = $save['parent'] != -1 ? 'selected="selected"' : '';
$html = str_replace('%root%', $selected, $html);

$pwd = $xms_php.'/apps';
$select = '<select name="app">';
foreach(scandir($pwd) as $file)
{
	if($file == '.' || $file == '..') continue;
	
	if(is_file($pwd.'/'.$file.'/index.php'))
	{
		$select .= '<option value="'.$file.'">'.$file.'</option>';
	}
		
}
$select .= '</select>';
/*
$menuselect = '<option value="%">All</option>'.$menu_obj->select;
if(isset($menu_obj->current))
{
	$menuselect = '
	<optgroup label="currently selected">
		<option selected="selected" value="'.$menu_obj->current['id'].'">
			'.$menu_obj->current['link'].'
		</option>
	</optgroup>
	<optgroup label="select other">'.$menuselect.'</optgroup>';
}*/
	
$html .= '
	<div class="left border">
		<h2>App Linker: '.$linksh.'</h2>
		<p>This tool can be used to link "Apps" to each Navigation item.<br />Select a navigation item on the left to attach apps to it.</p>
';



$sql = 'SELECT * FROM lf_links WHERE include = ';

if($edit)
	$sql .= ''.$edit;
else
	$sql .= "'%'";
	
$result = $database->query($sql);
	
$html .= '<h3>Linked Apps</h3>
<p>Below is a list of currently linked apps.</p>
<table>
	<tr>
		<th>App</th>
		<th>ini</th>
		<th>Section</th>
		<th>Recursive</th>
	</tr>
';

while($row = mysql_fetch_assoc($result))
{
	if($row['ini'] == '') $row['ini'] = 'none';
	if($row['include'] == '%') $row['include'] = 'All';
	$html .= '
		<tr>
		<td>
		[<a onclick="return confirm(\'Do you really want to delete this?\');" href="%baseurl%/rmlink/'.$row['id'].'">x</a>] '.$row['app'].' </td><td> '.$row['ini'].' </td><td> '.$row['section'].' </td><td> '.$row['recursive'].'</td></tr>
	';
}
$html .= '</table>
	</div>
	<div class="right border">
		<h3>Add Apps</h3>
		<form action="%baseurl%/newlink" method="post">
			<ul>
				<li>App: '.$select.' ini: <input type="text" name="ini"/></li>
				<li>Section: '.$section.' Recursive? <input type="checkbox" name="recursive"/></li>
				<li>'.$menuselect.'<input type="submit" value="New Link" /> (ADD ACL)</li>
			</ul>
		</form>
	</div>
	<div class="clear"></div>
	';
	
//$html = str_replace('%baseurl%', $baseurl, $html);
/*

Each app has an admin page for managing

to do: select box that contains all nav items, radio buttons for selecting "at" and "child"

backend: 
use 
	update set var++ where parent = X
	update set var++ where parent = X and var > pos


*/

?>