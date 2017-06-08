/*
 * cms.ajax.js - javascript for cms ajax actions
 */
$(document).ready(function(){

	var language 	= $("#lang_menu > .active").attr('value');	

	// Delete Item
	$(".icon-delete").click(function(){
		item 		= $(this);
		li_item 	= $(this).parents('li').filter(':first');
		id 			= li_item.attr('value');	
		title 		= item.siblings('.label').text();
		menu 		= $(this).parents(".cms").children("ul").attr('id');
		
		$.ajax({
			type: "POST",
			url: "/cms/de/ajax_actions/delete_menu",
			data: ({'value':id,'title':title,'language':language,'menu':menu }),
			success: function(data){
				$("#notices").css('opacity','1');
				$("#notices").html(data);
				$("#notices").children(".notice").delay(2000).fadeTo(1000, 0).slideUp(500);
				item_content = li_item.children("ul").html();
				li_item.children(".item").fadeTo(500, 0).slideUp(500);

				if(item_content != null)
				{
					li_item.replaceWith(li_item.children("ul").html());
				}	

			}
		});
		li_item.addClass("deleted");
	});


	// Edit Item
	$("old.edit").click(function(){
		menu = $(this).parents(".cms").children("ul").attr('id');		
		id = $(this).parents('li').filter(':first').attr('value');	
		label = $(this).prevAll("span.label").text();
		dialog_title = $(this).attr('title') + " ("+label+")";
		$.post("/cms/de/ajax_actions/menu_edit_form", {'value':id, 'menu':menu, 'language':language}, function(data){
			$("#ui-dialog-title-dialog-box").text(dialog_title);
			$("#dialog-box").html(data);
			$("#dialog-box").dialog("open");
			$('#dialog-box #label').alphanumeric({allow:"., ?!&+-_/\\"});
			$("#dialog-box #label").keyup(function(e){
				$("#dialog-box #path").val(
					$(this).val().replace(/[\ _\.\,\/\\\&\!\?]/g,'-')
				);
			});
			$("#dialog-box #label").change(function(){
				$("#dialog-box #path").val(
					$(this).val().replace(/[\ _\.\,\/\\\&\!\?]/g,'-')
				);
			});
			$(".button.save").click(function(){
				//
				var values = {};
				$('.js_form').each(function() {
					if($(this).is(':checkbox'))
					{
						if($(this).is(':checked')){
							values[this.name] = $(this).val();	
						}
					}
					else
					{
						values[this.name] = $(this).val();
					}
				});
				//
				$.ajax({
					type: "POST",
					url: "/cms/de/ajax_actions/edit_menu_item",
					data: ({'values':values, 'menu':menu, 'language':language}),
					success: function(data){
						$("#dialog-box").dialog("close");
						$("#notices").css('opacity','1');
						$("#notices").html(data);
						$("#notices").children(".notice").delay(2000).fadeTo(1000, 0).slideUp(500);
						item_content = $(this).parents('li').filter(':first').children("ul").html();
						$(this).parents('li').filter(':first').children(".item").fadeTo(500, 0).slideUp(500);

						if(item_content != null)
						{
							$(this).parents('li').filter(':first').replaceWith($(this).parents('li').filter(':first').children("ul").html());
						}	

					}
				});
			});
			$(".cancel.cancel").click(function(){
				$("#dialog-box").dialog("close");
			});
			$("#alias").click(function(){
				$("#path, #path_select").attr('disabled','')
			},
			function(){
				$("#path, #path_select").attr('disabled','disabled');
			});
			$('.select').selectmenu({style:'popup'});
		});
	});
	// Add Item

	// add_form = $.ajax({type:"GET",url:"../views/forms/add_menu.php"}).responseText; 
	$("old.add").click(function(){
 		menu = $(this).parents(".cms").children("ul").attr('id');		
		id = $(this).parents('li').filter(':first').attr('value');	
		title = $(this).attr('title');
		if(id == null)
		{
			position = $(this).parents(".cms").children("ul").children("li").length + 1;
		}
		else
		{
			position = $("#item_"+id).children("ul").children("li").length + 1;	
		}
			
		$.post("/cms/de/ajax_actions/menu_add_form", {'value':id}, function(data){
			$("#ui-dialog-title-dialog-box").text(title);
			$("#dialog-box").html(data);
			$("#dialog-box").dialog("open");
			$('#dialog-box #label').alphanumeric({allow:"., ?!&+-_/\\"});
			$("#dialog-box #label").keyup(function(e){
				$("#dialog-box #path").val(
					$(this).val().replace(/[\ _\.\,\/\\\&\!\?]/g,'-')
				);
			});
			$("#dialog-box #label").change(function(){
				$("#dialog-box #path").val(
					$(this).val().replace(/[\ _\.\,\/\\\&\!\?]/g,'-')
				);
			});
			$(".button.save").click(function(){
				//
				var values = {};
				$('.js_form').each(function() {
					if($(this).is(':checkbox'))
					{
						if($(this).is(':checked')){
							values[this.name] = $(this).val();	
						}
					}
					else
					{
						values[this.name] = $(this).val();
					}
				});
				//
				$.ajax({
					type: "POST",
					url: "/cms/de/ajax_actions/add_menu_item",
					data: ({'values':values, 'menu':menu, 'language':language, 'position':position}),
					success: function(data){
						$("#dialog-box").dialog("close");
						$("#notices").css('opacity','1');
						$("#notices").html(data);
						$("#notices").children(".notice").delay(2000).fadeTo(1000, 0).slideUp(500);
						item_content = $(this).parents('li').filter(':first').children("ul").html();
						$(this).parents('li').filter(':first').children(".item").fadeTo(500, 0).slideUp(500);

						if(item_content != null)
						{
							$(this).parents('li').filter(':first').replaceWith($(this).parents('li').filter(':first').children("ul").html());
						}	

					}
				});
			});
			$(".cancel.cancel").click(function(){
				$("#dialog-box").dialog("close");
			});
			$("#alias").click(function(){
				$("#path, #path_select").attr('disabled','')
			},
			function(){
				$("#path, #path_select").attr('disabled','disabled');
			});
			$('.select').selectmenu({style:'popup'});
		});
	});

	// $("#dialog.input-field").val( ,function);
	// Hide Notices
	$(".notice").delay(4000).fadeTo(1000, 0).slideUp(500);
	

});