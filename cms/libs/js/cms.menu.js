$(document).ready(function(){
	// -----------------
	// variables
	// language
	var language = $("#lang_menu li.active").attr("value");
	var base_url = "http://www:8888/cms";
	// -----------------
	// icons
	// delete	
	$("span.delete").live('mouseenter mouseleave', function(){
		$(this).siblings("span.label").toggleClass('strike');
	});
	// -----------------	
	// Rearrange
	$('ul.sortable').nestedSortable({
		forcePlaceholderSize: true,
		disableNesting: 'no-nesting',
		handle: 'div.item',
		items: 'li',
		opacity: .6,
		placeholder: 'placeholder',
		tabSize: 5,
		tolerance: 'pointer',
		toleranceElement: '> div',
		listType: 'ul',
		errorClass: 'notice error',
		connectWith: 'ul.sortable',
		dropOnEmpty: true,
		cancel: '.edit, .delete, .add',
		helper: function( event, ui ) {
			return $("<div class='helper rounded'><div style='padding: 10px;'>"+$(ui).find("> div > span.label").text()+"</div></div>");
		},
		remove: function(event) { 
			if($(this).children("li").length == 0)
			{
				$(this).addClass("empty");
			}
		},
		receive: function(event) {
			$(this).removeClass("empty");
		},
		update: function(event, ui) {
			items 		= $(this).sortable("myToArray");
			menu		= $(this).attr("menu_id");
			$.ajax({
				type: "POST",
				url: base_url+"/de/ajax/update",
				data: ({'items':items,'language':language,'menu':menu}),
				success: function(data){
					$("#notices").css('opacity','1');
					$("#notices").html(data);
					$("#notices").children(".notice").delay(2000).fadeTo(1000, 0).slideUp(500);
					$('ul > li.no-nesting, ul ul > li.no-nesting, ul ul ul > li.no-nesting').removeClass('no-nesting');
					$('ul ul ul ul li').addClass('no-nesting');
				}
			});
		}
	});
	// -----------------	
	// Delete item
	$("ul li span.delete").live('click', function(){
		// -----------------
		// define variables
		$item = $(this).parents("div.item");
		$menu = $(this).parents("ul.menu-edit");
		// -----------------
		// ajax
		$.ajax({
			type: "POST",
			url: base_url+"/de/ajax/delete",
			data: ({'id':$item.attr("value"),'language':language,'menu':$menu.attr("menu_id")}),
			success: function(data){
					$item.fadeTo(500, 0).slideUp(500, function(){
						$parent_li = $item.parent("li");
						$parent_li.replaceWith($item.siblings("ul").html());
					});
					$item.siblings("ul").animate({"marginLeft": "0px"}, "fast");
					$menu.addClass('empty');
			}
		});
	});

	// -----------------	
	// Add item	
     $('ul.sortable > li > div >span.add, ul.level_1 > li > div >span.add, ul.level_2 > li > div >span.add, div.menu-edit-box > h4 > span.add').live('click', function() {
		// -----------------
		// define variables
		$add = $(this);
		// get id
		id = $(this).parents("div.item").attr("value");
		//
		$menu = $add.closest(".menu-edit-box").children(".menu-edit");
		// -----------------		
		$('#dialog_box').reveal({
				animation: 'fadeAndPop',			//fade, fadeAndPop, none
				animationspeed: 300, 				//how fast animtions are
				closeonbackgroundclick: false,		//if you click background will modal close?
				dismissmodalclass: 'close-dialog'	//the class of a button or element that will close an open modal
		});
			$.post(base_url+"/ajax/template/forms:menu_add_form", {'value':id, 'language':language,'menu':$menu.attr("menu_id")}, function(resp){
				$("#dialog_box p").html(resp.html);

				$("#dialog_box #label").focus().add_replace($("#dialog_box #path"),'','',resp.path+'/');		
				$("#dialog_box #path").filter_input({regex:'[a-zA-Z0-9_\/-]'});
				
				$(".inlinelabel > input, .inlinelabel > textarea").inlinelabel();

				// Toggle
				$("span.input-toggle").toggle(
					function(){
						$(this).prev("input").removeAttr("disabled").focus().select();
						$(this).parent("div").removeClass("disabled");
						$('input#type').val('1');						
					},
					function() 
					{ 
					    $(this).prev("input").attr("disabled", true); 
						$(this).parent("div").addClass("disabled");
						$('input#type').val('0');				
					}
				);
				
				// -----------------
				$("#add_item").submit(function () { return false; });
				$("#add_item input:submit").click(function(){
					// menu
					
					// values
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
					// position
					if(id == null)
					{
						position = $add.closest(".menu-edit-box").children(".menu-edit").children("li").length + 1;
					}
					else
					{
						position = $("#item_"+id).next("ul").children("li").length + 1;	
					}
					// -----------------
					
					$.ajax({
						type: "POST",
						url: base_url+"/ajax/add",
						data: ({'values':values, 'menu':$menu.attr("menu_id"), 'language':language, 'position':position}),
						dataType: "json",
						success: function(data){
							$("#dialog_box").reveal("close");
							// -----------------
							if(data.success == 'true' && data.parent_id == 0)
							{
								$menu.append('<li><div class="item rounded" value="'+data.id+'" id="item_'+data.id+'"><span title="move" class="icon move float-left"></span><span class="label">'+values['label']+'</span><span title="add" class="icon add float-right"></span><span title="edit" class="icon edit float-right"></span><span title="delete" class="icon delete float-right"></span></div></li>');
							}
							else
							{
								if($("#item_"+data.parent_id).next("ul").children("li").length > 0)
								{
									$("#item_"+data.parent_id).next("ul").append('<li><div class="item rounded" value="'+data.id+'" id="item_'+data.id+'"><span title="move" class="icon move float-left"></span><span class="label">'+values['label']+'</span><span title="add" class="icon add float-right"></span><span title="edit" class="icon edit float-right"></span><span title="delete" class="icon delete float-right"></span></div></li>');
								}
								else
								{
									var level = 0;
									$("#item_"+data.parent_id).each(function() {
									  var cur = $(this).parents("ul").length;
									  if (cur > level) level = cur;
									});
									$("#item_"+data.parent_id).after('<ul class="level_'+level+'"><li><div class="item rounded" value="'+data.id+'" id="item_'+data.id+'"><span title="move" class="icon move float-left"></span><span class="label">'+values['label']+'</span><span title="add" class="icon add float-right"></span><span title="edit" class="icon edit float-right"></span><span title="delete" class="icon delete float-right"></span></div></li></ul>');
								}
							}
							
							
							item_content = $(this).parents('li').filter(':first').children("ul").html();
							$(this).parents('li').filter(':first').children(".item").fadeTo(500, 0).slideUp(500);

							if(item_content != null)
							{
								$(this).parents('li').filter(':first').replaceWith($(this).parents('li').filter(':first').children("ul").html());
							}
							$('ul.level_3 > li').addClass('no-nesting');
						}
					});
					// -----------------
					
				});
			}, "json");
     });
	
	// -----------------	
	// Edit item	
	$('span.edit').live('click', function() {
		// -----------------
		// define variables
		$edit = $(this);
		// menu
		$menu = $edit.closest(".menu-edit-box").children(".menu-edit");
		// get id
		id = $(this).parents("div.item").attr("value");
		// -----------------
		$('#dialog_box').reveal({
				animation: 'fadeAndPop',			//fade, fadeAndPop, none
				animationspeed: 300, 				//how fast animtions are
				closeonbackgroundclick: false,		//if you click background will modal close?
				dismissmodalclass: 'close-dialog'	//the class of a button or element that will close an open modal
			});		
		// send request
		req = $.ajax(base_url+'/ajax/form/form_menu_edit', {
            data : { id: id, headline: 'edit_menu_item', menu: $menu.attr("menu_id"), language: language },
            dataType : 'json',
            type : 'POST'
        });
		// on success
	    req.success(function(resp) {
			$('#dialog_box > h3 > span.title').text(resp.title);
			$("#dialog_box p").html(resp.html);
			
			$("#dialog_box #label").focus().add_replace($("#dialog_box #path"),'','',resp.path+'/');		
			$("#dialog_box #path").filter_input({regex:'[a-zA-Z0-9_\/-]'});
			
			$(".inlinelabel > input, .inlinelabel > textarea").inlinelabel();
			// -----------------
			// check for type
			if( $('input#type').val() == '1' )
			{
				$('input#path').removeAttr("disabled");
				$('input#path').parent("div").removeClass("disabled");				
			}
			// Toggle
			$("span.input-toggle").toggle(
				function(){
					$(this).prev("input").removeAttr("disabled").focus().select();
					$(this).parent("div").removeClass("disabled");
					$('input#type').val('1');						
				},
				function() 
				{ 
				    $(this).prev("input").attr("disabled", true); 
					$(this).parent("div").addClass("disabled");
					$('input#type').val('0');				
				}
			);
			// -----------------
			$("#edit_item input:submit").bind('click submit', function (event) { 
				event.preventDefault(); 
				// -----------------
				// get values
				var values = {};
				$('#edit_item input.js_form').each(function() {
					values[this.name] = $(this).val();	
				});
				// -----------------
				// send submit
				submit = $.ajax(base_url+'/ajax/edit', {
		            data : { id: id, values: values, menu: $menu.attr("menu_id"), language: language },
		            dataType : 'json',
		            type : 'POST'
		        });
				// -----------------
				// on submit success
				submit.success(function(resp){
					$("#dialog_box").reveal("close");
					$("#item_"+resp.id+" span.label").text(resp.label);
				});
			});
	    });
		// -----------------
		
	});
	
	$(".inlinelabel > input, .inlinelabel > textarea").inlinelabel();
	// -----------------	
	// End of document.ready	
});