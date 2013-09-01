<?php



/*

-views
[ Default ] list of discussion boards (general, item-specific, feedback)
	allow for child boards to narrow a given topic (tech support -> windows, linux, etc)
	allow for items to be directly commented on, but have the discussion persist within the forum as well
[ board/$board_id/ ] list of threads in a given discussion board
	these are user made posts with a subject
	only the subject, date, popularity, author is shown per entry
[ thread/$thread_id/ [$reply_to_post_id/] ] the thread itself
	other users can respond to a post, or to the op directly.
		this allows for converstaions to fork
[] Reply

other
*/

$owd = getcwd();

chdir(dirname(__FILE__));

include 'controller/forum.php';

if(!isset($this->vars[0])) $this->vars[0] = 'view';

$class = new forum($this, $this->db, $_app['ini']);

$methods = get_class_methods($class); // Get list of public methods
unset($methods[0]); // remove __construct from the list

$success = preg_match('/^('.implode('|', $methods).')$/', $this->vars[0], $match); // Sanitize based on available methods
if($success) // If a match is found from the implode
{
	// Pass class methods the rest of the request variables.
	$func = $match[1];
	
	ob_start();
	echo $class->$func($this->vars);
	$app = ob_get_clean();
	$app = str_replace('%baseurl%', $this->base, $app);
	$app = str_replace('%appurl%', $this->relbase.implode('/',$this->action), $app);
}
else 
	$app = "Invalid Request. That class does not exist.";

	ob_start();
include('view/nav.php');
$nav = ob_get_clean();
$nav = str_replace('%appurl%', $this->base.implode('/',$this->action), $nav);

include('view/forum.frame.php');

chdir($owd);

?>