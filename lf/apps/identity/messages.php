<?php


$break = 0;

/*
//speed test
time a curl call to the website

//ftp manager in php (store user variables in session)
*/

$output = "Identity / Messages<br />";

/* connect to gmail */
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'joe@bioshazard.com';
$password = 'asdf896325';

$start = microtime(true);
/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());

/* grab emails */
$emails = imap_search($inbox,'ALL');

/* if emails are returned, cycle through each... */
if($emails) {
  
  /* begin output var */
  $mail = '';
  
  /* put the newest emails on top */
  rsort($emails);
  
  /* for every email... */
  foreach($emails as $email_number) {
    
	if($break++ > 10) break;
	
    /* get information specific to this email */
    $overview = imap_fetch_overview($inbox,$email_number,0);
    //$message = imap_fetchbody($inbox,$email_number,2);
    
    /* output the email header information */
    $mail.= '<div class="toggler '.($overview[0]->seen ? 'read' : 'unread').'">';
    $mail.= '<span class="subject">'.$overview[0]->subject.'</span> ';
    $mail.= '<span class="from">'.$overview[0]->from.'</span>';
    $mail.= '<span class="date">on '.$overview[0]->date.'</span>';
    $mail.= '</div>';
    
    /* output the email body */
    //$mail.= '<div class="body">'.$message.'</div>';
  }
  
  $output .= $mail;
} 

/* close the connection */
imap_close($inbox);

/* grab emails 
$emails = imap_search($inbox,'ALL');

if($emails === false)
  echo "The search failed";

imap_close($inbox);

$output .= microtime(true) - $start;

$output .= '<pre>'.print_r($emails,true).'</pre>';*/

?>