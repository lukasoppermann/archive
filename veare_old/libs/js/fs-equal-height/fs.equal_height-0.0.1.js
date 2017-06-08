// jquery equal height fn
//
(function($) {
	$.fn.fs_equal_height = function(){
		// create variables
		var _this, _column = $(this), position_top, row_divs = new Array(), current_tallest, current_row_start, current_div;
		// set column height to auto (so it can be calculate without padding)
		_column.height('auto');
		// loop through columns
		_column.each(function(){
			// cache selection
			_this = $(this);
			// get top position
			position_top = _this.offset().top;
			// check for new row
			if( current_row_start != position_top )
			{
				var len = row_divs.length;
				if( len > 1)
				{
					// set heights for completed row
					for(var current_div = 0; current_div < len; current_div++) 
					{
						row_divs[current_div].height(current_tallest);
					}
				}
				// set the variables for the new row
				row_divs.length = 0; // empty the array
				current_row_start = position_top;
				current_tallest = _this.height();
				row_divs.push(_this);
			} 
			else 
			{
				 // add column
				 row_divs.push(_this);
				 current_tallest = Math.max(current_tallest, _this.height());
			}
			// do the last row if more than 1 item in it
			var len = row_divs.length;
			if( len > 1)
			{
				for(var current_div = 0; current_div < len; current_div++)
				{
					 row_divs[current_div].height(current_tallest);
				}
			}
		});
		// return columns for chainability
		return _column;
	}
})(jQuery);