$(function(){
// start on ready
$("#entries_grid").width($("body").width()-250);
$('.grid-group .group').sortable({
    items: '.drag'
}).bind('sortupdate', function(e, ui)
{
	// define object
	var data = {};
	// build object
	$(this).find('.item').each(function(i, e)
	{
		data[$(e).data('id')] = i+1;
	});
	// send ajax request
	$.ajax({
		url: CI_ROOT+'ajax/entry/pos',            
        type: "POST",
        dataType: 'json',
        data: {'products' : data},
        success: function(r)
        {
        }
	});
});
// ----------------------------
	// tabs
	$(".tab-container").on('click', '.btn', function()
	{
	    var _this = $(this);
		var _container = $('#'+_this.parents(".tab-container").attr('id'));
	    var _tabs = _container.find(".tabs");
	    _this.addClass('active').siblings().removeClass('active');
	    _tabs.children('.active').fadeOut(200, function()
		{
	        _tabs.children('.active').removeClass('active');  
	        _tabs.find('.'+_this.data('tab')).fadeIn().addClass('active');
	    });
	});
// ----------------------------
	// limit
	var _limit_chars = $('.limit-chars');
	if(_limit_chars.size() > 0)
	{
		_limit_chars.each(function(){
			var el = $(this);
			el.limit(el.attr('length'),'.'+el.data('chars_left'));	
		});
	}
// jQuery End
});