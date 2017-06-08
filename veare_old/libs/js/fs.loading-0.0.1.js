// fn to check for history api
function supports_history_api(){ return !!(window.history && history.pushState); };
// default animation
// var animation
// once jquery is loaded
$(function(){
	// cache selsections
	var content = {}, 
			_head 	= $('head'),
			_body 	= $('body'),
			loc 		= location.href.split("#")[0];
	// set content
	content[loc] = {};
	content[loc]['page'] = $('.current-page');
	content[loc]['css'] 	= {};
	$.each(_head.find("link[data-type='page']"), function(i, file){
		content[loc]['css'][i] 	= $(file);
	}); 
	content[loc]['namespace'] = _head.find("link[data-type='page']").data("namespace"); 
	// check for history api support
	if( supports_history_api() )
	{
		_body.on('click', '.ajax-link', function()
		{
			// get current path
			var _this = $(this),
					path 	= _this.attr('href');
			// get current page
			var current = {};
					current.page = $('.current-page');
					current.path = location.href.split("#")[0];
			// remove active
			current.page.removeClass('current-page');
			// set url
			history.pushState(null, null, path);
			// scroll to top
			_body.animate({'scrollTop': 0}, 400);
			// check if activatable
			if( _this.hasClass('activatable') )
			{
				// if clicked item is activatable
				if( _this.data('connect') != null )
				{
					$("[data-connect='"+_this.data('connect')+"']").removeClass('active');
					_this.addClass('active');
				}
				// else
				else
				{
					var parent = _this.parent();
					// if clicked items parent is activatable
					if( parent.data('connect') != null )
					{
						$("[data-connect='"+parent.data('connect')+"']").removeClass('active');
						parent.addClass('active');
					}
				}
			}
			// check if content exists
			if( content[path] )
			{
				// check if content is not active
				if( ! content[path]["page"].hasClass('current-page') )
				{
					// hide current page
					current["page"].removeClass('current-page').animate({'marginTop':'20%','opacity':0}, 300, function()
					{
						$(this).hide();
						// remove data
						if( content[current.path] != undefined && content[current.path]['css'] != undefined )
						{
							$.each(content[current.path]['css'], function(i, file){
								file.attr('disabled','disabled');
							});
						};
						// activate pages
						if( content[path] != undefined && content[path]['css'] != undefined )
						{
							$.each(content[path]['css'], function(i, file){
								file.removeAttr("disabled");
							});
						};
						// show page
						content[path]['page'].css('display','block').animate({'opacity':'1','marginTop':0}, 300).addClass('current-page');
						// run init
						if( content[path]['namespace'] != undefined && pages[content[path]['namespace']] != undefined )
						{
							pages[content[path]['namespace']].init();
						}
					});
					
					if( current != undefined && current.path != undefined && content[current.path] != undefined &&
						content[current.path]['namespace'] != undefined && pages[content[current.path]['namespace']] != undefined)
					{
						pages[content[current.path]['namespace']].destroy();
					}
				}
			}
			// content does not exists
			// load content using ajax
			else
			{
				// activate loading animation
				gCache.loading.find('.loading-box').animate({'top':'50%'}, 250);
				gCache.loading.animate({'opacity':'1'}, 100, function(){
					gCache.loading.addClass('active');
				});
				// run ajax request
				var ajax = $.ajax({
					type : 'post',
					data: {'ajax':true},
					dataType: 'json',
					url: path
				});
				// animate out current page
				current['page'].animate({'marginTop':'20%','opacity':0}, 300, function()
				{
					// hide curren page when faded out
					$(this).hide();
					// define ajax done method
					ajax.done(function( response )
					{
						// create content var
						content[path] = {};
						// add content
						current['page'].after($('<div class="current-page page">'+response.content+'</div>').css({'opacity':'0','marginTop':'20%'}));
						// deactivate css
						if( content[current.path] != undefined && content[current.path]['css'] != undefined )
						{
							$.each(content[current.path]['css'], function(i, file){
								file.attr('disabled','disabled');
							});
						};
						//
						content[path]['page'] = $('.current-page');
						content[path]['page'].css('display','block');
						// prepare js
						if( response.js != undefined && response.js != '' )
						{
							// split js files
							var js = response.js.split(",");
							
							// loop through js files
							var count = js.length;
							$.each(js, function( i, file )
							{
								if( pages.js[file] == undefined )
								{
									$.getScript(file).done(function(){
										// check if last script
										if( count == 1+i )
										{
											if( response.namespace != undefined )
											{
												content[path]['namespace'] = response.namespace;
												if( pages[response.namespace] != undefined )
												{
													pages[response.namespace].init();
												}
											}
										}
									});
									pages.js[file] = 'loaded';
								}
							});
						}
						else
						{
							//
							if( response.namespace != undefined )
							{
								content[path]['namespace'] = response.namespace;
								if( pages[response.namespace] != undefined )
								{
									pages[response.namespace].init();
								}
							}
						}
						
						if( current != undefined && current.path != undefined && content[current.path] != undefined &&
							content[current.path]['namespace'] != undefined && pages[content[current.path]['namespace']] != undefined && pages[content[current.path]['namespace']].destroy != undefined)
						{
							pages[content[current.path]['namespace']].destroy();
						}
						// prepare css
						if( response.css != undefined && response.css != '' )
						{
							// add testing element
							var _css_loaded = $('<div>').attr('id', 'namespace_'+response.namespace).appendTo('body');
							//
							var output = '';
							// split css files
							var css = response.css.split(",");
							// add css object
							content[path]['css'] = {};
							// loop through css files
							var count = css.length;
							//
							$.each(css, function( i, file )
							{
								//
								if( _head.find("link[href='"+file+"']").length == 0 )
								{
									output += "<link href='"+file+"' type='text/css' rel='stylesheet' />";
								}
								else
								{
									_head.find("link[href='"+file+"']").removeAttr('disabled');
								}
								// check if last one is added
								if( 1+i == count )
								{
									// checking fn
									var check_css_loaded = function() {return _css_loaded.height() == '1'; }
									// checking loop
									if ( !check_css_loaded() ) {
										var tries = 0,
												interval = 10,
												timeout = 20000; // max ms to check for
										setTimeout(function timer() 
										{
											if ( check_css_loaded() ) 
											{
												gCache.loading.find('.loading-box').animate({'top':'0'}, 300);
												gCache.loading.animate({'opacity':'0'}, 300, function(){
													gCache.loading.removeClass('active');
													content[path]['page'].animate({'opacity':1,'marginTop':0}, 300);
												});
												
												_css_loaded.remove();
											} 
												else if (tries*interval >= timeout) {
											} else {
												tries++;
												setTimeout(timer, interval);
											}
										}, interval);
									}
									//
								}
							});
							// add to DOM
							_head.append(output);
							// cache css selection
							// loop through css files
							$.each(css, function( i, file )
							{
								content[path]['css'][i] = _head.find("link[href='"+file+"']");
							});
						}
						else
						{
							gCache.loading.find('.loading-box').animate({'top':'0'}, 300);
							gCache.loading.animate({'opacity':'0'}, 300, function(){
								gCache.loading.removeClass('active');
								content[path]['page'].animate({'opacity':1,'marginTop':0}, 300);
							});
						}
					});
					// define ajax fail method
					ajax.fail(function()
					{
						current['page'].css('display','block').animate({'marginTop':'0','opacity':1}, 300).addClass('current-page');
						gCache.loading.removeClass('active');
					});
				});
			}
			// stop propagation if link
			return false;
		});
	}
	
});