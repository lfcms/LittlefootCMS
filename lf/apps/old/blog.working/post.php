<?php

//if($auth->vars['user'] != 'anonymous')

switch($cmd)
{
	case 'unlike':
		if($auth['user'] == 'anonymous') break;
		preg_match('/([a-z])_like([0-9]+)/', $vars['what'], $matches);
		$sql = "
			SELECT id
			FROM io_like
			WHERE
				user_id = ".$auth['id']."
			AND
				link = '".$matches[0]."'
		";
		
		$output['data'] = $matches;
		
		$result = $database->query($sql);
		
		if(!mysql_num_rows($result))
		{
			$output['success'] = 0;
		}
		else
		{
			$sql = "DELETE FROM io_like WHERE user_id = ".$auth['id']." AND link = '".$matches[0]."'";
			$result = $database->query($sql);
			$sql = "UPDATE io_";
			switch($matches[1])
			{
				case 't':
					$sql .= "threads";
					break;
				case 'm':
					$sql .= "messages";
					break;
			}
				
			$sql .= " SET likes=likes-1 WHERE ";
			switch($matches[1])
			{
				case 't':
					$sql .= "id";
					break;
				case 'm':
					$sql .= "msg_id";
					break;
			}
			$sql .= " = ".$matches[2];
			$result = $database->query($sql);
			$output['success'] = $result;
			$output['refresh'] = true;
		}
		break;
	case 'like':
		if($auth['user'] == 'anonymous') break;
		preg_match('/([a-z])_like([0-9]+)/', $vars['what'], $matches);
		//$output['result'] = print_r($matches, true);
		
		$output['matches'] = $matches;
		
		$sql = "
			SELECT *
			FROM io_like
			WHERE
				user_id = ".$auth['id']."
			AND
				link = '".$matches[0]."'
		";
		
		$result = $database->query($sql);
		
		if(mysql_num_rows($result))
		{
			$output['success'] = 0;
		}
		else
		{
			$sql = "INSERT INTO io_like VALUES ( NULL, '".$matches[0]."', ".$auth['id'].", 'int')";
			$result = $database->query($sql);
			
			$sql = "UPDATE io_";
				
			switch($matches[1])
			{
				case 't':
					$sql .= "threads";
					break;
				case 'm':
					$sql .= "messages";
					break;
			}
				
			$sql .= " SET likes=likes+1 WHERE ";
			switch($matches[1])
			{
				case 't':
					$sql .= "id";
					break;
				case 'm':
					$sql .= "msg_id";
					break;
			}
			$sql .= " = ".$matches[2];
			$result = $database->query($sql);
			$output['success'] = $result;
			$output['refresh'] = true;
		}
		
		break;
		
	case 'rmthread':
		if($auth['user'] == 'anonymous') break;
		preg_match('/[0-9]+/', $vars['id'], $thread);
		$thread = $thread[0];
		
		if($thread != '')
		{
			$sql = "
				DELETE FROM io_threads
				WHERE
					owner_id = ".$auth['id']."
				AND
					id = ".$thread."
			";
			$result = $database->query($sql);
			if($result)
			{
				$sql = "
					DELETE FROM io_messages
					WHERE
						parent_id = ".$thread."
				";
				$result = $database->query($sql);
			}
			$output['success'] = $result;
		}
		
		$output['user_id'] = $auth['id'];
		$output['rm'] = 'thread_'.$thread;
		break;
	case 'rmpost':
		preg_match('/[0-9]+/', $vars['id'], $msg_id);
		$msg_id = $msg_id[0];
		$sql = "
			DELETE FROM io_messages
			WHERE
				sender_id = ".$auth['id']."
			AND
				msg_id = ".$msg_id."
		";
		
		$result = $database->query($sql);
		$output['success'] = $result;
		$output['user_id'] = $auth->vars['id'];
		$output['rm'] = 'msg_'.$msg_id;
		break;
	case 'mkthread':
		if($auth['user'] == 'anonymous') break;
		
		$input = htmlentities($vars['input'], ENT_QUOTES);
		preg_match("/[a-zA-Z0-9]+/",$vars['access'],$matches);
		$acl = $matches[0];
		
		if($input == '') break;
		
		$sql = "
			INSERT INTO io_threads
			VALUES
			(
				NULL, 
				NOW(),
				'".$auth['id']."',
				'".mysql_real_escape_string($input)."',
				'".$acl."',
				0,
				0
			)
		";
		
		$result = $database->query($sql);

		$output['ins_id'] = mysql_insert_id();
		$output['user_id'] = $auth['id'];
		$output['success'] = $result;
		$output['add'] = 'threads';
		$output['data'] = '
			<div id="thread_'.$output['ins_id'].'" class="jsReplace">
				'.$input.'
			</div>
		';
		$output['refresh'] = true;
		break;
	case 'mkpost':
		preg_match('/[0-9]+/', $vars['at'], $at);
		$at = $at[0];
		preg_match('/[0-9]+/', $vars['thread'], $thread);
		$thread = $thread[0];
		$input = htmlentities($vars['input'], ENT_QUOTES);
		
		$sql = "
			INSERT INTO io_messages 
			VALUES 
			(
				NULL, 	
				NOW(),
				".$thread.",
				'".$auth['id']."', 
				'desktop', 
				'".$at."',
				'".mysql_real_escape_string($input)."',
				''
			)
		";
		$result = $database->query($sql);
		
		$output['ins_id'] = mysql_insert_id();
		$output['success'] = $result;
		$output['user_id'] = $auth['id'];
		$output['add'] = 'thread_'.$thread.' ul';
		$output['data'] = '
			<li id="msg_'.$output['ins_id'].'" class="jsReplace">
				'./*$input.*/'updating...
			</li>
		';
		$output['refresh'] = true;
		break;
}

?>