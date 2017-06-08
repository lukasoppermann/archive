// ----------------------------------------------------
// Slideshow Class
//
// dependencies:
//
// TODO:
// - add possibility to include different effects
// - add previous and next via click on right / left half
// - add animation for caption
// ----------------------------------------------------
// define functions 
;(function( $, window, document )
{
	// define vars
	var plugin_name = 'fs_slides',
			// methods
			methods = {
			// initialize gallery class
			init: function( settings ) 
			{
				return $(this).each(function(){
					// set variables
					var _slide = $(this),
							data 	= _slide.data(plugin_name),
							opts 	= $.extend({}, $.fn[plugin_name].defaults, settings);
					// check if it needs to be initializes
					if( !data )
					{
						_slide.data(plugin_name, {
							target: 		_slide,
							opts: 			opts,
							remaining: 	opts.speed,
							images: 		_slide.find(opts.image),
							first: 			_slide.find(opts.image).first(),
							wrap: 			_slide.find(opts.wrap),
							refreshed:  false
						});
					}
					else
					{
						_slide.data(plugin_name).refreshed == false;
					}
					// add click event for move
					_slide.on({
						click: function()
						{
							methods.next(_slide);
							// reset time
							methods.reset(_slide);
							// save start time
							_slide.data(plugin_name).start = new Date();
						},
						mouseenter: function()
						{
							methods.pause(_slide);
						},
						mouseleave: function()
						{
							methods.resume(_slide);
						}
					});
					// on load events
					$(document).ready(function()
					{
						// set sizes
						methods.refresh(_slide);
						// start loading images
						methods.load(_slide, _slide.data(plugin_name).first);
						// start autoplay
						methods.autoplay(_slide);
					});
					// add refresh to resize event
					$(window).on('resize', function(){
						clearTimeout( _slide.data(plugin_name).resize_fn );
						_slide.data(plugin_name).resize_fn = setTimeout( function(){
							methods.refresh(_slide);
						}, 100);
					});
				});
			},
			refresh: function( _slides )
			{
				// get size variables
				var parent_width 	= _slides.parent().width();
				var max_width 		= _slides.data(plugin_name).opts.max_width;
				if( max_width == 0 ){ max_width = parent_width; }
				// check image size
				var image					= _slides.data(plugin_name).images.first().find('img');
				if( image[0].naturalWidth != undefined && image[0].naturalWidth != 0 )
				{
					// set image width
					var img_width 		= image.width();
				}
				// width
				if( (_slides.data(plugin_name).opts.width == 0 
						&& parent_width >= max_width )
						|| 
						(_slides.data(plugin_name).opts.width >= max_width
						&& parent_width >= max_width)
						||
						( parent_width > max_width && _slides.data(plugin_name).opts.width < max_width) 
				)
				{
					_slides.data(plugin_name).opts.width = max_width;
				}
				else if( parent_width <= max_width )
				{
					_slides.data(plugin_name).opts.width = parent_width;
				}
				// set image width
				_slides.data(plugin_name).images.width(_slides.data(plugin_name).opts.width);
				// set slidehow container width
				_slides.css({'width':_slides.data(plugin_name).opts.width, 'height':'auto'});
				// set width of wrapper element
				_slides.data(plugin_name).wrap.width(_slides.data(plugin_name).images.length*_slides.data(plugin_name).opts.width);
				// set first active
				methods.first(_slides);
			},
			load: function(_this, image )
			{
				// cache img selection
				var _img = image.find('img');
				//
				if( !_img.attr('src') || (_img.attr('src') != _img.attr('data-src') && _img.attr('src') != _img.attr('data-mobile-src')) )
				{
					if( _img.data('mobile-src') != undefined && $('body').hasClass('mobile') )
					{
						_img.attr('src',_img.data('mobile-src'));
					}
					else
					{
						_img.attr('src',_img.data('src'));
					}
					// load image
					if( _img.height() != '0' )
					{
						// once image is loaded
						image.addClass('loaded');
						// check if not refreshed yet
						if( _this.data(plugin_name).refreshed !== true )
						{
							_this.data(plugin_name).refreshed = true;
							methods.refresh(_this);
						}
						// get next image, index and size of image obj
						var next 	= image.next(_this.data(plugin_name).opts.image);
						var size 	= _this.data(plugin_name).images.size();
						var index = _this.data(plugin_name).images.index(next);
						// check if next image needs to be loaded
						if( next.find('img').data('src') != undefined && !next.hasClass('loaded') )
						{
							// load next image
							methods.load( _this, next );
						}
						else
						{
							while( index > -1 && index < size && (next.find('img').data('src') == undefined || !next.hasClass('loaded')) )
							{
								// move to next item
								next = next.next(_this.data(plugin_name).opts.image);
								index = _this.data(plugin_name).images.index(next);
								// check if it works
								if( next.find('img').data('src') != undefined && !next.hasClass('loaded') )
								{
									// load next image
									methods.load( _this, next );
									break;
								}
							}
						}
					}
					else
					{
						_img.load(function()
						{
							// once image is loaded
							image.addClass('loaded');
							// check if not refreshed yet
							if( _this.data(plugin_name).refreshed != true )
							{
								_this.data(plugin_name).refreshed = true;
								methods.refresh(_this);
							}
							// load next image
							methods.load( _this, image.next(_this.data(plugin_name).image) );
						});
					}
				}
				else
				{
					// once image is loaded
					image.addClass('loaded');
					// check if not refreshed yet
					if( _this.data(plugin_name).refreshed !== true )
					{
						_this.data(plugin_name).refreshed = true;
						methods.refresh(_this);
					}
				}
			},
			// adding autoplay
			autoplay: function(_this)
			{
				// save start time
				_this.data(plugin_name).start = new Date();
				// start slideshow
				methods.resume(_this);
			},
			// stop autoplay
			pause: function(_this)
			{
				// remove autoplay
				window.clearInterval(_this.data(plugin_name).autoplay);
				// save remaining time
				_this.data(plugin_name).remaining -= new Date() - _this.data(plugin_name).start;
			},
			// reset autoplay duration
			reset: function(_this)
			{
				// reset remaining time
				_this.data(plugin_name).remaining = _this.data(plugin_name).opts.speed;
				// remove autoplay
				window.clearInterval(_this.data(plugin_name).autoplay);
				// add autoplay with reset time
				_this.data(plugin_name).autoplay = window.setInterval(function(){
					methods.next(_this)
				}, _this.data(plugin_name).remaining);
			},
			// resume autoplay
			resume: function(_this)
			{
				// remove autoplay
				window.clearInterval(_this.data(plugin_name).autoplay);
				// resume autoplay
				_this.data(plugin_name).autoplay = window.setInterval(function()
				{
					// move to next slide
					methods.next(_this);
					// reset time
					methods.reset(_this);
					// save start time
					_this.data(plugin_name).start = new Date();
					//
				}, _this.data(plugin_name).remaining);
			},
			// move to next element
			next: function(_this)
			{
				// set current and next item
				var _current = _this.find('.'+_this.data(plugin_name).opts.active);
				var _next 	= _current.next(_this.data(plugin_name).opts.image);
				//
				if( _next.length > 0 )
				{
					// anmiate forward
					_this.data(plugin_name).wrap.animate({'left':'-='+_this.data(plugin_name).opts.width});
					// change active
					_current.removeClass(_this.data(plugin_name).opts.active);
					// set next to active
					_next.addClass(_this.data(plugin_name).opts.active);
				}
				else
				{
					// anmiate to first
					_this.data(plugin_name).wrap.animate({'left':'0'});
					// change active
					_current.removeClass(_this.data(plugin_name).opts.active);
					_this.data(plugin_name).first.addClass(_this.data(plugin_name).opts.active);
				}
			},
			// set first active
			first: function(_this)
			{
				// set current item
				_this.data(plugin_name).current = _this.find('.'+_this.data(plugin_name).opts.active);
				// anmiate to first
				_this.data(plugin_name).wrap.animate({'left':'0'});
				// change active
				if( _this.data(plugin_name).current != undefined )
				{
					_this.data(plugin_name).current.removeClass(_this.data(plugin_name).opts.active);
				}
				// set first active
				_this.data(plugin_name).first.addClass(_this.data(plugin_name).opts.active);
			},
			// move to previous element
			previous: function( _this )
			{
				// // set current and previous item
				// _current = _this.find('.'+methods.settings.active);
				// _next 	= _current.prev(methods.settings.image);
				// //
				// _wrap.animate({'left':'+='+methods.settings.width});
				// _this.find('.'+methods.settings.active).removeClass(methods.settings.active).prev(methods.settings.image).addClass(methods.settings.active);
			},
			// destory
			destroy: function()
			{
				$(this).each(function()
				{
					var _slides = $(this);
					_slides.off("click mouseenter mouseleave");
					methods.pause(_slides);
				});
			}
		};
	//-------------------------------------------
	// default options
	$.fn[plugin_name] = function( method )
	{
		// fetch arguments
		var settings = arguments;
		// Method calling logic
		if ( methods[method] ) 
		{
			return $(this).each(function(){
				 methods[ method ].apply( this, Array.prototype.slice.call( settings, 1 ));
			});
		} 
		else if ( typeof method === 'object' || ! method ) 
		{
			return $(this).each(function(){
				methods.init.apply( this, settings );
			});
		}
		else
		{
			$.error( 'Method ' +  method + ' does not exist on jQuery.'+plugin_name );
		}
	}
	//-------------------------------------------
	// default options
	$.fn[plugin_name].defaults = {
		fx: 					'slide',
		loaded: 			'loaded',
		active: 			'active',
		wrap: 				'.image-wrap',
		image: 				'.slide',
		width: 				0,
		height: 			0,
		max_width: 		0,
		min_height:   0,
		speed: 				5000,
		easing: 			'swing'
	};
})( jQuery, window, document);