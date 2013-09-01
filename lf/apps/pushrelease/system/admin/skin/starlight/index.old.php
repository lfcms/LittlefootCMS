<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Dashboard | Starlight Premium Admin Template</title>
<link rel="stylesheet" href="%skinbase%css/style.css" type="text/css" />
<link rel="stylesheet" href="%skinbase%css/custom.css" type="text/css" />
<!--[if IE 9]>
    <link rel="stylesheet" media="screen" href="css/ie9.css"/>
<![endif]-->

<!--[if IE 8]>
    <link rel="stylesheet" media="screen" href="css/ie8.css"/>
<![endif]-->

<!--[if IE 7]>
    <link rel="stylesheet" media="screen" href="css/ie7.css"/>
<![endif]-->
<script type="text/javascript" src="%skinbase%js/plugins/jquery-1.7.min.js"></script>
<script type="text/javascript" src="%skinbase%js/plugins/jquery.flot.min.js"></script>
<script type="text/javascript" src="%skinbase%js/plugins/jquery.flot.resize.min.js"></script>
<script type="text/javascript" src="%skinbase%js/plugins/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="%skinbase%js/custom/general.js"></script>
<script type="text/javascript" src="%skinbase%js/custom/dashboard.js"></script>
<!--[if lt IE 9]>
	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="%skinbase%js/plugins/excanvas.min.js"></script><![endif]-->
</head>

<body class="loggedin">

	<!-- START OF HEADER -->
	<div class="header radius3">
    	<div class="headerinner">
        	
            <!--<a href=""><img src="%skinbase%images/starlight_admin_template_logo_small.png" alt="" /></a>-->
			<a href="" class="title" style="font-size: 32px; color: white; font-family: 'BebasNeueRegular', Arial, Helvetica, sans-serif">LittleFoot</a>
			
            
              
            <div class="headright">
            	<div class="headercolumn">&nbsp;</div>
            	<div id="searchPanel" class="headercolumn">
                	<div class="searchbox">
                        <form action="" method="post">
                            <input type="text" id="keyword" name="keyword" class="radius2" value="Search here" /> 
                        </form>
                    </div><!--searchbox-->
                </div><!--headercolumn-->
            	<div id="notiPanel" class="headercolumn">
                    <div class="notiwrapper">
                        <a href="%skinbase%ajax/messages.php" class="notialert radius2">5</a>
                        <div class="notibox">
                            <ul class="tabmenu">
                                <li class="current"><a href="%skinbase%ajax/messages.php" class="msg">Messages (2)</a></li>
                                <li><a href="%skinbase%ajax/activities.php" class="act">Recent Activity (3)</a></li>
                            </ul>
                            <br clear="all" />
                            <div class="loader"><img src="%skinbase%images/loaders/loader3.gif" alt="Loading Icon" /> Loading...</div>
                            <div class="noticontent"></div><!--noticontent-->
                        </div><!--notibox-->
                    </div><!--notiwrapper-->
                </div><!--headercolumn-->
                <div id="userPanel" class="headercolumn">
                    <a href="" class="userinfo radius2">
                        <img src="%skinbase%images/avatar.png" alt="" class="radius2" />
                        <span><strong><?=$this->auth['display_name'];?></strong></span>
                    </a>
                    <div class="userdrop">
                        <ul>
                            <li><a href="">Profile</a></li>
                            <li><a href="">Account Settings</a></li>
                            <li><a href="<?=$this->relbase;?>?logout=true">Logout</a></li>
                        </ul>
                    </div><!--userdrop-->
                </div><!--headercolumn-->
            </div><!--headright-->
        
        </div><!--headerinner-->
	</div><!--header-->
    <!-- END OF HEADER -->
        
    <!-- START OF MAIN CONTENT -->
    <div class="mainwrapper">
     	<div class="mainwrapperinner">
         	
        <div class="mainleft">
          	<div class="mainleftinner">
            
              	<div class="leftmenu">
				
				<?php echo $nav; ?>
				   
                </div><!--leftmenu-->
            	<div id="togglemenuleft"><a></a></div>
            </div><!--mainleftinner-->
        </div><!--mainleft-->
        
        <div class="maincontent">
        	<div class="maincontentinner">
            	<ul class="maintabmenu">
                	<li class="current"><a href="#"><?php echo $class; ?></a></li>
                </ul><!--maintabmenu-->
				
                <div class="content">
				
					<?php echo $app; ?>
					<div style="clear: both" />
                </div><!--content-->
            </div><!--maincontentinner-->
            
            <div class="footer">
            	<p>Starlight Admin Template &copy; 2012. All Rights Reserved. Designed by: <a href="">ThemePixels.com</a></p>
            </div><!--footer-->
            
        </div><!--maincontent-->
        
        <div class="mainright">
        	<div class="mainrightinner">
            	
                <div class="widgetbox">
                	<div class="title"><h2 class="chart"><span>Visitors Overview</span></h2></div>
                    <div class="chartbox widgetcontent">
                    	<div id="chartplace" class="chartplace"></div>
                        
                        <div class="one_half">
                        	<div class="analytics analytics2">
                                <small>Visitors For Today</small>
                                <h1>23 321</h1>
                                <small>18,222 unique</small>
                            </div><!--visitoday-->
                        </div><!--one_half-->
                        
                        <div class="one_half last">
                        	
                            <div class="one_half">
                            	<div class="analytics">
                                    <small>bounce</small>
                                    <h3>23.2%</h3>
                                </div><!--analytics-->
                            </div><!--one_half-->
                            
                            <div class="one_half last">
                            	<div class="analytics textright">
                                    <small class="block">visitors</small>
                                    <h3>56.8%</h3>
                                </div><!--analytics-->
                            </div><!--one_half last-->
                            <br clear="all" />
                            
                            <div class="analytics average margintop10">
                                Average <h3>87.44%</h3>
                            </div><!--analytics-->
                            
                        </div><!--one_half-->
                        
                        
                        <br clear="all" />
                    </div><!--widgetcontent-->
                </div><!--widgetbox-->
                
                
                <div class="widgetbox">
                	<div class="title"><h2 class="calendar"><span>Event Calendar</span></h2></div>
                    <div class="widgetcontent padding0">
                    	<div id="datepicker"></div>
                    </div><!--widgetcontent-->
                </div><!--widgetbox-->
                
                <div class="widgetbox">
                	<div class="title"><h2 class="tabbed"><span>Tabbed Widget</span></h2></div>
                    <div class="widgetcontent padding0">
                    	<div id="tabs">
                        	<ul>
                                <li><a href="#tabs-1">Products</a></li>
                                <li><a href="#tabs-2">Posts</a></li>
                                <li><a href="#tabs-3">Media</a></li>
                            </ul>
                            <div id="tabs-1">
                                <ul class="listthumb">
                                	<li><img src="%skinbase%images/thumb/small/thumb1.png" alt="" /> <a href="">Proin elit arcu, rutrum commodo</a></li>
                                    <li><img src="%skinbase%images/thumb/small/thumb2.png" alt="" /> <a href="">Aenean tempor ullamcorper leo</a></li>
                                    <li><img src="%skinbase%images/thumb/small/thumb3.png" alt="" /> <a href="">Vehicula tempus, commodo a, risus</a></li>
                                    <li><img src="%skinbase%images/thumb/small/thumb4.png" alt="" /> <a href="">Donec sollicitudin mi sit amet mauris</a></li>
                                    <li><img src="%skinbase%images/thumb/small/thumb5.png" alt="" /> <a href="">Curabitur nec arcu</a></li>
                                </ul>
                            </div>
                            <div id="tabs-2">
                                <ul>
                                	<li><a href="">Proin elit arcu, rutrum commodo</a></li>
                                    <li><a href="">Aenean tempor ullamcorper leo</a></li>
                                    <li><a href="">Vehicula tempus, commodo a, risus</a></li>
                                    <li><a href="">Donec sollicitudin mi sit amet mauris</a></li>
                                    <li><a href="">Curabitur nec arcu</a></li>
                                </ul>
                            </div>
                            <div id="tabs-3">
                                <ul class="thumb">
                                	<li><a href="#"><img src="%skinbase%images/thumb/xsmall/thumb1.png" alt="" /></a></li>
                                    <li><a href="#"><img src="%skinbase%images/thumb/xsmall/thumb2.png" alt="" /></a></li>
                                    <li><a href="#"><img src="%skinbase%images/thumb/xsmall/thumb3.png" alt="" /></a></li>
                                    <li><a href="#"><img src="%skinbase%images/thumb/xsmall/thumb4.png" alt="" /></a></li>
                                    <li><a href="#"><img src="%skinbase%images/thumb/xsmall/thumb5.png" alt="" /></a></li>
                                    <li><a href="#"><img src="%skinbase%images/thumb/xsmall/thumb6.png" alt="" /></a></li>
                                    <li><a href="#"><img src="%skinbase%images/thumb/xsmall/thumb7.png" alt="" /></a></li>
                                    <li><a href="#"><img src="%skinbase%images/thumb/xsmall/thumb8.png" alt="" /></a></li>
                                </ul>     
                        	</div>
                        </div><!--#tabs-->
                    </div><!--widgetcontent-->
                </div><!--widgetbox-->
                
            </div><!--mainrightinner-->
        </div><!--mainright-->
                
     	</div><!--mainwrapperinner-->
    </div><!--mainwrapper-->
	<!-- END OF MAIN CONTENT -->
    

</body>
</html>