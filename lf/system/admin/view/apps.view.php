<h2><a href="%appurl%">Site Manager</a></h2>

<div id="appgallery">

        <h3>App Gallery</h3>
        <p>Install Apps packaged as .zip files or <a href="%appurl%download/">Download Apps</a> from online.</p>
        <form enctype="multipart/form-data" action="%appurl%install/" method="post">
                <ul>
                        <li>
                                <input type="hidden" name="MAX_FILE_SIZE" value="55000000" />
                                Source: <input type="file" name="app" /><br />
                                <?=$install;?>
                        </li>
                </ul>
        </form>

        <p>Click on the name of an app to attach it to the website.</p>

        <style type="text/css">
                .left_header { float: left; }
                .right_header { float: right; }
                .left_header, .right_header { padding: 0 5px; }
                .left_header a, .right_header a { font-size: small; }
        </style>
        <ul class="applist">
        <?php
                foreach(scandir($pwd) as $file)
                {
                        if($file == '.' || $file == '..') continue;

                        $app = $pwd.'/'.$file;

                        if(is_dir($app)):
                                ?>
                                <li style="padding: 5px;">
                                        <div class="left_header">
                                                <a onclick="return confirm('Do you really want to delete this?');" href="%appurl%delapp/<?=$file;?>/">x</a>
                                        </div>

                                        <div class="right_header">
                                                <?php if(is_file($app.'/admin.php')): ?>
                                        <a href="%appurl%manage/<?=$file;?>/">Admin</a>
                                                <?php endif; ?>
                                                <?php if(is_file($app.'/index.php')): ?>
                                        <a href="%appurl%manage/<?=$file;?>/preview">Preview</a>
                                                <?php endif; ?>
                                                <?php /* if(is_dir($app.'/.git')): ?>
                                        <a href="%appurl%manage/<?=$file;?>/git">Git</a>
                                                <?php endif;*/ ?>
                                        </div>
                                        <div style="clear:both; padding: 5px;">
                                                <a href="%appurl%linkapp/<?=$file;?>/"><?=$file;?></a>
                                        <div>
                                </li>
                        <?php

                        endif;
                        if(isset($vars['app']) && $vars['app'] == $file)
                                $save = $file;
                }
        ?>
        </ul>
</div>

<script type="text/javascript">
/*
function apply_ajax()
{
	$(".nav_selector").click(function(){
	
		//$('.deselect_nav_form').click();
	
		var that = this;
		$.get(
			$(that).attr('href'),
			function(data){
				$(that).parent().html($(data).find('#nav_form').clone())
				apply_ajax();
			}
		);
		
		
		return false;
	});
	
	$('.deselect_nav_form').click(function(){
		var that = this;
		$.get(
			$(that).attr('href'),
			function(data){
				var nav_id = $(that).parent().parent().attr('id');
				
				$("#" + nav_id).html($(data).find("#" + nav_id).html());
				
				apply_ajax();
			}
		);
		
		
		return false;
	});
}

$(document).ready(function() {
	apply_ajax();
});*/


$(document).ready(function() {

	// Expand / Collapse
	$('#actions li ol').parent().prepend('<a href="#" class="toggle">+</a> ');
	$('#actions .toggle').click(function() {
		$(this).parent().find('>ol').toggle('fast');
	});

});
</script>

<div id="actions">
        <h3>Navigation</h3>
        <p>Navigation items are used to link apps to user requests in the URL and display their output in the selected skin.</p>
        <p>Nav from item: manage admin of (app)</p>
        <?php
                if(isset($nav['html']))
                {
                        echo $nav['html'];
                }
                else
                        echo '<p>- No nav set -</p>';
        ?>
        <h3>Hidden</h3>
        <p>Hidden nav items are used when you want to add a function, but don't want it on the nav menu.</p>
        <?php
                if(isset($hooks['html']))
                        echo $hooks['html'];
                else
                        echo '<p>- No hidden nav items set -</p>';
        ?>
</div><?php /*
<h3>Linked Apps</h3>
<p>Below is a list of apps currently linked to <?php echo isset($save['label']) ? 'the selected item on the right.' : 'all pages.' ;?></p>
<ul>
        <?=$apps;?>
</ul>
<h4>Link new app</h4>
<form action="%baseurl%menu/newapp/" method="post">
        <ul>
                <li>App: <?=$app_list;?> <input type="submit" value="New Link ..." />   <input type="hidden" name="include" value="<?php echo isset($save['id']) ? $save['id'] : '%' ;?>" /> </li>
        </ul>
</form>*/ ?>
