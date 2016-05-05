<?php

namespace lf;

class nav
{
	public function refreshCache()
	{
		// Grab all possible actions
		$actions = (new \lf\orm)->fetchall("
			SELECT * FROM lf_actions 
			WHERE position != 0 
			ORDER BY ABS(parent), ABS(position) ASC");
		// $actions = (new \LfActions)
			// ->order('ABS(parent), ABS(position)', 'ASC')
			// ->findByPosition('!=', 0)
			// ->matrix(['parent', 'position']);
		
		// Make a matrix sorted by parent and position
		$menu = array();
		foreach($actions as $action)
			$menu[$action['parent']][$action['position']] = $action;
		
		$nav = $this->buildHtml($menu);
		if(!is_dir(LF.'cache')) 
			mkdir(LF.'cache', 0755, true); // make if not exists
		file_put_contents(ROOT.'cache/nav.cache.html', $nav);
		
		return $this;
	}
	
	private function buildHtml($menu, $parent = -1, $prefix = '')
	{
		$items = $menu[$parent];
		
		$html = '<ul>';
		if($items)
		foreach($items as $item) // loop through the items
		{
			$newprefix = $prefix;
			$newprefix[] = $item['alias'];
			
			// Generate printable request in/this/form
			$link = implode('/',$newprefix);
			if(strlen($link) != 0) 
				$link .= '/';
			
			$icon = '';
			if(isset($menu[$item['id']]))
				$icon = '<i class="fa fa-caret-down fsmall"></i>';
			
			// and generate the <li></li> element content
			$html .= '<li><a href="%baseurl%'.$link.'" title="'.$item['title'].'">'.$item['label'].' '.$icon.'</a>';
			
			// Process any submenus before closing <li>
			if(isset($menu[$item['id']]))
				$html .= $this->buildHtml($menu, $item['id'], $newprefix);
				
			$html .= '</li>';
		}
		$html .= '</ul>';
		
		return $html;
	}
	
	public function setPosition($id, $destPosition, $destParent)
	{
		// position cant be negative, defaulted to hidden
		if($destPosition <= 0) 
			$destPosition = 0;
		
		// save original position and parent of this item
		$original = (new \LfActions)
			->cols('position, parent')
			->getById($id);
		
		// get # of children of destination parent
		$result = (new \lf\orm)->fetch('
			SELECT COUNT(id) as count 
			FROM lf_actions 
				WHERE parent = '.(new \lf\orm)->escape($destParent));
		$count = $result['count'];
		
		// if parent is being changed
		if($destParent != $original['parent'])
		{
			// cant be more than last
			if($destPosition > $count + 1)
				$destPosition = $count + 1;
			
			// if destination is 0, it is hidden, we dont need to make
			// room if it is going to be hidden like the others
			if($destPosition != 0)
				(new \lf\orm)->query("
					UPDATE lf_actions SET position = position + 1 
					WHERE parent = ".intval($destParent)." 
						AND position >= ".intval($destPosition));
			
			// Move item to destination parent and position in all cases
			(new \lf\orm)->query("
				UPDATE lf_actions 
				SET parent = ".intval($destParent).", 
					position = ".intval($destPosition)." 
				WHERE id = ".$id);
			
			// if it wasn't hidden before, close the gap we left behind
			if($original['position'] != 0)
				(new \lf\orm)->query("
					UPDATE lf_actions SET position = position - 1 
					WHERE parent = ".$original['parent']." 
						AND position > ".$original['position']);
		}
		// else if moving within current siblings, no change to parent
		else if($destPosition != $original['position']) 
		{
			if($destPosition > $count) // cant be further down than last
				$destPosition = $count;
				
			// starting from 0
			// make room for new item
			if($original['position'] == 0) 
				(new \lf\orm)->query('
					UPDATE lf_actions SET position = position + 1 
					WHERE parent = '.$original['parent'].' AND position >= '.intval($destPosition)); 
					
			// going to 0
			// make room for new item
			else if($destPosition == 0) 
				(new \lf\orm)->query('
					UPDATE lf_actions SET position = position - 1 
					WHERE parent = '.$original['parent'].' 
						AND position > '.intval($original['position'])); 
					
			// moving to lower position
			else if($destPosition < $original['position']) 
				(new \lf\orm)->query('
					UPDATE lf_actions SET position = position + 1 
					WHERE parent = '.$original['parent'].' 
						AND position >= '.intval($destPosition).' 
						AND position < '.$original['position']);
					
			// moving to higher position
			else if($destPosition > $original['position']) 
				(new \lf\orm)->query('
					UPDATE lf_actions SET position = position - 1 
					WHERE parent = '.$original['parent'].' 
						AND position <= '.intval($destPosition).' 
						AND position > '.$original['position']);
			
			// move to destination
			(new \lf\orm)->query("
				UPDATE lf_actions 
				SET position = ".intval($destPosition)." 
					WHERE id = ".$id);
		}
		
		return $this;
	}
	
	// take a REST approach
	// http://www.vinaysahni.com/best-practices-for-a-pragmatic-restful-api#restful
	public function updateAction($id, $data)
	{
		// if position and parent is specified
		if( isset( $data['position'], $data['parent']) )
		{
			// apply position update
			$this->setPosition($id, $data['position'], $data['parent']);
		}
		
		// parse out positional update
		unset($data['position'], $data['parent']);
		
		// update the nav item
		(new \LfActions)->updateById($id, $data);
		$this->updatenavcache();
		return $this;
	}
	
	public function createAction($data)
	{
		$originalData = $data;
		$data['parent'] = -1;
		$data['position'] = 0; // set hidden at first, this is a lazy approach to inserting a new navigation item 
		$newId = (new \LfActions)->insertArray($data);
		$this->updateAction($newId, $originalData);
		return $this;
	}
	
	public function updateLink($id, $data)
	{
		(new \LfLinks)->updateById($id, $data);
		return $this;
	}
}