<?php

// Query lf_actions navigation items, loop to generate parent => child hierarchy
$result = orm::q('lf_actions')->filterByposition('!=', 0)->order('ABS(position)')->getAll();
$actions = array();
foreach($result as $action)
	$actions[$action['parent']][] = $action;

// Also pull lf_actions items that are hidden from nav
$hidden = (new orm)
	->qActions('lf')
	->filterByPosition(0)
	->order('label')
	->getAll();

// If an id is specified in the URL, save as the 'edit' variable in current class.
$this->edit = 0;
if(isset($vars[1]))
	$this->edit = $vars[1];

// Pull lf_links, reorganize as $nav_id => $linkdata[]
$result = orm::q('lf_links')->getAll();
foreach($result as $link)
	$this->links[$link['include']][] = $link;
	
// Generate list of apps for App Gallery
$apps = array();
foreach(scandir(ROOT.'apps') as $file)
{
	if($file == '.' || $file == '..') continue;

	$app = ROOT.'apps/'.$file;

	if(is_dir($app))
		$apps[] = $file;
}