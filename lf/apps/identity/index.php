<?php


/*
//speed test
time a curl call to the website

//ftp manager in php (store user variables in session)
*/

$output = "Identity<br />";

//$auth->vars['user'];

print_r($variables);

$result = preg_match('/^(contacts|messages|notes)$/', $variables[0], $match);

if($result != 0)
{
	$match = $match[0];
	include($match . '.php');
}
else
{
	$output .= '
		<ul>
			<li><a href="/identity/messages">Message</a></li>
			<li><a href="/identity/contacts">Contacts</a></li>
			<li><a href="/identity/notes">Notes</a></li>
		</ul>
	';
}

//if($vars)
//include("contacts.php");

?>

<!--

/* connect to gmail */
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = '';
$password = '';


$start = microtime(true);
/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());

/* grab emails */
$emails = imap_search($inbox,'ALL NEW');

if($emails === false)
  echo "The search failed";

imap_close($inbox);

$output .= microtime(true) - $start;

$output .= '<pre>'.print_r($emails,true).'</pre>';

-->