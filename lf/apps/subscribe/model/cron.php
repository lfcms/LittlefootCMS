<?php

if(!isset($argv[1]) || !preg_match('/^(monthly|annually|late)$/', $argv[1], $cycle)) die('Usage: cron.php (monthly|annually|late)');

include '../../../config.php';
include '../../../system/db.class.php';

$db = new Database($db);

if($argv[1] == 'late')
{
	$late = $db->fetchall("
		SELECT p.id, p.client_id, p.title, p.amount, p.cycle, i.due, i.status, i.id as inv_id
		FROM subscribe_invoice i
		LEFT JOIN subscribe_pay p
			ON p.id = i.plan_id
		WHERE i.status = 'Open'
		AND NOW() > i.due
	");
	
	foreach($late as $suspend)
	{
		$db->query("
			UPDATE subscribe_invoice
			SET status = 'Late'
			WHERE id = ".intval($suspend['id'])."
		");
	}
	
	exit();
}

$plans = $db->fetchall("
	SELECT id
	FROM subscribe_pay
	WHERE cycle = '".$cycle[1]."'
");

foreach($plans as $plan)
{
	// Create invoice
	$db->query("
		INSERT INTO subscribe_invoice 
		VALUES (NULL, ".$plan['id'].", 0, CURDATE() + INTERVAL 10 DAY, 'Open')
	");
}

?>