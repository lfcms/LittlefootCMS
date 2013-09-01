/*

	Table of Contents
	1. //#jsWindow


*/

hypervisor = {
	dom: "",
	request: "",
	debug: true,
	functest: 
		function() 
		{ 
			alert("functest"); 
		}
};

$(document).ready(
	function()
	{
		$("#content form").submit(
			function()
			{
				$.post(
					$(this).attr("action") + "&mode=ajax",
					$(this).serialize(),
					function(data)
					{
						var obj = JSON.parse(data)
						
						if(obj.success != true && hypervisor.debug)
							alert(data);
						
						if('rm' in obj)
						{
							$("#" + obj.rm).remove();
						}
						if('add' in obj && 'data' in obj)
						{
							if(obj.add = 'threads')
							{
								$("#" + obj.add).prepend(obj.data);
							}
							else
							{
								$("#" + obj.add).append(obj.data);
							}
						}
						if('refresh' in obj)
						{
							window.location = document.location.href;
						}
					}
				)
				
				$(':input','.add_post')
					.not(':button, :submit, :reset, :hidden')
					.val('') 
					.removeAttr('checked')
					.removeAttr('selected');
					
				$(':input','.add_thread')
					.not(':button, :submit, :reset, :hidden')
					.val('') 
					.removeAttr('checked')
					.removeAttr('selected');
				
				return false;
			}
		);
		
		$("a.hrefapi").click( 
			function()
			{
				$.get(
					$(this).attr("href") + "&mode=ajax",
					function(data)
					{
						var obj = JSON.parse(data)
						if(obj.success != true && hypervisor.debug)
							alert(data);
						if('rm' in obj)
						{
							$("#" + obj.rm).remove();
						}
						
						if('add' in obj && 'data' in obj)
						{
							if(obj.add = 'threads')
							{
								$("#" + obj.add).prepend(obj.data);
							}
							else
							{
								$("#" + obj.add).append(obj.data);
							}
						}
						
						if('refresh' in obj)
						{
							window.location = document.location.href;
						}
					}
				);
				return false;
			}
		);
	}
);

/*

Need to make a single API call every 5 seconds that will cover all page updates

use json to handle the return, apply id tags to each peice of content that needs to be updated




function applyAJAX()
{
	$(".like").click(
		function()
		{
			$.post(
				"http://bioshazard.com/dev/aios/api.php?mode=ajax", 
				"cmd=like&what="+$(this).attr("ref"),
				function(data) {
					
				}
			);
			return false
		}
	);
	$(".unlike").click(
		function()
		{
			$.post(
				"http://bioshazard.com/dev/aios/api.php?mode=ajax", 
				"cmd=unlike&what="+$(this).attr("ref"),
				function(data) {
					
				}
			);
			return false
		}
	);
	
	$(".removepost").submit(
		function()
		{
			$.post(
				"http://bioshazard.com/dev/aios/api.php?mode=ajax", 
				$(this).serialize()
			);
			$(this).parent().remove();
			
			return false;
		}
	);
	$(".removethread").submit(
		function()
		{
			$.post(
				"http://bioshazard.com/dev/aios/api.php?mode=ajax", 
				$(this).serialize()
			);			
			 $(this).parent().parent().remove(); 
			return false;
		}
	);
}

function ajaxeverything()
{ 

	
	$(".add_post").submit(
		function()
		{
			$(this).parent().find(".msg_0").remove();
			
			if($(this).find("input[name=input]").val() == '') return false;
			$(this).parent().find("ul").append('<li class="pending"><a href="http://bioshazard.com/dev/aios/wall">'+$(this).find("input[name=display]").val() + '</a> ' + $('<div/>').text($(this).find("input[name=input]").val()).html()	+ '</li>');
			$(this).parent().find("ul").attr({ scrollTop: $(this).parent().find("ul").attr("scrollHeight") });
			
			$.post(
				"http://bioshazard.com/dev/aios/api.php?mode=ajax", 
				$(this).serialize()
			);
			
			// http://stackoverflow.com/questions/680241/blank-out-a-form-with-jquery
			$(':input','.add_post')
				.not(':button, :submit, :reset, :hidden')
				.val('') 
				.removeAttr('checked')
				.removeAttr('selected');
			
			return false;
		} 
	);
	$(".chat_input").submit(
		function()
		{
			
				
			$.post(
				"http://bioshazard.com/dev/aios/api.php?mode=ajax", 
				$(this).serialize()
				
			);
			$(this).find("input[type=text]").val("");
				
			return false;
		}
	);
	applyAJAX();
}

function updatePage()
{
	
	// $_POST user id, action, thread, input to api for thread updates
	$.get(
		document.location.href, // this is what breaks the jsWindow thing
		function(data)
		{
			//alert(data);
			$("#friendslist").html($(data).find("#friendslist").html()); 
			$("#chat").html($(data).find("#chat").html())
			
			var cur_obj = "";
			var get_obj = "";
			
			if($(".thread").length < $(data).find(".thread").length)
				window.location = document.location.href;
				
			cur_obj = $(".thread ul");
			get_obj = $(data).find(".thread ul");
			
			for(var i = 0; i < cur_obj.length; i++)
				$(cur_obj[i]).html($(get_obj[i]).html());
			
			cur_obj = $(".thread div");
			get_obj = $(data).find(".thread div");
			
			for(var i = 0; i < cur_obj.length; i++)
				$(cur_obj[i]).html($(get_obj[i]).html());
			
			applyAJAX();
		}
	);
}

$(document).ready(
	function()
	{
		$.post(
			"/dev/aios/newapi.php",
			"cmd=jsReplace&widget=" + $(".jsReplace").attr("widget"),
			function(data)
			{
				$(".jsReplace").html(data);		
				$(".jsReplace").attr("class","jsWindow");
			}
		);
		ajaxeverything();
	
		// this.after(//sibling txt)
		
		var update = setInterval("updatePage()", 5000);
		
		$(".add_thread").submit(
			function()
			{
				var input = $(this).find("input[name=input]").val();
				if(input == '') return false;
				
				$.post(
					"http://bioshazard.com/dev/aios/api.php?mode=ajax", 
					$(this).serialize(),
					function(data) {
						
					}
				);
				
				$(':input','.add_thread')
					.not(':button, :submit, :reset, :hidden')
					.val('') 
					.removeAttr('checked')
					.removeAttr('selected');
				
				return false;
			}
		)
		
		$(".hrefapi").click( 
			function()
			{
				$.post(
					"http://bioshazard.com/dev/aios/api.php?mode=ajax",
					$(this).attr("data"),
					function(data)
					{
						alert(data);
					}
				);
				window.location = document.location.href;
				return false;
			}
		);
		
		$("#admin form").submit(
			function()
			{
				$(':input','.add_thread')
					.not(':button, :submit, :reset, :hidden')
					.val('') 
					.removeAttr('checked')
					.removeAttr('selected');
					
				$.post(
					$(this).attr("action") + "?mode=ajax",
					$(this).serialize()
				);
				
				window.location = document.location.href;
				return false;
			}
		);
	}
);*/