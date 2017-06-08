(function($)
{
	// create fn
	$.fs_load = function( fn )
	{
		if( document.readyState == 'complete' )
		{
			fn();
		}
		else
		{
			$(window).load(function(){
				fn();
			});
		}
	};
// close 
})(jQuery);