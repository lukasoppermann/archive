(function($) {
    $.fn.serializer = function() {
        var toReturn    = [];
        var elements         = $(this).find(':input').get();
        $.each(elements, function() {
            if (this.name && !this.disabled && (this.checked || /select|textarea/i.test(this.nodeName) || /text|hidden|password/i.test(this.type))) 
			{
                var val = $(this).val();
				// if is select, check selected
				if(this.nodeName == "SELECT")
				{
					val = $(this).find('option:selected').val();	
				}
                toReturn.push( encodeURIComponent(this.name) + "=" + encodeURIComponent( val ) );
            }
        });
        return toReturn.join("&").replace(/%20/g, "+");
    }
})(jQuery);