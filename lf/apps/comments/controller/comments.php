<?php

class comments extends app
{
	public function main($vars)
	{	
		$posts = array();
		// c.msg_id as id, p.from, p.reply, p.body as content, p.date, u.user 
		$sql = "
			SELECT c.*, u.user
			FROM lf_comments c
			LEFT JOIN lf_users u ON c.from = u.id
			WHERE c.inst = '".$this->ini."'
		";
		
		$this->db->query($sql);
		$options = '';
		while($row = $this->db->fetch())
		{
			if($row['from'] == 0) $row['user'] = '[deleted]';
			$posts[$row['parent']][] = $row;
					
			$options .= ' 
				<option value="'.$row['id'].'">
					Reply to '.$row['user'].' - '.$row['content'].'
				</options>
			';
		}
		
		if(!function_exists('parsepost'))
		{
			function parsepost($posts, $uid, $parent = 0)
			{	
				$items = $posts[$parent];
				$html = '<ul>';
				
				if(isset($items))
				foreach($items as $item)
				{
					$html .= '<li>';
				
					$parent = '';
					if($item['parent'] != 0)
						$parent = '<a href="#reply_'.$item['parent'].'">Parent</a>';
				
					$x = '';
					if($item['parent'] == $uid)
						$x = '[<a href="%appurl%rm/post/'.$item['id'].'/">x</a>] ';
					
					$html .= $x.'<strong>';
					
					/*if($item['parent'] != 0)
						$html .= '<a href="%baseurl%profile/'.$item['parent'].'/">';*/
					
					$html .= $item['user'];
					
					/*if($item['parent'] != 0)
						$html .= '</a>';*/
					
					$html .= '</strong> at '.date("F j, Y g:i a",strtotime($item['date'])).' '.$parent.'<a id="reply_'.$item['id'].'" href="#"></a><br />
								'.nl2br($item['content']);
					
					if(isset($posts[$item['id']]))
						$html .= parsepost($posts, $uid, $item['id']);
					
					$html .= '</li>';
				}
				
				$html .= '</ul>';
				return $html;
			}
		}

		if(isset($this->get['success']))
			echo '<div class="alert alert-success"><i class="icon-trash"></i> Message erased <a class="close" data-dismiss="alert" href="#">×</a></div>';
			
		?>
		<div id="postlist">
		<?php
			if(count($posts))
			{
				$msgs = parsepost($posts, $this->request->api('getuid'));
				preg_match_all('/%(m_like[0-9]+)%/', $msgs, $matchs);
				foreach($matchs[1] as $add)
				{
					$like[] = $add;
				}
				echo $msgs;
			}	
			else
				echo '<ul><li>No comments found</li></ul>';
		?>
		</div>
		<?php if($this->request->api('getuid') > 0): ?>
		<style type="text/css">
			.add_post select { margin-bottom: 10px; padding: 5px; width: 95%; font-size:20px; }
			.add_post textarea { margin-bottom: 10px; padding: 5px; width: 95%; font-family: 'trebuchet ms',arial,sans-serif; }
			.add_post input { padding: 5px; }
		</style>
		<div class="add_post">
			<form action="%appurl%mkpost/" method="post">
					<input type="hidden" name="inst" value="<?=$this->ini;?>" />
					<br />
					<select name="reply">
						<option value="0">Reply to thread</option>
						<?=$options;?>
					</select>
					<br />
				<textarea class="span12" name="msg" id="" rows="6"></textarea>
				
				<div class="form-actions">
					<input type="submit" class="submit" value="Post">
				</div>
				
			</form>
		</div>
		<?php endif;
	}
	
	public function mkpost($vars)
	{
		// Authenticated users only
		if($this->request->api('getuid') == 0) return 'Unauthorized';
		
		$sql = "INSERT INTO lf_comments (`id`, `parent`, `inst`, `content`, `from`, `date`)
			VALUES (
				NULL, 
				".intval($_POST['reply']).",
				'".$_POST['inst']."',
				'".mysql_real_escape_string(htmlentities($_POST['msg'], ENT_QUOTES))."', 
				".$this->request->api('getuid').", 
				NOW()
			)";
			
		$this->db->query($sql);
		
		redirect302();
	}
}

?>
