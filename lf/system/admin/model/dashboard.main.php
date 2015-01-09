<?php

$result = orm::q('lf_actions')->filterByposition('!=', 0)->order('position')->get();

$actions = array();
foreach($result as $action)
	$actions[$action['parent']][] = $action;

$hidden = orm::q('lf_actions')->filterByposition(0)->order('label')->get();
	
$this->edit = 0; // no nav item selected by default
if(isset($vars[1]))
	$this->edit = $vars[1];
	
	/*
echo '<pre>';
print_r($actions);
echo '</pre>';*/

$result = orm::q('lf_links')->get();
foreach($result as $link)
	$this->links[$link['include']][] = $link;