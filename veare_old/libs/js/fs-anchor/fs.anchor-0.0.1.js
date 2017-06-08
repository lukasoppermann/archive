// jquery anchor fn
//
;(function($) {
	
	$.fn.fs_anchor = function(){
		// check for safari
		if( $.browser.safari )
		{
			// if safari, use body
			var _scroll_body = $("body");
		}
		else
		{
			// else use html body
			var _scroll_body = $("html,body");
		}
		// cache selection
		var _this = $(this);
		// loop through selection
		_this.each(function()
		{
			// cache selection
			var _this 	= $(this);
			var _anker 	= $(this.hash);
			// create click event
			_this.on('click', function(event)
			{
				// prevent click event
				event.preventDefault();
				// calc offset
				var offset =  Math.round(_anker.offset().top);
				// scroll to offset
				_scroll_body.animate({'scrollTop': offset-70}, 400);
			});
		});	
	}
//
})(jQuery);