<!-- LF Debug Info

	Version: <?=$this->version;?>

	PHP Execution Time: <?=$exectime;?>ms
	Peak Memory Usage: <?=$memusage;?> MB
	SQL Queries: <?=$this->db->getNumQueries();?>

	Load Times:

	<?php foreach($this->timer as $function => $time): ?>
		<?=round($time, 6)*(1000);?>ms - <?=$function;?>
		
	<?php endforeach; ?>

	Included, Required files:

	<?php foreach(get_included_files() as $included): ?>
		<?=$included;?>	
	<?php endforeach; ?>
-->