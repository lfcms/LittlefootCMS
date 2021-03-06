<?php

namespace lf\admin;

class navigation
{
	public function main()
	{
		$param = \lf\requestGet('Param');
		
		// if no alias/ is present,
		if( $param == array() )
		{
			$debug[] = 'no alias selected';
			// navigation on first (parent: -1, position: 1) alias nav item
			$this->printEditForm();
			return;
		}
		// if we provided an action ID (likely from old dashboard)
		else if ( $param[0] == 'id' && isset($param[1]) )
		{
			$action = (new \LfActions)->getById($param[1]);
			$this->printEditForm($action);
		}
		// if alias/ is used in param,
		else
		{
			$action = $this->actionFromParam();
				
			// and run with that specific action
			$this->printEditForm($action);
			
			// do navSelect() process to determine nav item selected
			/// need to update navSelect() into its own `nav` class resource (new \lf\nav)->select(['alias', 'list'])->getNavHTML()
			// navigation on the selected nav item
		}
	}
	
	public function rmaction()
	{
		$param = \lf\requestGet('Param');
		
		if( ! isset( $param[1] ) )
			return;
		
		(new \LfActions)->deleteById($param[1]);
		(new \LfLinks)->deleteByInclude($param[1]);
		(new \lf\nav)->refreshCache();
		notice('<div class="success">Action and links deleted</div>');
		redirect302( \lf\requestGet('ActionUrl') );
	}
	
	public function rmlink()
	{
		$param = \lf\requestGet('Param');
		
		if( ! isset( $param[1] ) )
			return;
		
		(new \LfLinks)->deleteById($param[1]);
		notice('<div class="success">Link deleted</div>');
		redirect302();
	}
	
	/** Tried to build this like REST */
	public function links()
	{
		$param = \lf\requestGet('Param');
		
		// if no link ID is specified
		if( ! isset( $param[1] ) )
		{
			// if a $_POST is provided
			if( count( $_POST ) )
			{
				
				if( isset( $_POST['newnav'] ) && $_POST['newnav'] == 'on' )
				{
					unset($_POST['newnav']);
					$newAction = [
						'parent' => -1,
						'position' => 9999, // idk, something high, the createAction() function will set it to the last position
						'alias' => $_POST['app'],
						'title' => $_POST['app'],
						'label' => $_POST['app'],
						'app' => 0,
						'template' => 'default'
					];
					
					// overwrite post include so the following app link hits this new nav item
					$_POST['include'] = (new \lf\nav)->createAction($newAction);
				}
				
				(new \LfLinks)->insertArray($_POST);
				notice('<div class="success">Link Added Successfully</div>');
				redirect302( \lf\requestGet('ActionUrl').'id/'.$_POST['include'] );
			}
			// if they just want a list of links (this is not used by the lf admin yet)
			else
			{
				echo '<pre>'.json_encode( (new \LfLinks)->getAll(), JSON_PRETTY_PRINT ).'</pre>';
				exit;
			}
		}
		
		// if they did provide a link ID and a post,
		if( count( $_POST ) )
		{
			$id = $param[1];
			
			if( isset( $_POST['id'] ) )
				return "Don't post an id";
			
			(new \LfLinks)->updateById($id, $_POST);
			notice('<div class="success">Link Updated Successfully</div>');
			redirect302();
		}
	}
	
	public function postNavEdit()
	{
		$param = \lf\requestGet('Param');
		
		if( ! isset( $param[1] ) )
		{
			notice('<div class="error">No ID specified in param[1]</div>');
			redirect302();
		}
		
		(new \lf\nav)->updateAction($param[1], $_POST);
		notice('<div class="success">Action updated</div>');
		redirect302( \lf\requestGet('ActionUrl').'id/'.$param[1] ); 
	}
	
	public function postLinkEdit()
	{
		pre($_POST);
	}
	
	public function postAddNew()
	{
		pre($_POST);
	}
	
	private function printEditForm($action = NULL)
	{
		$param = \lf\requestGet('Param');
		
		// load home page if none provided
		if( is_null( $action ) )
			$action = (new \LfActions)
				->byParent(-1)
				->byPosition(1)
				->get();
		
		/* and stuff for showing links */
		
		// loop through linked apps
		$links = (new \LfLinks)
					->order('id')
					->getAllByInclude($action['id']);
		//$links['skin'] = $action['template'];
		if($action['template'] == 'default')
			$skin = \lf\getSetting('default_skin');
		else
			$skin = $action['template'];
		
		if(is_file(LF.'skins/'.$skin.'/index.php'))
			$skinSourceCode = file_get_contents(LF.'skins/'.$skin.'/index.php');
		else
			$skinSourceCode = '';
		$locationMatch = '/printContent\(["\']([^)]+)["\']\)/';
		$locations = [];
		if(preg_match_all($locationMatch, $skinSourceCode, $match))
			$locations = array_unique($match[1]);
		
		include 'view/navigation.frame.php';
	}
	
	private function actionFromParam()
	{
		// determines current action request
		
		// by default, not found. needed to detect request for / when action at -1, 1 doesnt have an empty alias
		$select['alias'] = '404'; 
		
		/* Determine requested nav item from lf_actions */
		// get all possible matches for current request, 
		// always grab the first one in case nothing is selected
		$matches = (new \lf\orm)->fetchAll("
			SELECT * FROM lf_actions 
			WHERE alias IN ('".implode("', '", \lf\requestGet('Param') )."') 
				OR (position = 1 AND parent = -1)
			ORDER BY  ABS(parent), ABS(position) ASC
		");
		
		// Assign as parent,position => array()
		$base_save = NULL;
		foreach($matches as $row)
		{
			// save item in first spot of base menu if it is an app, 
			// just in case nothing matches
			if($row['position'] == 1 && $row['parent'] == -1)
			{
				// save row in case "domain.com/" is requested
				$base_save = $row;
			}
				
			$test_select[$row['parent']][$row['position']] = $row;
		}
		
		// loop through action to determine selected nav
		// trace down to last child
		
		// start at the root nav items
		$parent = -1; 
		// nothing selected to start with
		$selected = array(); 
		// loop through action array
		for($i = 0; $i < count( \lf\requestGet('Param') ); $i++) 
			// if our compiled parent->position matrix has this parent set
			if(isset($test_select[$parent])) 
				foreach($test_select[$parent] as $position => $nav)	// loop through child items 
					if($nav['alias'] == \lf\requestGet('Param')[$i]) // to find each navigation item in the hierarchy
					{
						// we found the match, 
						// move on to next action item matching
						
						// this result in all/that/match(/with/params/after)
						$selected[] = $nav;
						
						$parent = $nav['id'];
						break;
					}
		
		// if a selection was made, alter the action so it has proper params
		if($selected != array())
		{
			// separate action into vars and action base, 
			// pull select nav from inner most child
			// eg, given `someparent/blog/23-test`, pop twice
//			(new request)->load()
//				->actionKeep( count($selected) )
//				->save();
			
			// This is where we find which navigation item we are visiting
			$select = end($selected);
		}
		
		
		// If home page is an app and no select was made from getnav(), 
		// set current page as /
		if($select['alias'] == '404' && $base_save != NULL)
		{		
			//(new request)->load()->fullActionPop()->save(); // pop all actions into param, we are loading the first nav item
			$select = $base_save;
		}
		
		return $select;
	}
}