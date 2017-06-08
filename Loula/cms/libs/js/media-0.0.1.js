// on load
$(function(){
    var mswitch = $(".media-switch");
    if(mswitch.size() > null)
    {

        var $file_uploader = $('#file_uploader');
        var $filename = $("#filename");
        // file uploader
        var uploader = new qq.FileUploader({
            // pass the dom node (ex. $(selector)[0] for jQuery users)
            element: document.getElementById('file_uploader'),
            // path to server-side upload script
            action: CI_ROOT+'ajax/media/upload',
            allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
            params: {
                filename: $filename.val(),
            },
            onSubmit: function(id, fileName){
                uploader.setParams({
					filename: $filename.val(),
					id: $('#id').val(),
					article_id: $('#id').val(),
					dir: $file_uploader.data('dir')
                });
				$filename.val('');
				$file_uploader.fadeOut();
				$('.upload-list').append('<li class="upload_thumb uploading-loading"><div class="thumb">uploading...</div></li>');
            },
            onComplete: function(id, fileName, responseJSON){
				if((responseJSON.error == 'undefined' || responseJSON.error == null) && responseJSON != undefined && responseJSON.path != undefined)
				{
                	$('.upload-list').find('.uploading-loading').html('<span class="delete" data-img_id="'+responseJSON.id+'">X</span><div class="thumb"><img src="'+CLIENT_IMAGES+responseJSON.thumb_150+'" alt="'+responseJSON.filename+'"></div><span class="filename">'+responseJSON.path+'</span>').removeClass('uploading-loading');
				}
				else
				{
					$('.upload-list').find('.thumb').addClass('error').html('Error file has not been uploaded.<br /><br />Reasons may be the size of the image or the proportions.').delay(10000).fadeOut(function(){
						$('.upload-list').find('.uploading-loading').remove();
					});
				}
            },
            template: '<div class="qq-uploader">' +
                          '<div class="qq-upload-drop-area"><span>Drop files here to upload</span></div>' +
                          '<div class="qq-upload-button">Upload a file</div>' +
                          '<ul class="qq-upload-list" style="display:none;"></ul>' +
                      '</div>'
        });
        // delete files
        $('.upload-list').on('click', '.delete', function(){
            var img = $(this).parents('li');
            $.ajax({
                url: CI_ROOT+'ajax/media/delete',            
                type: "POST",
                dataType: 'json',
                data: {img_id : $(this).data('img_id'), id: $('#id').val()},
                success: function(r)
                {
                    if(r.success == true)
                    {
                        img.fadeOut(300, function(){
                            img.remove();
                        });			
                    }
                }
            });
        });
        // activate file uploader
        if($filename.val().length > 2)
        {
            $file_uploader.fadeIn();
        }
        $(".media-switch").on('keyup', $filename, function(){
            if($filename.val().length > 2)
            {
                $file_uploader.fadeIn();
            }
            else
            {
               $file_uploader.fadeOut(); 
            }
        });
        // resize_thumbs($('.upload-list').find("img"));
    }
	// select hero
	$('.upload-list').on('click', '.thumb', function(){
		var _li = $(this).parents('li');
		if(_li.hasClass('hero'))
		{
			var _this_hero = 'true';
		}
		// remove hero from images
		$('.upload-list').find('.hero').removeClass('hero').each(function(){
			var _elem = $(this);
			$.ajax({
				url: CI_ROOT+'ajax/media/edit',            
				type: "POST",
				dataType: 'json',
				data: {id : _elem.find('.delete').data('img_id'), update : {"key":null}},
				success: function(r)
				{
					if( $('#type').find('option:selected').val() == '2' )
					{
						$.ajax({
							url: CI_ROOT+'ajax/entry/hero',            
							type: "POST",
							dataType: 'json',
							data: {id : $('#id').val(), 'menu_id':''}
						});
					}
				}
			});
		});
		if(_this_hero != 'true')
		{
			// add hero to images
			_li.addClass('hero');
			$.ajax({
				url: CI_ROOT+'ajax/media/edit',            
				type: "POST",
				dataType: 'json',
				data: {id : _li.find('.delete').data('img_id'), update : {"key":"hero"}},
				success: function(r)
				{
					if( $('#type').find('option:selected').val() == '2' )
					{
						$.ajax({
							url: CI_ROOT+'ajax/entry/hero',            
							type: "POST",
							dataType: 'json',
							data: {id : $('#id').val(), 'menu_id':999}
						});
					}
				}
			});
		}
	});
});