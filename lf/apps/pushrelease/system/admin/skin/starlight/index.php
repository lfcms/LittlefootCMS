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
                
     	</div><!--mainwrapperinner-->
    </div><!--mainwrapper-->
	<!-- END OF MAIN CONTENT -->
    

</body>
</html>