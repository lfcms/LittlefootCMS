<?php
function since($timestamp)
{
	$timestamp = time() - $timestamp;
	$ret = '';
	
	if($timestamp > 86400*30)
		$ret .= (int)($timestamp / (86400*30)) . " months";
	else if($timestamp > 86400)
		$ret .= (int)($timestamp / 86400) . " days";
	else if($timestamp > 3600)
		$ret .= (int)($timestamp / 3600) . " hours";
	else if($timestamp > 60)
		$ret .= (int)($timestamp / 60) . " minutes";
	else
		$ret .= $timestamp . " seconds";
	
	$ret .= " ago";
	
	return $ret;
}
?>
<h2><a href="%appurl%">Blog</a> / View Post</h2>
<?php if($this->request->api('me') != 'anonymous' && false): ?>
<form action="%appurl%mkthread/" method="post" class="add_thread">
	<textarea name="input"></textarea>
	<input type="hidden" name="access" value="public" />
	<input type="submit" class="submit" value="Create Thread" />
</form>
<?php endif; if(!count($thread)): ?>
<p>No threads to show</p>
<?php else: ?>
<?php $like = array(); /* loop through blog posts */ ?>
<div id="thread_<?php echo $id; ?>" class="thread">
	<div class="t_head">
		<?php if($thread['owner_id'] == $this->request->api('getuid')): /* show delete button */ ?>
		<a href="%appurl%rmthread/<?php echo $thread['id']; ?>/" method="post" class="removethread hrefapi">X</a>
		<?php endif; 
			$like_disp = ''; // Display 'like' button if logged in
			if($this->request->api('me') != 'anonymous')
			{
				$like_disp = '%t_like'.$thread['id'].'%';
				$like[] = 't_like'.$thread['id'];
			}
		?>
		<h4><?php echo $thread['title'] ?></h4>
		<p><?=$thread['content'];?></p>
		<br />
		<span class="date">
			<?=' '.$like_disp; ?> +<?=$thread['likes'];?> Promotes | Posted by <?php echo $thread['user'] ?> <?=since(strtotime($thread['date']));?>
		</span>
		<ul class="msg">
		<?php
		
		if(count($comments))
		{	
			$like_disp = ''; // Display 'like' button if logged in
			if($this->request->api('me') != 'anonymous')
				$like_disp = '%m_like%msg_id%%';
			
			$skin_reply = '
				<li id="msg_%msg_id%">
						%x%
					<span class="msg_sender">%sender_id%</span>
					
					<p class="msg_body">%body%</p>
					<span class="date">%since% via %device% '.$like_disp.' +%likes% Promotes</span>
				</li>
			';
			
			// Print replies
			$options = '';
			foreach($comments as $comment)
			{
				if($this->request->api('me') != 'anonymous')
					$like[] = 'm_like'.$comment['msg_id'];
					
				if($comment['user'] == NULL) $comment['user'] = 'Anonymous';
				if(strlen($comment['body']) > 50)
					$reply = substr_replace($comment['body']."", '...', 70);
				else
					$reply = $comment['body'];
				$timestamp = strtotime($comment['date']);
				
				$replace = array(
					"%form_action%" => '%appurl%rmpost/',
					"%msg_id%" 		=> $comment['msg_id'],
					//"%sender_id%" => $comment['sender_id'],
					"%likes%" 		=> $comment['likes'],
					"%body%" 		=> $comment['body'],
					"%since%" 		=> since($timestamp),
					"%device%" 		=> $comment['device'],
					'%x%' 			=> ''
				);
				
				if($thread['owner_id'] == $this->request->api('getuid') || $comment['sender_id'] == $this->request->api('getuid'))
					$replace['%x%'] = '<a href="%appurl%rmpost/'.$comment['msg_id'].'/" method="post" class="removepost hrefapi">X</a>';
					
				$replace['%sender_id%'] = $comment['user'];
				
				$reg_http = '/http:\/\/[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.?=_\/]+/';
				preg_match_all( $reg_http, $comment['body'], $matches);
				for($i = 0; $i < count($matches[0]); $i++)
					$comment['body'] = str_replace($matches[0][$i], '<a href="'.$matches[0][$i].'">'.$matches[0][$i].'</a>', $comment['body']);
				
				echo str_replace(array_keys($replace), array_values($replace), $skin_reply);
				
				$options .= '
					<option value="'.$comment['msg_id'].'">
						Reply to '.$comment['user'].' - '.$reply.'
					</options>
				';
			}
		}
		else
		{
			?>
			<li class="msg_0">
				<span class="msg_body">No Comments</span>
			</li>
			<?php
		}
		?>
		</ul>
		<span class="msg_date"><?php echo date("F j, Y, g:i a"); ?></span>
		<form action="%appurl%mkpost/" method="post" class="add_post">
			<select name="at" class="at">
				<option>Reply to thread</option>
				<?=$options;?>
			</select>
			<input type="text" name="input" />
			<input type="submit" class="submit" name="submit" value="Send" />
			<input type="hidden" name="thread" value="<?=$thread['id'];?>" />
		</form>
	</div>
</div>
<?php endif; ?>