<h2><a href="%appurl%">Bugtrack</a> / Latest Tickets</h2>
<div id="projects">
	<h3>Projects</h3>
	<form action="%appurl%addproject/" method="post"><input type="text" name="project" placeholder="New project" /></form>
	<?php if($projects)
		foreach($projects as $project)
		{
			$project = $project['project'];
			echo '[<a href="%appurl%rmproject/'.urlencode($project).'/">x</a>] <a href="%appurl%'.urlencode($project).'/">'.$project.'</a><br />';
		}
	?>
</div>

<div id="note">
	<h3>Open Tickets</h3>
	<ul>
	<?php

	$project = '';
	$cat = '';
	foreach($posts as $post)
	{
		if($project != $post['project'] | $cat != $post['category'])
		{
			$project = $post['project'];
			$cat = $post['category'];
			echo '</ul><h4><a href="%appurl%'.urlencode($post['project']).'">'.$post['project'].'</a> / <a href="%appurl%'.urlencode($post['project']).'/cat/'.urlencode($post['category']).'">'.$post['category'].'</a></h4><ul>';
		}
		
		echo '<li><a href="%appurl%'.urlencode($post['project']).'/view/'.$post['id'].'/">'.$post['title'].'</a></li>';
	}
	?>
	</ul>
</div>