<?php

$this->db->query("SELECT * FROM lf_pages WHERE id = '".$_app['ini']."'");
$row = $this->db->fetch();
 
echo '<h2>'.$row['title'].'</h2>';
echo $row['content'];

?>