<?php

class blog
{
	private $request;
	private $html;
	private $pwd;
	private $dbconn;
	
	public function __construct($request, $dbconn, $ini = '')
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->pwd = $request->absbase.'/apps';
		$this->ini = $ini;
	}
	
	//default
	public function main($vars)
	{
		if($this->ini == 'grid')
		{
			$sql = "SELECT * FROM io_threads";
			$this->db->query($sql);
			$blog = $this->db->fetchall();

			?>
			<h2>Blog Posts</h2>
			<style type="text/css">
				.overlay:hover p { display: block !important; }
				.overlay:hover div span { display: none !important; }
			</style>
			<?php

			// Print blog posts
			foreach($blog as $post)
			{
				preg_match('/"([^"]+.jpg)"/', $post['content'], $match);
				
				$bg = 'http://dev4.bioshazard.com/littlefoot/lf/apps/feed/keep/1692321469_635c0fc79e_b-288x221.jpg'; //default
				if(isset($match[1]))
					$bg = $match[1];

				?>
				<div style="width: 200px; background: #000; overflow: hidden; margin-bottom: 10px; float: left; margin-right: 10px;" class="overlay" >
					<a href="%appurl%view/<?=$post['id'];?>/">
						<p style="z-index: 100; font-weight: bold; position: absolute; float: left; color: white; width: 130px; font-size: 15px; display: none; background: url(%relbase%lf/media/transparent.png); width: 200px; height: 200px;"><span style="display: block; padding: 20px;"><?php echo date('M d, Y',strtotime($post['date'])); ?></span></p>
					</a>
					<div style="height: 200px">
						<span style="background: url(%relbase%lf/media/transparent.png); display: block; position: absolute; z-index: 99; float: left; color: white; padding: 20px; width: 160px; font-size: 16px;">
							<?=$post['title'];?>
						</span>
						<img height="200px" src="<?=$bg;?>" alt="" />
					</div>
				</div>
				<?php
			}
			?><div style="clear: both"></div><?php
		
		}
		else
		{
			// print blog articles
			$sql = "
				SELECT t.id, t.title, t.owner_id, t.content, t.date, t.likes, u.display_name as user
				FROM io_threads t
					LEFT JOIN lf_users u ON t.owner_id = u.id
			";
			$this->db->query($sql);
			while($row = $this->db->fetch())
				$blog[$row['id']] = $row;
			
			ob_start();
			include 'view/main.php';
			$out = ob_get_clean();
			
			// put likes in key place
			$like = array_flip($like);
			
			//Like replace
			include 'model/like.php';
			
			echo $out;
		}
	}
	
	public function view($vars)
	{
		//Thread
		$sql = "
			SELECT t.id, t.title, t.owner_id, t.content, t.date, t.likes, u.display_name as user
			FROM io_threads t
			LEFT JOIN lf_users u ON t.owner_id = u.id
			WHERE t.id = ".intval($vars[1])."
		";
		
		$this->db->query($sql);
		$thread = $this->db->fetch();
		
		// get comments
		$options = '';
		$posts = array();
		$sql = "
			SELECT p.likes, p.msg_id as id, p.sender_id as owner, p.reply, p.body as content, p.date, u.user 
			FROM io_messages p 
			LEFT JOIN lf_users u ON p.sender_id = u.id 
			WHERE p.parent_id = '".intval($vars[1])."'
		";
		
		$this->db->query($sql);
		while($row = $this->db->fetch())
		{
			if($row['owner'] == 0) $row['user'] = '[deleted]';
			$posts[$row['reply']][] = $row;
					
			$options .= '
				<option value="'.$row['id'].'">
					Reply to '.$row['user'].' - '.$row['content'].'
				</options>
			';
		}
		
		ob_start();
		include('view/comments.php');
		$comments = ob_get_clean();
		
		ob_start();
		include 'view/thread2.php';
		$out = ob_get_clean();
		
		// put likes in key place
		$like = array_flip($like);
		
		//Like replace
		include 'model/like.php';
		
		echo $out;
	}
	
	public function mkpost($vars)
	{
		// Authenticated users only
		if($this->request->api('me') == 'anonymous') exit();
		
		
		$sql = "
			INSERT INTO io_messages (`msg_id`, `date`,`parent_id`,`sender_id`,`device`,`link`,`body`,`likes`,`reply`)
			VALUES (
				NULL, 
				NOW(), 
				".intval($vars[1]).",
				".$this->request->api('getuid').", 
				'desktop', 
				0, 
				'".mysql_real_escape_string(htmlentities($_POST['msg'], ENT_QUOTES))."', 
				0, 
				".intval($_POST['reply'])."
			)
		";
		
		$this->db->query($sql);
		
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function like($vars)
	{
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		
		if($this->request->api('me') == 'anonymous') exit();
		
		preg_match('/([mt])_like([0-9]+)/', $vars[1], $matches);
		
		$sql = "
			SELECT * FROM io_like
			WHERE user_id = ".$this->request->api('getuid')."
			AND	link = '".$matches[0]."'
		";
		$result = $this->db->query($sql);
		
		if(mysql_num_rows($result))
		{
			$output['success'] = 0;
		}
		else
		{
			$sql = "INSERT INTO io_like VALUES ( NULL, '".$matches[0]."', ".$this->request->api('getuid').", 'int')";
			$result = $this->db->query($sql);
			
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
			$result = $this->db->query($sql);
			$output['success'] = $result;
			$output['refresh'] = true;
		}
		
		exit();
	}
	
	public function unlike($vars)
	{
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		
		if($this->request->api('me') == 'anonymous') exit();
		
		preg_match('/([mt])_like([0-9]+)/', $vars[1], $matches);
		
		$sql = "
			SELECT * FROM io_like
			WHERE user_id = ".$this->request->api('getuid')."
			AND	link = '".$matches[0]."'
		";
		$result = $this->db->query($sql);
		
		if(!mysql_num_rows($result))
		{
			$output['success'] = 0;
		}
		else
		{
			$sql = "DELETE FROM io_like WHERE user_id = ".$this->request->api('getuid')." AND link = '".$matches[0]."'";
			$result = $this->db->query($sql);
			
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
			$result = $this->db->query($sql);
			$output['success'] = $result;
			$output['refresh'] = true;
		}
		
		exit();
	}
}

?>
