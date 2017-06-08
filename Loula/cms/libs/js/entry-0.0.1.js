$(function(){
	// -------------------
	// save form	
	$(".save").on('click', '.button', function(e){
		var _this = $(this);
		var _saved = _this.siblings('.last-saved');
		var now = new Date();
		var saved_time = now.getHours()+':'+now.getMinutes();
		// set last saved
		_saved.text(saved_time);
		setTimeout(function(){_saved.text('just now');}, 0);
		setTimeout(function(){_saved.text(saved_time);}, 60000);
		// ajax call to save entry
		$.ajax({
			url: CI_ROOT+'ajax/entry/save',            
            type: "POST",
            dataType: 'json',
            data: $("#entry_form").serializer(),
			beforeSend: function(){
				$('#save').addClass('working').text('saving ...');
			},
            success: function(r)
            {
				$('#save').removeClass('working').text('save changes');
				if(r != undefined && r.twitter != undefined && r.twitter == false)
				{
					$('#tw_post').find('.check').removeClass('checked').addClass('crossed');
					$('#tw_post').find('.hidden-elements').find('[name=twitter]').attr('checked','');
				}
            }
		});
	});
	// -------------------
	// change status
	$(".status").on('click', '.btn', function(btn){
		var _this = $(this);
		var _hidden = _this.siblings('.hidden-elements');
		if(!_this.hasClass('active'))
		{
			_this.siblings('.active').removeClass('active');
			_this.addClass('active');
			_hidden.find('input').attr("checked", false);
			_hidden.find('.'+_this.data('value')).attr("checked", true);
			//
			if(_this.data('value') == "publish")
			{
				$("#datepicker").removeAttr('disabled');
			}
			else
			{
				$("#datepicker").attr('disabled','disabled');
			}
		}
	});
	// -------------------
	// change type
	$("#type").change(function(){
		var _this = $(this);
		var val = _this.val();
		//
		if(val == '2')
		{
			$('.product').show();
			$('#fb_text_button').removeClass('active btn-left');
			$('.tab.facebook').removeClass('active');
		}
		else
		{
			$('.product').hide();
			$('#fb_text_button').addClass('active btn-left');
			$('.tab.facebook').addClass('active');
		}
	});
	$("#type").change();
	// -------------------
	// change social media	
	$(".social-media").on('click', '.check-button', function(){
		var _this = $(this);
		var _check = _this.find('.check');
		var _checkbox = _this.find('.hidden-elements').find('input');
		if(_check.hasClass('crossed'))
		{
			_check.toggleClass('checked crossed');
			_checkbox.attr('checked', 'checked');
		}
		else
		{
			_check.toggleClass('checked crossed');
			_checkbox.attr('checked', false);				
		}
		_this.addClass('clickhover');
	});
	$(".social-media").on('mouseleave', '.check-button', function(){
		$(this).removeClass('clickhover');
	});
	// -------------------
	// delete
	$("#entries").on('click', '.delete a', function(){
		var _this = $(this);
		var _item = _this.parents('.item');
		// send ajax request
		$.ajax({
			url: CI_ROOT+'ajax/entry/delete',             
            type: "POST",
            dataType: 'json',
			data: {"id":_item.data('id')},
            success: function(r)
            {
				_item.find('.status').removeClass('status-'+_item.data('status')).addClass('status-3');
				_item.data('status', '3');
            }
		});
		// return false
		return false;
	});
	// -------------------
	// delete from within entry
	$(".delete-entry").on('click', function(){
		// send ajax request
		$.ajax({
			url: CI_ROOT+'ajax/entry/delete',             
            type: "POST",
            dataType: 'json',
			data: {"id":$("#id").val()},
            success: function(r)
            {
				window.location = CI_ROOT+'content/list';
            }
		});
		// return false
		return false;
	});
	// ---------------------------------------------------------------------
	// Filter Entries
	$('.filter-list').fs_filter();
	// ---------------------------
	$('.filter-list').on('click', 'li', function()
	{
		var _list = $('.filter-list');
		if(_list.find('.active').size() == 1 && _list.find('#deleted').hasClass('active'))
		{
			$("#remove_articles").fadeIn();
		}
		else
		{
			$("#remove_articles").fadeOut();		
		}
	} );	
	// ---------------------------
	// delete articles
	$('#delete').click(function(){
		// send ajax request
		$.ajax({
			url: CI_ROOT+'ajax/entry/trash',             
            type: "POST",
            dataType: 'json',
            success: function(r)
            {
				if(r.id != false && r.id != null && r.id != undefined)
				{
					$("ul").find("[data-id='" + r.id +"']").fadeOut().remove();
				}
				else
				{
					$("ul").find("[data-status='3']").fadeOut(function(){
						$(this).remove();
					});
				}
            }
		});
	});
	// --------------------------------------------------
	// date picker
	$("#datepicker").datepicker({
				showButtonPanel: true,
				dateFormat: 'dd/mm/yy'
	});
	$("#ui-datepicker-div").css("marginTop", "+=80");
	// --------------------------------------------------
	// date range picker	
	var dates = $( "#sales_start, #sales_end" ).datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 1,
		dateFormat: 'dd/mm/yy',
		onSelect: function( selectedDate ) {
			var option = this.id == "sales_start" ? "minDate" : "maxDate",
				instance = $( this ).data( "datepicker" ),
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
			dates.not( this ).datepicker( "option", option, date );
		}
	});
	// --------------------------------------------------
	// changing product type
	$("#product_type").on('change', function(){
		var _sizes = $('#product_sizes'); 
		if(_sizes.val() == '')
		{
			_sizes.val($('#product_type_edit').find('option[data-tag='+$(this).find('option:selected').val()+']').data('sizes'));
		}
	});
	// ------------------------------------------------------------------------------------------------------------------
	// open window to add/edit designer / type
	$(".add-button").on('click', function(){
		var _edit = $(this).next();
		_edit.children('select').val(_edit.children('select').find("option:first").val());
		_edit.find('.label').val('Label');
		_edit.find('.sizes').val('Default sizes separated by ","');
		_edit.find('.position').val(_edit.find('.position').find("option:last-child").val());
		_edit.toggleClass('active');
	});
	// -------------------------------------
	// changing select menu
	$(".edit-window").on('change', '.category-select', function()
	{
		var _edit = $(this).parents('.edit-window');
		var _selected = _edit.find("option:selected");
		if( _selected.val() == 'add_new')
		{
			_edit.find('.label').val('Label');
			_edit.find('.sizes').val('Default sizes separated by ","');
			_edit.find('.position').val(_edit.find('.position').find("option:last-child").val());
		}
		else
		{
			_edit.find('.label').val(_selected.data('label'));
			_edit.find('.sizes').val(_selected.data('sizes'));
			_edit.find('.position').val(_selected.data('position'));
		}
	});
	// -------------------------------------
	// changing label in input field
	var inputText = null;
	$(".edit-window").find('input[type=text]').on('focus', function(){
		var _edit = $(this).parents('.edit-window');
		if(_edit.find("option:selected").val() == 'add_new')
		{
			inputText = $(this).val();
			$(this).val('');
		}
	});
	$(".edit-window").find('input[type=text]').on('blur', function(){
		var _edit = $(this).parents('.edit-window');
		if(_edit.find("option:selected").val() == 'add_new')
		{
			if($(this).val() == '')
			{
				$(this).val(inputText);
			}
			inputText = '';
		}
	});
	// -------------------------------------
	// save type / designer
	$(".edit-window").on('click', '.save', function()
	{
		var _edit = $(this).parents('.edit-window');
		var label = _edit.find('.label').val();
		// add new item
		if(_edit.find("option:selected").val() == 'add_new')
		{
			if(label != null && label != 'Label')
			{
				$.ajax({
					url: CI_ROOT+'ajax/data/add',             
					type: "POST",
					data: {'label': label, 'sizes': _edit.find('.sizes').val(), 'type': _edit.data('type'), 'position': _edit.find(".position option:selected").val(), 'last_pos': _edit.find('.position').find("option:last-child").val()},
					dataType: 'json',
					success: function(r)
					{
						if(r.success == 'true')
						{
							$.ajax({
								url: CI_ROOT+'ajax/data/get/'+_edit.data('type'),
								dataType: 'json',
								success: function(res)
								{
									_edit.find('.category-select').html(res.edit);
									_edit.parents('div').siblings('select').html(res.normal);
								}
							});
							_edit.toggleClass('active');
						}
		            }
				});
			}
		}
		// edit item
		else
		{
			if(label != null && label != 'Label')
			{
				$.ajax({
					url: CI_ROOT+'ajax/data/edit',             
					type: "POST",
					data: {'label': label, 'sizes': _edit.find('.sizes').val(), 'type': _edit.data('type'), 'position': _edit.find(".position option:selected").val(), 'id' : _edit.find("option:selected").val()},
					dataType: 'json',
					success: function(r)
					{
						if(r.success == 'true')
						{
							$.ajax({
								url: CI_ROOT+'ajax/data/get/'+_edit.data('type'),
								dataType: 'json',
								success: function(res)
								{
									_edit.find('.category-select').html(res.edit);
									_edit.parents('div').siblings('select').html(res.normal);
								}
							});
							_edit.toggleClass('active');
						}
		            }
				});
			}
		}
		return false
	});
	// -------------------------------------
	// delete type / designer
	$(".edit-window").on('click', '.delete', function()
	{
		var _edit = $(this).parents('.edit-window');
		if(_edit.find("option:selected").val() != 'add_new')
		{
			$.ajax({
				url: CI_ROOT+'ajax/data/delete',             
				type: "POST",
				data: {'id': _edit.find("option:selected").val(), 'position': _edit.find(".position option:selected").val(), 'last_pos': _edit.find('.position').find("option:last-child").val(), 'type': _edit.data('type')},
				dataType: 'json',
				success: function()
				{
					$.ajax({
						url: CI_ROOT+'ajax/data/get/'+_edit.data('type'),
						dataType: 'json',
						success: function(res)
						{
							_edit.find('.category-select').html(res.edit);
							_edit.parents('div').siblings('select').html(res.normal);
						}
					});
					_edit.toggleClass('active');
	            }
			});
		}
		else
		{
			_edit.toggleClass('active');
		}
		return false
	});
});
