// ----------------------------------------------------
// Loading Class
//
// dependencies:
//
// ----------------------------------------------------
// define functions 
;(function( $, window, document )
{
	var plugin_name = 'fs_history',
			plugin_data = {},
	// methods
	methods = {
		// plugin options
		opts: {},
		// check for history api support
		supports_history_api: function()
		{
			return !!(window.history && history.pushState);
		},
		// initialize gallery class
		init: function( settings ) 
		{
			// check if history api is supported
			if( methods.supports_history_api() )
			{
				// set options
				methods.opts	= $.extend({}, $.fn[plugin_name].defaults, settings);
				// set animations
				methods.animations = $.extend(methods.animations, methods.opts.animations);
				// set variables
				var _this 				= $(this),
						this_selector =	this.selector,
						_window 			= $(window),
						_head 				= $('head'),
						_title 				= _head.find('title'),
						_css					= _head.find(methods.opts.link);
				// check if it needs to be initializes
				if( !plugin_data[this_selector] )
				{
					plugin_data[this_selector] = {
						selection: 			_this,
						this_selector:	this_selector,
						body: 					$('body'),
						window: 				_window,
						head: 					_head,
						title: 					_title,
						pushed: 				false,
						loading: 				false,
						js: 						{},
						resources:  		2,
						content: 				{},
						new: 						{}
					};
					// add css
					var css = new Array();
					$.each(_css.get(), function(i, file){
						css[i] 	= $(file).attr('href');
					}); 
					// add content
					plugin_data[this_selector]['content'][_css.data(methods.opts.path)] = {
						'page'			: $('.'+methods.opts.current_page),
						'css' 			: css,
						'_css'			: _css.get(),
						'namespace'	: _css.data(methods.opts.namespace),
						'title' 		: _title.text(),
						'body_class': plugin_data[this_selector].body.data('page_class'),
						'element' 	: plugin_data[this_selector].body.find('.'+methods.opts.activatable+methods.opts.active)
					};
				}
				// create current path
				var current_path = _css.data(methods.opts.path);
				// add click event
				plugin_data[this_selector].body.on('click', this_selector, function(e)
				{
					// stop event propagation
					e.stopPropagation();
					// set current page
					if( current_path != undefined )
					{
						var path = current_path;
						current_path = undefined;
					}
					else
					{
						var path = location.href.split("#")[0]
					}
					// set pages.loaded to true
					pages.loaded = true;
					// define current
					plugin_data[this_selector].current = {
						'path': path,
						'page': $('.'+methods.opts.current_page)
					};
					methods.click( $(this), this_selector);
					// return false
					return false;
				});
				// listen for popstate
				_window.bind('popstate', function(e)
				{
					if( plugin_data[this_selector].pushed == true )
					{	
						// run popstate
						methods.popstate( this_selector, e.state );
					}
				});
			}
			// return selection
			return $(this);
		},
		// click event
		click: function( _this, selector )
		{
			// check if new item is clicked
			if( _this.attr('href') != plugin_data[selector].current.path )
			{
				if( plugin_data[selector].loading != true )
				{
					var path = _this.attr('href');
					plugin_data[selector].loading = true;
					
					// check if item exists
					if( !plugin_data[selector].content[path] )
					{
						plugin_data[selector].new.loaded = false;
					}
					else
					{
						plugin_data[selector].new.loaded = true;
					}
					// set var true to show, that a pushstate was added
					plugin_data[selector].pushed = true;
					// get page selection
					plugin_data[selector].current.page = plugin_data[selector].content[plugin_data[selector].current.path].page;
					// start loading
					methods.loading( true, selector );
					// get new
					methods.load(path, selector );
				}
			}
		},
		// fn to load content
		load: function( path, selector, callback )
		{
			if(typeof(callback)==='undefined') callback = function(){};
			// define variables
			var response = {};
			// check if content exists
			if( !plugin_data[selector].content[path] || plugin_data[selector].content[path].page == undefined )
			{
				// create content object
				plugin_data[selector].content[path] = {};
				// run ajax request
				var ajax = $.ajax({
					type : 'post',
					data: {'ajax':true},
					dataType: 'json',
					url: path
				}).done(function( r ){
					// response
					response = r;
					// create new page
					response.page = $('<div class="'+methods.opts.page+' '+methods.opts.current_page+'">'+response.content+'</div>').css({'opacity':'0','marginTop':'20%'});
					// add page to DOM
					plugin_data[selector].current.page.after(response.page);
					// set path
					plugin_data[selector].new.path = response.path = path;
					// create entry
					plugin_data[selector].content[path] = response;
					// push history
					methods.pushstate(response);
					// activate css
					methods.css( response.css, selector );
					// activate js
					methods.js( response.js, selector);
					// callback
					callback( response );
					// if loading fails
				}).fail(function(r)
				{
					plugin_data[selector].new.path = plugin_data[selector].current.path;
					// get page selection
					plugin_data[selector].new.page = plugin_data[selector].content[plugin_data[selector].current.path].page;
					// stop loading animation
					methods.animations.loading(true, selector);
				});
			}
			// page is already cached
			else
			{
				// set response
				response = plugin_data[selector].content[path];
				// set path
				plugin_data[selector].new.path = response.path = path;
				// push history
				methods.pushstate(response);
				// activate css
				methods.css( response.css, selector );
				// activate js
				methods.js( response.js, selector );
				// callback
				callback( response );
			}
		},
		// load css
		css: function( css, selector, callback, failcallback )
		{
			// preset variables
			if(typeof(callback)==='undefined') callback = function(){};
			if(typeof(failcallback)==='undefined') failcallback = function(){};
			//
			if( css != undefined && css != '' )
			{
				// split css files
				if( typeof(css) != 'object' )
				{
					css = css.split(",");
				}
				// add testing element
				var _css_loaded = $('<div>').attr('id', 'namespace_'+plugin_data[selector].content[plugin_data[selector].new.path].namespace).appendTo('body'),
				// define variables
				output 	= '',
				count 	= css.length;
				// loop through css files
				$.each(css, function( i, file )
				{
					var link = plugin_data[selector].head.find("link[href='"+file+"']");
					// check ig css file exists
					if( link.length == 0 )
					{
						output += "<link href='"+file+"' type='text/css' rel='stylesheet' />";
					}
					else
					{
						link.removeAttr('disabled');
					}
					// check if last file is loaded
					if( 1+i == count )
					{
						// add files to DOM
						plugin_data[selector].head.append(output);
						// create _css object 
						plugin_data[selector].content[plugin_data[selector].new.path]['_css'] = {};
						// loop through css files
						$.each(css, function( i, file )
						{
								plugin_data[selector].content[plugin_data[selector].new.path]['_css'][i] = plugin_data[selector].head.find("link[href='"+file+"']");
						});
						// checking fn
						methods.css_check(_css_loaded, selector, methods.show_page, failcallback);
					}
				});
			}
			// no css files to be loaded
			else
			{
				callback( selector );
				methods.show_page( selector );
			}
		},
		// check if css is loaded
		css_check: function(element, selector, callback, failcallback)
		{
			// preset variables
			if(typeof(callback)==='undefined') callback = function(){};
			if(typeof(failcallback)==='undefined') failcallback = function(){};
			// initial check
			var check_css_loaded = function() { return element.height() == '1'; }
			// loop
			if( !check_css_loaded() )
			{
				var tries = 0,
						interval = 10,
						timeout = 20000; // max ms to check for
				setTimeout(function timer() 
				{
					if ( check_css_loaded() ) 
					{
						callback( selector );
						element.remove();
					} 
					else if (tries*interval >= timeout)
					{
						failcallback( selector );
					}
					else {
						tries++;
						setTimeout(timer, interval);
					}
				}, interval);
			}
			else
			{
				callback( selector );
				element.remove();
			}
		},
		// load js
		js: function( js, selector, callback)
		{
			// preset variables
			if(typeof(callback)==='undefined') callback = function(){};
			//
			if( js != undefined && js != '' )
			{
				// split css files
				if( typeof(js) != 'object' )
				{
					js = js.split(",");
				}
				// define variables
				var count = js.length;
				// loop through js files
				$.each(js, function( i, file )
				{
					if( plugin_data[selector].js[file] == undefined )
					{
						$.getScript(file).done(function(){
							// check if last script
							if( count == 1+i )
							{
								callback( selector );
								methods.show_page( selector );
							}
						});
						//
						plugin_data[selector].js[file] = 'loaded';
					}
					else
					{
						// check if last script
						if( count == 1+i )
						{
							callback( selector );
							methods.show_page( selector );
						}
					}
				});
			}
			else
			{
				callback( selector );
				methods.show_page( selector );
			}
		},
		// js init
		js_init: function( init, selector )
		{
			if( init == true )
			{
				if( plugin_data[selector].new.path != undefined && pages[plugin_data[selector].content[plugin_data[selector].new.path].namespace] != undefined )
				{
					pages[plugin_data[selector].content[plugin_data[selector].new.path].namespace].init();
				}
			}
			else if( pages[plugin_data[selector].content[plugin_data[selector].current.path].namespace] != undefined )
			{
				pages[plugin_data[selector].content[plugin_data[selector].current.path].namespace].destroy();
			}
		},
		// show page if all resources are available
		show_page: function( selector )
		{
			// all assets loaded?
			if( plugin_data[selector].resources == 1 )
			{
				// deactivate loading
				methods.loading(false, selector);
				// set resources
				plugin_data[selector].resources = 2;
			}
			// not all assets loaded
			else
			{
				plugin_data[selector].resources--;
			}
		},
		// page animation object
		animations: 
		{
			default: function( hide, selector, callback )
			{
				// preset variables
				if(typeof(callback)==='undefined') callback = function(){};
				// content hide animation
				if( hide === true )
				{
					$('.'+methods.opts.load_hide).animate({'opacity':0}, methods.opts.loading_speed);
					plugin_data[selector].current.page.removeClass(methods.opts.current_page).animate({'marginTop':'20%','opacity':0}, methods.opts.loading_speed, function()
					{
						$(this).hide();
						callback(false, selector);
						methods.disabled_css( selector );
					});
				}
				// // content show animation
				else
				{
					plugin_data[selector].content[plugin_data[selector].new.path].page.css({'display':'block','opacity':0}).animate({'marginTop':'0','opacity':1}, methods.opts.loading_speed, function(){
						$('.'+methods.opts.load_hide).animate({'opacity':1}, methods.opts.loading_speed);
					});
				}
			},
			// show/hide loading animation
			loading: function( stop, selector, callback )
			{
				// preset variables
				if(typeof(callback)==='undefined') callback = function(){};
				// start the loading animation
				if( stop == undefined || stop == false )
				{
					methods.opts.loading.find('.loading-box').css('top',0).animate({'top':'50%'}, methods.opts.loading_speed);
					methods.opts.loading.animate({'opacity':'1'}, 100, function(){
						methods.opts.loading.addClass('active');
						callback(true, selector);
					});
				}
				// stop the loading animation
				else
				{
					methods.opts.loading.find('.loading-box').animate({'top':'-'+methods.opts.loading.find('.loading-box')}, methods.opts.loading_speed/2);
					methods.opts.loading.animate({'opacity':'1'}, methods.opts.loading_speed/2, function(){
						methods.opts.loading.removeClass('active');
						callback(false, selector);
						methods.activate( selector );
					});
				}
			}
		},
		// disabled css
		disabled_css: function( selector )
		{
			// disbaled css files
			$.each(plugin_data[selector].content[plugin_data[selector].current.path]['_css'], function()
			{
				$(this).attr('disabled', 'disabled');
			});
			// disabled js
			methods.js_init(false, selector);
		},
		// load page
		loading: function( start, selector )
		{
			// get content animation type
			var animate = methods.animations.default;
			// check for path specific animation
			if( selector != undefined && methods.animations[plugin_data[selector].new.path] != undefined )
			{
				 animate = methods.animations[plugin_data[selector].new.path];
			}
			// check for namespace specific animation
			else if( plugin_data[selector].content[plugin_data[selector].new.path] != undefined && methods.animations[plugin_data[selector].content[plugin_data[selector].new.path].namespace] != undefined  )
			{
				animate = methods.animations[plugin_data[selector].content[plugin_data[selector].new.path].namespace];
			}
			// start loading
			if( start == undefined || start == true )
			{
				if( plugin_data[selector].new.loaded == true )
				{
					animate(true, selector, animate);
				}
				else
				{
					animate(true, selector, methods.animations.loading);
				}
			}
			// end loading
			else
			{
				methods.animations.loading(true, selector, animate);
			}
		},
		// push history
		pushstate: function( page )
		{
			// set url
			history.pushState('', page.title, page.path);
		},
		// on popstate change page
		popstate: function( selector, statedata )
		{
			plugin_data[selector].loading = true;
			plugin_data[selector].current.path = plugin_data[selector].new.path;
			// set current page
			if( typeof(plugin_data[selector].content[plugin_data[selector].new.path]) !== undefined )
			{
				plugin_data[selector].current.page = plugin_data[selector].content[plugin_data[selector].new.path].page;
			}
			else
			{
				plugin_data[selector].current.page = '';
			}
			// get path
			path = location.href;
			// set response
			response = plugin_data[selector].content[path];
			// set path
			plugin_data[selector].new.path = response.path = path;
			// check if item exists
			if( !plugin_data[selector].content[path] )
			{
				plugin_data[selector].new.loaded = false;
			}
			else
			{
				plugin_data[selector].new.loaded = true;
			}
			// start loading
			methods.loading( true, selector );
			// activate css
			methods.css( response.css, selector );
			// activate js
			methods.js( response.js, selector );
		},
		activate: function( selector )
		{
			// set title
			plugin_data[selector].title.text(plugin_data[selector].content[plugin_data[selector].new.path].title);
			// remove body class
			if( plugin_data[selector].body.data('page_class') != undefined && plugin_data[selector].body.data('page_class') != "" )
			{
				plugin_data[selector].body.removeClass(plugin_data[selector].body.data('page_class'));
			}
			// add class to body
			if( plugin_data[selector].content[plugin_data[selector].new.path]['body_class'] != undefined )
			{
				plugin_data[selector].body.addClass(plugin_data[selector].content[plugin_data[selector].new.path]['body_class']).data('page_class', plugin_data[selector].content[plugin_data[selector].new.path]['body_class']);
			}
			// add class to active page
			plugin_data[selector].content[plugin_data[selector].new.path].page.addClass(methods.opts.current_page);
			// run js init
			methods.js_init(true, selector);
			
			plugin_data[selector].body.find('[href="'+plugin_data[selector].new.path+'"]').each(function()
			{
				// define vars
				var _this 		= $(this),
						parent 		= _this.parent(),
						data_nav 	= _this.data(methods.opts.data_nav);
				// check for data-nav agrument
				if( data_nav != null )
				{
					// define variables
					var nav 					= $(data_nav);
					var nav_item 			= nav.find('[href="'+_this.attr('href')+'"]');
					var parent_item		= nav_item.parent();
					// remove active class from item
					nav.find('.'+methods.opts.activatable).removeClass(methods.opts.active);
					// check if activatable is link item
					if( nav_item.hasClass(methods.opts.activatable) )
					{
						nav_item.addClass(methods.opts.active);
					}
					// if activatable is parent of link
					else if( parent_item.hasClass(methods.opts.activatable) )
					{
						parent_item.addClass(methods.opts.active);
					}
				}
				// check if activatable
				else if( _this.hasClass(methods.opts.activatable) )
				{
					_this.siblings('.'+methods.opts.activatable).removeClass(methods.opts.active);
					_this.addClass(methods.opts.active);
				}
				// if parent has class
				else if( parent.hasClass(methods.opts.activatable) )
				{
					parent.siblings('.'+methods.opts.activatable).removeClass(methods.opts.active);
					parent.addClass(methods.opts.active);
				}
			});
			// set loading to false
			plugin_data[selector].loading = false;
		},
		// destory
		destroy: function( selector )
		{
			// remove event handler
			plugin_data[selector].body.off('click', selector);
			plugin_data[selector].window.off('popstate');
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
			return methods[ method ].apply( this, Array.prototype.slice.call( settings, 1 ));
		} 
		else if ( typeof method === 'object' || ! method ) 
		{
				return methods.init.apply( this, settings );
		}
		else
		{
			$.error( 'Method ' +  method + ' does not exist on jQuery.'+plugin_name );
		}
	};
	//-------------------------------------------
	// default options
	$.fn[plugin_name].defaults = {
		'current_page' 	: 'current-page',
		'page' 					: 'page',
		'namespace' 		: 'namespace',
		'data_nav'			: 'nav',
		'path' 					: 'path',
		'link' 					: 'link[data-type="page"]',
		'activatable' 	: 'activatable',
		'active' 				: 'active',
		'loading' 			: $('.loading'),
		'loading_speed' : 400,
		'load_hide' 		: 'hide-on-load'
	};
	
	$('.ajax-link').fs_history({'animations':
		{
			'portfolio': function( hide, selector, callback )
			{
				// preset variables
				if(typeof(callback)==='undefined') callback = function(){};
				// content hide animation
				if( hide === true )
				{
					$('.'+methods.opts.load_hide).animate({'opacity':0}, methods.opts.loading_speed);
					plugin_data[selector].current.page.removeClass(methods.opts.current_page).animate({'marginTop':'-40%','opacity':0}, methods.opts.loading_speed, function()
					{
						$(this).hide();
						callback(false, selector);
						methods.disabled_css( selector );
					});
				}
				// // content show animation
				else
				{
					plugin_data[selector].content[plugin_data[selector].new.path].page.css({'display':'block','opacity':0,'marginTop':'-40%'}).animate({'marginTop':'0','opacity':1}, methods.opts.loading_speed, function(){
						$('.'+methods.opts.load_hide).animate({'opacity':1}, methods.opts.loading_speed);
					});
				}
			}
		}
	});
})( jQuery, window, document);