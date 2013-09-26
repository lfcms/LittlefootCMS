<div id="categories">
	<h3>Categories</h2>
	<form action="%appurl%addcategory/" method="post"><input type="text" name="category" placeholder="New category" /></form>
	<ul id="category_list">
	<?php if($categories)
		foreach($categories as $cat)
		{
			$active = '';
			$edit = '';
			if($cat['id'] == $category)
			{
				$active = ' class="active"';
				
				$edit = '(<a href="%appurl%editcat/'.$cat['id'].'/">edit</a>)';
				
			}
			echo '<li'.$active.'><a href="%appurl%cat/'.$cat['id'].'/">'.$cat['category'].'</a>'.$edit.'</li>';
		}
	?>
	</ul>
</div>

<?php

$caturl = '';
if($category != '') $caturl = 'cat/'.urlencode($category).'/';

$status_options = array('open', 'closed', 'backburner');
$nav = array();
foreach($status_options as $option)
{
	if($option == $status)
	{
		
		$nav[] = '<a class="'.$option.' active" href="%appurl%'.$caturl.$option.'/">'.ucfirst($option).' ('.$ticket_count.')</a>';
	}
	else $nav[] = '<a class="'.$option.'" href="%appurl%'.$caturl.$option.'/">'.ucfirst($option).'</a>';

	
	
	
	
	
}

?>

<script type="text/javascript">
	setInterval(
		function(){
			$.get('<?php echo $_SERVER['REQUEST_URI']; ?>', function(data) {
				$("#note").html($(data).find("#note").html());
			})
		},
		20000
	); 
</script>

<div id="note" class="ticket_queue">
	<div id="ticket_nav">
		<a id="new_ticket_button" href="%appurl%newarticle/<?php if($category != '') echo urlencode($category); ?>">Create new ticket</a> 
		<?php echo implode(' ', $nav); ?></div>
	<table>
	<tr>
		<th>#</th>
		<th>Title</th>
		<th>Assigned</th>
		<th><?php echo $this->ini == '' ? 'Project / Category' : 'Category'; ?></th>
		<th>Replies</th>
		<th>Last Reply</th>
	</tr>
	<?php

	$cat = '';
	foreach($posts as $date => $post)
	{
		$class = '';
		$classes = array();
		if($post['flagged'] != 'none')
			$classes[] = $post['flagged'];
		if($post['assigned'] == $this->lf->api('getuid'))
			$classes[] = 'assigned';
		
		if($classes != array())
			$class = ' class="'.implode(' ', $classes).'"';
		
		if($post['assigned'] != 0)
			$assigned = '%user:'.$post['assigned'].'%';
		else
			$assigned = '';
		
		$since = date('U') - date('U', strtotime($date)); // # of seconds that went by
		
		$time = array();
		if($since > 24*60*60)
		{
			$time['d'] = (int) ($since / (24*60*60)); // # of days
			$since = $since % (24*60*60); 
		}	
		
		if($since > 60*60)
		{
			$time['h'] = (int) ($since / (60*60)); // # of hours
			$since = $since % (60*60);
		}	
		
		if($since > 60)
		{
			$time['m'] = (int) ($since / (60)); // # of minutes
			$since = $since % (60);
		}	
		
		if($since > 0 && !isset($time['d']))
		{
			$time['s'] = (int) $since;
		}
		
		$since = '';
		foreach($time as $unit => $val)
		{
			$since .= $val.$unit;
		}
		
		$project_name = '';
		if($this->ini == '')
		{
			$ticket_url = $this->lf->appurl.$post['project'].'/tickets/view/'.$post['id'].'/';
			$project_name = '%project:'.$post['project'].'% / ';
		}
		else
			$ticket_url = '%appurl%view/'.$post['id'].'/';
		
		echo '<tr'.$class.'>
			<td>'.$post['id'].'</td>
			<td><a href="'.$ticket_url.'">'.htmlentities($post['title']).'</a></td>
			<td>'.$assigned.'</td>
			<td>'.$project_name.'<a href="%appurl%cat/'.urlencode($post['category']).'">%category:'.$post['category'].'%</a></td>
			<td>'.$post['replies'].'</td>
			<td>'.$post['last'].' - '.$since.'</td>
		</tr>';
	}
	?>
	</table>
</div>
