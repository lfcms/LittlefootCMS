<html>
	<head>
		<title>lf Admin | <?=$_SERVER['SERVER_NAME'];?></title>
		<style type="text/css">
			html { overflow-y: scroll; } /*thnx to http://www.sitepoint.com/forums/showthread.php?638348-Stopping-the-scrollbar-from-moving-my-whole-page*/
		
			body { font-family: Arial;  padding: 0 10px; margin: 0; background: #FFF }
			#wrapper { width: 950px; margin: 0 auto; background: #FFF }
			/*#header { border-bottom: 1px solid #CCC; }*/
			ul, ol {list-style: none; padding: 0; margin: 0}
			
			h1 { background: none; margin: 0; padding: 5px 0; color: #333; float: left; }
			
			#login { float: right; margin: 15px 5px 15px 0; }
			#login a { text-decoration: underline; color: #00F; }

			
			/*
			li ul { display: none; } 
			li:hover > ul { display: block; position:relative; }*/
			
			a { text-decoration: none; color: #009; }
			
			#content { clear: both; display: block; margin: 0 auto; }
			textarea { overflow: auto; }
			
			form ul li { padding: 0; }
			
			#actions { display: block; width: 100%; float: left; }
			#actions ul, #actions ol { padding: 0px; margin: 0px; /*border: 1px solid #000;*/ border-right: none; margin-top: 0px;}
			#actions li { 	background: #FFF; border-left: #CCC 1px solid;/* border-bottom: #CCC 1px solid;*/ padding: 10px; margin: 0}
			#actions li span { display: block; float: right; font-size: 14px; }
			
			#edit { padding: 10px; margin: 10px; margin-left: 300px; }
			
			.clear { clear: both; display: block; } 
			p, h2, h3, h4, h5 { padding: 5px 0; margin: 0; } 
			#conns { float: left; }
			form li { margin: 0px; border: none !important; }
			form { padding: 0; margin: 0; font-size: 12px; /*border-bottom: 1px solid #000;*/ }
			hr { color: #000; }
			
			#testasdf ul { padding: 10px !important}
			#testasdf li { padding: 10px 0 !important}
			li.selected ol { background: #FFF !important; }
			
			.left { clear:both; float: left; }
			.right { float: right; }
			.short { width: 360px; }
			hr { color: #000;background: #000;height: 1px; }
			input, select { border: #CCC solid 1px; background: #FFF; }
			#apps_save { margin: 10px 10px 0 0; }
			p { font-size: 12px; }
			.selected { background: #ADF !important; }
			textarea { width: 100%; height: 80%; background: #555; color: #FFF; }
			
			.items ol { margin: 0; padding: 0; }
			.items li { border: 1px solid #CCC; margin: 5px 0 0 0px; padding: 5px; }
			
			.border { border-top: 1px solid #CCC; padding-right: 5px; }
			
			#appgallery { }
			#appgallery li { border: #000 1px solid; margin-bottom: 5px; margin-left: 5px; padding: 5px 0; width: 150px; float: left; }
			#appgallery li a.x { border-right: 1px solid #000; padding: 6px; padding-right: 10px; background: #DDD }
			
			
			fieldset { width: 600px; margin: 10px 0 10px 10px; border: 1px solid #000; }
			legend { border: 1px solid #000; padding: 5px; }
			
			#nav { display: block; width: 100%; padding: 10px 0;margin: 0; background: #333; }
			#nav li { display: inline; padding-left: 5px;}
			#nav li a { color: white; text-decoration: none; padding: 5px;}
			#nav li a:hover { background: #DDD; color: black; }
			
		</style> 
        
        <!-- Including The jQuery Library -->
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	</head>
	<body>
		<?php if($_SESSION['upgrade']) 
			echo '<a style="font-family: Arial; display: block; padding: 10px; background: #DDF" href="'.$this->base.'upgrade">Upgrade to '.$_SESSION['upgrade'].' available!</a>'; ?>
		<div id="wrapper">
			<div id="header">
				<h1>LittleFoot Admin</h1>
				<div id="login">
					Hello <?=$this->auth['display_name'];?>. (<a href="<?=$this->relbase;?>?logout=true">Logout</a>)
				</div>
				<div class="clear"></div>
				<?=$nav;?>
			</div>
			<div id="content">
				<?=$app;?>
				<div class="clear"></div>
			</div>
	</body>
</html>