$(document).ready(function(){

	// form validation
	$("#form1").validate({
	 		submitHandler: function(form) 
	 		{
		   		$('#form1').ajaxSubmit({
				url: 'includes/php/function.php', 
				type: 'post', 
				data: {task: 'send_email'}, 
				success: function(responseText){
					$('#notice_box').html(responseText);
					document.getElementById('form1').reset();
					genCaptcha();
				}
				});
	   		},
	   		invalidHandler:function(form)
	   		{
		   		$('#notice_box').html('<div class="alert">Wrong information</div>');	
	   		},
		     messages: 
		     {
			     name: "<span class='error'>Missing</span>",
			     email: {
			       required:"<span class='error'>Missing</span>",
			       email: "<span class='error'>(invalid email)</span>"
			     },
			     
			     url: {
			       
			       url: "<span class='error'>(invalid url)</span>"
			     },
			     subject: "<span class='error'>Missing</span>",
			     message: "<span class='error'>Missing</span>",
			     ver_code: "<span class='error'>Missing</span>"
			       
			  }
		    
		});
		
	// get captcha image    
	genCaptcha();
		
	// google map
	var map = null;
  	var geocoder = null;
	  if (GBrowserIsCompatible()) {
	    map = new GMap2(document.getElementById("map_canvas"));
	
	    map.setUIToDefault();
	    geocoder = new GClientGeocoder();
	    address = 'Kastanjestraat, Helchteren, Limburg, Vlaams Gewest, Belgium';
	    
	    geocoder.getLatLng(
	      	address,
	      		function(point) 
	      		{
	        		if (!point) 
	        		{
	          			alert(address + " not found");
	        		}
	        		else
	        		{
	        		 	map.setCenter(point, 15);
	          			var marker = new GMarker(point, {draggable: true});
	          			map.addOverlay(marker);
	          			GEvent.addListener(marker, "dragend", function() {
	            		marker.openInfoWindowHtml(marker.getLatLng().toUrlValue(6));
	          			});
	          			     GEvent.addListener(marker, "click", function() {
	                marker.openInfoWindowHtml(address);
	              });
		      		GEvent.trigger(marker, "click");
	          			
					}
	        	}
	      	)
	  	}
  	
  	$("div#map_canvas").toggle(); 
		$("a.show_map span.show").show();
		$("a.show_map span.hide").hide();
		// When a.show_map is clicked...
		$("a.show_map").click( 
			function () {
				$(this).parent().find("div#map_canvas").slideToggle("slow"); 
				$("a.show_map span.show").toggle();
				$("a.show_map span.hide").toggle();
				return false;
			}
		);		
			
});

function genCaptcha()
{
 	$.post("includes/php/function.php", { task:'gen_image'},  
				function(data, textStatus){
					document.getElementById('image_td').innerHTML = data;
					fixBase64();
				}
	);
}
function fixBase64(img) 
{
	var BASE64_DATA = /^data:.*;base64/i;
	var base64Path = "includes/php/base64.php";  
	img = document.getElementById('ver_code_image');
	if (BASE64_DATA.test(img.src)) 
	{    
		img.src = base64Path + "?" + img.src.slice(5);  
	}
}