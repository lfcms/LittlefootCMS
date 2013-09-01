<?php 

if(count($_POST) > 0 && $_POST['submit'] == 'Create new project')
	$this->db->query("INSERT INTO hq_projects VALUES (NULL, '".mysql_real_escape_string($_POST['project'])."', 'New Project')");

/*

/ - list projects
/project_id - project default view
/project_id/app - full screen of that app

*/

$save = $this->request->vars;
if(isset($this->request->vars[0]))
{
	
	$project = intval($this->request->vars[0]);
	
	if(isset($this->request->vars[1]))
	{
		$load = $this->request->vars[1];
		$this->request->vars = array_slice($this->request->vars, 2);
	}
	
	$proj_data = $this->db->fetch('SELECT id, title, wiki FROM hq_projects WHERE id = '.$project);
	
	echo '<h2>Project: <a href="%appurl%'.$project.'/">'.$proj_data['title'].'</a></h2>';
	
	
}

if(isset($load))
{
	$out = $this->request->apploader($load, $project);
	echo str_replace('%appurl%', '%appurl%'.$project.'/'.$load.'/', $out);
}
else if(isset($project))
{
	$wiki = $proj_data['wiki'];
	
	$cal = $this->request->apploader('calendar', $project);
	$cal = str_replace('%appurl%', '%appurl%'.$project.'/calendar/', $cal);
	
	$notes = $this->request->apploader('notes', $project);
	$notes = str_replace('%appurl%', '%appurl%'.$project.'/notes/', $notes);
	
	$events = $this->request->apploader('events', $project);
	$events = str_replace('%appurl%', '%appurl%'.$project.'/events/', $events);
	
	//echo $this->request->apploader('wiki'); 
	?>
	<div style="float:left; width: 50%">
		[<a href="%appurl%<?php echo $project; ?>/wiki/">Edit</a>]<br />
		<?php echo $wiki; ?>
	</div>
	<div style="float:right; width: 50%"><?php echo $cal; ?></div>
	<div style="clear:both;"></div>
	<div style="float:left; width: 50%"><?php echo $notes; ?></div>
	<div style="float:right; width: 50%"><?php echo $events; ?></div>
	<?php
}
else
{
	$projects = $this->db->fetchall('SELECT id, title, wiki FROM hq_projects');
	echo 'Pick a project or create a new one: 
		<form action="?" method="post">
			<input type="text" name="project" placeholder="New project title"/>
			<input type="submit" name="submit" value="Create new project" />
		</form><br />';
	foreach($projects as $project)
		echo '<a href="%appurl%'.$project['id'].'">'.$project['title'].'</a> - '.substr(strip_tags($project['wiki']), 0, 40).'<br />';
}
	
$this->request->vars = $save;

?>