<?php

$options = array('grid', 'classic');

$args = '<select name="ini" id="">';
foreach($options as $option)
	$args .= '<option value="'.$option.'">'.$option.'</option>';
$args .= '</select>';

?>