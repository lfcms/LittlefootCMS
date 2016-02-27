<html>
	<head>
		<title><?=$this->getTitle();?></title>
		<link rel="stylesheet" type="text/css" href="<?=$this->getSkinBase();?>/css/styles.css" />
	</head>
	<body>
		<h1>Blank Template</h1>
		<?=$this->printLogin();?>
		<br />
		<?=$this->printContent('nav');?>
		<br />
		<div><?=$this->printContent('content');?></div>
	</body>
</html>