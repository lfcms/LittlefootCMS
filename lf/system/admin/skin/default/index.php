<!DOCTYPE html>
<html class="lf" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,user-scalable=1" name="viewport">
		<title><?php echo $_SERVER['HTTP_HOST']; ?> | Littlefoot CMS Admin</title>
		<meta name="description" content="Littlefoot CMS was deigned to help webmasters create websites and integrate custom apps easily and efficiently."/>
		<meta name="keywords" content="cms, content management system, website, web development, web design, littlefoot, littlefoot cms" />
		
		<link href="<?=$this->getSkinBase();?>css/custom.css" rel="stylesheet">
		<!-- <link href="<?=$this->getSkinBase();?>css/styles.css" rel="stylesheet"> -->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>

		<!-- Load in jQuery for handy hover function | Removes titles of links on hover-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				// All links in main menu div
				var menu_links = $('ul.navigation a');
				// On mouse hover
				menu_links.hover(
					// In: Store and remove title
					function() {
						old_title = $(this).attr('title');
						$(this).attr('title','');
					},
					// Out: Replace title
					function() {
						$(this).attr('title', old_title);
					}
				);
			});
		</script>
	</head>

	<body class="off-white contain">
		<div class="wrapper userbar dark_gray light light_bb scroll-y">
			<nav class="mobile_nav">
				<input type="checkbox" id="mobile-nav" name="mobile-nav" class="dropdown marbot" />
				<label for="mobile-nav" class="dark_gray">
						<span id="admin_title_mobile pull-left">
							<img class="fit-font icon pull-left left_space martop" src="<?=\lf\requestGet('LfUrl');?>system/template/images/lf-icon-white-transparent.png"/> 
							<a id="site_preview" class="pull-left" href="<?=\lf\requestGet('IndexUrl');?>" target="blank_"><?=\lf\requestGet('IndexUrl');?></a>
						</span>
						<span class="open-content pull-right light pad fxlarge"><i class="fa fa-bars"></i></i></span>
						<span class="close-content pull-right red_fg pad fxlarge"><i class="fa fa-bars"></i></span>
						<div class="drop-content clear">
								<nav class="vlist dark_gray">
									<?=$this->printContent('nav');?>
								</nav>
						</div>
				</label>
			</nav>
			<div class="wide_container h50">
				<div class="row no_martop no_marbot">
					<div class="col-12">
						<div>
							<span id="admin_title">
								<img class="fit-font icon pull-left martop" src="<?=\lf\requestGet('LfUrl');?>system/template/images/lf-icon-white-transparent.png"/> 
								<a id="site_preview" class="pull-left left_space" href="<?=\lf\requestGet('IndexUrl');?>" target="blank_"><?=\lf\requestGet('IndexUrl');?></a>
							</span>
						
							<span id="logout_button" class="pull-right">
								<a class="x" href="<?=\lf\requestGet('IndexUrl');?>_auth/logout" title="Sign Out"><i class="fa fa-sign-out"></i></a>
							</span>
							<span id="admin_greeting" class="pull-right right_space">
								Hello, <?=(new \lf\user)
											->fromSession()
											->getDisplay_name();?>.
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="wide_container admin_drop">
			<div class="row no_martop">
				<div class="col-2 dark_gray no_pad marbot">
					<nav class="admin_main_nav">
						<?=$this->printContent('nav');?>
						<footer>
							<span class="gray_a">
								<a href="http://littlefootcms.com">Powered by &copy; Littlefoot</a>
							</span>
						</footer>
					</nav>
				</div>
				<div class="col-10">
					<div id="controller-<?php \lf\requestGet('ActionUrl'); ?>">
						<?=$this->printContent('content');?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
