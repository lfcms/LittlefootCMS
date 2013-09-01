<?php

class forum
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
	
	public function board($vars)
	{
		if(!isset($vars[1])) return 'Invalid Request';
		
		$sql = 'SELECT * FROM lf_forum_boards WHERE id = '.intval($vars[1]).' LIMIT 1';
		$this->db->query($sql);
		$board = $this->db->fetch();
		
		$sql = '
			SELECT t.*, u.user, p.content
			FROM lf_forum_threads t 
			LEFT JOIN lf_forum_posts p
				ON t.lastpost = p.id
			LEFT JOIN lf_users u 
				ON t.owner = u.id 
			WHERE t.board = '.intval($vars[1]).'';
		$this->db->query($sql);
		$threads = $this->db->fetchall();
		
		include('view/board.view.php');
	}
	
	public function thread($vars)
	{
		if(!isset($vars[1])) return 'Invalid Request';
		
		$sql = '
			SELECT t.*, b.title as board_title
			FROM lf_forum_threads t 
			LEFT JOIN lf_forum_boards b 
				ON t.board = b.id
			WHERE t.id = '.intval($vars[1]).' LIMIT 1';
		$this->db->query($sql);
		$thread = $this->db->fetch();
		
		$options = '';
		$sql = 'SELECT p.*, u.user FROM lf_forum_posts p LEFT JOIN lf_users u ON p.owner = u.id WHERE p.thread = '.intval($vars[1]);
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
		
		include('view/thread.view.php');
	}
	
	//default
	public function view($vars)
	{
		$boards = array();
		$this->db->query('SELECT * FROM lf_forum_boards');
		while($row = $this->db->fetch())
		{
			$boards[] = $row;
			
		}
		include 'view/view.all.php';
	}
	
	public function add($vars)
	{
		switch($vars[1])
		{
			case 'post':
				$sql = "
					INSERT INTO lf_forum_posts
					(`id`, `owner`, `thread`, `content`, `reply`, `date`)
					VALUES
					(NULL, '".$this->request->api('getuid')."', '".intval($vars[2])."', '".mysql_real_escape_string($this->request->post['msg'])."', '".$this->request->post['reply']."', NOW())
				";
				$this->db->query($sql);
				
				$sql = "UPDATE lf_forum_threads SET lastpost = ".mysql_insert_id()." WHERE id = '".intval($vars[2])."'";
				$this->db->query($sql);
				break;
				
			case 'thread':
				$sql = "
					INSERT INTO lf_forum_threads
					(`id`, `owner`, `board`, `subject`)
					VALUES
					(NULL, '".$this->request->api('getuid')."', '".intval($vars[2])."', '".mysql_real_escape_string($this->request->post['subject'])."')
				";
				$this->db->query($sql);
				
				$thread_id = mysql_insert_id();
				
				$sql = "
					INSERT INTO lf_forum_posts
					(`id`, `owner`, `thread`, `content`, `reply`, `date`)
					VALUES
					(NULL, '".$this->request->api('getuid')."', '".$thread_id."', '".mysql_real_escape_string($this->request->post['msg'])."', '0', NOW())
				";
				$this->db->query($sql);
				
				$sql = "UPDATE lf_forum_threads SET lastpost = ".mysql_insert_id()." WHERE id = ".$thread_id;
				$this->db->query($sql);
				
				break;
		}
	}
	
	public function rm($vars)
	{
		print_r($vars);
		
		switch($vars[1])
		{
			case 'post':
				
				/*$sql = '
					DELETE FROM lf_forum_posts
					WHERE
						id = '.intval($vars[2]).'
					AND
						owner = '.$this->request->api('getuid').'
				';*/
				
				$sql = "
					UPDATE lf_forum_posts
					SET
						owner = 0,
						content = '[deleted]'
					WHERE
						id = ".intval($vars[2])."
					AND
						owner = ".$this->request->api('getuid')
				;
				
				$this->db->query($sql);
				header('Location: '.$_SERVER['HTTP_REFERER'].'?success=1');
				break;
			case 'board':
				
				break;
			case 'thread':
				
				break;
				
			default:
				break;
		}
	}
}

?>