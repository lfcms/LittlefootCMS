<style type="text/css" media="screen">
	#editor { 
		position: relative;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		height: <?=($linecount*16);?>px;
	}
</style>

Back to <a href="%appurl%">Skin Manager</a>
<h2>
	Edit Skin: <a href="%appurl%edit/<?=$matches[0];?>/"><?=$matches[0];?></a>
</h2>

<form action="%appurl%update/<?=$vars[1];?>/" method="post" id="skinform">
	<div class="row">
		<!-- New Skin -->
		<div class="col-3">
			<div id="skin_nav">
				<ul class="fvlist">
					<?php if(!is_file($skin.'/home.php')): ?>

					<li><a href="%appurl%makehome/<?=$matches[0];?>">(create home.php)</a></li>

					<?php endif;

					foreach($files as $id => $url):

						$li = '';
						$a = '';
						if($id == $vars[2]) $li = ' class="green"'; 
						if($id == $vars[2]) $a = ' class="light"'; 

					?>
						
					<li<?=$li;?>><a<?=$a;?> title="<?=$url;?>" href="%appurl%edit/<?=$matches[0];?>/<?=$id;?>/"><?=$url;?></a></li>

					<?php endforeach; ?>
					
				</ul>
				<input type="submit" class="martop blue" value="Save" />
			</div>
		</div>
		<!-- New Skin -->
		<div class="col-9">
			<div id="editor" class="dark_b"><?=htmlentities($data);?></div>
			<input type="submit" class="martop blue" value="Save" />
		</div>
	</div>
</form>

<script src="https://d1n0x3qji82z53.cloudfront.net/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
$(document).ready(function(){
	var editor = ace.edit("editor");
	
	editor.commands.addCommand({
		name: "unfind",
		bindKey: {
			win: "Ctrl-F",
			mac: "Command-F"
		},
		exec: function(editor, line) {
			return false;
		},
		readOnly: true
	})
	
	editor.setShowPrintMargin(false);
	editor.setTheme("ace/theme/textmate");
	editor.getSession().setMode("ace/mode/<?php echo $ext; ?>");
	editor.focus(); //To focus the ace editor
	
	$("#skinform").append('<textarea style="display: none" name="file" id="file" cols="30" rows="10"></textarea>');
	
	$("#skinform").submit(function(){
	
		$("textarea#file").val(editor.getValue());
		
		$("#skinform").append('<input type="hidden" id="hidden_ajax" name="ajax" value="true" />');
		
		//   var dataString = 'name='+ name + '&email=' + email + '&phone=' + phone;
		$.ajax({
		  type: "POST",
		  url: $("#skinform").attr("action") + '?json',
		  data: $("#skinform").serialize(),
		  success: function(data) {
			  console.log(data);
			$("#hidden_ajax").remove(); // unset ajax
			
			
			// account for debug...
			var result = /^(.*)(<|$)/.exec(data)
			data = result[1];
			
			if (data == "Session timed out") { alert ('Session timeout!'); }
			
			
			$("#skinform input[name=csrf_token]").val(data);
			
			//display message back to user here
			$(".ajax_message").remove();
			
			$('#skin_nav').append('<p class="ajax_message">Saved!</p>');
			
			$(".ajax_message")
				.hide()
				.slideToggle('slow')
				.slideToggle('slow');
			
			
			//$("#ajax_message").remove();
			
			
			/*$token = NoCSRF::generate( 'csrf_token' );
			$out = str_replace($match[0][$i], $match[0][$i].' <input type="hidden" name="csrf_token" value="'.$token.'" />', $out);*/
		  }  
		});  
		return false;  
	});
	
	
	/*$(window).scroll(function(){
	  if($(this).scrollTop() > 400$('#editor').position().top){
		$('#skin_nav').css({position:'fixed',top:10,left:10});
	  }else{
		$('#skin_nav').css({position:'relative'});
	  } 

	});*/
});
</script>