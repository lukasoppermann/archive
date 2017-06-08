var gCache = {};
var pages = {};
		pages.js = {},
		pages.loaded = false,
		pages.empty = function(){};
// ----------------------------------------------------
// Base functions used in all or many files
// ----------------------------------------------------
// define functions 
$(function()
{
	// -----------------------
	gCache.window = $(window);
	gCache.document = $(document);
	gCache.body = $('body');
	gCache.stage = $('#stage');
	gCache.sidebar = $('#sidebar');
	// -----------------------
	// Animate Content
	// var animate_content = function( resolution )
	// {
	// 	if(loaded == false)
	// 	{
	// 		if(resolution == 'mobile-portrait')
	// 		{
	// 			$('#sidebar').css({'left':'0px'}).animate({'top':'0px'}, 700, 'swing');
	// 		}
	// 		else
	// 		{
	// 			$('#sidebar').css({'top':'0px'}).animate({'left':'0px'}, 500, 'swing');
	// 		}
	// 		// define loaded
	// 		loaded = true;
	// 	}
	// };
	// -----------------------
	// Load Content
	var content = function(  )
	{
		if( $('#about').length == 0 )
		{
			$.ajax({
				type : 'post',
				data: {'ajax':true},
				dataType: 'json',
				url: CI_BASE+'en/about',
			}).done(function(response)
			{
				gCache.stage.prepend('<div id="about" class="hidden-page" style="display:none;">'+response.content+'</div>');
				$('.current-page').fadeOut();
				$('#about').addClass('current-page').removeClass('hidden-page').css({'display':'block','marginTop':50}).animate({'opacity':1,'marginTop':0}, 500);
				$('head').append(response.css+response.js);
			}
			).fail(function(){ 
				alert("Something went wrong."); 
			});
		}
		else
		{
			$('.current-page').fadeOut();
			$('#about').addClass('current-page').removeClass('hidden-page').fadeIn();
		}
	}
	// $('#nav').on('click', '.menu-item-link', function(e){	
	// 	e.preventDefault();
	// 	content();
	// });
	// -----------------------
	// query actions	
	var query_actions = function( resolution )
	{
		// console.log(resolution);
		// console.log($(window).width());
		// $('html, body').height(_stage.height());
		gCache.body.trigger('resolutionChange', resolution);
		// animate_content(resolution);
		// _stage.width($(document).width());
	};
	// -----------------------
	// create event
	// fire event
	// media queries	
	var queries = [
		{
			context: 'mobile-portrait',
			callback: function() {
				query_actions('mobile-portrait');
				gCache.body.addClass('mobile portrait hide-line-text nav-narrow').removeClass('hovers tablet-small nav-wide landscape tablet screen wide-screen');
			}
		},
		{
			context: 'mobile-landscape',
			callback: function() {
				query_actions('mobile-landscape');
				gCache.body.addClass('mobile landscape hide-line-text nav-narrow').removeClass('hovers tablet-small nav-wide portrait tablet screen wide-screen');
			}
		},
		{
			context: 'tablet-small',
			callback: function() {
				query_actions('tablet-small');
				gCache.body.addClass('tablet-small hide-line-text nav-narrow').removeClass('hovers nav-wide tablet mobile screen wide-screen');
				if( !gCache.body.hasClass('loaded-tablet') )
				{
					gCache.body.addClass('hovers');
				}
			}
		},
		{
			context: 'tablet',
			callback: function() {
				query_actions('tablet');
				gCache.body.addClass('tablet hide-line-text nav-narrow').removeClass('hovers tablet-small nav-wide mobile screen wide-screen');
				if( !gCache.body.hasClass('loaded-tablet') )
				{
					gCache.body.addClass('hovers');
				}
			}
		},
		{
			context: 'min-screen',
			callback: function() {
				query_actions('min-screen');
				gCache.body.addClass('hovers nav-wide min-screen hide-line-text').removeClass('tablet-small mobile tablet wide-screen nav-narrow');
			}
		},
		{
			context: 'screen',
			callback: function() {
				query_actions('screen');
				gCache.body.addClass('hovers nav-wide screen').removeClass('tablet-small mobile tablet wide-screen min-screen hide-line-text nav-narrow');
			}
		},
		{
			context: 'wide-screen',
			callback: function() {
				query_actions('wide-screen');
				gCache.body.addClass('hovers nav-wide screen wide-screen').removeClass('tablet-small mobile tablet min-screen hide-line-text nav-narrow');
			}
 		}
	];
	// Go!
	MQ.init(queries);
});