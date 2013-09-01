<?php

echo '<pre>';
$table_desc = $this->db->fetchall('DESC io_threads');

echo md5(json_encode($table_desc)).'<br />';
print_r($table_desc);

echo '</pre>';

//echo 'asdf';
//chdir('../blog');
//echo $this->apploader('blog', 'inst=Blog', $this->vars);

?>
