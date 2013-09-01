<?php


/*
//speed test
time a curl call to the website

//ftp manager in php (store user variables in session)
*/

$output = "Identity / Contacts<br />";



//$auth->vars['user'];

$sql = "SELECT * FROM id_contacts";

$result = $database->query($sql);

while($row = mysql_fetch_assoc($result))
{
	//$row['metadata'] = '{"a":1,"b":2,"c":3,"d":4,"e":5}';	$output .= $row['metadata'];
	$row['metadata'] = json_decode($row['metadata'], true);
	$output .= print_r($row,true);
}

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