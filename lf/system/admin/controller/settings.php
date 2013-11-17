<?php 

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
		// Query for settings, save as array[var] = val)
		$sql = 'SELECT * FROM lf_settings ORDER BY var';
		$this->db->query($sql);
		$result = $this->db->fetchall();
		foreach($result as $setting)
			$settings[$setting['var']] = $setting['val'];
			
		if(count($_POST))
		{
			/*if(isset($_POST['newvar']) && isset($_POST['newval']) && $_POST['newval'] != '' && !array_key_exists($_POST['newvar'], $_POST['setting']))
			{
				$sql = "
					INSERT INTO lf_settings (id, var, val)
					VALUES (NULL, '".mysql_real_escape_string($_POST['newvar'])."', '".mysql_real_escape_string($_POST['newval'])."')
				";
				$this->db->query($sql);
			}*/
			
			if(isset($_POST['setting']))
			{
				$sql = "UPDATE lf_settings SET val = CASE var";
			
				foreach($_POST['setting'] as $var => $val)
				{
				
					$sql .= " WHEN '".mysql_real_escape_string($var)."' THEN '".mysql_real_escape_string($val)."'";
					$params[] = mysql_real_escape_string($var);
				}
				
				$sql .= " END WHERE var IN ('".implode("', '", $params)."')";
				
				$this->db->query($sql);
			}
			
			redirect302();
		}
		
		
		// Settings form
		$rewrite = 'URL Rewrite:  <select name="setting[rewrite]" id=""><option value="on">on</option><option value="off">off</option></select>';
		if(!isset($settings['rewrite']) || $settings['rewrite'] == 'off')
			$rewrite = str_replace(' value="off"', ' selected="selected" value="off"', $rewrite);
			
		if(!isset($settings['force_url']) || $settings['force_url'] != '')
			$url = $settings['force_url'];
		else $url = '';
		$force_url = 'Force URL (empty to not force URL): <input type="text" name="setting[force_url]" size="50" value="'.$url.'" />';
		
		if(!isset($settings['nav_class']) || $settings['nav_class'] != '')
			$class = $settings['nav_class'];
		else $class = '';
		$navclass = 'Navigation CSS class: <input type="text" name="setting[nav_class]" value="'.$class.'" />';
		
		$debug = 'Debug:  <select name="setting[debug]" id=""><option value="on">on</option><option value="off">off</option></select>';
		if(!isset($settings['debug']) || $settings['debug'] == 'off')
			$debug = str_replace(' value="off"', ' selected="selected" value="off"', $debug);
		
		$apps = scandir(ROOT.'apps'); // get app list
		unset($apps[1], $apps[0]);
		$simple_options = '<option value="_lfcms">Full CMS</option>';
		foreach($apps as $app)
		{
			if(is_file(ROOT.'apps/'.$app.'/index.php'))
				$simple_options .= '<option value="'.$app.'">'.$app.'</option>';
		}
		$simple_options = str_replace(' value="'.$settings['simple_cms'].'"', ' selected="selected" value="'.$settings['simple_cms'].'"', $simple_options);
		$simplecms = 'Simple CMS:  <select name="setting[simple_cms]" id="">'.$simple_options.'</select>';
		
		// Settings form
		$signup = 'Enable Signup:  <select name="setting[signup]" id=""><option value="on">on</option><option value="off">off</option></select>';
		if(!isset($settings['signup']) || $settings['signup'] == 'off')
			$signup = str_replace(' value="off"', ' selected="selected" value="off"', $signup);
		 
		echo '
			<div id="admin_settings">
				<h2>Settings</h2>
				<form action="?" method="post">
					<ul>
						<li>'.$rewrite.'</li>
						<li>'.$force_url.'</li>
						<li>'.$navclass.'</li>
						<li>'.$debug.'</li>
						<li>'.$signup.'</li>
						<li>'.$simplecms.' (works, but no option for ini yet)</li>
						<li><input type="submit" value="submit" /></li>
					</ul>
				</form>
			</div>
		';
		
		
		
		
		
		
		
	}
}

?>