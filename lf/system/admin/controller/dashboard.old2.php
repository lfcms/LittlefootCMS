<?php
class dashboard extends app

       public function main($var)
       {
		if($this->lf->settings['simple_cms'] != '_lfcms')
		{
			$cwd = getcwd();
			chdir(ROOT.'apps/'.$this->lf->settings['simple_cms']);
			
			if(is_file('admin.php')) include 'admin.php';
			else echo 'No Admin';
			
			chdir($cwd);
			return;
		}
	
		$cwd = getcwd();
		$apps = scandir(ROOT.'apps');
		
		$rows = count($apps) - 2;
		$count = 0;
		
		echo '
		<style type="text/css">
			#dashgrid { list-style: none; display: block; float: left; width: 400px; padding: 0 }
		</style>
	
		<div id="dash-admin">';
			$li = array('', '', '');
			foreach($apps as $app)
			{
				if($app == '.' || $app == '..' || !is_file(ROOT.'apps/'.$app.'/admin.php')) continue;
					$count++;
				
				if($count > 2) $count = 0;
				
				chdir(ROOT.'apps/'.$app);
				
				ob_start();
				echo '<li>';
					echo '<div class="dash-admin-app">
							<h3>'.$app.'</h3>';
							
					if(is_file('widget.php')) 
						include 'widget.php';
					else 
						include 'admin.php';								
					echo '</div>';
				echo '</li>';
				$li[$count] .= str_replace('%appurl%', '%baseurl%apps/manage/'.$app.'/', ob_get_clean());
			}
			$count = 0;
			echo '<ul id="dashgrid">';
				echo implode('</ul><ul id="dashgrid">', $li);
			echo '</ul>';
		echo '</div>';
		chdir($cwd);
       }

?>