// once jquery is loaded and DOM is ready
$(function()
{
	// ---------------------
	// delete
	$('.entry-list').on('click', '.delete', function(e){
		e.stopPropagation();
		var _item = $(this).parents('.item');
		$.ajax({
			type : 'post',
			data: {'id':_item.data('id'),'status':$(this).parents('.item').data('status')},
			url: CI_BASE+'tickets/delete',
			dataType: 'text',
			success: function(response)
			{
				console.log(response);
				if( response == 'deleted' )
				{
					_item.fadeOut(300, function(){
						$(this).remove();
					});
				}
				else
				{
					_item.appendTo(_item.parents('.group').find('.deleted-list'));
					_item.find('[name=status] option[value=3]').attr('selected', true);
					var new_status = 3;
					_item.find('.status').removeClass('status-'+_item.data('status')).addClass('status-'+new_status);
					_item.data('status',new_status);
				}
			}
		});
	});
	// ---------------------
	// show deleted entries
	$('body').on('click', '.deleted-entries', function(e){
		var _this = $(this);
		var text = _this.text();
		// change text
		_this.text(_this.data('text')).data('text', text).toggleClass('deleted');
		// toggle lists
		_this.parents('.group').find('.entry-list').each(function(){
			$(this).toggleClass('hidden');
		});
		// prevent link from working
		e.preventDefault();	
	});
	// ---------------------
	// expand
	$('.entry-list').on('click', '.item', function(){
		//
		var _item = $(this);
		var active = _item.hasClass('open');
		//
		if( active != true )
		{
			if($('.open').size() > 0)
			{
				$('.open').find('.info').animate({'height':0}, 200, function(){
					$('.open').removeClass('open').find('.customer-name').attr('contenteditable', false);
					$('.open').find('.time-span').removeClass('time-span');
					_item.addClass('open');
					_item.find('.info').animate({'height':_item.find('.columns').height()}, 300, function(){
						_item.find('.info').height('auto');
					});
					_item.find('.customer-name').attr('contenteditable', true);
					_item.find('.time').find('span.date').addClass('time-span');
					// ---------------------
					// datepicker
					_item.find('.time-span').DatePicker({
						mode: 'single',
						showOn: 'click',
						onBeforeShow: function(el){
							if( $(this).text().trim() != "set date" ) 
							{
								$(this).DatePickerSetDate($(this).text(), true);
							}
							else
							{
								var today = new Date();
								$(this).DatePickerSetDate(today, true);
							}
					  	},
						onChange: function(date, el) {
							$(el).text(date.getDate()+'/'+(date.getMonth()+1)+'/'+date.getFullYear());
							$(el).DatePickerHide();
						}
					});
				});
			}
			else
			{
				_item.addClass('open');
				_item.find('.info').animate({'height':_item.find('.columns').height()});
				_item.find('.info').animate({'height':_item.find('.columns').height()}, 300, function(){
					_item.find('.info').height('auto');
				});
				_item.find('.customer-name').attr('contenteditable', true);
				_item.find('.time').find('span.date').addClass('time-span');
				// ---------------------
				// datepicker
				_item.find('.time-span').DatePicker({
					mode: 'single',
					showOn: 'click',
					onBeforeShow: function(el){
						if( $(this).text().trim() != "set date" ) 
						{
							$(this).DatePickerSetDate($(this).text(), true);
						}
						else
						{
							var today = new Date();
							$(this).DatePickerSetDate(today, true);
						}
				  	},
					onChange: function(date, el) {
						$(el).text(date.getDate()+'/'+(date.getMonth()+1)+'/'+date.getFullYear());
						$(el).DatePickerHide();
					}
				});
			}
		}
		
		$('label').on('click', function(){
			$(this).next('input, textarea, .textarea').focus();
			//
			if($(this).next('input, textarea, .textarea').attr('type') == 'checkbox') {
				$(this).next('input').trigger('click');
			}
		});
	});
	// ---------------------
	// save
	$('.entry-list').on('click', '.save', function(){
		//
		var _form = $(this).parents('form');
		var _button = $(this);
		_button.addClass('disabled');
		//
		$.ajax({
			type : 'post',
			data: _form.serialize()+"&notes="+_form.find('div[name=notes]').text()+"&customer_name="+_form.find('div.customer-name').text()+"&resolved="+_form.find('.date').text()+"&pickup_time="+_form.find('.pickup-time').text(),
			url: CI_BASE+'tickets/save',
			dataType: 'json',
			success: function(response)
			{
				if( response.success == true )
				{
					var _item = _button.parents('.item');
					_item.find('[name=notify]').attr('checked',false);
					if( _form.find("[name=store_id]").find("option:selected").val() == _form.parents('.group').data('group') && _button.parents('.item').data('status') != 3 && _button.parents('.item').find("[name=status] option:selected").val() != 3 )
					{
						var text = _button.text();
						_button.removeClass('disabled').text('saved');
						setTimeout(function(){
							_button.text(text);
						}, 1000);
						var new_status = _form.find('[name=status] option:selected').val();
						_item.find('.status').removeClass('status-'+_item.data('status')).addClass('status-'+new_status);
						_item.data('status',new_status);
					}
					else
					{
						window.location.reload();
					}
				}
				else
				{
					alert('Changes could not be saved.');
				}
			}
		});
	});
	// ---------------------
	// new ticket
	$('.group').on('click', '.new-entry', function(e){
		e.preventDefault();
		var _this = $(this);
		$.ajax({
			type : 'post',
			url: CI_BASE+'tickets/new_ticket/'+_this.parents('.group').data('group'),
			dataType: 'json',
		}).done(function(r){
			if(r.success == true)
			{
				_this.parents('.group').find('.new-items').prepend(r.ticket).addClass('open');
			}
		});
	});
});