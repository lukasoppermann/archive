// ---------------------
// delete entry from list
$('body').on('click', '.delete a', function(e){
	var _this = $(this);
	var _parent = _this.parents('.item');
	$.ajax({
		type : 'post',
		dataType: 'text',
		data: {ajax:true, status: _parent.data('status')},
		url: _this.attr('href'),
		success: function(data)
		{
			if( _parent.data('status') == 3)
			{
				_parent.animate({'opacity':0, 'height':0},300, 'swing', function(){
					_parent.remove();
				});
			}
			else
			{
				_parent.animate({'opacity':0, 'height':0},300, 'swing', function(){
					_parent.removeClass('status-'+$(this).data('status')).addClass('status-3').appendTo(_parent.parents('ul').next('ul.deleted')).removeAttr('style').find('.status').removeClass('status-'+$(this).data('status')).addClass('status-3');
				});
			}
			
		}
	});
	// prevent link from working
	e.preventDefault();
});
// ---------------------
// show deleted entries
$('body').on('click', '.deleted-entries', function(e){
	var _this = $(this);
	var text = _this.text();
	// change text
	_this.text(_this.data('text')).data('text', text).toggleClass('deleted');
	// toggle lists
	_this.parents().siblings('ul').toggle();
	// work states
	if( _this.hasClass('deleted') )
	{
		_this.siblings('.trash').show();
		_this.siblings('.new-entry').hide();
	}
	else
	{
		_this.siblings('.trash').hide();
		_this.siblings('.new-entry').show();
	}
	// prevent link from working
	e.preventDefault();	
});
// ---------------------
// trash deleted entries
$('body').on('click', '.trash', function(e){
	var _this = $(this);
	var _parent = _this.parents('.group');	
	//
	$.ajax({
		type : 'post',
		dataType: 'text',
		data: {ajax:true, deleteType: _parent.data('group')},
		url: _this.attr('href'),
		success: function(data)
		{
			_parent.find('ul.deleted').find('.item').remove();
			_parent.find('.deleted-entries').click();
		}
	});
	// prevent link from working
	e.preventDefault();	
});
// ---------------------
// sort entries
$('.group .entry-list').sortable({
    items: '.item'
}).bind('sortupdate', function(e, ui)
{
	// define object
	var data = {};
	// build object
	$(this).find('.item').each(function(i, e)
	{
		data[$(e).data('id')] = i+1;
	});
	// send ajax request
	$.ajax({
		url: CI_ROOT+'content/sort',            
	        type: "POST",
	        dataType: 'json',
	        data: {items: data, ajax: 'true'}
	});
});