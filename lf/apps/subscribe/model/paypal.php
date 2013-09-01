<?php

require_once('paypal.class.php'); // include the class file

$p             = new paypal_class; // initiate an instance
$p->paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr"; //test url

if ($p->validate_ipn()) {
	file_put_contents('p2.txt', var_dump($p->ipn_data));
	$subject = 'Instant Payment Notification - Recieved Payment';
	$to      = 'joe@bioshazard.com';
	$body    = "An instant payment notification was successfully recieved\n";
	$body .= "from " . $p->ipn_data['payer_email'] . " on " . date('m/d/Y');
	$body .= "\n\nDetails:\n";
	foreach ($p->ipn_data as $key => $value) {
		$body .= "\n$key: $value";
	}
	@mail($to, $subject, $body);
}


/*
//'https://www.paypal.com/cgi-bin/webscr'; // paypal url
$this_script   = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
// if no action variable, set 'process' as default action
if (empty($_GET['ipn']))
    $_GET['action'] = 'ipn';
switch ($_GET['action']) { //356818546
    case 'process': // Process and order...
        $p->add_field('business', 'joe@bioshazard.com');
        $p->add_field('return', $this_script . '?action=success');
        $p->add_field('cancel_return', $this_script . '?action=cancel');
        $p->add_field('notify_url', $this_script . '?action=ipn');
        $p->add_field('item_name', 'Test Invoice'); // 'ITEM NAME'
        $p->add_field('amount', '100'); // 'ITEM AMOUNT'
        $p->add_field('currency_code', 'USD'); //CURRENCY VALUE USD/EUR…
        $p->submit_paypal_post(); // submit the fields to paypal
        break;
    case 'success': // successful order...
        echo "<html>
<head><title>Success....</title></head>
<body>
<h2>Thank you for your order!</h2>";
        foreach ($_POST as $key => $value) {
            echo "$key: $value<br>";
        }
        echo "</body></html>";
        break;
    case 'cancel': // Canceled Order...
        echo "<html>
<head><title>Canceled</title></head>
<body><h2>The order was canceled.</h2>";
        echo "</body></html>";
        break;
    case 'ipn': // For IPN validation...
        if ($p->validate_ipn()) {
            $subject = 'Instant Payment Notification - Recieved Payment';
            $to      = 'joe@bioshazard.com';
            $body    = "An instant payment notification was successfully recieved\n";
            $body .= "from " . $p->ipn_data['payer_email'] . " on " . date('m/d/Y');
            $body .= "\n\nDetails:\n";
            foreach ($p->ipn_data as $key => $value) {
                $body .= "\n$key: $value";
            }
            @mail($to, $subject, $body);
        }
        break;
}*/


?>