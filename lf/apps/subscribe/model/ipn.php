<?php
/*
ipn.php - example code used for the tutorial:

PayPal IPN with PHP
How To Implement an Instant Payment Notification listener script in PHP
http://www.micahcarrick.com/paypal-ipn-with-php.html

(c) 2011 - Micah Carrick
*/

//file_put_contents('out.txt', print_r($_POST,1));


// tell PHP to log errors to ipn_errors.log in this directory
ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');

// intantiate the IPN listener
include('ipnlistener.php');
$listener = new IpnListener();

// tell the IPN listener to use the PayPal test sandbox
$listener->use_sandbox = true;

// try to process the IPN POST
try {
    $listener->requirePostMethod();
    $verified = $listener->processIpn();
} catch (Exception $e) {
    error_log($e->getMessage());
    exit(0);
}

if ($verified) {

	file_put_contents('invoice_'.intval($_POST['item_number']).'.txt', json_encode($_POST)); //exit();
	/*
	$log = fopen('out.txt', 'a');
	fwrite($log, print_r($_POST,1));
	fclose($log);

	// PDO
	try {
		$db = new PDO('sqlite:ipn.db');

		$res = $db->exec("CREATE TABLE IF NOT EXISTS pay (id INTEGER PRIMARY KEY, inv_id INTEGER, status STRING)");
		
		$db->exec("INSERT INTO pay (inv_id, status) VALUES (".intval($_POST['item_number']).", 'PAID')");

		$result = $db->query('SELECT * FROM foo');
		echo '<pre>';
		foreach($result as $row)
		{
			print_r($row);
		}

		var_dump($result);
		var_dump($db);

		echo '</pre>';
	}

	catch(PDOException $e)
	{
		echo 'ERROR: '.$e->getMessage();
	}
	
	exit();
	
	
	*/
	
	
	
	
	
	
	
    $errmsg = '';   // stores errors from fraud checks
    
    // 1. Make sure the payment status is "Completed" 
    if ($_POST['payment_status'] != 'Completed') { 
        // simply ignore any IPN that is not completed
        exit(0); 
    }

    // 2. Make sure seller email matches your primary account email.
    if ($_POST['receiver_email'] != 'YOUR PRIMARY PAYPAL EMAIL') {
        $errmsg .= "'receiver_email' does not match: ";
        $errmsg .= $_POST['receiver_email']."\n";
    }
    
    // 3. Make sure the amount(s) paid match
    if ($_POST['mc_gross'] != '9.99') {
        $errmsg .= "'mc_gross' does not match: ";
        $errmsg .= $_POST['mc_gross']."\n";
    }
    
    // 4. Make sure the currency code matches
    if ($_POST['mc_currency'] != 'USD') {
        $errmsg .= "'mc_currency' does not match: ";
        $errmsg .= $_POST['mc_currency']."\n";
    }

    // 5. Ensure the transaction is not a duplicate.
    mysql_connect('localhost', 'DB_USER', 'DB_PW') or exit(0);
    mysql_select_db('DB_NAME') or exit(0);

    $txn_id = mysql_real_escape_string($_POST['txn_id']);
    $sql = "SELECT COUNT(*) FROM orders WHERE txn_id = '$txn_id'";
    $r = mysql_query($sql);
    
    if (!$r) {
        error_log(mysql_error());
        exit(0);
    }
    
    $exists = mysql_result($r, 0);
    mysql_free_result($r);
    
    if ($exists) {
        $errmsg .= "'txn_id' has already been processed: ".$_POST['txn_id']."\n";
    }
    
    if (!empty($errmsg)) {
    
        // manually investigate errors from the fraud checking
        $body = "IPN failed fraud checks: \n$errmsg\n\n";
        $body .= $listener->getTextReport();
        mail('joe@bioshazard.com', 'IPN Fraud Warning', $body);
        
    } else {
    
        // add this order to a table of completed orders
        $payer_email = mysql_real_escape_string($_POST['payer_email']);
        $mc_gross = mysql_real_escape_string($_POST['mc_gross']);
        $sql = "INSERT INTO orders VALUES 
                (NULL, '$txn_id', '$payer_email', $mc_gross)";
        
        if (!mysql_query($sql)) {
            error_log(mysql_error());
            exit(0);
        }
        
        // send user an email with a link to their digital download
        $to = filter_var($_POST['payer_email'], FILTER_SANITIZE_EMAIL);
        $subject = "Your digital download is ready";
        mail($to, "Thank you for your order", "Download URL: ...");
        mail('joe@bioshazard.com', "Thank you for your order", "Download URL: ...");
    }
    
} else {
    // manually investigate the invalid IPN
    mail('joe@bioshazard.com', 'Invalid IPN', $listener->getTextReport());
}

fclose($log);

?>