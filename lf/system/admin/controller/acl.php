<?php

function make_table($data, $type, $db)
{
	$nav = file_get_contents(ROOT.'cache/nav.cache.html');
	$nav = str_replace('%baseurl%', '', $nav);
	
	preg_match_all('/href="([^"]+)\/"/', $nav, $match);
	
	$action = '';
	foreach($match[1] as $url)
	{
		$action .= '<option value="'.$url.'">BASE/'.$url.'</option>';
	}
	
	$perm = '<option value="0">Deny</option><option value="1">Allow</option>';
	
	$affects = '<optgroup label="Groups">';
	$groups = $db->fetchall("SELECT DISTINCT access FROM lf_users");
	foreach($groups as $group)
		$affects .= '<option value="'.$group['access'].'">'.$group['access'].'</option>';
	$affects .= '</optgroup>';
	
	$inherits = $affects; // should be made to inherit from user
	
	$users = $db->fetchall("SELECT id, display_name FROM lf_users");
	$affects .= '<optgroup label="Users">';
	foreach($users as $user)
		$affects .= '<option value="'.$user['id'].'">'.$user['display_name'].'</option>';
	$affects .= '</optgroup>';
		
		
	$group = $affects;
	
	if($data == array())
	{
		$result = $db->fetchall('SHOW COLUMNS FROM lf_acl_'.$type);
		foreach($result as $column)
			$keys[] = $column['Field'];
	}
	else
		$keys = array_keys($data[0]);
		
	unset($keys[0]);
	
	$ret = '<form class="add" action="%appurl%add/'.$type.'/" method="post">';
	foreach($keys as $key) 
		$ret .= '<select name="'.$key.'" id="">'.$$key.'</select> ';
	
	if($type != 'inherit') { $ret .= '<input type="text" name="appurl" placeholder="(optional) app url" /> '; } 
	
	$ret .= '<input type="submit" value="Add New" />
			</form>
			<table>
				<tr>
					<th>'.implode('</th><th>', $keys).'</th>
					<th></th>
					<th></th>
				</tr>
	';
	foreach($data as $row)
	{
		if(isset($row['perm'])) $row['perm'] = $row['perm'] ? 'Allow' : 'Deny';
			
		// user list
		if(isset($row['affects']))
			if(is_numeric($row['affects'])) 
				$row['affects'] = 'User / %user:'.$row['affects'].'%';
			else
				$row['affects'] = 'Group / '.$row['affects'];
			
		// inherit list
		if(isset($row['group']))
		{
			if(is_numeric($row['group'])) 
				$row['group'] = 'User / %user:'.$row['group'].'%';
			else
				$row['group'] = 'Group / '.$row['group'];
		}
			
		// inherit list
		if(isset($row['inherits']))
			if(is_numeric($row['inherits'])) 
				$row['inherits'] = 'User / %user:'.$row['inherits'].'%';
			else
				$row['inherits'] = 'Group / '.$row['inherits'];
			
		$id = $row['id'];
		unset($row['id']);
		$ret .= '<tr>
				 <td>'.implode('</td><td>', $row).'</td>
				 <td><!-- <a href="%appurl%edit/'.$type.'/'.$id.'/">edit</a> coming soon --></td>
				 <td><a href="%appurl%rm/'.$type.'/'.$id.'/" class="delete_item">delete</a></td>
			</tr>
		';
	}
	$ret .= '
				
			</table>';		
	
	return $ret;
}

/**
 * @ignore
 */
class acl
{
	private $db = NULL;
	private $request;
	
	function __construct($request, $dbconn)
	{
		$this->db = $dbconn;
		$this->request = $request;
	}
	
	public function main($vars)
	{
		if($vars[0] == '') $vars[0] = 'user';
		$request = str_replace('acl_', '', $vars[0]);
		
		$user = $this->db->fetchall('SELECT * FROM lf_acl_user ORDER BY action ASC');
		$inherit = $this->db->fetchall('SELECT * FROM lf_acl_inherit');
		$global = $this->db->fetchall('SELECT * FROM lf_acl_global ORDER BY action ASC');
		
		$header = '<a href="%appurl%acl_user/">User</a> | <a href="%appurl%acl_inherit/">Inherit</a> | <a href="%appurl%acl_global/">Global</a>';
		$header = str_replace('<a href="%appurl%acl_'.$request.'/">'.ucfirst($request).'</a>', '<a class="activeacl" href="%appurl%acl_'.$request.'/">'.ucfirst($request).'</a>', $header);
		
		?>			
		
		<h2>Access Control Lists <?php echo $header; ?></h2>
		<?php 
		
		$table = make_table($$request, $request, $this->db);
		
		if(preg_match_all('/%user:([0-9]+)%/', $table, $matches))
		{
			$users = $matches[1];
			$sql = 'SELECT id, display_name FROM lf_users 
				WHERE id IN ('.implode(',', $users).')';
				
			$usernames = $this->db->fetchall($sql);
			foreach($usernames as $user)
				$replace['%user:'.$user['id'].'%'] = $user['display_name'];
			
			$table = str_replace(
				array_keys($replace), 
				array_values($replace), 
				$table);
		}
		
		
		echo $table;
		
		/*
		<div class="widgetbox">
			<div class="title"><h3 class="tabbed"><span>User</span></h3></div>
			<div class="widgetcontent padding10">
				<div><?php echo make_table($user, 'user', $this->db); ?></div>
			</div><!--widgetcontent-->
		</div><!--widgetbox-->
		<div class="widgetbox">
			<div class="title"><h3 class="tabbed"><span>Inherit</span></h3></div>
			<div class="widgetcontent padding10">
				<div><?php echo make_table($inherit, 'inherit', $this->db); ?></div>
			</div><!--widgetcontent-->
		</div><!--widgetbox-->
		<div class="widgetbox">
			<div class="title"><h3 class="tabbed"><span>Global</span></h3></div>
			<div class="widgetcontent padding10">
				<div><?php echo make_table($global, 'global', $this->db); ?></div>
			</div><!--widgetcontent-->
		</div><!--widgetbox-->
		*/ ?>
		
		<?php // http://dev4.bioshazard.com/littlefoot/index.php/admin/acl/#tabs-2
		//echo make_table($user, 'user');
		//echo make_table($inherit, 'inherit');
		//echo make_table($global, 'global');
	}
	
	private function acl_user($vars)
	{
		
	}
	
	private function acl_inherit($vars)
	{
		
	}
	
	private function acl_global($vars)
	{
		
	}
	
	public function edit($vars)
	{
		echo '<pre>';
		print_r($vars);
		print_r($_POST);
		echo '</pre>';
	}
	
	public function update($vars)
	{
		
		echo '<pre>';
		print_r($vars);
		print_r($_POST);
		echo '</pre>';
		
		return;
		
		header("Location: ".$_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function add($vars)
	{
		if($_POST['appurl'] != '') $_POST['action'] = $_POST['action'].'|'.$_POST['appurl'];
		
		unset($_POST['appurl']);
		
		foreach($_POST as $key => $val)
			$_POST[$key] = $this->db->escape($val);
			
		$this->db->query("
			INSERT INTO lf_acl_".$this->db->escape($vars[1])."
			VALUES (NULL, '".implode("', '", $_POST)."')
		");
		
		header("Location: ".$_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function rm($vars)
	{
		$this->db->query("
			DELETE FROM lf_acl_".$this->db->escape($vars[1])."	
			WHERE id = ".intval($vars[2])."
		");
		
		header("Location: ".$_SERVER['HTTP_REFERER']);
		exit();
	}
}

?>