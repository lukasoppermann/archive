$(document).ready(function() {
	
	var filter = {};
	$('div#entry_filter ul:not(#filter-all)').each(function(){
		tmp = $(this).attr("id");
		filter[tmp] = new Array();
	});
	
	$("div#entry_filter .current").each(function(){
		var var_class = $(this).children().attr('class');
		var group = $(this).parent().attr("id");
		filter[group].push(var_class);
	});
	
	$('div#entry_filter a').click(function() {

		var group = $(this).parent().parent().attr("id");

		if($(this).parent().hasClass('current'))
		{
			$(this).parent().removeClass('current');
			var idx = filter[group].indexOf($(this).attr('class')); // Find the index
			if(idx!=-1) filter[group].splice(idx, 1); // Remove it if really found!	
			$.cookie($(this).attr('class'), null);
		}
		else
		{	
			if(group != "filter-all"){		
				$(this).parent().addClass('current');
				$.cookie($(this).attr('class'), true);
				filter[group].push($(this).attr('class'));
			}
		}
		
		if($(this).attr('class') == 'all') {
			$('#entries_overview .item.hidden').fadeIn('slow').removeClass('hidden');
			
			$(".current").parent().each(function(){
				filter[$(this).attr("id")] = [];
			});
			$(".current").removeClass("current").each(function(){
				$.cookie($(this).children().attr('class'), null);
			});
		} else {			
			$('#entries_overview .item').each(function() {
				
			var show = false;
			var hidden = false;
			var $item = $(this);
				
				$.each(filter, function(index, values){
					if(values.length > 0)
					{
						show = false;
						$.each(values, function(index, value){
							if($item.hasClass(value) || value == null) 
							{				
								show = true;
								return;
							}
						});
						if(show == false)
						{
							hidden = true;
						}
					}
				});
				
				if(hidden == true) {
					$item.fadeOut('normal').addClass('hidden');
				} else {
					$item.fadeIn('slow').removeClass('hidden');
				}
			});
		}
		
		$("div.items").each(function()
		{
			if($(this).children("div.item:not(.hidden)").length == 0)
			{
				$(this).prev().fadeOut('normal').addClass('hidden');
				$(this).fadeOut('normal').addClass('hidden');
			}
			else
			{
				$(this).prev(".hidden").fadeIn('fast').removeClass('hidden');
				$(this).fadeIn('fast').removeClass('hidden');
			}
		});

		
		return false;
	});
	
	
});