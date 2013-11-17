<!DOCTYPE html>
<html lang="en">
        <head>
                <meta charset="utf-8">
                <title><?php echo $_SERVER['HTTP_HOST']; ?> | Littlefoot CMS Admin</title>
                <meta name="description" content="Littlefoot CMS was deigned to help webmasters create websites and integrate custom apps easily and efficiently."/>
                <meta name="keywords" content="cms, content management system, website, web development, web design, littlefoot, littlefoot cms" />
                <!-- Le styles -->
                <link href="%skinbase%css/styles.css" rel="stylesheet">
                <link href="%skinbase%css/nav.css" rel="stylesheet">

                <!-- Load in jQuery for handy hover function | Removes titles of links on hover-->
                <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>
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
                <div class="userbar">
                        <header>
                            <h1>LittlefootCMS</h1>
                        <div id="greeting">
                                Hello <?=$this->auth['display_name'];?>. (<a href="<?=$this->base;?>_auth/logout">Logout</a>)
                        </div>
                </div>
                <div class="container">
                        <nav>
                                <?php echo $nav; ?>
                        </nav>
                        <div class="content">
                                <div id="app-<?php echo $class; ?>" class="wrapper">
                                                <?php echo $app; ?>
                                </div>
                                <div class="clear"></div>
                        </div>
                        <footer>
                                <div class="footer-credits">
                                        Powered by &copy; <a href="http://littlefootcms.com">LittlefootCMS</a>
                                </div>
                        </footer>
                </div>
        </body>
</html>
