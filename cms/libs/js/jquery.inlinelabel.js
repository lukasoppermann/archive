jQuery.fn.inlinelabel = function() {
	return this.each (function (event){
		if ($(this).val() != "") {
			$(this).prev().addClass('has-text');
		}

		$(this).bind('focus', function () {
			$(this).prev("label").addClass("focus");
		});

		$(this).bind('keypress keydown paste change', function () { // 
			$(this).prev("label").addClass("has-text").removeClass("focus");
		});

		$(this).bind('blur', function () {
			if($(this).val() == "") {
				$(this).prev("label").removeClass("has-text").removeClass("focus");
			}
		});
	});
}