<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information. 

class settings
{
	private $isadmin;
	private $request;
	private $html;
	private $pwd;
	private $dbconn;
	
	function __construct($request, $dbconn)
	{
		$this->db = $dbconn;
		$this->request = $request;
	}
	
	public function main($var)
	{
		$note = '';
		if(count($_POST))
		{
			if(isset($_POST['newvar']) && isset($_POST['newval']) && $_POST['newval'] != '' && !array_key_exists($_POST['newvar'], $_POST['setting']))
			{
				$sql = "
					INSERT INTO lf_settings (id, var, val)
					VALUES (NULL, '".mysql_real_escape_string($_POST['newvar'])."', '".mysql_real_escape_string($_POST['newval'])."')
				";
				$this->db->query($sql);
				$note .= '<p>Updated ['.$_POST['newvar'].'] to '.$_POST['newval'].'</p>';
			}
			if(isset($_POST['setting']))
				foreach($_POST['setting'] as $setting => $value)
				{
					if($value == '') continue;
					
					$sql = "
						UPDATE lf_settings 
						SET val = '".mysql_real_escape_string($value)."' 
						WHERE var = '".mysql_real_escape_string($setting)."'
					";
					$this->db->query($sql);
					$note .= '<p>Updated ['.$setting.'] to '.$value.'</p>';
				}
		}
		
		
		$sql = 'SELECT * FROM lf_settings ORDER BY var';
		
		$this->db->query($sql);
		$settings = $this->db->fetchall();
		
		echo '
		<form action="?" method="post">
			<table cellspacing="20">
				<tr style="text-align:left">
					<th>Variable</th>
					<th>Current Value</th>
					<th>New Value</th>
				</tr>';
		foreach($settings as $setting)
		{
			echo '
				<tr>
					<td><label for="'.$setting['var'].'">'.$setting['var'].'</label></td>
					<td>'.$setting['val'].'</td>
					<td><input type="text" id="'.$setting['var'].'" name="setting['.$setting['var'].']" /></td>
				</tr>';
		}
		echo '
				<tr>
					<td><input type="text" name="newvar" value="new var" /></td>
					<td>none</td>
					<td><input type="text" name="newval" /></td>
				</tr>
			</table>
			<input type="submit" value="submit" />
		</form>';
		echo $note;
		
		/*
		echo '<pre>';
		foreach($settings as $setting)
			echo implode(' | ', $setting).'<br />';
		echo '</pre>';*/
	}
}

?>