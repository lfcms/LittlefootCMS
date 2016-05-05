<?php

namespace lf\admin;

class wysiwyg
{
	public function main()
	{
		$param = \lf\requestGet('Param');
		
		// if no alias/ is present,
		if( $param == array() )
		{
			$debug[] = 'no alias selected';
			// wysiwyg on first (parent: -1, position: 1) alias nav item
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
			// wysiwyg on the selected nav item
		}
	}
	
	public function postNavEdit()
	{
		echo '<h3>Nav Edit</h3>';
		pre($_POST);
		
		// 
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
		
		include 'view/wysiwyg.frame.php';
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

	public function preview()
	{
		$param = \lf\requestGet('param');
		// generate normal frontend
		
		(new \lf\cms)->loadLfCss();
		
		// load in nav cache
		$previewNav = (new \lf\cms)->getNavCache();
		//$replace = [
			// '%baseurl%' => \lf\requestGet('AdminUrl').'wysiwyg/',
			// '<a ' => '<a target="_parent"'
		// ];
		//$previewNav = str_replace(array_keys($replace), array_values($replace), $navCache);
		
		
		
		$replace = [
			'%baseurl%' => \lf\requestGet('AdminUrl').'wysiwyg/',
			'<a ' => '<a target="_parent"'
		];
		
		$previewNav = str_replace(array_keys($replace), array_values($replace), $previewNav);
		
		
		
		// new request
		$request = (new \lf\request)->load()
			// Drop wysiwyg into Cwd (doesnt affect anything really...)
			->actionDrop()
			// set param as what $this controller method received
			->paramShift(2)
			//->setAction([])
			->save();
		
		(new \lf\cms)->getContent($param[1]);
		
		$rendered = (new \lf\template)
			->addContent($previewNav, 'nav')
			->setAdmin(false)
			->render();
			
		
		//$rendered = str_replace(array_keys($replace), array_values($replace), $rendered);
		echo $rendered;
			
		
	//	pre(new \lf\template, 'var_dump');
		
			// replace /admin/wysiwyg/preview/ into template nav output
		// set request to frontend mode and execute apps (getcontent?)
		
		
		// print template
		
		
		
		exit;
		return;
		
		
		
		
		
		
		
		
		
		
		
		$vars =  \lf\requestGet('Param');
		
		$action = (new \LfActions)->findById($vars[1]);
		$links = (new \LfLinks)->findByInclude($vars[1]);
		
		
		$skin = $action->template;
		if($skin == 'default')
			$skin = \lf\getSetting('default_skin');
		
		
		//(new \lf\cms)->getContent();
		
		ob_start();
		include LF.'cache/nav.cache.html';
		$nav = ob_get_clean();
		
		(new \lf\template)->addContent('nav', $nav);
		
		ob_start();
		include LF.'skins/'.$skin.'/index.php';
		$template = ob_get_clean();
		
		$content = '<h2>%content%</h2>';
		
		
		//pre($links->result);
		
		$content .= implode(', ', $action->get()).'<br />';
		
		foreach($links->result as $row)
		{
			$content .= implode(', ', $row).'<br />';
		}
		
		
		
		$content .= '
			<div class="row">
				<div class="col-4">Add new app:</div>
				<div class="col-4">
					<select name="" id="">
						<option value="">App1</option>
					</select>
				</div>
				<div class="col-4">
					<input type="submit" />
				</div>
			</div>
		 
		
		';
		
		
		
		$template = str_replace(
			array(
				'%content%',
				'%skinbase%',
				'%nav%',
				'%baseurl%',
				'</head>'
			),
			array(
				$content,
				\lf\requestGet('LfUrl').'skins/'.$skin,
				$nav,
				\lf\requestGet('AdminUrl').'dashboard/preview/'.$vars[1].'/',
				'<link rel="stylesheet" href="'.\lf\requestGet('LfUrl').'system/lib/lf.css" /><link rel="stylesheet" href="'.\lf\requestGet('LfUrl').'system/lib/3rdparty/icons.css" /></head>'
			),
			$template
		);
		
		echo $template;
		
		exit();
	}
}