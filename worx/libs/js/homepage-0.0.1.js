var sortable = function()
{
	$('.column').sortable('destroy');
	$('.column').sortable({
		items: '.block', 
	    connectWith: '.column',
		handle: '.handle'
	}).bind('sortupdate', function(e, ui)
	{
		$('.column').each(function(){
			if( $(this).html() == '' )
			{
				$(this).addClass('empty');
			}
			else
			{
				$(this).removeClass('empty');
			}
		});
		// --------------
		// update column order
		var _moved = $(ui.item);
		var new_column = _moved.parents('.column').data('column');
		var old_column = _moved.data('column');	
		var position = _moved.parents('.column').find('.block').index(_moved);
		// --------------
		// serialize columns
		var column_old = '';
		var column_new = '';	
		$('.column[data-column="'+new_column+'"]').find('.block').each(function(i){
			column_new += "\""+i+"\":{\"id\":\""+$(this).data('id')+"\", \"position\":\""+i+"\"},";	
		});
		$('.column[data-column="'+old_column+'"]').find('.block').each(function(i){
			column_old += "\""+i+"\":{\"id\":\""+$(this).data('id')+"\", \"position\":\""+i+"\"},";	
		});
		columns = "{\""+old_column+"\":{"+column_old.slice(0,-1)+"},\""+new_column+"\":{"+column_new.slice(0,-1)+"}}";
			// --------------
		// send ajax
		$.ajax({
			type : 'post',
			data: {'column':new_column, 'position': position, 'id': _moved.data('id'), columns: columns},
			url: CI_BASE+'homepage/edit_block',
			dataType: 'text'
		}).done(function(r){
			_moved.data('column', new_column);
		});	
	}
	).bind('dragstart', function(e, ui){
		$('.column').addClass('show-bg');
		setTimeout(function(){
			$('.parent').find('.add-block').hide();
			$('.block').addClass('moving');
		}, 10);
	}
	).bind('dragend', function(e, ui){
		$('.column').removeClass('show-bg');
		$('.block').removeClass('moving')
		$('.parent').find('.add-block').show();
		$('.column').each(function(){
			// --------------
			// toggle empty add block button
			if($(this).find('.block').size() != 0)
			{
				$(this).find('.column-add').parents('.parent').hide();
			}
			else
			{
				$(this).find('.column-add').parents('.parent').fadeIn();
			}
		});
	});
};
sortable();
// ----------------------------------
// save blocks
var autosave = function() 
{
	$('.autosave').fs_form_autosave({
		file:'homepage/edit_block', 
		data : function( _this ){
			return {	'id'    :_this.parents('.block').data('id'),
						'key' 	:_this.attr('name'),
						'value' :_this.val()
					}
		}
	});
	$('.autosave-edit').fs_form_autosave({
		file:'homepage/edit_block', 
		data : function( _this ){
			return {	'id'    :_this.parents('.block').data('id'),
						'key' 	:_this.attr('name'),
						'value' :_this.text()
					}
		}
	});
};
autosave();
// ----------------------------------
// move blocks blocks
$('.column').on('mouseenter', '.text', function(){
	$('.column').sortable('destroy');
});
$('.column').on('mouseleave', '.text', function(){
	sortable();
});
// ----------------------------------
// add new blocks
$('.column').on('click', '.add', function(){
	var _add_block = $(this).parents('.add-block');
	var _before_block = _add_block.parents('.parent');
	_add_block.hide();
	_add_block.parents('.column').find('.column-add').parents('.parent').hide();
	// add new item
	$.ajax({
		type : 'post',
		dataType: 'text',
		data: {'column':_add_block.parents('.column').data('column')},
		url: CI_BASE+'homepage/create_block',
	}).done(function(block){
		_before_block.after(block);
		var _new_block = _before_block.next('.block');
		_new_block.find('.add-new').hide();
		_new_block.hide().slideToggle(300, function(){
			_add_block.fadeIn();
			_new_block.find('.add-new').fadeIn();
		});
		// init sortable
		sortable();
		// add autosave
		autosave();		
		// --------------
		// update column order
		var new_column = _new_block.parents('.column').data('column');
		// --------------
		// serialize columns
		var column_new = '';	
		$('.column[data-column="'+new_column+'"]').find('.block').each(function(i){
			column_new += "\""+i+"\":{\"id\":\""+$(this).data('id')+"\", \"position\":\""+i+"\"},";	
		});
		columns = "{\""+new_column+"\":{"+column_new.slice(0,-1)+"}}";
			// --------------
		// send ajax
		$.ajax({
			type : 'post',
			data: {columns: columns},
			url: CI_BASE+'homepage/sort_blocks'
		});
		// --------------
	});
});
// ----------------------------------
// delete image
$('body').on('click', '.edit-image.delete-image', function(){
	var _this = $(this);
	$.ajax({
		type : 'post',
		data: {'id': _this.parents('.block').data('id')},
		dataType: 'JSON',
		url: CI_BASE+'homepage/delete_image',
	}).done(function(block)
	{
		_this.parents('.parent').find('.image').hide();
		_this.removeClass('delete-image').addClass('add-image');
		if(block.delete == 'true')
		{
			_this.parents('.parent').remove();
		}
		
	}).fail(function(){ 
		alert("Something went wrong."); 
	});
});
// ----------------------------------
// delete blocks
$('.column').on('click', '.delete', function(){
	
	var _item = $(this).parents('.block');
	var _column = _item.parents('.column');
	
	$.ajax({
		type : 'post',
		data: {'id': $(this).data('id')},
		dataType: 'text',
		url: CI_BASE+'homepage/delete_block',
	}).done(function(block){
		// --------------
		// update column order
		var new_column = _column.data('column');
		// --------------
		// serialize columns
		var column_new = '';	
		$('.column[data-column="'+new_column+'"]').find('.block').each(function(i){
			column_new += "\""+i+"\":{\"id\":\""+$(this).data('id')+"\", \"position\":\""+i+"\"},";	
		});
		columns = "{\""+new_column+"\":{"+column_new.slice(0,-1)+"}}";
			// --------------
		// send ajax
		$.ajax({
			type : 'post',
			data: {columns: columns},
			url: CI_BASE+'homepage/sort_blocks'
		});
		// animation
		_item.fadeOut(300, function(){
			_item.remove();
			if(_column.find('.block').size() == 0)
			{
				_column.find('.parent').fadeIn();
			}
		});
	});
});
// ----------------------------------------------------
// replace button
$('body').on('mousemove', '.edit-image.add-image', function(e){
	var _this = $(this);
	var offset = _this.offset();
   _this.find('.upload-input').css({'top':(e.pageY-offset.top)+'px','left':e.pageX-offset.left});
});
var xhr;
function traverseFiles (files, _this) {
	if (typeof files !== "undefined") {
		for (var i=0, l=files.length; i<l; i++) {
			var file = files[i];
			xhr = new XMLHttpRequest();
			var formData = new FormData();
			// Append our file to the formData object
			// Notice the first argument "file" and keep it in mind
			formData.append('file', file);
			// create event for on submit done
			xhr.onreadystatechange = function()
			{
				if (xhr.readyState==4 && xhr.status==200)
				{
					response = $.parseJSON(xhr.responseText);
					if( response.success == 'true')
					{
						_this.parents('.edit-image').removeClass('loading add-image').addClass('delete-image');
						_this.parents('.block').find('img').attr('src', response.dir).parents('.image').show();
					}
					else
					{
						_this.parents('.edit-image').removeClass('loading');
						alert('Error: file not uploaded.');
					}
				}
			};
			xhr.open("POST", CI_BASE+'homepage/upload_image/'+_this.parents('.block').data('id')+'/thumb_280');
			// Send the file
			xhr.send(formData);
		}
	}
	else {
		fileList.innerHTML = "No support for the File API in this web browser";
	}
}
$('body').on('change', '.upload-input', function(e){
	$(this).parents('.edit-image').addClass('loading');
	traverseFiles(this.files, $(this));
});