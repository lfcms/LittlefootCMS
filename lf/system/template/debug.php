<!-- LF Debug Info

Version: <?=$this->version;?> 
PHP Execution Time: <?=$exectime;?>ms
Peak Memory Usage: <?=$memusage;?> MB

Load Times (in the order they are first called):

<?php foreach( (new \lf\cache)->getTimerResults() as $function => $time): ?>
	<?=sprintf("%.03f", round($time, 6)*(1000));?>ms 	<?=$function;?>
	
<?php endforeach; ?>

Included, Required files:

<?php foreach(get_included_files() as $included): ?>
	<?=$included;?>	
<?php endforeach; ?>

-->