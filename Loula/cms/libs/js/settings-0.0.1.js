$(function(){
	// -------------------
	// save form	
	$(".save").on('click', '.button', function(e){
		var _this = $(this);
		// ajax call to save entry
		$.ajax({
			url: CI_ROOT+'ajax/settings/save',            
	        type: "POST",
	        dataType: 'json',
	        data: $(".form").serializer(),
	        success: function(r)
	        {
				$("#messages").remove();
				if(r.error != null)
				{
					$(".form-container").prepend('<div id="messages">'+r.error+"</div>");
					$(".error").delay(1000).fadeOut();					
				}
				else if(r.success != null)
				{
					$(".form-container").prepend('<div id="messages">'+r.success+"</div>");
					$(".success").delay(1000).fadeOut();					
				}
	        }
		});
	});
	// -------------------
	// twitter_connect
	$("#settings_social_media").on('mouseenter', '.active', function(){
		$(this).text($(this).data('disconnect'));
	});
	$("#settings_social_media").on('mouseleave', '.active', function(){
		$(this).text($(this).data('connect'));
	});
	// -------------------
	// add user
	$("#add_user").on('click', function(){
		// ajax call to save entry
		$.ajax({
			url: CI_ROOT+'ajax/settings/save',            
	        type: "POST",
	        dataType: 'json',
	        data: $(".form").serializer(),
	        success: function(r)
	        {
				$("#messages").remove();
				if(r.error != null)
				{
					$(".form-container").prepend('<div id="messages">'+r.error+"</div>");
					$(".error").delay(1000).fadeOut();					
				}
				else if(r.success != null)
				{
					$(".form-container").prepend('<div id="messages">'+r.success+"</div>");
					$(".success").delay(1000).fadeOut();					
				}
	        }
		});	
	});
});