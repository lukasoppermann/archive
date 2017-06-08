$(function(){
	// ----------------------------------------------------
	// debounced resize event (fires once every 100ms)
	$.fn.fs_resize = function( c, t )
	{
		onresize = function(){
			clearTimeout( t );
			t = setTimeout( c, 100)
		};
		return c;
	};

	var _window = $(window);
	// ----------------------------------------------------------------
	// check browser
	if( $.browser.msie )
	{
		if( $.browser.version < 9 )
		{
			$('.dialog').fs_dialog('show', '<div class="browser-warning"><h1>You are using an old browser</h1><p style="font-size: 14px; line-height: 18px;">Please update to a modern browser like <a href="http://www.mozilla.org/en-US/firefox/new/" target="_blank" class="box-link">Firefox</a>, <a href="http://www.apple.com/safari/download/" target="_blank" class="box-link">Safari</a> or <a href="https://www.google.com/chrome" target="_blank" class="box-link">Chrome</a> or download <a href="http://google.com/chromeframe" target="_blank" class="box-link">Google Chromeframe for InternetExplorer</a>.</p></div>','','',{'close_label':'&times;',overlay_close:true});
		}
	}
	// ----------------------------------------------------------------
	$('.contact-us').on('click', function (e) {
		$('html, body').animate({scrollTop: $('html').height()}, 800);
		return false;
	});

	var _form = $('#contact_form');
	_form.on('click', '.submit', function()
	{
		$.ajax({
			type : 'post',
			data: _form.serialize(),
			url: CI_BASE+'ajax/submit_form/',
			dataType: 'json'
		}).done(function( response )
		{
			$('.error').removeClass('error');
			//
			if( response.success == 'true' )
			{
				_form.find('input, textarea').val('');
				_form.addClass('success');
				setTimeout( function(){
					_form.removeClass('success');
				}, 3000);
			}
			else
			{
				$.each(response.errors, function(i,k)
				{
					_form.find('[name='+i+']').addClass('error');
				});
			}
		});
	});

	$('.newsletter').on('click', function(){
		$('.dialog').fs_dialog('show', 'ajax', {url:CI_BASE+'ajax/newsletter', 'refresh':false}, '', {'close_label':'&times;',overlay_close:true});
	});
	$('.dialog').on('click', '.button.submit', function(){
		$('.dialog').fs_dialog('hide');
	});

});