<style type="text/css">
	#motd { width: 50%; float: left; }
	#agenda { margin-left: 50%; }
</style>
<div id="motd"><?=$wiki;?></div>
<div id="agenda">
	<?=$this->lf->extmvc($this->inst.'/calendar', 'hq/calendar', $this->inst, array('agenda'));?>
</div>