<?php
	$sql = 'SELECT * FROM lf_forum_boards';
	$this->db->query($sql);
	$boards = $this->db->fetchall();
?>
<ul class="nav nav-list">
  <li class="nav-header">User Tools</li>
	<li>PM</li>
	<li>My Threads</li>
	<li>Saved Threads</li>
	<li>Settings</li>
  <li class="nav-header">Boards</li>
	<?php 
	
		foreach($boards as $board)
		{
			?>
			<li><a href="%appurl%board/<?=$board['id']?>/"><?=$board['title']?></a></li>
			<?php
		}
	?>
</ul>