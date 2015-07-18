<!DOCTYPE html>
<html class="lf" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,user-scalable=1" name="viewport">
		<title><?php echo $_SERVER['HTTP_HOST']; ?> | Littlefoot CMS Admin</title>
		<meta name="description" content="Littlefoot CMS was deigned to help webmasters create websites and integrate custom apps easily and efficiently."/>
		<meta name="keywords" content="cms, content management system, website, web development, web design, littlefoot, littlefoot cms" />
		<!-- Le styles -->
		<link href="%relbase%lf/system/lib/lf.css" rel="stylesheet">
		<link href="%relbase%lf/system/lib/3rdparty/icons.css" rel="stylesheet">
		<link href="%skinbase%css/custom.css" rel="stylesheet">
		<!-- <link href="%skinbase%css/styles.css" rel="stylesheet"> -->
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
		</script (http://december.com/html/4/element/script.html)>
	</head>

	<body>
		<div class="wrapper userbar h50 dark_gray light">
			<div class="wide_container">
				<div class="row no_martop no_marbot">
					<div class="userbar">
						<div class="col-12">
							<img class="fit-font icon pull-left martop" src="%relbase%lf/system/template/images/lf-icon-white-transparent.png"/> 
							<a id="site_preview" class="pull-left" href="%relbase%" target="blank_"><?=$this->domain;?>%relbase%</a>
						<?php if($_SESSION['upgrade']): ?>
							<span id="upgrade" class="pull-left">
								<a class="blue button" href="%baseurl%settings/">Upgrade Now!</a>
							</span>
						<?php endif; ?>
							<span id="logout_button" class="pull-right">
								<a class="x" href="%baseurl%_auth/logout" title="Sign Out"><i class="fa fa-sign-out"></i></a>
							</span>
							<span id="admin_greeting" class="pull-right">
								Hello <?=$this->api('me');?>.
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="wide_container">
			<div class="row">
				<div class="col-2">
					<nav>
						<?php /*pre($this->content); */
						echo implode($this->content['%nav%']); ?>
					</nav>
				</div>
				<div class="col-10">
					<div id="controller-<?php echo $this->lf->action[0]; ?>">
						<?php echo implode($this->content['%content%']); ?>
					</div>
				</div>
			</div>
		</div>
		<footer>
			<div class="wide_container">
				powered by &copy; <a href="http://littlefootcms.com">littlefoot</a>
			</div>
		</footer>
	</body>
</html>
