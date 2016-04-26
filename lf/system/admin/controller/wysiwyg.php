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
		
		if( is_null( $action ) )
		{
			pre( (new \LfActions)->byParent(-1)->byPosition(1)->find() );
		}
	}

	public function preview()
	{
		$vars =  \lf\requestGet('Param');
		
		$action = (new \LfActions)->findById($vars[1]);
		$links = (new \LfLinks)->findByInclude($vars[1]);
		
		
		$skin = $action->template;
		if($skin == 'default')
			$skin = \lf\get('default_skin');
		
		ob_start();
		readfile(LF.'skins/'.$skin.'/index.php');
		$template = ob_get_clean();
		
		ob_start();
		include LF.'cache/nav.cache.html';
		$nav = ob_get_clean();
		
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
				$this->lf->wwwInstall.'lf/skins/'.$skin,
				$nav,
				$this->lf->wwwAdmin.'dashboard/preview/'.$vars[1].'/',
				'<link rel="stylesheet" href="'.$this->lf->relbase.'lf/system/lib/lf.css" /><link rel="stylesheet" href="'.$this->lf->relbase.'lf/system/lib/3rdparty/icons.css" /></head>'
			),
			$template
		);
		
		echo $template;
		
		exit();
	}
}