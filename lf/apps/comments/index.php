<?php 

/*

Add this to view/id:
	$this->comment_id = 'myapp/'.$this->inst.'/'.intval($vars[1]);
	$comments = $this->comment(array());
	include 'view/myapp.view.php';
	
Add this to the main class:
	
	public function comment($vars)
	{
		$vars = array_slice($vars, 1); // to get vars from subdir mount
		
		if(!isset($this->comment_id)) $this->comment_id = $_POST['inst'];
		
		$cwd = getcwd();
		chdir('../comments');
		$comments = $this->request->apploader('comments', $this->comment_id, $vars);
		$comments = str_replace('%appurl%', '%appurl%comment/', $comments);
		chdir($cwd);
		
		return $comments;
	}
	
*/

echo $this->apploader('comments_default'); ?>