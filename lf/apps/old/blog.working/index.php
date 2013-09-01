<?php

if(!function_exists('since'))
{
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
}

$reg_http = '/http:\/\/[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.?=_\/]+/';
$like = '';
$options = '';
if($auth->vars['user'] != 'anonymous' || true)
{
	//thread maker
	$sql = "
		SELECT
			t.id,  t.owner_id, t.content, t.date as tdate, t.likes as tlikes, t.acl,
			c.msg_id, c.date, c.device, c.link as reply_to, c.body, c.sender_id, c.likes
		FROM io_threads t
			LEFT JOIN io_messages c ON t.id = c.parent_id
			LEFT JOIN io_links l ON t.owner_id = l.id_two AND t.acl = l.access
		WHERE
			t.acl = 'public' OR t.owner_id = ".$auth->vars['id']."
	";
	
	$protocol = "http://";
	
	$url = array(
		$conf['domain'],
		$conf['subdir'],
		'api.php?app=blog&cmd=mkthread'
	);
	if($auth->vars['user'] != 'anonymous')
		$output .= '
			<form action="'.$protocol.implode('/',$url).'" method="post" class="add_thread">
				<textarea name="input"></textarea>
				<input type="hidden" name="access" value="public" />
				<input type="submit" class="submit" value="Create Thread" />
			</form>
		';
		
	$sql .= " ORDER BY c.msg_id";
	$result = $database->query($sql);
	
	
	
	if(!mysql_num_rows($result))
		$output .= "No Threads";
	else
	{
		$t_id = -1;
		$switch = true;
		while($row = mysql_fetch_assoc($result))
		{
			if($row['id'] != $t_id) $switch = true;
			
			$t_id = $row['id'];
			if($switch)
			{
				$switch = false;
					
				preg_match_all($reg_http, $row['content'], $matches);				
				for($i = 0; $i < count($matches[0]); $i++)
					$row['content'] = str_replace($matches[0][$i], '<a href="'.$matches[0][$i].'">'.$matches[0][$i].'</a>',$row['content']);
				
				$threads[$t_id]['head'] = array( 
					'owner_id' => $row['owner_id'],
					'content' => nl2br($row['content']),
					'user' => $row['owner_id'],
					'likes' => $row['tlikes'],
					'acl' => $row['acl'],
					'date' => since(strtotime($row['tdate']))//date("F j, Y, g:i a",strtotime($row['tdate']))
				);
			}
			 
			$row['body'] = nl2br($row['body']);
			 
			unset($row['id'], $row['owner_id'], $row['content'], $row['tdate'], $row['tlikes']);
			if($row['msg_id'] != NULL)
				$threads[$t_id]['replies'][$row['msg_id']] = $row;
		}
		
		krsort($threads);
		$output .= '
			<div id="threads">
		';
		
		foreach($threads as $thread_id => $thread)
		{
			$output .= '
				<div id="thread_'.$thread_id.'" class="thread">
					
					<div class="t_head">';
			if($thread['head']['owner_id'] == $auth->vars['id'])
			{
				$protocol = 'http://';
				$url = array(
					$conf['domain'],
					$conf['subdir'],
					'api.php?app=blog&cmd=rmthread&id='.$thread_id
				);
				$output .= '
						<a href="'.$protocol.implode('/',$url).'" method="post" class="removethread hrefapi">X</a>';
			}
			
			$like_disp = '';
			if($auth->vars['user'] != 'anonymous')
				$like_disp = '%t_like'.$thread_id.'%';
				
			$output .= '
						<h4><a href="http://'.$conf['domain'].'/'.$conf['subdir'].'/wall/friends/'.$thread['head']['owner_id'].'">%disp'.$thread['head']['user'].'%</a> to <a href="http://'.$conf['domain'].'/'.$conf['subdir'].'/wall/'.$thread['head']['acl'].'">'.$thread['head']['acl'].'</a>:</h4>
						<p>'.$thread['head']['content'].'</p>
						<br /><span class="date">'.$thread['head']['date'].' | '.$like_disp.' +'.$thread['head']['likes'].' Promotes</span>
					
				';
			$like['t_like'.$thread_id] = $thread_id;
			$output .= '<ul class="msg">';
			$users[] = $thread['head']['user'];
			if(isset($thread['replies']))
			{
				$protocol = 'http://';
				$url = array(
					$conf['domain'],
					$conf['subdir'],
					'api.php?app=blog&cmd=rmpost&id='
				);
				
				$like_disp = '';
				if($auth->vars['user'] != 'anonymous')
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
				foreach($thread['replies'] as $msg_id => $msg)
				{	
					if(strlen($msg['body']) > 50)
						$reply = substr_replace($msg['body']."", '...', 70);
					else
						$reply = $msg['body'];
					$timestamp = strtotime($msg['date']);
					
					$replace = array(
						"%form_action%" => 'http://'.$conf['domain'].'/'.$conf['subdir'].'/api.php?app=blog&cmd=rmpost',
						"%msg_id%" => $msg_id,
						//"%sender_id%" => $msg['sender_id'],
						"%likes%" => $msg['likes'],
						"%body%" => $msg['body'],
						"%since%" => since($timestamp),
						"%device%" => $msg['device'],
						'%x%' => ''
					);
					
					if($thread['head']['owner_id'] == $auth->vars['id'] || $auth->vars['id'] == $msg['sender_id'])
						$replace['%x%'] = '<a href="'.$protocol.implode('/',$url).$msg_id.'" method="post" class="removepost hrefapi">X</a>';
						
					$replace['%sender_id%'] = '<a href="/dev/aios/wall';
					if($msg['sender_id'] != $auth->getID())
						$replace['%sender_id%'] .= '/friends/'.$msg['sender_id'];
					$replace['%sender_id%'] .= '">%disp'.$msg['sender_id'].'%</a>';
					
					$temp_skin = str_replace(array_keys($replace), array_values($replace), $skin_reply);
					
					$like['m_like'.$msg_id] = $msg_id;
					
					preg_match_all( $reg_http, $msg['body'], $matches);
					for($i = 0; $i < count($matches[0]); $i++)
						$msg['body'] = str_replace($matches[0][$i], '<a href="'.$matches[0][$i].'">'.$matches[0][$i].'</a>', $msg['body']);
					
					$users[] = $msg['sender_id'];
					
					$output .= $temp_skin; 
					
					$options .= '
						<option value="'.$msg_id.'">
							Reply to %disp'.$msg['sender_id'].'% - '.$reply.'
						</options>
					';
				}
			}
			else
			{
				$output .= '
					<li class="msg_0">
						<span class="msg_body">No Comments</span>
					</li>
				';
			}
			
			$output .=
					'</ul>'.
					'<span class="msg_date">'.date("F j, Y, g:i a").'</span>'.
					'<form action="http://'.$conf['domain'].'/'.$conf['subdir'].'/api.php?app=blog&cmd=mkpost" method="post" class="add_post">
						<select name="at" class="at">
							<option>Reply to thread</option>
							'.$options.'
						</select>
						<input type="text" name="input" />
						<input type="submit" class="submit" name="submit" value="Send" />
						<input type="hidden" name="thread" value="'.$thread_id.'" />
					</form>'.
				'</div></div>'
			;
			$options = '';
		}
		$unique = array_keys(array_flip($users)); // http://www.php.net/manual/en/function.array-unique.php#70786
		
		$sql = "SELECT id, display_name FROM users WHERE id = ".$unique[0];
		unset($unique[0]);
		
		foreach($unique as $replace)
			$sql .= " OR id = ".$replace;
		
		$result = $database->query($sql);
		$replace = ''; $with = '';
		
		if(!mysql_num_rows($result))
			$output .= "No Output";
		else
		{
			$row['id'] = '0';
			$row['display_name'] = 'Anonymous';
			do
			{
				$replace[] = "%disp".$row['id']."%";
				$with[] = $row['display_name'];
			} while($row = mysql_fetch_assoc($result));
			$output = str_replace($replace, $with, $output);
		}
		
		//Like replace
		if(count($like) > 0 && $auth->vars['user'] != 'anonymous')
		{
			$sql = "SELECT * FROM io_like WHERE user_id = ".$auth->vars['id']." AND scope = 'int' AND (";
			
			foreach($like as $replace => $status)
				$sql .= " link = '".$replace."' OR";
			
			$sql = substr($sql, 0, strlen($sql)-3);
			$sql .= " )";
			
			$result = $database->query($sql);
			
			$protocol = 'http://';
			$url = array(
				$conf['domain'],
				$conf['subdir'],
				'api.php?app=blog&cmd=unlike&what='
			);
			
			$replace = ''; $with = '';
			if(!mysql_num_rows($result))
				$output['success'] = false;
			else
			{
				while($row = mysql_fetch_assoc($result))
				{
					$replace[] = '%'.$row['link'].'%';
					
					$with[] = '<a class="unlike hrefapi" href="'.$protocol.implode('/',$url).$row['link'].'">Unlike</a>';
					unset($like[$row['link']]);
				}
				$output = str_replace($replace, $with, $output);
			}
			
			$protocol = 'http://';
			$url = array(
				$conf['domain'],
				$conf['subdir'],
				'api.php?app=blog&cmd=like&what='
			);
				
			$replace = ''; $with = '';	
			foreach($like as $var => $val)
			{
				$replace[] = '%'.$var.'%'; 
				$with[] = '<a class="like hrefapi" href="'.$protocol.implode('/',$url).$var.'">Like</a>';
			}
			
			
			$output = str_replace($replace, $with, $output);
		}
	}
}
else
{
	$output = "Please log in to view this page.<br>%login%";
}


?>