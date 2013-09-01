<?php 

$dir = ROOT.'..';

chdir($dir);

$base = implode('/', $this->vars);

echo '<a href="%appurl%'.implode('/', array_slice($this->vars, 0, -1)).'">back</a><br />';

if(is_file($base))
{
	$code = file_get_contents($base);
	//preg_match_all('/([^'.chr(13).']+)?(\/\/[^'.chr(13).']+)?(?:'.chr(13).')/', $code, $match);
	//preg_match_all('/([^'.chr(13).']+)?(?:'.chr(13).')/', $code, $match);
	//$lines = $match[1];
	//$lines = explode("\n", $code);
	
	$f = fopen($base, 'r');
	if ($f) {
		echo '<pre style="font-family: Helvetica">';	
		while (($buffer = fgets($f, 4096)) !== false) {
			//$lines
			$line = $buffer;
			
			preg_match("/(^\s*)?\/\/.*/", $line, $comments);
			
			//print_r($comments);
			//echo '</pre>';
			
			//$line = str_replace(
			//	array('/*', '*/'),
			//	array('<h4>','</h4>'),
				$line = htmlentities($line);
			//);
		
			if(isset($comments[1])) $line = str_replace($line, '<h3>'.$line.'</h3>', $line);
			else if(isset($comments[0])) $line = str_replace($comments[0], '<strong>'.$comments[0].'</strong>', $line);
			
			echo $line;//.'<br />';
			
		}
		echo '</pre>';
		if (!feof($f)) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose($f);
	}
	
	if(false){
	echo '<pre style="font-family: Arial">';	
	$i=0;
	var_dump(ord($code[5]));
	var_dump(ord($code[6]));
	var_dump(ord($code[7]));
	var_dump(ord($code[8]));
	var_dump(ord($code[9]));
	var_dump(ord($code[10]));
	
	foreach($lines as $line)
	{
		preg_match('/(^\s*)?\/\/.*/', $line, $comments);
		
		if(isset($comments[1])) $line = str_replace($line, '<h2>'.$line.'</h2>', $line);
		else if(isset($comments[0])) $line = str_replace($comments[0], '<strong>'.$comments[0].'</strong>', $line);
		
		print_r($lines);
		//echo '</pre>';
		
		$line = str_replace(
			array('/*', '*/'),
			array('<h4>','</h4>'),
			$line
		);
		
		echo $line.'<br />';
	}
	echo '</pre>';
	}
	/*
	echo '<table>';
	for($i = 0; $i < count($match[0]); $i++)
	{
		if(trim($match[1][$i]) == '') echo '</table><h2>'.$match[2][$i].'</h2><table>';
		else
		{
			echo '<tr><td><p>'.$match[2][$i].'<p></td><td><code>'.$match[1][$i].'</code></td></tr>';
		}
	}
	echo '</table>';*/
	
	echo '<br />'.$base; ?>
	FILE!<br />
	<textarea name="" id="" style="width: 100%; height: 500px;"><?php echo $code; ?></textarea>
	<?php
}
else
{
	$this->vars[] = '.';
	$files = scandir(implode('/', $this->vars));

	print_r($this->vars);
	
	if($base != '') $base .= '/';
	
	foreach($files as $file)
	{
		if($file == '.' || $file == '..' || $file == '.svn' || $file == 'config.php') continue;
		
		echo '<a href="%appurl%'.$base.$file.'">';
		echo $file;
		echo '</a>';
		echo '<br />';
		
	}
}



?>
