// once jquery is loaded and DOM is ready
$(function()
{
	window.onbeforeunload = function() {
		clean_up();
	};
	// cleanup empty entries
	var clean_up = function(){
		$.post(CI_BASE+'content/clean_up', {'id':$('input[name=id]').val(),'title':$('input[name=title]').val(),'text':$('.wysiwyg.text').text(),'ajax':'true'});
	}
	// ---------------------
	// update url	
	if( window.history && history.pushState )
	{
		history.pushState('', "Creating New Entry "+$('input[name=id]').val(), $('input[name=id]').val());
	}
	// ---------------------
	// save
	var _save = $('div.button.save');
	_save.on('click', function()
	{
		_save.text('saving ...').addClass('saving');
		// start data
		var data = $("#content_edit").serializeArray();
		// grab blocks
		var blocks = new Array();
		var count = 0;
		$('.blocks').find('.block').each(function(){
			if( count > 0 )
			{
				blocks += ',"'+count+'":{"title":"'+$(this).find('.block-headline').val()+'","content":"'+encodeURI($(this).find('.block-content').val().replace("\n", "<br />"))+'"}';
			}
			else
			{
				blocks += '"'+count+'":{"title":"'+$(this).find('.block-headline').val()+'","content":"'+encodeURI($(this).find('.block-content').val().replace("\n", "<br />"))+'"}';
			}
			// increase count
			++count;
		});
		// --------------------------------
		// grab modules
		var modules = new Array();
		var count = 0;
		$('.active-modules').find('.module:not(".empty")').each(function(){
			if( count > 0 )
			{
				modules += ',"'+count+'":"'+$(this).data('module')+'"';
			}
			else
			{
				modules += '"'+count+'":"'+$(this).data('module')+'"';
			}
			// increase count
			++count;
		});
		// --------------------------------
		// add to data
		data.push({name: 'blocks', value:'{'+blocks+'}'});
		data.push({name: 'modules', value:'{'+modules+'}'});
		data.push({name: 'text', value:$('.wysiwyg.text').html()});
		data.push({name: 'twitter', value:$('[name=twitter]').text()});
		data.push({name: 'facebook', value:$('[name=facebook]').text()});
		data.push({name: 'homepage_text', value:$('[name=homepage_text]').text()});
		//
		$.ajax({
			type : 'post',
			data: data,
			url: CI_BASE+'content/save',
			dataType: 'json',
		}).done(function(response){
			if( response.success === 'TRUE' )
			{
				_save.text('Save changes').removeClass('saving');
				// if history api does not work, reload
				var pathArray = window.location.pathname.split( '/' );
				if( pathArray[pathArray.length-1] == 'new' )
				{
					var loc = new String(window.location);
					
					window.location.href = loc.substr(0,-3)+$('input[name=id]').val();
				}
				$('[name="position"]').val(response.data.data.position);
			}
			else
			{
				_save.text('Save changes').removeClass('saving');
				alert('Changes couldn\'t be saved.');
			}
		}).fail(function()
		{
			_save.text('Save changes').removeClass('saving');
			alert('Changes couldn\'t be saved.');
		});
	});
	// ---------------------
	// datepicker
	// $('.date-input').DatePicker({
	// 	mode: 'single',
	// 	position: 'right',
	// 	onBeforeShow: function(el){
	// 		if($('.date-input').val())
	// 		$('.date-input').DatePickerSetDate($('.date-input').val(), true);
	//   	},
	// 	onChange: function(date, el) {
	// 		$(el).val(date.getDate()+'/'+(date.getMonth()+1)+'/'+date.getFullYear());
	// 		if($('#closeOnSelect input').attr('checked')) {
	// 			$(el).DatePickerHide();
	// 		}
	// 	}
	// });
	// ---------------------
	// delete single entry
	$('#delete_entry').on('click', function(){
		$.ajax({
			type : 'post',
			dataType: 'text',
			url: CI_BASE+'content/delete/'+$('[name="id"]').val(),
			success: function()
			{
				location.href = CI_BASE+"content/";
			}
		});
	});
	// ---------------------
	// merged buttons
	var _button_bar = $('.button-bar');
	_button_bar.fs_button_bar();
	var _publish = _button_bar.find('.publish');
	var _selection = $('#type');
	// ---------------------
	// published button
	$('#sidebar').on('hover', function()
	{
		// get permalink
		var permalink = true;
		$.ajax({
			type : 'post',
			data: {'permalink':$('[name="permalink"]').val(),'ajax':true,'post_id':$('[name=id]').val()},
			url: CI_BASE+'content/check_permalink',
			dataType: 'text',
		}).done(function(response)
		{
			permalink = response;
			//
			if( $('[name="title"]').val() != '' && $('.text').text() != '' )
			{
				if( _selection.find('.active').data('value') == 4 || 
						( 
							( _selection.find('.active').data('value') == 3 ||
					 	 	 	_selection.find('.active').data('value') == 2 ||
								_selection.find('.active').data('value') == 7 )
							&& $('[name="permalink"]').val() != '' 
						) 
						|| 
						( 
							$('#menu_item').val() != '' && $('[name="permalink"]').val() != '' && permalink == 'false'
						) 
					)
				{
					_publish.removeClass('disabled');
				}
				else
				{
					if( ($('[name="permalink"]').val() != '' && permalink == 'true') || $('[name="permalink"]').val() == '' )
					{
						_publish.addClass('disabled');
					}
				}
			}
			else
			{
				_publish.addClass('disabled').removeClass('active');
				_publish.siblings('.draft').addClass('active').parents('.button-bar').find('.hidden-elements').find('.draft').trigger('click');
			}
			});
	});
	_publish.on({
		mouseenter: function(){
			if( _publish.hasClass('disabled') )
			{
				$('[name="title"], #menu_item, [name="permalink"], .text').addClass('required');
			}
		},
		mouseleave: function(){
			$('[name="title"], #menu_item, [name="permalink"], .text').removeClass('required');
		}
	});
	// ---------------------
	// buttons
	$('.dropdown').fs_dropdown();
	// ---------------------
	// add styles when dropdown changes
	$('#product_type').on('dropdownChange', function(e)
	{
		if( e.detail.selected == 1)
		{
			$('.slots').fadeIn();
			$('.modules, .module-image, .module-slots').fadeOut();
		}
		else if( e.detail.selected == 2 )
		{
			$('.module-image, .module-slots').fadeIn();
			$('.slots').fadeOut();
		}
		else if( e.detail.selected == 3 )
		{
			$('.module-image,.slots').fadeOut();
			$('.modules').fadeIn();
		}
		else
		{
			$('.slots, .modules, .module-image, .module-slots').fadeOut();
		}
	});
	// ---------------------
	// add styles when dropdown changes
	$('#type').on('dropdownChange', function(e)
	{
		if( e.detail.selected == 4 )
		{
			$('.blocks').fadeIn();
			$('.menu-item').fadeOut();
			$('.price, .product-type, .slots, .product-code, .modules, .module-image').fadeOut();
		}
		else if( e.detail.selected == 3 )
		{
			$('.blocks').fadeIn();
			$('.menu-item').fadeOut();
			$('.price, .product-type, .slots, .product-code, .modules, .module-image').fadeOut();
		}
		else if( e.detail.selected == 2 )
		{
			$('.blocks').fadeOut();
			$('.menu-item').fadeOut();
			$('.price, .product-type, .slots, .product-code').fadeIn();
		}
		else if( e.detail.selected == 7 )
		{
			$('.blocks').fadeOut();
			$('.menu-item').fadeOut();
			$('.price, .product-type, .slots, .product-code, .modules, .module-image').fadeOut();
		}
		else
		{
			$('.blocks').fadeOut();
			$('.menu-item').fadeIn();
			$('.price, .product-type, .slots, .product-code, .modules, .module-image').fadeOut();
		}
		// test
		var _pos = $('[name="position"]');
		if( _pos.data('type') != e.detail.selected && _pos.val() != "")
		{
			_pos.data('type',e.detail.previous).data('position',_pos.val()).val('');
		}
		else if( _pos.data('type') != e.detail.selected )
		{
			_pos.val("");
		}
		else
		{
			_pos.val(_pos.data('position'));
		}
	});
	// ---------------------
	// social media
	$('.social-media').on('click', '.label-button', function(){
		var open = false;
		if( !$(this).parents('.form-element').hasClass('open')){ open = true; }
			$('.social-media').find('.open').removeClass('open');
		if( open == true ){ 
			$(this).parents('.form-element').addClass('open').find('.textarea').focus(); 
		}
		if( !$(this).parents('.form-element').hasClass('active')){ 
	 		$(this).parents('.form-element').removeClass('passive').addClass('active');
		}
	});
	$('.social-media').on('click', '.close', function(e){
		e.stopPropagation();
		$(this).parents('.form-element').removeClass('active open').addClass('passive').find('.textarea').text('');
		$(this).parents('.form-element').removeClass('active open').addClass('passive').find('input').val('');
	});
	$('.social-media').on('keyup', '.textarea', function(){
		var span = $(this).parents('.form-element').find('span.chars-left');
		span.text(span.data('chars') - $(this).text().length);
	});
	// ---------------------
	// upload
	var dropbox = $('#media'),
		message = $('.message', dropbox),
		images = $("#images"),
		uploaded_image;

	images.on('click', '.close', function()
	{
		// get image
		var image = $(this).parents('.preview');
		
		$.ajax({		
			type : 'post',
			data: {'image_id':image.data('id')},
			url: CI_BASE+'media/delete/'+$('[name="id"]').val()
		});
		// hide image
		image.fadeOut(300);
	});

	dropbox.filedrop({
		// The name of the $_FILES entry:
		paramname:'pic',

		maxfiles: 5,
    	maxfilesize: 2, // in mb
		url: CI_BASE+'media/upload/'+$('[name="id"]').val(),

		uploadFinished:function(i,file,response){
			var _this =  $.data(file).addClass('upload-done').data('id', response.id);
			console.log(response);
			setTimeout(function(){
				$.data(file).addClass('done');
			}, 1500);
			// response is the JSON object that post_file.php returns
		},

    	error: function(err, file) {
			switch(err) {
				case 'BrowserNotSupported':
					showMessage('Your browser does not support HTML5 file uploads!');
					break;
				case 'TooManyFiles':
					alert('Too many files! Please select 5 at most!');
					break;
				case 'FileTooLarge':
					alert(file.name+' is too large! Please upload files up to 1.5mb.');
					break;
				default:
					break;
			}
		},
	    docOver: function() {
			dropbox.addClass('active');
	    },
	    docLeave: function() {
			dropbox.removeClass('active');
	    },
	    dragOver: function() {
			dropbox.addClass('active');
	    },
	    dragLeave: function() {
			dropbox.removeClass('active');
	    },
	    drop: function() {
			dropbox.removeClass('active');
	    },
		// Called before each upload is started
		beforeEach: function(file){
			if(!file.type.match(/^image\//)){
				alert('Only images are allowed!');
				// Returning false will cause the
				// file to be rejected
				return false;
			}
		},
		uploadStarted:function(i, file, len){
			createImage(file);
		},
		progressUpdated: function(i, file, progress) {
			$.data(file).find('.progress').width(progress);
		}
	});
	var template = '<div class="preview">'+
						'<span class="close">×</span>'+
						'<div class="imageHolder">'+
							'<img />'+
							'<span class="info">i</span>'+
							'<span class="uploaded"></span>'+
						'</div>'+
						'<div class="progressHolder">'+
							'<div class="progress"></div>'+
						'</div>'+
						'<div class="social-channels">'+
							'<span class="channel all">all</span>'+
							'<span class="channel cover" data-type="cover" title="cover"></span>'+
							'<span class="channel news" data-type="news" title="news"></span>'+
							'<span class="channel facebook" data-type="facebook" title="facebook"></span>'+
						'</div>'+
					'</div>'; 
	function createImage(file)
	{
		var preview = $(template),
			image = $('img', preview);

		var reader = new FileReader();

		image.width = 100;
		image.height = 100;

		reader.onload = function(e){
			// e.target.result holds the DataURL which
			// can be used as a source of the image:
			image.attr('src',e.target.result).css({'height':140});
		};
		// Reading the file as a DataURL. When finished,
		// this will trigger the onload function above:
		reader.readAsDataURL(file);
		preview.appendTo(images);
		// Associating a preview container
		// with the file, using jQuery's $.data():
		$.data(file,preview);
	}
	function showMessage(msg){
		message.html(msg);
	}
	// ---------------------
	// image channels
	images.on('click', '.channel', function()
	{
		var _this = $(this);
		var data = {};
		if( _this.hasClass('all') )
		{
			data = {'news':_this.parents('.preview').data('id'),'facebook':_this.parents('.preview').data('id'), 'twitter':_this.parents('.preview').data('id')};
			images.find('span.channel'+'.active').removeClass('active');
			_this.siblings('.channel:not(.all)').addClass('active');
		}
		else
		{
			if( _this.hasClass('active') )
			{
				_this.removeClass('active');
				data[_this.data('type')] = false;
			}
			else
			{
				images.find('span.channel'+'.'+_this.data('type')+'.active').removeClass('active');
				_this.addClass('active');
				data[_this.data('type')] = _this.parents('.preview').data('id');
			}
		}
		// run ajax
		$.ajax({		
			type : 'post',
			data: {'social_images':data},
			url: CI_BASE+'content/social_images/'+$('[name="id"]').val()
		});
	});
	// ------------------------------------------
	// image browser
	$('#images').on('click', '.info', function(){
		var _draw = $('#top_draw');
		_draw.css({'top': -(_draw.height()+50), 'display': 'block'}).animate({'top':'45px'}, 1000, function(){
			if( _draw.find('.content').html().trim() == '' )
			{
				_draw.find('.loader').show();
			}
		});
		// load content
		$.ajax({
			type : 'post',
			dataType: 'html',
			url: CI_BASE+'media/image_settings/'+$(this).parents('.preview').data('id'),
			data: {entry:$('[name="id"]').val()},
			success: function(response)
			{
				_draw.find('.loader').hide();
				_draw.find('.content').html(response);
				// ------------------------------------------
				// autosave
				$('.autosave').fs_form_autosave({
					file:'media/edit_image', 
					data : function( _this ){
						return {
							'id' 	:_this.data('id'),
							'value' :_this.text()
						}
					},
					callback : function(response){
						$(this).text(response.filename);
					}
				});
				// ---------------------
				// autosize homepage caption
				$('.autosave-home').fs_form_autosave({
					file:'media/image_to_homepage', 
					data : function( _this ){
						return {
							'id' 	:_this.data('id'),
							'value' :_this.find('.textarea').val(),
							'link': $('[name="link"]').val()
						}
					},
					callback : function(response){}
				});
			}
		});
	});
	$('#top_draw').on('click', '.close-draw', function(){
		var _draw = $('#top_draw');
		_draw.animate({'top': -(_draw.height()+50)}, 500, function(){
			_draw.hide();
		});		
	});
	// ----------------------------------------------------
	// replace button
	$('body').on('mousemove', '.button', function(e){
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
						if(xhr.responseText == true)
						{
							_this.parents('.image-thumb').find('.loading-holder').hide();
							_this.parents('.image-thumb').find('img').attr('src', _this.parents('.image-thumb').find('img').attr('src')+'?' + new Date().getTime());
						}
					}
				};
				xhr.open("POST", CI_BASE+'media/replace_image/'+_this.parents('.image-thumb').data('id')+'/'+_this.parents('.image-thumb').data('thumb'));
				// Send the file
				xhr.send(formData);
			}
		}
		else
		{
			fileList.innerHTML = "No support for the File API in this web browser";
		}
	}
	$('body').on('change', '.upload-input', function(e){
		$(this).parents('.image-thumb').find('.loading-holder').show();
		traverseFiles(this.files, $(this));
	});
	//----------------------
	// module image upload
	var mod_xhr;
	function mod_traverseFiles (files, _this) {
		if (typeof files !== "undefined") {
			for (var i=0, l=files.length; i<l; i++) {
				var file = files[i];
				mod_xhr = new XMLHttpRequest();
				var formData = new FormData();
				// Append our file to the formData object
				// Notice the first argument "file" and keep it in mind
				formData.append('file', file);
				// create event for on submit done
				mod_xhr.onreadystatechange = function()
				{
					if (mod_xhr.readyState==4 && mod_xhr.status==200)
					{
						var response = JSON.parse(mod_xhr.responseText);
						if(response.success == 'true')
						{
							_this.parents('#module_image').find('.upload-text').text(_this.parents('#module_image').find('.upload-text').data('txt'));
							// check for image link
							if( $('.current-module-image').size() > 0)
							{
								$('.current-module-image').attr('href', response.path);
							}
							else
							{
								$('<a class="current-module-image" href="'+response.path+'" target="_blank">current module image</a>').insertAfter(_this.parents('#module_image'));
							}
							// _this.parents('.image-thumb').find('img').attr('src', _this.parents('.image-thumb').find('img').attr('src')+'?' + new Date().getTime());
						}
					}
				};
				mod_xhr.open("POST", CI_BASE+'media/module_image/'+$('[name="id"]').val());
				// Send the file
				mod_xhr.send(formData);
			}
		}
		else
		{
			fileList.innerHTML = "No support for the File API in this web browser";
			_this.parents('#module_image').removeClass('loading').text(_this.data('txt'));
		}
	}
	//
	$('#module_image_upload').on('change',function(){
		$(this).parents('#module_image').find('.upload-text').text('Uploading ...');
		mod_traverseFiles(this.files, $(this));
	});
	// ------------------------------------------
	// add blocks
	$('.add-block').on('click', function(){
		$(this).before('<div class="block"><span class="close">&times;</span><input type="text" class="block-headline" value="" placeholder="headline" /><textarea class="block-content" placeholder="content"></textarea></div>');
	});
	// ---------------------
	// delete blocks
	$('.blocks').on('click', '.close', function(){
		$(this).parents('.block').fadeOut(300, function(){
			$(this).remove();
		});
	});
	// ---------------------
	// add image from post to homepage
	$('body').on('click', '.add-to-homepage', function(){
		var _bottom = $(this).parents('.bottom');
		var id = $(this).parents('.content').find('.filename').data('id');
		//
		if(!_bottom.hasClass('active'))
		{
			//
			$.ajax({
				type : 'post',
				data: {id:id,entry:$('[name=id]').val(),value:_this.find('.textarea').val(),link:_bottom.data('link')},
				dataType: 'text',
				url: CI_BASE+'media/image_to_homepage',
			}).done(function(response)
			{
				_bottom.addClass('active').find('.add-to-homepage').text(_bottom.find('.add-to-homepage').data('remove'));
			}).fail(function(){ 
				alert("Something went wrong."); 
			});
		}
		else
		{
			// 
			$.ajax({
				type : 'post',
				data: {id:id},
				dataType: 'text',
				url: CI_BASE+'media/remove_image_homepage',
			}).done(function(response){
				_bottom.removeClass('active').find('.add-to-homepage').text(_bottom.find('.add-to-homepage').data('add'));	
			}).fail(function(){ 
				alert("Something went wrong."); 
			});
		}
	});
	//----------------------
	// boat	
	$('#boat').on('dropdownChange', function(e)
	{
		var slots = e.detail.active.data('slots');
		modules(slots);
	});
	
	var modules = function( slots )
	{
		var count = 0;
		$('.module:not(".empty")').each(function(){
			count += $(this).data('slots');
		});
		//
		$('.module-count').text(count);
		$('.boat-count').text(slots);
		if( count <= slots )
		{
			$('.form-element.modules').removeClass('warning');
		}
		else
		{
			$('.form-element.modules').addClass('warning');
		}
		//
		if( count < slots && $('.module:visible').length < slots )
		{
			var mods = $('.module.empty:visible').length;
			for(var c = count + mods; c < slots; c++)
			{
				$('.module.empty:not(":visible")').first().fadeIn();
			}
		}
		else if( count >= slots )
		{
			$('.module.empty').fadeOut();
		}
		else if( $('.module:visible').length > slots )
		{
			var c = $('.module:visible').length;
			var empty = $('.module.empty:visible').length;
			while( c >= slots)
			{
				$('.module.empty:visible').eq(empty).fadeOut();
				empty--;
				c--;
				if(empty == 0)
				{
					c = slots-1;
				}
			}
		}
	};
	//----------------------
	// system-box
	$('.system-bg, .close-systems').on('click', function(){
		$('.system-box').find('.system').animate({'margin-top':-$('.system').height()}, 200);
		$('.system-box').animate({'opacity':'0.01'},300, function(){
			$('.system-box').css({'height':0,'width':0});
		});
		$('.choose-modules').animate({'top':-($('.choose-modules').height()+50)}, function(){
			$('.choose-modules').css('display','block');
		});
	});
	$('.modules').on('click', function(){
		$('.system-box').css({'height':'100%','width':'100%'}).animate({'opacity':1}, 200);
		$('.system-box').find('.system').animate({'margin-top':'100px'}, 400);
	});
	//----------------------
	// active modules
	$('.active-modules').on('click', '.close', function(){
		$(this).parent('.module').addClass('empty').data('slots', 0).data('module','').find('.label').text('Add module');
		$('.choose-modules').animate({'top':-($('.choose-modules').height()+50)}, function(){
			$('.choose-modules').css('display','block');
		});
		//
		modules($('#boat').find('.selection .option.active').data('slots'));
	});
	//----------------------
	// add module
	var waiting;
	$('.active-modules').on('click', '.empty', function(){
		waiting = $(this);
		$('.choose-modules').css({'top':-$('.choose-modules').height(), 'display':'block'}).animate({'top':'50px'});
	});
	
	$('.close-modules').on('click', function(){
		$('.choose-modules').animate({'top':-($('.choose-modules').height()+50)}, function(){
			$('.choose-modules').css('display','block');
		});
	});
	
	$('.choose-modules').on('click', '.choose-module', function(){
		$('.choose-modules').animate({'top':-($('.choose-modules').height()+50)}, function(){
			$('.choose-modules').css('display','block');
		});
		waiting.removeClass('empty').data('slots', $(this).data('slots')).data('module',$(this).data('module')).find('.label').text($(this).text());
		waiting = null;
		modules($('#boat').find('.selection .option.active').data('slots'));
	}); 
	//----------------------
	// wysiwyg
	$('.wysiwyg').on('focus click', function(){
		var _this = $(this);
		if( _this.data('placeholder') == _this.html() )
		{
			_this.html('')
		}
	});
	$('.wysiwyg').on('blur', function(){
		var _this = $(this);
		if( _this.html() == '' )
		{
			_this.html(_this.data('placeholder'))
		}
	});
});