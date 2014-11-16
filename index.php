<?php

### XHProf

if(isset($_GET['xhprof']))
{

	xhprof_enable();

	class LastSay {
		function __construct() {
		}
		function __destruct() {	
			$xhprof_data = xhprof_disable();

			$XHPROF_ROOT = ROOT.'../../xhprof';

			include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
			include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";

			$xhprof_runs = new XHProfRuns_Default();

			$run_id = $xhprof_runs->save_run($xhprof_data, "lf");

			echo '<div style="clear: both">'.
				 '<a href="/xhprof/xhprof_html/index.php?run='.$run_id.'&source=lf">Profile Result</a>'.
				 "</div>";

			### End XHProf
		}
	}

	$lolhax = new LastSay();
}

define('ROOT', dirname(__FILE__).'/lf/'); // The absolute path to the lf/ directory is the ROOT of the application
if(!chdir(ROOT)) die('Access Denied to '.ROOT); // if unable to cd there, kill script
include 'system/init.php';