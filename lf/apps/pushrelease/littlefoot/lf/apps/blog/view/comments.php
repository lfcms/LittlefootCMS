<?php

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
			if($item['reply'] != 0)
				$parent = '<a href="#reply_'.$item['reply'].'">Parent</a>';
		
			$x = '';
			if($item['owner'] == $uid)
				$x = '[<a href="%appurl%rm/post/'.$item['id'].'/">x</a>] ';
			
			$html .= $x.'<strong>';
			
			/*if($item['owner'] != 0)
				$html .= '<a href="%baseurl%profile/'.$item['owner'].'/">';*/
			
			$html .= $item['user'];
			
			/*if($item['owner'] != 0)
				$html .= '</a>';*/
			
			$likedisp = '';
			if($uid > 0)
				$likedisp = '%m_like'.$item['id'].'% ';
			
			$html .= '</strong> at '.date("F j, Y g:i a",strtotime($item['date'])).' '.$parent.'<a id="reply_'.$item['id'].'" href="#"></a>
			 | '.$likedisp.' +'.$item['likes'].' Promotes
			<br />'.$item['content'];
			
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
	<form action="%appurl%mkpost/<?=$thread['id'];?>/" method="post">
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
<?php endif; ?>