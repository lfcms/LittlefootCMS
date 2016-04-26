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
		// if alias/ is present,
		else
		{
			echo 'not really ready for more alias just yet...';
			
			$action = (new \LfActions)
				->byParent(-1)
				->byPosition(2)
				->get();
				
			// and run with that specific action
			$this->printEditForm($action);
			
			// do navSelect() process to determine nav item selected
			/// need to update navSelect() into its own `nav` class resource (new \lf\nav)->select(['alias', 'list'])->getNavHTML()
			// wysiwyg on the selected nav item
		}
		
		
		return;
		
		
		
		
		
		
		// $action = (new \LfActions)->getById($param[1]);
		// $link = (new \LfLinks)->getById($param[1]);
		
		// include 'view/dashboard.wysiwyg.php';
	}
	
	private function printEditForm($action = NULL)
	{
		echo '<p>Return to <a href="%baseurl%dashboard/main/">dashboard</a></p>';
		echo '<h2>WYSIWYG</h2>';
		
		$param = \lf\requestGet('Param');
		
		// load home page if none provided
		if( is_null( $action ) )
			$action = (new \LfActions)
				->byParent(-1)
				->byPosition(1)
				->get();
		
		include 'view/wysiwyg.frame.php';
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
		
		(new \lf\cms)->getContent($param[1]);
		$rendered = (new \lf\template)
			->addContent($previewNav, 'nav')
			->setAdmin(false)
			->render();
			
		$replace = [
			'%baseurl%' => \lf\requestGet('AdminUrl').'wysiwyg/',
			'<a ' => '<a target="_parent"'
		];
		$rendered = str_replace(array_keys($replace), array_values($replace), $rendered);
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