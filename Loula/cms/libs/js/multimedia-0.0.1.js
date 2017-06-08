// on load
$(function(){
	// ---------------------------------------------------------
	// toggle options
	//
	$('.page-content').on('click', '.edit', function(){
		var _item = $(this).parents('.media-item');
		if(!_item.hasClass('editing'))
		{
			$('.editing').removeClass('editing');
			_item.addClass('editing');
		}
		else
		{
			_item.removeClass('editing');
		}
	});
	// ---------------------------------------------------------
	// save label
	//
	$('.page-content').on('click', '.save-button', function(){
		var _item = $(this).parents('.media-item');
		var hero = false;
		if(_item.find('.input.link').hasClass('active'))
		{
			hero = _item.find('.input.link input').val();
		}
		$.ajax({
			url: CI_ROOT+'multimedia/edit/'+_item.data('id'),             
            type: "POST",
            dataType: 'json',
			data: {"label":_item.find('.label').val(), "hero": hero},
            success: function(r)
            {
				_item.removeClass('editing');
				_item.find('.filename').text(r.filename);
				_item.find('img').attr('src',r.thumb);
				_item.find('.lightbox-link').attr('href',r.file);
            }
		});
	});
	// ---------------------------------------------------------
	// delete item
	//
	$('.page-content').on('click', '.delete', function(){
		var _item = $(this).parents('.media-item');
		$.ajax({
			url: CI_ROOT+'multimedia/delete/'+_item.data('id'),             
            type: "POST",
            success: function(r)
            {
				_item.fadeOut(400, function(){
					_item.remove();
				});
            }
		});
	});
	// ---------------------------------------------------------
	// status item
	//
	$('.page-content').on('click', '.visibility', function(){
		var _button = $(this);
		var _item = _button.parents('.media-item');
		var status = 1;
		if(_button.hasClass('visible'))
		{
			status = 0;
		}
		_button.toggleClass('visible hidden');
		$.ajax({
			url: CI_ROOT+'multimedia/status/'+_item.data('id'),             
            type: "POST",
			data: {"status":status}
		});
	});
	// ---------------------------------------------------------
	// columns
	//
	$('.page-content').on('click', '.column', function(){
		var _button = $(this);
		_button.siblings('.column').removeClass('active');
		_button.addClass('active');
		
		var _item = _button.parents('.media-item');
		$.ajax({
			url: CI_ROOT+'multimedia/column/'+_item.data('id'),             
            type: "POST",
			data: {"column":_button.data('column')}
		});
	});
	// ---------------------------------------------------------
	// edit hero item
	//
	$('.page-content').on('click', '.instore-button', function(){
		var _button = $(this);
		var _item = $(this).parents('.media-item');
		if(!_button.hasClass('active'))
		{
			_button.siblings('.active').removeClass('active');
			$('.page-content').find('.instore-button.active').removeClass('active');
			$(this).addClass('active');
			$.ajax({
				url: CI_ROOT+'multimedia/edit_hero/'+_item.data('id'),             
	            type: "POST",
				data: {"hero":"instore"}
			});
		}
		else
		{
			$(this).removeClass('active');
			$.ajax({
				url: CI_ROOT+'multimedia/edit_hero/'+_item.data('id'),             
	            type: "POST",
				data: {"hero":""}
			});	
		}
	});
	$('.page-content').on('click', '.eboutique-button', function(){
		var _button = $(this);
		var _item = $(this).parents('.media-item');
		if(!_button.hasClass('active'))
		{
			_button.siblings('.active').removeClass('active');
			$('.page-content').find('.eboutique-button.active').removeClass('active');		
			$(this).addClass('active');
			$.ajax({
				url: CI_ROOT+'multimedia/edit_hero/'+_item.data('id'),             
	            type: "POST",
				data: {"hero":"eboutique"}
			});
		}
		else
		{
			$(this).removeClass('active');
			$.ajax({
				url: CI_ROOT+'multimedia/edit_hero/'+_item.data('id'),             
	            type: "POST",
				data: {"hero":""}
			});	
		}
	});
	$('.page-content').on('click', '.link label', function()
	{
		var _button = $(this).parents('.link');
		var _item = _button.parents('.media-item');
		if(!_button.hasClass('active'))
		{
		_item.find('.eboutique-button, .instore-button').removeClass('active').hide();		
		_button.removeClass('passive').addClass('active');
		}
		else
		{
			_button.removeClass('active').addClass('passive');
			_item.find('.eboutique-button, .instore-button').show();				
		}
	});
	// ---------------------------------------------------------
	// file uploader
	//
	var _file_uploader = $('#media_upload');
	// file uploader
	var uploader = new qq.FileUploader({
		// pass the dom node (ex. $(selector)[0] for jQuery users)
		element: _file_uploader[0],
		// path to server-side upload script
		action: CI_ROOT+'multimedia/upload',
		allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
		onSubmit: function(id, fileName)
		{
		    uploader.setParams({
				filename: fileName,
				dir: _file_uploader.data('dir')
			});
			//
			$('.page-content').append('<div class="media-item uploading-'+id+'"><div class="empty-image image"><img src="'+CI_ROOT+'media/layout/loading.gif" /></div><span class="filename">uploading...</span></div>');
		},
		onComplete: function(id, fileName, responseJSON)
		{
			if((responseJSON.error == 'undefined' || responseJSON.error == null || responseJSON.success == true) && responseJSON != undefined && responseJSON.path != undefined)
			{
				$('.uploading-'+id).data('id',responseJSON.id).html('<div class="delete">×</div>'+
					'<div class="edit-box">'+
						'<div class="input name">'+
							'<label for="label">Name</label><input type="text" class="label" name="label" placeholder="Name" value="'+fileName+'">'+
						'</div>'+
						'<div class="input save-button"><div>Save</div></div>'+
						'<div class="hero-label"><div>Hero</div></div>'+
						'<div class="input link passive">'+
							'<label for="link">Link</label><input type="text" value="" placeholder="http://" name="link" class="link" /></div>'+
						'<div class="input instore-button"><div>instore</div></div>'+
						'<div class="input eboutique-button"><div>e-boutique</div></div>'+
						'<div class="columns">'+
							'<div class="input media-button column" data-column="column-one"><div>1 column</div></div>'+
							'<div class="input media-button column" data-column="column-two"><div>2 columns</div></div>'+
						'</div></div>'+
					'</div><div class="image"><div class="options">'+
							'<a rel="lightbox" class="lightbox-link" href="'+CLIENT_IMAGES+responseJSON.path+'"><span class="expand"></span></a><span class="edit"></span></div>'+
						'<img src="'+CLIENT_IMAGES+responseJSON.thumb_150+'"></div>'+
					'<span class="filename"><span class="icon visibility visible"></span><span>'+responseJSON.filename+'</span></span>').removeClass('.uploading-'+id);
					$("a[rel^='lightbox']").slimbox();
			}
			else
			{
				$('.uploading-'+id).find('.empty-image').addClass('error').html('Error file has not been uploaded.<br /><br />Reasons may be the size of the image or the proportions.').delay(10000).fadeOut(function(){
					$('.uploading-'+id).remove();
				});
			}
			},
			template: '<div class="qq-uploader">' +
		              '<div class="qq-upload-drop-area"><span>Drop files here to upload</span></div>' +
		              '<div class="qq-upload-button">Upload a file</div>' +
		              '<ul class="qq-upload-list" style="display:none;"></ul>' +
		          '</div>'
	   		});
});