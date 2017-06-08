$('.orders').on('click', '.item', function()
{
	$(this).parents('.orders').find('.information').fadeOut();
	$(this).parents('.order').find('.information').fadeIn();
});
// ------------------------------
// add tracking number
$('.orders').on('click', '.button', function(e)
{
	var parent = $(this).parents('.order');
	e.stopPropagation();
	// save tracking number and close
	if( parent.find('.tracking').val().length > 0 )
	{
		var tracking = parent.find('.tracking').val(); 
		// manipulate DOM
		parent.removeClass('paid payment-pending').addClass('closed').insertBefore($('.order-list.closed').find('li').first());
		parent.find('.tracking').replaceWith('<span class="tracking-span">'+tracking+'</span>');
		parent.find('.button').remove();
		// run ajax request
		$.ajax({
			type : 'post',
			data: {'id':parent.data('id'),'tracking':tracking, 'email':parent.find('.customer-email').text(),'ajax':true},
			url: CI_BASE+'orders/close',
			dataType: 'json'
		});
	}
});
// ------------------------------
// delete order
$('.orders').on('click', '.delete', function(e)
{
	e.stopPropagation();
	$(this).hide();
	$(this).siblings('.confirm-delete, .cancel-delete').fadeIn();
});
// ------------------------------
// confitm delete
$('.orders').on('click', '.confirm-delete', function(e)
{
	var item = $(this).parents('li');
	// run ajax request
	$.ajax({
		type : 'post',
		data: {'id':$(this).parents('li').data('id'),'ajax':true},
		url: CI_BASE+'orders/delete',
		dataType: 'json'
	}).done(function(r)
	{
		item.fadeOut(300, function(){
			item.remove();
		});
	});
});
// ------------------------------
// cancel delete
$('.orders').on('click', '.cancel-delete', function(e)
{
	$(this).hide();
	$(this).siblings('.confirm-delete').hide();
	$(this).siblings('.delete').fadeIn();
});
// ------------------------------
// orders filter
$('.order-filter').on('click', '.button', function(e)
{
	e.stopPropagation();
	var _this = 	$(this);
	// check clicked item
	if( !_this.hasClass('active') )
	{
		// activate button
		_this.siblings('.active').removeClass('active');
		_this.addClass('active');
		// activate list
		$('.group').not('.'+_this.data('status')).fadeOut(function(){
			$('.'+_this.data('status')).fadeIn();
		});
	}
});
// ------------------------------
// orders update status
$('.orders').on('change', 'select[name="status"]', function(){
	// cache selection
	var _this = $(this);
	var newClass = _this.find('option:selected').val().replace(/\s+/g, '-').toLowerCase();
	var item = _this.parents('.order').removeClass('payment-pending closed paid').addClass(newClass);
	// change classes
	if(newClass == 'closed')
	{
		$('.orders').find('.order-list.closed').prepend(item);
	}
	else
	{
		$('.orders').find('.order-list.open').prepend(item);
	}
	// update db
	$.ajax({
		type : 'post',
		data: {'id':_this.parents('.order').data('id'),'status':_this.find('option:selected').val()},
		dataType: 'text',
		url: CI_BASE+'orders/update_status'
	});
});
// ------------------------------
// orders update note	
$('.orders .notes').fs_form_autosave({
	file:'orders/update_note', 
	data : function( _this ){
		return {
			'id' 		:_this.parents('.order').data('id'),
			'value' :_this.val(),
			'ajax' 	:true
		}
	}
});