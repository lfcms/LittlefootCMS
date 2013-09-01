<?php

class menu
{
	private $request;
	private $html;
	private $pwd;
	private $dbconn;
	public $default_method = 'view';
	
	public function __construct($request, $dbconn)
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->pwd = $request->absbase.'/apps';
		//include('view/menu.head.php');
	}
	
	public function view($vars)
	{	
		include('model/navgen.php');
			$result = $this->db->query("
				SELECT a.*, l.id as lid, l.app, l.section, l.ini
				FROM lf_actions a 
				LEFT JOIN lf_links l
					ON l.include = a.id
				ORDER BY ABS(a.position)
			");

			$save = '';
			while($row = mysql_fetch_assoc($result))
			{
				if(isset($vars[1]) && $row['id'] == $vars[1])
					$save = $row;
				
				if($row['position'] == 0)
					$hidden[] = $row;
				else
					$menu[$row['parent']][$row['position']] = $row;
			}
			
			if(isset($vars[1])) $edit = $vars[1];
			else				$edit = 0;
			
			if(isset($menu)) 	$nav = build_menu($menu, $save);
			if(isset($hidden))	$hooks = build_hidden($hidden, $save);
			
		include('model/templateselect.php');
		
		// Get list of apps currently linked
		include('model/appgen.php');
			$sql = 'SELECT * FROM lf_links WHERE include = ';
			
			if($save != '')	$sql .= $edit;
			else $sql .= "'%'";
			
			$sql .= ' ORDER BY section ASC, id ASC';
			
			$result = $this->db->query($sql);
			$apps = build_apps($result, $save);
		
		if(strlen($apps) == 0) $apps = '<li><blockquote><p>* No apps are currently linked, use the form below to link new apps.</p></blockquote></li>';
		
		// Generate list of apps for linker
		$pwd = $this->request->absbase.'/apps';
		$app_list = '<select name="app">';
		foreach(scandir($pwd) as $file)
		{
			if($file == '.' || $file == '..') continue;
			
			if(is_file($pwd.'/'.$file.'/index.php'))
			{
				$app_list .= '<option value="'.$file.'">'.$file.'</option>';
			}
				
		}
		$app_list .= '</select>';
			
		include('view/menu.view.php');
	}
	
	public function view_old($vars)
	{	
		include('model/navgen.php');
			$result = $this->db->query("SELECT * FROM lf_actions ORDER BY ABS(position)");

			$save = '';
			while($row = mysql_fetch_assoc($result))
			{
				if(isset($vars[1]) && $row['id'] == $vars[1])
					$save = $row;
				
				if($row['position'] == 0)
					$hidden[] = $row;
				else
					$menu[$row['parent']][$row['position']] = $row;
			}
			
			if(isset($vars[1])) $edit = $vars[1];
			else				$edit = 0;
			
			if(isset($menu)) 	$nav = build_menu($menu, $save);
			if(isset($hidden))	$hooks = build_hidden($hidden, $save);
			
		include('model/templateselect.php');
		
		// Get list of apps currently linked
		include('model/appgen.php');
			$sql = 'SELECT * FROM lf_links WHERE include = ';
			
			if($save != '')	$sql .= $edit;
			else $sql .= "'%'";
			
			$sql .= ' ORDER BY section ASC, id ASC';
			
			$result = $this->db->query($sql);
			$apps = build_apps($result, $save);
		
		if(strlen($apps) == 0) $apps = '<li><blockquote><p>* No apps are currently linked, use the form below to link new apps.</p></blockquote></li>';
		
		// Generate list of apps for linker
		$pwd = $this->request->absbase.'/apps';
		$app_list = '<select name="app">';
		foreach(scandir($pwd) as $file)
		{
			if($file == '.' || $file == '..') continue;
			
			if(is_file($pwd.'/'.$file.'/index.php'))
			{
				$app_list .= '<option value="'.$file.'">'.$file.'</option>';
			}
				
		}
		$app_list .= '</select>';
			
		include('view/menu.view.php');
	}
	
	public function updatenav($vars)
	{
		include('model/navgen.php');
			$result = $this->db->query("SELECT * FROM lf_actions ORDER BY ABS(position)");

			$save = '';
			while($row = mysql_fetch_assoc($result))
			{
				if(isset($vars[1]) && $row['id'] == $vars[1])
					$save = $row;
				
				if($row['position'] == 0)
					$hidden[] = $row;
				else
					$menu[$row['parent']][$row['position']] = $row;
			}
			
			if(isset($vars[1])) $edit = $vars[1];
			else				$edit = 0;
			
			if(isset($menu)) 	$nav = build_menu($menu, $save);
			if(isset($hidden))	$hooks = build_hidden($hidden, $save);
			
		include('model/templateselect.php');
		
		// Get list of apps currently linked
		include('model/appgen.php');
			$sql = 'SELECT * FROM lf_links WHERE include = ';
			
			if($save != '')	$sql .= $edit;
			else $sql .= "'%'";
			
			$sql .= ' ORDER BY section ASC, id ASC';
			
			$result = $this->db->query($sql);
			$apps = build_apps($result, $save);
		
		if(strlen($apps) == 0) $apps = '<li><blockquote><p>* No apps are currently linked, use the form below to link new apps.</p></blockquote></li>';
		
		// Generate list of apps for linker
		$pwd = $this->request->absbase.'/apps';
		$app_list = '<select name="app">';
		foreach(scandir($pwd) as $file)
		{
			if($file == '.' || $file == '..') continue;
			
			if(is_file($pwd.'/'.$file.'/index.php'))
			{
				$app_list .= '<option value="'.$file.'">'.$file.'</option>';
			}
				
		}
		$app_list .= '</select>';
		
		include 'view/menu/updatenav.php';
	}
	
	public function update($vars)
	{
		// redirect them after this completes
		header("Location: ".$_SERVER['HTTP_REFERER']);
		
		$vars = $this->request->post;
		if(isset($vars['parent']))
		{
			$id = mysql_real_escape_string($vars['id']);
			
			//select current children id's and positions
			$sql = 'SELECT position, parent FROM lf_actions WHERE id = '.$id;
			$result = $this->db->query($sql);
			$from = mysql_fetch_assoc($result);
			$pos = intval($vars['position']); // default target position is as requested
			
			// if from hook to nav
				// do like create
			// else if from nav to hook
				// do like rm
			// else if from nav to nav
			
			if($from['position'] == 0 && $vars['position'] > 0)
			{
				// Get number of items under parent
				$sql = 'SELECT COUNT(id) FROM lf_actions WHERE parent = '.mysql_real_escape_string($vars['parent']).' AND position != 0';
				$result = $this->db->query($sql);
				$row = mysql_fetch_array($result);
				
				// $pos = target position, $row[0] = number of children under the same parent
				if($row[0] >= $pos)
					$this->db->query('UPDATE lf_actions SET position = position + 1 WHERE parent = '.mysql_real_escape_string($vars['parent']).' AND position >= '.$pos);
				else 
					$pos = $row[0] + 1;
			}
			else if ($from['position'] > 0 && $vars['position'] == 0) // $from = updating item's original values, $vars = $_POSTed var values
			{
				// update positions of all item behind the rm'd sibling
				$sql = 'UPDATE lf_actions SET position = position - 1 WHERE parent = '.$from['parent'].' AND position > '.$from['position'];
				$this->db->query($sql);
			}
			else if($from['position'] > 0 && $vars['position'] > 0)
			{
				if($vars['parent'] != $from['parent'])
				{
					// change in parent node
					$sql = 'SELECT COUNT(id) FROM lf_actions WHERE parent = '.mysql_real_escape_string($vars['parent']);
					$result = $this->db->query($sql);
					$row = mysql_fetch_array($result);
			
					if($row[0] >= $pos)
						$this->db->query('UPDATE lf_actions SET position = position + 1 WHERE parent = '.mysql_real_escape_string($vars['parent']).' AND position >= '.$pos);
					else 
						$pos = $row[0] + 1;
					
					// deal with gap
					$this->db->query('UPDATE lf_actions SET position = position - 1 WHERE parent = '.$from['parent'].' AND position > '.$from['position']);
				} 
				else if($vars['position'] < $from['position'])
				{
					$this->db->query('UPDATE lf_actions SET position = position + 1 WHERE parent = '.mysql_real_escape_string($vars['parent']).' AND position >= '.$pos.' AND position < '.$from['position']);
				}
				else if($vars['position'] > $from['position'])
				{
					$this->db->query('UPDATE lf_actions SET position = position - 1 WHERE parent = '.mysql_real_escape_string($vars['parent']).' AND position <= '.$pos.' AND position > '.$from['position']);
				}
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
			$sql = "UPDATE lf_actions SET ".implode(', ', $update)." WHERE id = ".$id;
			//echo $sql;
			$this->db->query($sql);
		}
		
		exit();
	}
	
	public function rm($vars)
	{
		// redirect them after this completes
		header("Location: ".$_SERVER['HTTP_REFERER']);
		
		if($vars[1] == 'link')
		{
			$sql = 'DELETE FROM lf_links WHERE id = '.intval($vars[2]);
			$this->db->query($sql);
		}
		else if($vars[1] == 'menu')
		{
			// be sure to have all positions update when an item is rm'd
			$sql = 'SELECT position, parent FROM lf_actions WHERE id = '.intval($vars[2]); 
			$this->db->query($sql);
			$row = $this->db->fetch();
			
			if(isset($row['parent']))
			{
				$sql = 'DELETE FROM lf_actions WHERE id = '.intval($vars[2]);
				$this->db->query($sql);
				
				// update positions of all item behind the rm'd sibling
				if($row['position'] > 0)
				{
					$sql = 'UPDATE lf_actions SET position = position - 1 WHERE parent = '.$row['parent'].' AND position > '.$row['position'];
					$this->db->query($sql);
				}
			}
		}
		
		exit();
	}
	
	public function newapp($vars)
	{	
		if(isset($vars[1]))
			$_POST['app'] = $vars[1];
			
		// get template vars
		include('model/templateselect.php');
		
		$pwd = $this->request->absbase.'/apps/';
			
		$args = '<input type="text" name="ini" />';
		
		if(is_file($pwd.$_POST['app'].'/args.php'))
			include $pwd.$_POST['app'].'/args.php';
		
		// if the selected app 
		include 'view/newapp.php';
	} 
	
	public function create($vars)
	{
		
		if($vars[1] == 'link')
		{	
			$url = '';
			if($_POST['include'] != '%')
				$url = 'view/'.$_POST['include'].'/';
			
			// redirect them after this completes
			header("Location: ".$this->request->base.'menu/'.$url);
			$pwd = $this->request->absbase.'/apps';
			$vars = $this->request->post;
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
				exit();
			
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
					lf_links	( `id`, `".implode('`, `',array_keys($insert))."`)
					VALUES	( NULL, '".implode("', '",array_values($insert))."')
			";
			
			$this->db->query($sql);
		}
		else if($vars[1] == 'menu')
		{
			// redirect them after this completes
			header("Location: ".$_SERVER['HTTP_REFERER']);
			$vars = $this->request->post;
			
			$pos = intval($vars['position']);
			
			if($pos != 0)
			{
				$sql = 'SELECT COUNT(id) FROM lf_actions WHERE parent = '.mysql_real_escape_string($vars['parent']).' AND position != 0';
				$result = $this->db->query($sql);
				$row = mysql_fetch_array($result);
				
				if($row[0] >= $pos)
					$this->db->query('UPDATE lf_actions SET position = position + 1 WHERE parent = '.mysql_real_escape_string($vars['parent']).' AND position >= '.$pos);
				else 
					$pos = $row[0] + 1;
			}
			
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
					lf_actions	( `id`, `".implode('`, `',array_keys($insert))."`)
					VALUES	( ".$id.", '".implode("', '",array_values($insert))."')
			";
			
			$this->db->query($sql);
		}
		
		exit();
	}
}

?>