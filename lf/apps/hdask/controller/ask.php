<?php

class ask extends app
{
	private $appurl;
	
	protected function init($vars)
	{
		$this->appurl = $this->lf->base.implode('/', $this->lf->action).'/';
		echo '<h2><a href="%appurl%">ASK</a></h2>
		<p>[Acquire Super Knowledge]</p>';
		
	}
	
	public function main($vars)
	{
		echo '<p><a href="%appurl%form/">Submit question</a></p>';
		echo '<h3>Questions</h3>';
		// List all questions
		$questions = $this->db->fetchall("SELECT * FROM ask_questions");
		foreach($questions as $q)
		{
			echo '<a href="%appurl%q/'.$q['id'].'/">'.$q['q_title'].'</a><br />';
		}
	}
	
	public function q($vars)
	{
		$q = $this->db->fetch("SELECT * FROM ask_questions WHERE id = ".intval($vars[1]));
		
		echo '<h3>'.$q['dept'].' / '.$q['category'].' / '.$q['q_title'].'</h3>';
		
		echo '<h4>Question (asked by '.$q['user'].')</h4>';
		echo '<p>'.$q['q_text'].'</p>';
		
		echo '<h4>Answer [<a href="%appurl%edit/'.$q['id'].'/">edit</a>]</h4>';
		echo '<p>'.$q['a_text'].'</p>';
	}
	
	public function form($vars)
	{
		$cats = $this->db->fetchall("SELECT DISTINCT category FROM ask_questions");
		$options = '';
		foreach($cats as $cat)
			$options .= '<option value="'.$cat['category'].'">'.$cat['category'].'</option>';
			
		echo '
			<form action="%appurl%submit/" method="post">
				<input type="submit" value="Submit Question" /><br /><br />
				Category <select name="category">'.$options.'</select> or 
				<input type="text" placeholder="New Category" name="newcat"><br /><br />
				User <input type="text" name="user" /><br /><br />
				Title <input type="text" name="q_title" /><br /><br />
				Question<br /><textarea name="q_text"></textarea><br /><br />
				<input type="submit" value="Submit Question" />
			</form>
		';
	}
	
	public function submit($vars)
	{
		if($_POST['newcat'] != '') $_POST['category'] = $_POST['newcat'];
		
		$this->db->query("INSERT INTO ask_questions VALUES (
			NULL, 'Tech: Linux', 
			'".mysql_real_escape_string($_POST['category'])."', 
			'".mysql_real_escape_string($_POST['user'])."', 
			'".mysql_real_escape_string($_POST['q_title'])."', 
			'".mysql_real_escape_string($_POST['q_text'])."', 
			'No answer yet' 
		)");
		
		$id = $this->db->last();
		redirect302($this->appurl.'q/'.$id);
	}
	
	public function edit($vars)
	{	
		$q = $this->db->fetch("SELECT * FROM ask_questions WHERE id = ".intval($vars[1]));
		
		$cats = $this->db->fetchall("SELECT DISTINCT category FROM ask_questions");
		$options = '';
		foreach($cats as $cat)
		{
			if($cat['category'] == $q['category']) $select = 'selected="selected"';
			else $select = '';
			
			$options .= '<option value="'.$cat['category'].'" '.$select.'>'.$cat['category'].'</option>';
		}
		
		echo '
			<form action="%appurl%update/'.$vars[1].'/" method="post">
				<input type="submit" value="Update Question" /><br /><br />
				Category <select name="category">'.$options.'</select> or 
				<input type="text" placeholder="New Category" name="newcat"><br /><br />
				User <input type="text" name="user" value="'.$q['user'].'" /><br /><br />
				Title <input type="text" name="q_title" value="'.$q['q_title'].'" /><br /><br />
				Question<br /><textarea name="q_text">'.$q['q_text'].'</textarea><br /><br />
				Answer<br /><textarea name="a_text">'.$q['a_text'].'</textarea><br /><br />
				<input type="submit" value="Update Question" />
			</form>
		';
	}
	
	public function update($vars)
	{
		if(!isset($vars[1])) redirect302();
		
		$edit = intval($vars[1]);
		if($_POST['newcat'] != '') $_POST['category'] = $_POST['newcat'];
		
		unset($_POST['newcat']);
		
		foreach($_POST as $key => $val)
			$set[] = mysql_real_escape_string($key)." = '".mysql_real_escape_string($val)."'";
		$set = implode(', ', $set);
		
		$sql = "UPDATE ask_questions SET ".$set." WHERE id = ".$edit;
		//echo $sql;
		
		$this->db->query("UPDATE ask_questions SET ".$set." WHERE id = ".$edit);
		
		redirect302($this->appurl.'q/'.$edit);
	}
}