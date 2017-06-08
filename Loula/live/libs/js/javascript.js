$(function(){
	var _window = $(window);
	// ----------------------------------------------------------------
	// check browser
	if( $.browser.msie )
	{
		if( $.browser.version <= 8 )
		{
			if( $('#overlay').css('display') == 'none' )
			{
				$('#overlay').width(_window.width()).height(_window.height()).fadeIn(300);
				$(".dialog_box").fadeIn(600);
				$("#dialog_content").html('<h1>You are using a very old browser</h1><p style="font-size: 14px; line-height: 18px;">Please update to a modern browser like <a href="http://www.mozilla.org/en-US/firefox/new/" target="_blank" class="box-link">Firefox</a>, <a href="http://www.apple.com/safari/download/" target="_blank" class="box-link">Safari</a> or <a href="https://www.google.com/chrome" target="_blank" class="box-link">Chrome</a> or download <a href="http://google.com/chromeframe" target="_blank" class="box-link">Google Chromeframe for InternetExplorer</a>.</p>');
				$(".dialog_box").css({"width":450,"height":150,"minHeight":150,"left":"50%","marginLeft":-225,"top":200});
				$("html").animate({ scrollTop: 0 }, 300);
			}
		}
	}
	// ----------------------------------------------------------------
	// image sorting for homepage
	var $container = $('.page-content');
	// calculate hight to fit tiles
	$container.width(310*(Math.floor($container.width()/310)));
	$container.height(360*(Math.floor($container.height()/360)));
	//
	$(window).resize(function(){
		$("#footer").width(_window.innerWidth());
		$container.width(310*(Math.floor($('#content').width()/310)));
		$container.height(360*(Math.floor($('#content').height()/360)));
	});
	//
	$container.imagesLoaded( function(){
		$container.masonry({
			itemSelector : '.home-item',
			columnWidth: 310,
			isAnimated: true
		});
	});
	// resize news container
	
	var _flex = $('.flex-height');
	var flex_count = Math.ceil(_flex.height()/350);
	_flex.height(flex_count*350 + (10*(flex_count-1)));
	// footer function
	var _footer = $('#footer'),
	_document = $(document),
	_body = _document.find('body'),
	bottomPadding = 40,
	totalHeight = 0, 
	footer_active = false,
	foot_nav = $("#footer_nav").outerHeight();
	//
	_footer.height(_footer.find('#footer_body').height()+_footer.find('#footer_nav').height()+bottomPadding);
	// $("#content").css({'marginBottom': bottomPadding+'px'});
	var c_height = (bottomPadding + $("#content").height() + $("#header").height());
	// ----------------------------------------------------------------
	// IE
	//
	$(window).load(function()
	{
		if( $('html').hasClass('ie') )
		{
			$('#content').height($('#content').height()+40);
		}
	});
	var ieHeight = 0;
	if( $('html').hasClass('ie') )
	{
		ieHeight = 250;
	}

	// ----------------------------------------------------------------
	// footer
	$(window).load(function()
	{
		_window = $(window);
		c_height = $('#content').height();
		// if less content then total window height
		if(c_height < _window.height())
		{
			if(c_height + foot_nav < _window.height())
			{
				_footer.css({'position':'absolute', 'top':bottomPadding+c_height+150, 'width' : _window.innerWidth()}).addClass('absolute').removeClass('fixed');
			}
			else
			{
				_footer.css({'position':'absolute', 'top':c_height, 'width' : _window.innerWidth()}).addClass('absolute').removeClass('fixed');			
			}
			_body.height(c_height+_footer.height());
		}
		// ----------------------------------------------------------------
		// more content than total window height
		else
		{
			_footer.css({'bottom': -_footer.height()+'px'}).delay(300).animate({'bottom': -(_footer.height()-foot_nav)+'px'}, 600).addClass('fixed');
			$("#content").css({'marginBottom': '+='+_footer.height()+'px'});
			// -------------------------------------
			// on scroll
			_window.scroll(function() 
			{
				// calculate total height
				totalHeight = ( (bottomPadding + $("#content").innerHeight() + $("#header").innerHeight()) - _window.innerHeight() );
				// -------------------------------------
				// if footer is not active
				if( footer_active == false )
				{
					if(_window.scrollTop() > ((totalHeight + foot_nav + bottomPadding) - ieHeight) ) 
					{
						footer_active = true;
						_footer.css({'position':'absolute', 'top':totalHeight+bottomPadding+bottomPadding+_window.height(), 'width' : _window.innerWidth()}).addClass('absolute').removeClass('fixed');
						if( $('html').hasClass('ie') && !$('#content').hasClass('ieadd') )
						{
							$('#content').addClass('ieadd').height($('#content').height());
						}
					}
				}
				// if footer is active
				else
				{
					if(_window.scrollTop() < totalHeight + bottomPadding + bottomPadding + foot_nav - ieHeight) 
					{
						footer_active = false;
						_footer.css({'position':'fixed', 'top': _window.height()-foot_nav}).addClass('fixed').removeClass('absolute');
					}	
				}
			});
		}
	});
	// links to scroll to bottom
	var scrollBottom = (bottomPadding + _footer.outerHeight() + $("#content").outerHeight() + $("#header").outerHeight()) - _window.height();
	$(".to-bottom").on('click', function(){
		$("html").animate({ scrollTop: scrollBottom }, 300);
	});
	//
	var dialog_content = new Array();
	$('body').on('click', '.dialog-box-link', function(){
		$('#overlay').width(_window.width()).height(_window.height()).fadeIn(300);
		$(".dialog_box").css({width:800,height:'auto',"left":"50%","marginLeft":-400,"top":100}).fadeIn(600);
		var _link 	= $(this).data('dialog-type');
		var _url 	= $(this).data('url');
		if(dialog_content[_link] == null && _link == 'policy')
		{
			var request = $.ajax({
				url: CI_ROOT+"ajax/get_page",
				type: "POST",
				data: {id : 1},
				dataType: "html"
			});
			request.done(function(msg) {
				dialog_content[_link] = msg;
				$("#dialog_content").html(dialog_content[_link]);
				$(".dialog_box").removeClass('map').height($("#dialog_content").outerHeight()+20);
			});
		} 
		if(dialog_content[_link] == null && _link == 'gmap')
		{

			dialog_content[_link] = '<iframe width="800" height="550" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com.au/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=149+Toorak+Road,+South+Yarra,+Vic.+3141,+Australia&amp;aq=&amp;sll=-25.324167,135.703125&amp;sspn=44.467116,93.076172&amp;ie=UTF8&amp;hq=&amp;hnear=149+Toorak+Rd,+South+Yarra+Victoria+3141&amp;t=m&amp;view=map&amp;ll=-37.829853,144.993181&amp;spn=0.035252,0.068665&amp;z=14&amp;iwloc=A&amp;output=embed"></iframe>';
			$("#dialog_content").html(dialog_content[_link]);
			$(".dialog_box").addClass('map').height($("#dialog_content").outerHeight()+20);
		}
		if(dialog_content[_link] == null && _link == 'what-size-am-i')
		{
			$.get(CI_ROOT+"media/files/what-size-am-i.html", function(data) {
				dialog_content[_link] = data;
				$("#dialog_content").html(dialog_content[_link]);
			});
		}
		if(dialog_content[_link] == null && _link == 'contact-us')
		{
			$.get(CI_ROOT+"media/files/contact-us.html", function(data) {
				dialog_content[_link] = data;
				$("#dialog_content").html(dialog_content[_link]);
				var data = {};
				data['url'] = _url;
				$('#email_form').contact_form(data);
			});
		}
		if(_link == 'contact-us')
		{
			var data = {};
			data['url'] = _url;
			$('#email_form').contact_form(data);	
		}
		if(dialog_content[_link] != null)
		{
			if( _link == 'gmap')
			{
				$(".dialog_box").addClass('map');
			}
			else
			{
				$(".dialog_box").removeClass('map');
			}
			$("#dialog_content").html(dialog_content[_link]);
			$(".dialog_box").height($("#dialog_content").outerHeight()+20);
		}
		$("html").animate({ scrollTop: 0 }, 300);
	});
	$('#overlay, .dialog_box .close').on('click', function(){
		$('#overlay, .dialog_box').fadeOut(400);
	})
	// launch slideshow
	$("#footer_images").on("click", function(){
		$.slimbox(slide_images, 1, {loop: true});	
	});
	// ------------------------
	// load shoe
	$(".products").on('click', '.item', function(){
		var request = $.ajax({
			url: CI_ROOT+"store/get_product_page",
			type: "POST",
			data: {id : $(this).data('product-id'), current_store: $('body').attr('id')},
			dataType: "html"
		});
		$("html").animate({ scrollTop: 0 }, 300);
		request.done(function(product) {
			$(".product-wrapper").html(product);
			if( $('#slideshow').find('img').size() > 1 )
			{
				$('#slideshow').cycle(slide_settings);
			}
			else
			{
				$('#slideshow').find('img').fadeIn();
			}
			$('.product-shots').on('click', '.shot', function(){
				$('#slideshow').cycle($(this).data('slide'));
			});
		});
	});
	//----------------
	var slide_settings = {
		speed: 		700, 
		timeout: 	4000,
		pause:  	1,
		next:   	'#slideshow'
	};
	if( $('#slideshow').find('img').size() > 1 )
	{
		$('#slideshow').cycle(slide_settings);
	}
	else
	{
		$('#slideshow').find('img').fadeIn();
	}
	var hash = window.location.hash.slice(1);
	if(hash != null && hash != undefined && hash != '')
	{
		var request = $.ajax({
			url: CI_ROOT+"store/get_product_page",
			type: "POST",
			data: {id : hash, current_store: $('body').attr('id')},
			dataType: "html"
		});
		request.success(function(product) {
			if(product != '')
			{
				$(".product-wrapper").html(product);
				if( $('#slideshow').find('img').size() > 1 )
				{
					$('#slideshow').cycle(slide_settings);
				}
				else
				{
					$('#slideshow').find('img').fadeIn();
				}
				$('.product-shots').on('click', '.shot', function(){
					$('#slideshow').cycle($(this).data('slide'));
				});
			}
		});
	}
	// ------------------------
	// add slideshow
	$('.product-shots').on('click', '.shot', function(){
		$('#slideshow').cycle($(this).data('slide'));
	});
	// ------------------------
	// add filters
	$(".filter-list").fs_filter();
	$("#all_none").on('click', function(){
		var _text = $(this).find('span').data('text');
		$(this).toggleClass('all none').removeClass('active').find('span').data('text', $(this).text()).text(_text);
	});
	
	// ----------------------------------------------------------------------------------------------------------------
	// Shopping Cart
	if( $("#shopping_cart").find(".amount").text() != '' && $("#shopping_cart").find(".amount").text() != '0' 
		&& $("#shopping_cart").find(".amount").text() != undefined)
	{
		$("#shopping_cart").fadeIn();
	}
	// -----------------------------------
	// add to cart
	$("body").on('click', '#add_to_cart', function(){
		var _this = $(this);
		$.ajax({
			type : 'post',
            dataType : 'json',
            data : {id:_this.data("product-id"), size: _this.parents('.product-order').find('.select-options').find('option:selected').val()},
            url : CI_ROOT+'ajax/add_cart/'+_this.data("product-id"),
            success : function( r ){
				if( r.error == undefined )
				{
					$("#shopping_cart").find(".amount").text(r.amount);
					if( r.amount == 1)
					{
						$("#shopping_cart").fadeIn();
					}
				}
			}
		});
	});
	// --------------------------
	// open cart
	$("#shopping_cart").on('click', function()
	{
		// show overlay
		$('#overlay').width(_window.width()).height(_window.height()).fadeIn(300);
		// load & fadeIn shopping cart
		$.ajax({
			dataType: 'html',
			url: CI_ROOT+'ajax/get_cart/',
			success: function( r )
			{
				$("#cart").html(r);
				$(".shopping_cart").fadeIn(600);
			}
		});
		// scroll to top
		$("html").animate({ scrollTop: 0 }, 300);
		//
		return false;
	});
	$('#overlay, .shopping_cart .close').on('click', function(){
		$('#overlay, .shopping_cart').fadeOut(400);
	})
	// --------------------------
	// live update cart
	$("#cart").on('change', 'select', function()
	{
		// vars
		var _changed_item	= $(this).parents('.cart-item');
		var total 			= 0;
		var data			= {};
		// calculate total
		$('.cart-item').each(function(){
			total += $(this).find('.select-qty option:selected').val() * $(this).find('.price span').text();
			if( $(this).find('.select-qty option:selected').val() == 0 )
			{
				$(this).fadeOut(300);
			}
		});
		// data
		data[_changed_item.data('id')] = { rowid: _changed_item.data('rowid'), id: _changed_item.data('id'), options: {size: _changed_item.find('.select-size').find('option:selected').val()}, qty: _changed_item.find('.select-qty').find('option:selected').val() };
		// update cart
		$.ajax({
			type : 'post',
			data: {products: data},
			dataType: 'json',
			url: CI_ROOT+'ajax/update_cart',
			success: function( r )
			{
				$("#shopping_cart").find(".amount").text( r.amount );
				if( r.amount == 0 )
				{
					$("#shopping_cart").fadeOut(600);
				}
				else
				{
					$("#shopping_cart").fadeIn(600);
				}
			}
		});
		// update total
		if( total == undefined || total == NaN || total == 'NaN' || isNaN(total))
		{
			total = '0';
		}
		
		$('.total-amount, #confirm_bubble .total .amnt').text(total);
		$('#paypalamount').val(total);
	});
	// --------------------------
	// update cart
	var order_id;
	$("body").on('click', '.checkout.button', function()
	{
		order_id = $("#item_number").val();
		var error = '';
		var data = {};
		// 
		$(".cart-item").each(function()
		{
			if( $(this).find('.select-size').find('option:selected').val() == 'Choose your size' )
			{
				error = 'error';
			}
			var _changed_item = $(this);
			// add items
			data[_changed_item.data('id')] = { rowid: _changed_item.data('rowid'), img: _changed_item.find('.image img').attr('src'), id: _changed_item.data('id'), options: {size: _changed_item.find('.select-size').find('option:selected').val()}, qty: _changed_item.find('.select-qty').find('option:selected').val() };
			// update cart
		});
		
		//
		if( error == '' )
		{
			$("html").animate({ scrollTop: 0 }, 300);
			$("#cart_overlay").css({'width':$('.shopping_cart').width(),'height':$('.shopping_cart').height()}).show();
			$("#confirm_bubble").show();
			
			$.ajax({
				type : 'post',
				data: {products: data},
				dataType: 'text',
				url: CI_ROOT+'ajax/get_price',
				success: function( r )
				{
					$('.total-amount, #confirm_bubble .total .amnt').text(r);
					$('#paypalamount').val(r);
					//
				}
			});
			
			$.ajax({
				type : 'post',
				data: {products: data, order_id: order_id},
				dataType: 'json',
				url: CI_ROOT+'ajax/insert_order'
			});
		}
		else
		{
			$("html").animate({ scrollTop: 0 }, 300);
			$("#cart_overlay").css({'width':$('.shopping_cart').width(),'height':$('.shopping_cart').height()}).show().delay(1500).fadeOut(500);
			$("#error_bubble").show().delay(1500).fadeOut(500);
		}
	});
	// --------------------------
	// update cart
	$("body").on('click', '#cart_overlay', function()
	{
		$("#cart_overlay, #confirm_bubble").fadeOut();
		$.ajax({
			type : 'post',
			data: {order_id: order_id},
			dataType: 'json',
			url: CI_ROOT+'ajax/delete_order'
		});
	});
	// ----------------------------------------------------------------------------------------------------------------
	// Order Confirm
	var _order_conf = $("#order_conf");
	if(_order_conf.size() > 0)
	{
		_order_conf.hide().fadeIn().delay(1500).fadeOut();
		$("#overlay").fadeIn().delay(1500).fadeOut();
	}
	// ----------------------------------------------------------------------------------------------------------------
	// mailchimp
	$("#mailchimp").click(function(){
		$('#overlay').width(_window.width()).height(_window.height()).fadeIn(300);
		$(".mailchimp_box").fadeIn(600);
		$("html").animate({ scrollTop: 0 }, 300);
		//
		return false;
	});
	$('#overlay, .mailchimp_box .close').on('click', function(){
		$('#overlay, .mailchimp_box').fadeOut(400);
	})
	// ----------------------------------------------------------------------------------------------------------------
	$.fn.contact_form = function( options ) {
        // settings for form
        var settings = $.extend( {
            'name' : '#name',
            'email' : '#email',
            'message' : '#text',
			'submit' : '#submit',
            'email_regex' : /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/,
            'form_url' : CI_ROOT+'ajax/sendform',
			'url' : ''
        }, options);
        // define vars
        var $form = this;
        var values = {
            'name' : null,
            'email' : null,
            'message' : null
        };
        var checked = true;
        // function for submit button
        $(settings['submit']).on('click', function(){
			if( !$(settings['submit']).hasClass('disabled') )
			{
				var data = $form.serialize()+"&page_url="+settings['url'];
	            // submit email
	            $.ajax({
	                type : 'post',
	                dataType : 'json',
	                data : data,
	                url : settings['form_url'],
	                success : function( r ){
	                    if( r != null && r.sent )
	                    {                          
	                        $form.slideUp(500,function(){
	                            $form.before('<p class="message_box_green" style="display:none;">Your Message has been sent successfully.</p>');      
	                            $('.message_box_green').fadeIn();
	                        });
	                    }
	                    else
	                    {
	                        $('.message_box_red').remove();
	                        $form.before('<p class="message_box_red" style="display:none;">There was a problem and the message was probably not sent.</p>');
	                        $('.message_box_red').fadeIn();
	                    }
	                }
	            });
			}     
        	// return false so site does not refresh
            return false; 
        });
        // function for toggle submit button
        return $form.on('keydown keyup focus blur change', 'input, textarea, select', function(){
            checked = true;
            $.each(values, function(v){
                if($(settings[v]).val() == '')
                {
                    checked = false;
					$(settings[v]).addClass('missing');
                }
                else
                {
                    if(settings[v+'_regex'] != null)
                    {
                        if (!settings[v+'_regex'].test($(settings[v]).val())) {
                            checked = false; 
							$(settings[v]).addClass('missing');
                        }
                    }
					$(settings[v]).removeClass('missing');
                }
            });
            if(checked == true)
            {
                $(settings['submit']).removeClass('disabled');
            }
            else
            {
                $(settings['submit']).addClass('disabled');
            }
        });
    };
});