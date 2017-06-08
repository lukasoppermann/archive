// ----------------------------------------------------
// jQuery Sticky Top fn
//
// dependencies
//
// TODO:
// ----------------------------------------------------
// define functions 
;(function( $, window )
{
	// define vars
	var plugin_name = 'fs_sticky_top',
			_this, _window, pos_start, active = false, _body, resize_fn;
	// define methods
	var methods = {
		// settings object
		settings: {},
		// initialize class
		init: function( settings ) 
		{ 
			// Extend default options with those provided
			methods.settings = $.extend({}, $.fn[plugin_name].defaults, settings);
			// cache selection
			_this = $(this);
			_window = $(window);
			_body = $('body');
			// check if this exists
			if( _this != undefined )
			{
				// calc start offset
				pos_start = Math.round(_this.offset().top + _this.height() + methods.settings.add_offset);
				// run scroll event once
				methods.do_scroll();
				// add scroll event to window
				_window.bind('scroll', methods.do_scroll);
				// add refresh to resize event
				resize_fn = function(fn){
					clearTimeout( fn );
					fn = setTimeout( methods.refresh, 100);
				};
				_window.on('resize', resize_fn);
			}
		},
		// refresh calculations for offset
		refresh: function()
		{
			if( active === false )
			{
				pos_start = Math.round(_this.offset().top + _this.height() + methods.settings.add_offset);
			}
			methods.do_scroll();
		},
		// scroll
		do_scroll: function()
		{
			if( _window.scrollTop() > pos_start && !_body.hasClass('mobile') && !_body.hasClass('tablet-small') && !_body.hasClass('loaded-tablet'))
			{	
				// check if menu is not active yet
				if( active === false )
				{
					active = true;
          _this.addClass(methods.settings.active_class).animate({'top':0}, methods.settings.speed, methods.settings.easing);
					methods.settings.scroll_active_fn.apply(this);
				}
			}
			else
			{
				active = false;
				_this.removeClass(methods.settings.active_class).attr('style','');
				methods.settings.scroll_deactive_fn.apply(this);
			}
		},
		destory: function(){
			// set active false
			active = false;
			_this.removeClass(methods.settings.active_class).attr('style','');
			methods.settings.scroll_deactive_fn.apply(this);
			// unbind scroll
			_window.unbind('scroll', methods.do_scroll);
			// add refresh to resize event
			_window.on('resize', resize_fn);
		}
	};
	
	$.fn[plugin_name] = function( method ){
		// Method calling logic
		if ( methods[method] ) 
		{
			if( _this != undefined )
			{
				return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
			}
		} 
		else if ( typeof method === 'object' || ! method ) 
		{
			return methods.init.apply( this, arguments );
		}
		else
		{
			$.error( 'Method ' +  method + ' does not exist on jQuery.'+[plugin_name] );
		}
	}
	//-------------------------------------------
	// default options
	$.fn[plugin_name].defaults = {
		fx: 								'slide',
		add_offset: 				10,
		speed: 							500,
		easing: 						'swing',
		add_top_link: 			false,
		active_class:				'fixed',
		scroll_active_fn: 	function(){},
		scroll_deactive_fn: function(){},
	};
	
})( jQuery, window);