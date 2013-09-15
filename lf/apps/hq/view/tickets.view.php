<?php 
echo '<div id="hq_breadcrumb">
	<a href="%appurl%cat/'.$post['category'].'/">%category:'.$post['category'].'%</a> > 
	<a href="%appurl%view/'.$post['id'].'/">'.$post['title'].' ('.$post['id'].')</a>
</div>';
?>

<div id="hq_view_ticket">

	<?php if(isset($vars[2]) && $vars[2] == 'edit') { 
	
	// Ticket status
	$modes = array('open', 'closed', 'backburner');
	$options = '';
	foreach($modes as $status)
	{
		$selected = '';
		if($status == $post['status'])
			$selected = ' selected="selected"';
		
		$options .= '<option'.$selected.' value="'.$status.'">'.$status.'</option>';
	}
	
	// Ticket flag
	$flags = array('none', 'urgent');
	$flag_options = '';
	foreach($flags as $flag)
	{
		$selected = '';
		if($flag == $post['flagged'])
			$selected = ' selected="selected"';
		
		$flag_options .= '<option'.$selected.' value="'.$flag.'">'.ucfirst($flag).'</option>';
	}
	
	// User assigned
	$users = $this->db->fetchall('SELECT id, display_name FROM lf_users');
	$user_options = '<option value="0">None</option>';
	foreach($users as $user)
	{
		$selected = '';
		if($user['id'] == $post['assigned'])
			$selected = ' selected="selected"';
		
		$user_options .= '<option'.$selected.' value="'.$user['id'].'">'.$user['display_name'].'</option>';
	}
	
	// Category options
	$cat_options = '<option value="0">Uncategorized</option>';
	foreach($categories as $category)
	{
		$selected = $post['category'] == $category['id'] ? $selected = ' selected="selected"' : '';
		$cat_options .= '<option'.$selected.' value="'.$category['id'].'">'.$category['category'].'</option>';
	}
	
	// project
	$projects = $this->db->fetchall('SELECT id, title FROM hq_projects');
	$project_options = '';
	foreach($projects as $project)
	{
		$selected = '';
		if($project['id'] == $post['project'])
			$selected = ' selected="selected"';
		
		$project_options .= '<option'.$selected.' value="'.$project['id'].'">'.$project['title'].'</option>';
	}
	
	?>
	
	<form id="update_form" action="%appurl%view/<?php echo $post['id'].'/'; ?>" method="post">
		<input class="ticket_update_button" type="submit" value="Update"  /> 
		Status: <select name="status" id=""><?php echo $options; ?></select>
		Assigned: <select name="assigned" id=""><?php echo $user_options; ?></select> 
		Flag: <select name="flagged" id=""><?php echo $flag_options; ?></select> 
		
		
		<br />
		Project: <select name="project" id=""><?php echo $project_options; ?></select>
		Category <select name="category" id=""><?php echo $cat_options; ?></select> or <input type="text" placeholder="New Category" name="newcat" />
		<?php echo '[<a onclick="return confirm(\'Do you really want to delete this note?\');"  href="%appurl%rm/'.$post['id'].'/">delete</a>] '; ?>
		<br />
		
		<input class="title" type="text" name="title" value="<?php echo $post['title']; ?>" />
		<textarea name="content" class="edit"><?php echo $post['content']; ?></textarea>
	</form>
	
	<?php } 
	
	
	
	
	else { ?>
	
	<!--
	<script type="text/javascript">
		$(document).ready(function(){
			$("#enable_edit").click(function(){
				$.get(
					'<?php echo $_SERVER['REQUEST_URI'].'edit'; ?>',
					function(data) 
					{
						$("#enable_edit").parent().html(
							$(data).find("#update_form").clone()
						);
					}
				);
				return false;
			});
		});
	</script> -->
	
	<p>
		[<a id="enable_edit" href="%appurl%view/<?=$post['id'];?>/edit/">edit</a>] .
		Status: <?=$post['status'];?> 
		<?php if($post['assigned'] != 0) echo ', Assigned: %user:'.$post['assigned'].'%'; ?>
		<?php if($post['flagged'] != '') echo ', Flag: '.$post['flagged']; ?> . 
		[<a href="<?=$this->lf->appurl.$this->ini.'/calendar/newevent/'.$post['id'];?>/">New Due Date</a>]
	</p>
	<p class="content">
		<span class="reply_header">
			<strong>%user:<?php echo $post['owner_id']; ?>%</strong> at <?php echo date("F j, Y g:i a", strtotime($post['date'])); ?> 
			{like:ticket/<?=$post['id'];?>} 
			{subscribe:ticket/<?=$post['id'];?>}
		</span>
		<?php 
		
		$post['content'] = htmlentities($post['content'], ENT_QUOTES); 
		
		
		if(preg_match_all('/https?:\/\/[^"\s\]]+/', $post['content'], $match))
		{
			for($i = 0; $i < count($match[0]); $i++)
			{
				$link = $match[0][$i];
				
				if(preg_match('/(jpe?g|gif|png)$/', $link, $ignore))			
					$post['content'] = str_replace(
						$link, '<a target="_blank" href="'.$link.'"><img src="'.$link.'" alt="" /></a>', 
						$post['content']
					);
				else				
					$post['content'] = str_replace(
						$link, '<a target="_blank" href="'.$link.'">'.$link.'</a>', 
						$post['content']
					);
			}
		}
		
		echo nl2br($post['content']);
		
		?>
	</p>
	
	<?php } ?>
	
	
	<?php
		
		
/*
	$cat = '';
	foreach($posts as $post)
	{
		if($cat != $post['category'])
		{
			$cat = $post['category'];
			echo '<h4>'.$cat.'</h4>';
		}
		
		echo '<li>
			[<a onclick="return confirm(\'Do you really want to delete this note?\');"  href="%appurl%'.urlencode($post['project']).'rmnote/'.$post['id'].'/">x</a>] 
			<a href="%appurl%'.urlencode($post['project']).'/view/'.$post['id'].'/">'.$post['title'].'</a>
		</li>';
	}*/
	?>
	
	
	
	<?php if(!isset($vars[2])) $this->comment();/*echo $comments;*/ ?>
</div>