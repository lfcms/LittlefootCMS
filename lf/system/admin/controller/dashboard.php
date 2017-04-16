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
				$widgets[] = ob_get_clean();
				chdir($oldwd);
			}
		
		include 'view/home.main.php';
	}
}