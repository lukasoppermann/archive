(function($) {
    $.fn.fs_filter = function(options) {
	    // settings
	    var settings = $.extend( {
	        'container': $(this), // holding all filter groups
			'filters' : '.filters',	// group of filters, e.g. all status filters
			'filter' : '.filter', // single filter e.g. published
	        'list': ".filtered-list", // list with elements to be sorted
			'item': ".item",
	        'all': 'all', // class namen, no .
			'none': 'none' // class namen, no .
	    }, options);
		// define vars
		var filters = {};
		// plugin functions
        settings['container'].on('click', settings['filter'], function() {
			var _this = $(this);
			var _list = _this.parents(settings['filters']);
			//
			if(_this.hasClass(settings['all']))
			{
				_list.find(settings['filter']).not("."+settings['all']+", ."+settings['none'] ).addClass('active');			
			}
			else if(_this.hasClass(settings['none']))
			{
				_list.find(settings['filter']).removeClass('active');			
			}
			else
			{
				_this.toggleClass('active');			
			}
			// get filters
			$(settings['filters']).each(function(){
				var _this = $(this);
				var _tmp = [];
				//
				_this.find('.active').each(function(i){
					_tmp[i] = $(this).data('value');
				});
				filters[_this.data('filter')] = _tmp;
			});
			// filter
			$(settings['list']).find(settings['item']).each(function(){
				var _this = $(this);
				var hide = null;
				$.each(filters, function(i, f){
					// some filters are active
					if(f.length > 0)
					{	
						// item is in filter
						if( $.inArray(_this.data(i), f) != -1 ){
							if(hide != true)
							{
								hide = false;
							}
						}
						else
						{
							hide = true;
						}
					}
					else
					{
						if(hide != true)
						{
							hide = null;
						}
					}
				});
				if(hide == false || hide == null)
				{
					_this.fadeIn();
				}
				else
				{
					_this.fadeOut();	
				}
			});
		});
		// return filters to make it chainable
		return settings['container'];
    }
})(jQuery);