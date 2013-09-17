<?php

$row = $this->db->fetch("SELECT * FROM lf_pages WHERE id = '".$_app['ini']."'");
 
echo '<h2>'.$row['title'].'</h2>';
echo $row['content'];

?>