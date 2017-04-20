<?php

namespace lf\admin;

class experimental
{
	public function init()
	{
		echo '<h2><a href="'.\lf\requestGet('AdminUrl').'experimental">Experimental</a></h2>';
	}
	
	public function main()
	{
		$reflection = new \ReflectionClass('\\lf\\admin\\experimental');
		$methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
		
		$skip_these = ['init', 'main'];
		echo '<p>Click a method below to try it</p>';
		echo '<ul class="efvlist">';
		foreach($methods as $index => $reflectionObject)
		{
			$method = $reflectionObject->name;
			if(in_array($method, $skip_these) )
				continue;
			echo '<li><a href="experimental/'.$method.'">'.$method.'</a></li>';
			
		}
		echo '</ul>';
	}
	
	public function preview()
	{
		/*$action['id'] = 109;
		$param = [];
		$iframeUrl = \lf\requestGet('AdminUrl').'navigation/preview/'.$action['id'].'/'.implode('/', $param);
		
		?>
	
	
	<div class="row">
		<div class="col-12">
			<h4 title="Preview Your Site and Make Updates in Realtime" class="no_martop"><i class="fa fa-eye"></i> Preview <a href="<?=$iframeUrl?>" class="pull-right" title="Fullscreen Preview"><i class="fa fa-expand"></i></a></h4>
			<iframe src="<?=$iframeUrl?>"
				class="white light_b" width="100%" height="700px" frameborder="0">
			</iframe>
		</div>
	</div>
		<?php*/
		
		
		
		$param = \lf\requestGet('param');
		if( !isset( $param[1] ) ) $param[1] = '';
		// generate normal frontend
		
		(new \lf\cms)->loadLfCss();
		
		// load in nav cache
		$previewNav = (new \lf\cms)->getNavCache();
		//$replace = [
			// '%baseurl%' => \lf\requestGet('AdminUrl').'navigation/',
			// '<a ' => '<a target="_parent"'
		// ];
		//$previewNav = str_replace(array_keys($replace), array_values($replace), $navCache);
		
		
		
		$replace = [
			'%baseurl%' => \lf\requestGet('AdminUrl').'experimental/preview/',
			'<a ' => '<a target="_parent"'
		];
		
		$previewNav = str_replace(array_keys($replace), array_values($replace), $previewNav);
		
		
		
		// new request
		$request = (new \lf\request)->load()
			// Drop navigation into Cwd (doesnt affect anything really...)
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
		
			// replace /admin/navigation/preview/ into template nav output
		// set request to frontend mode and execute apps (getcontent?)
		
		
		// print template
		
		
		
		exit;
		
		
		
		
		
		
		
		
		
		
		
		
		// never gets here
		
		
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