<?php 

if($this->api('getuid') == 0)
	echo 'Access Denied %login%';
else
{
	ob_start();
	echo $this->mvc('hq');
	$out = ob_get_clean();

	if(preg_match_all('/%user:([0-9]+)%/', $out, $match)) // use this for Littlefoot's base code
	{
		$users = array_unique($match[1]);
		$userlist = $this->db->fetchall('SELECT id, display_name FROM lf_users WHERE id IN ('.implode(',', $users).')');
		
		foreach($userlist as $user)
			$out = str_replace('%user:'.$user['id'].'%', $user['display_name'], $out);
	}

	if(preg_match_all('/%category:([0-9]+)%/', $out, $match))
	{
		$cats = array_unique($match[1]);
		$categories = $this->db->fetchall('SELECT id, category FROM hq_categories WHERE id IN ('.implode(',', $cats).')');
		
		foreach($categories as $category)
			$out = str_replace('%category:'.$category['id'].'%', $category['category'], $out);
			
		$out = str_replace('%category:0%', 'Uncategorized', $out);
	}

	if(preg_match_all('/%project:([0-9]+)%/', $out, $match))
	{
		$projects = array_unique($match[1]);
		$result = $this->db->fetchall('SELECT id, title FROM hq_projects WHERE id IN ('.implode(',', $projects).')');
		
		foreach($result as $project)
			$out = str_replace('%project:'.$project['id'].'%', $project['title'], $out);
			
		$out = str_replace('%project:0%', 'No Project', $out);
	}
	
	echo $out;
}