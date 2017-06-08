// declare object
pages.contact = {};
// variables
var loaded = false, map, 
		content;
//
$(function()
{
	// set variables
	var lat = 52.546167,
			lng = 13.4145,
		 	zoom = 15,
			dragevent;
	// -----------------------
	// init fn
	pages.contact.init = function(  )
	{	
		$.fs_load(function()
		{
			if( !gCache.body.hasClass('mobile') )
			{
				gCache.sidebar.addClass('shadow');

				gCache.stage.css({'height':gCache.window.height(),'paddingBottom':0});
				
				gCache.stage.find('.current-page').css({'height':gCache.window.height(),'paddingBottom':0});
				
				content = '<div class="marker-wrapper">'+$('.veare-contact').attr('class', 'veare-contact').clone().wrap('<div class="wrap" />').parents('.wrap').html()+'<div class="shadow"></div></div>';
				// remove style on resolution change
				gCache.body.on('resolutionChange', function(e, resolution)
				{
					if(resolution == 'mobile-portrait' || resolution == 'mobile-landscape' || resolution == 'mobile')
					{
						pages.contact.destroy();
					}
				});
			}
			else
			{
				gCache.body.on('resolutionChange', function(e, resolution){
					if(resolution != 'mobile-portrait' && resolution != 'mobile-landscape' && resolution != 'mobile')
					{
						pages.contact.init();
					}
				});
			}
			// run callback
			var tries = 0,
					interval = 60,
					timeout = 120000; // max ms to check for
			setTimeout(function timer() 
			{
				if ( typeof(window.google) === 'object' && typeof(window.google.maps) === 'object') 
				{
					pages.contact.map();
				} 
				else if (tries*interval < timeout)
				{
					tries++;
					setTimeout(timer, interval);
				}
				else if( tries*interval >= timeout )
				{
					location.reload();
				}
			}, interval);
		});
	};
	// run init map
	pages.contact.map = function()
	{
		if(window.google && window.google.maps)
		{
			function run_this()
			{
				// draw map  
				var dragfn = function(){
					map.setCenter(lat, lng);
					map.setZoom(zoom);	
				};
				// -----------------------
				// Create GMaps object
				map = new maps({
					div: '#veare_map',
					lat: lat, // lat: 52.546167, - neu:  52.5354,13.43315
					lng: lng, // lng: 13.415201,
					disableDefaultUI: true,
					dragstart: function(){
						clearTimeout( dragevent );	
					},
					dragend: function()
					{
						dragevent = setTimeout( dragfn, 3000);
					},
					zoom_changed: function(){
						clearTimeout( dragevent );
						dragevent = setTimeout( dragfn, 3000);
					},
					tilesloaded: function(){
						var _marker = $('.marker-wrapper');
						if(_marker.css('opacity') == 0)
						{
							_marker.find('.veare-contact').show();
							_marker.css({'marginTop':'-400px'}).delay(300).animate({'opacity':'1.0','marginTop':'-160px'}, 300, 'swing').animate({'marginTop':'-183px'}, 300);
						}
					}
				});
				// -----------------------
				// Draw Overlay
				map.drawOverlay({
					lat: map.getCenter().lat()-.0054,
					lng: map.getCenter().lng(),
					layer: 'overlayLayer',
					content: content
				});
				// debounced resize event (fires once every 100ms)
				$.fs_resize(function(){
					if(!gCache.body.hasClass('mobile'))
					{
						gCache.stage.height(gCache.window.height());
						gCache.stage.find('.current-page').height(gCache.window.height());
						gCache.stage.find('#veare_map').height(gCache.window.height());
						dragfn();
					}
				});
			};
			//
			init_map(run_this);
		}
	};
	// destruct fn
	pages.contact.destroy = function()
	{
		// remove shadow
		gCache.sidebar.removeClass('shadow');
		// reset size of windows
		gCache.stage.attr('style','');
		gCache.stage.find('.current-page').css({'height':'auto','paddingBottom':'auto'});
	};
	// run init on page load
	pages.contact.init();
// 
});