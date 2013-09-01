<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information.

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
		$global = $this->db->fetchall('SELECT * FROM lf_acl_global ORDER BY action ASC');
		$inherit = $this->db->fetchall('SELECT * FROM lf_acl_inherit');
		$user = $this->db->fetchall('SELECT * FROM lf_acl_user ORDER BY action ASC');
		
		function make_table($data, $type, $db)
		{
			if($data == array())
			{
				$result = $db->fetchall('SHOW COLUMNS FROM lf_acl_'.$type);
				foreach($result as $column)
					$keys[] = $column['Field'];
			}
			else
				$keys = array_keys($data[0]);
				
			unset($keys[0]);
			$ret = '
				<style type="text/css">
					td input { width: 90% !important; }
				</style>
				<form class="add" action="%appurl%add/'.$type.'/" method="post">
					<table width="100%">
						<tr style="text-align:left">
							<th>'.implode('</th><th>', $keys).'</th>
							<th>Delete</th><th>Edit</th>
						</tr>
			';
			foreach($data as $row)
			{
				if(isset($row['perm'])) $row['perm'] = $row['perm'] ? 'Allow' : 'Deny';
					
				$id = $row['id'];
				unset($row['id']);
				$ret .= '<tr><td>'.implode('</td><td>', $row).'</td>';
				$ret .= '
						 <td>[<a href="%appurl%rm/'.$type.'/'.$id.'/">x</a>]</td>
						 <td>[<a href="%appurl%edit/'.$type.'/'.$id.'/">e</a>]</td>
					</tr>
				';
			}
			$ret .= '
						<tr>';
			foreach($keys as $key)
			{
				$ret .= '<td><input type="text" name="'.$key.'" /></td>';
			}
			$ret .= '
							<td colspan="2">
									<input type="submit" value="Add New" />
							</td>
							</tr>
					</table>
				</form>';
			return $ret;
		}
		?>			
		<div class="widgetbox">
			<div class="title"><h2 class="tabbed"><span>User</span></h2></div>
			<div class="widgetcontent padding10">
				<div><?php echo make_table($user, 'user', $this->db); ?></div>
			</div><!--widgetcontent-->
		</div><!--widgetbox-->
		<div class="widgetbox">
			<div class="title"><h2 class="tabbed"><span>Inherit</span></h2></div>
			<div class="widgetcontent padding10">
				<div><?php echo make_table($inherit, 'inherit', $this->db); ?></div>
			</div><!--widgetcontent-->
		</div><!--widgetbox-->
		<div class="widgetbox">
			<div class="title"><h2 class="tabbed"><span>Global</span></h2></div>
			<div class="widgetcontent padding10">
				<div><?php echo make_table($global, 'global', $this->db); ?></div>
			</div><!--widgetcontent-->
		</div><!--widgetbox-->

		<?php // http://dev4.bioshazard.com/littlefoot/index.php/admin/acl/#tabs-2
		//echo make_table($user, 'user');
		//echo make_table($inherit, 'inherit');
		//echo make_table($global, 'global');
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
		$this->db->query("
			INSERT INTO lf_acl_".mysql_real_escape_string($vars[1])."
			VALUES (NULL, '".implode("', '", $_POST)."')
		");
		
		header("Location: ".$_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function rm($vars)
	{
		$this->db->query("
			DELETE FROM lf_acl_".mysql_real_escape_string($vars[1])."	
			WHERE id = ".intval($vars[2])."
		");
		
		header("Location: ".$_SERVER['HTTP_REFERER']);
		exit();
	}
}

?>