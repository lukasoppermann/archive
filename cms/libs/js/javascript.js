var base_url = "http://www:8888/cms";

$(document).ready(function(){
	$(".inlinelabel > input, .inlinelabel > textarea").inlinelabel();
	$("#user").focus();
	if($("select").length != 0)
	{	
		$('select').selectbox();
	}
	
	$('div.button input.copy').click(function(){
		alert('Zum verlinken den Pfad kopieren:'+"\n\n"+$(this).parents("form").children('input[name="full_path"]').val());
	});
	
	$("#type_container li").click(function(){
		var value = $("#type_input").val();
		var $options = $("#type option");
		$options.each(function(){
			if($(this).text() == value){
				if( !$("#entry_id").val() && !$("input[name='headline']").val() )
				{
					var template = $(this).val();
					top.location.href = base_url+'/de/entries/new-entry/'+template;
				}
			}
		});		
	});
	
	// $("div.one-link-box input.link-url").live('keydown keyup change blur focus', function(){
	// 	if($(this).val() && !$(this).parent().parent().next().hasClass('one-link-box'))
	// 	{
	// 		var count =  $("div.one-link-box").length + 1;
	// 		$(this).parents('.one-link-box').after('<div class="one-link-box"><div class="form-box input-field input inlinelabel"><label for="links[title]['+count+']">Name des Links</label><input type="text" class="link-title" name="links[title]['+count+']" /></div><div class="form-box input-field input inlinelabel"><label for="links[url]['+count+']">Link</label><input type="text" class="link-url" name="links[url]['+count+']" /></div></div>');
	// 		$(this).parents('.one-link-box').next().find(".inlinelabel > input").inlinelabel();
	// 	}
	// });
	
	$("div.one-link-box input.link-url").live('keydown keyup change blur focus', function(){
		if($(this).val() && !$(this).parent().parent().next().hasClass('one-link-box'))
		{
			var str = $(this).attr('name');
			var name = str.split('[')[0];	
			var count = $(this).parent().parent().parent().children("div.one-link-box").length;
			// alert($(this).parent().parent().parent().children("div.one-link-box").length);
			
			$(this).parents('.one-link-box').after('<div class="one-link-box"><div class="form-box input-field input inlinelabel"><label for="'+name+'[title]['+count+']">Name des Links</label><input type="text" class="link-title" name="'+name+'[title]['+count+']" /></div><div class="form-box input-field input inlinelabel"><label for="'+name+'[url]['+count+']">Link</label><input type="text" class="link-url" name="'+name+'[url]['+count+']" /></div></div>');
			$(this).parents('.one-link-box').next().find(".inlinelabel > input").inlinelabel();
		}
	});


});