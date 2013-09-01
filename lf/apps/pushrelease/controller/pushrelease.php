<?php

/*

public functions are the controllers
private functions are the models
view loads at the end

*/

class pushrelease
{
	public function main($var)
	{
//		echo 'To release LittleFoot, just run [<a href="%appurl%lfrelease/">lfrelease</a>]';
		echo '<h3>LFCMS Release updater</h3>';
		echo '<form action="%appurl%lfrelease/" method="post">
				Optionally provide a commit message: <input type="text" name="commit" placeholder="Commit text" /> <input type="submit" value="Update Littlefoot Release" />
			</form>';
	}
	public function lfrelease($var)
	{
		if($_POST['commit'] == '') $_POST['commit'] = 'AUTOMATED COMMIT';

		if(!preg_match('/^[a-zA-Z0-9 ]+$/', $_POST['commit'], $match)) return 'Invalid commit message: '.$_POST['commit'];

		$cwd = getcwd();
		chdir('/home/bios/domains/littlefootcms.com/files/build-release');
		echo '<pre>';
		echo shell_exec('/bin/sh release.sh "'.$match[0].'"');
		echo '</pre>';
		chdir($cwd);
		//redirect302();
	}
}

?>
