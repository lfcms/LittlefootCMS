<?php 

namespace lf\admin;//\controllers;

class dashboard
{
	public function main()
	{
		$apps = scandir(LF.'apps');
		array_shift($apps); // .
		array_shift($apps); // ..
		foreach($apps as $app)
			if(is_file(LF.'apps/'.$app.'/widget.php'))
			{
				ob_start();
				$oldwd = getcwd();
				chdir(LF.'apps/'.$app);
				include LF.'apps/'.$app.'/widget.php';
				$widgets[$app] = ob_get_clean();
				chdir($oldwd);
			}
		
		
		// get all mentions of the apps
		$appLinks = (new \LfLinks)
						->lJoinIncludeOnId( // finally got a chance to use this style of join
							'lf_actions', // this is the table we are joining on
							['title', 'alias'] // Selects colums from other table.SELECT ..., ..., othertable.title, othertable.alias WHERE ...
						)->findByApp($apps);
						
		foreach($appLinks->getAll() as $link)
		{
			$links[$link['app']][] = $link;
		}
				
		
		
		
		
		
		include 'view/dashboard.main.php';
	}
}