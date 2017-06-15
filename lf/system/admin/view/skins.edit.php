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

<h2 title="Use the text editor to update your html and css files."><i class="fa fa-edit"></i> Edit Skin: <a href="%appurl%edit/<?=$matches[0];?>/"><?=$matches[0];?></a></h2>
<span><a href="%appurl%" title="Back to Skins Manager"><i class="fa fa-arrow-left" aria-hidden="true"></i> Skins Manager</a></span>

<form action="%appurl%update/<?=$vars[1];?>/" method="post" id="skinform">
	<div class="row">
		<!-- New Skin -->
		<div class="col-3 pull-right">
			<div class="tile white">
				<div class="tile-header">
					<h3 title="Select the file you wish to edit. You can use home.php to create a custom home page.">
						<i class="fa fa-file-code-o"></i> Files
					</h3>
				</div>
				<div class="row no_martop no_marbot">
					<div class="col-12">
						<ul class="fvlist white">
							<?php if(!is_file($skin.'/home.php')): ?>

							<li><a href="%appurl%makehome/<?=$matches[0];?>">(create home.php)</a></li>

							<?php endif;

							foreach($files as $id => $url):

								$li = '';
								$a = '';
								if($id == $vars[2]) $li = ' class="blue"'; 
								if($id == $vars[2]) $a = ' class="light"'; 

							?>

							<li<?=$li;?>><a<?=$a;?> title="<?=$url;?>" href="%appurl%edit/<?=$matches[0];?>/<?=$id;?>/"><?=$url;?></a></li>

							<?php endforeach; ?>

						</ul>
					</div>
				</div>
				<div class="tile-content">
					<div class="row">
						<div class="col-12">
							<input type="submit" class="green" value="Save" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- New Skin -->
		<div class="col-9">
			<div id="editor" class="light_b white"><?=htmlentities($data);?></div>
			<input type="submit" class="martop green" value="Save" />
		</div>
	</div>
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.5/ace.js" type="text/javascript" charset="utf-8"></script>
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