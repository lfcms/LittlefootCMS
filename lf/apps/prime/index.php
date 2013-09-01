
<h2>Prime Numbers</h2>
<?php

$start = microtime(true);

$limit = 1000;

if(isset($_GET['limit']))
	$limit = $_GET['limit'];
	
/* Prime Generator v2, divide by previous primes and track powers of 2*/
$primelist = array();
$nonprime = array();
ob_start();
for($i = 2; $i < $limit; $i++)
{	
	if(in_array($i, $nonprime))	continue;
	
	$prime = true;
	
	foreach($primelist as $primetest)
		if($i % $primetest == 0)
		{
			$prime = false;
			break;
		}
		
	if($prime)
	{
		//echo implode(', ', $primelist).': ';
		
		foreach($primelist as $primetest)
		{
			$nonprime[] = $i * $primetest;
			//echo ($i * $primetest).', ';
		}
		
		$primelist[] = $i;
	}
	
	//echo '<br />';
}
$numpyramid = ob_get_clean();
	
	
/* Prime Generator v1, divide by previous primes
$primelist = array();

for($i = 2; $i < $limit; $i++)
{	
	$prime = true;
	foreach($primelist as $primetest)
		if($i % $primetest == 0)
		{
			$prime = false;
			break;
		}
		
	if($prime)
		$primelist[] = $i;
}*/

echo '<div style="float: right">'.$numpyramid.'</div>';

echo 'New Limit: <form action="?"><input type="text" name="limit" value="'.$limit.'"/><input type="submit" value="Submit" /></form>';
echo 'Executed in '.round((microtime(true) - $start) * 1000, 2).'ms <br />';
echo 'Total Primes found: '.count($primelist).'<br />';
echo implode('<br />', $primelist);
