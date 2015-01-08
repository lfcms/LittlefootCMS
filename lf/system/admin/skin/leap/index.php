<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php echo $_SERVER['HTTP_HOST']; ?> | Littlefoot CMS Admin</title>
		<meta name="description" content="Littlefoot CMS was deigned to help webmasters create websites and integrate custom apps easily and efficiently."/>
		<meta name="keywords" content="cms, content management system, website, web development, web design, littlefoot, littlefoot cms" />
		<!-- Le styles -->
		<link href="%relbase%lf/system/lib/littlefoot.css" rel="stylesheet">
		<link href="%skinbase%css/styles.css" rel="stylesheet">
		<link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">

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
		<!-- <header class="container">
			<?php 
							
				if($_SESSION['upgrade']) 
					echo '<a href="%baseurl%settings/lfup/">new littlefoot version available! </a>'; 
								
			?>
		</header> -->
		<div class="wrapper userbar dark_gray light light_gray_a">
			<div class="wide_container">
				<div class="row no_martop no_marbot">
					<div class="col-2">
						<a id="admin_title" href="<?=$this->base;?>">lf admin</a>
					</div>
					<div class="col-7">
						<a id="site_preview" href="%relbase%" target="blank_"><?=$this->domain;?>%relbase%</a>
					</div>
					<div class="col-3">
						<span id="logout_button" class="pull-right light_gray_a">
							<a class="x" href="%baseurl%_auth/logout">logout</a>
						</span>
						<span id="admin_greeting" class="pull-right">
							Hello <?=$this->auth['display_name'];?>.
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="wide_container">
			<div class="row">
				<div class="col-2">
					<nav class="efvlist">
						<?php echo $nav; ?>
					</nav>
				</div>
				<div class="col-10">
					<div id="app-<?php echo $class; ?>">
						<?php echo $app; ?>
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
