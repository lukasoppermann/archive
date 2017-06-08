(function($){

	$.fn.alphanumeric = function(p) { 

		p = $.extend({
			ichars: "!@#$%^°&*()§+=[]\\\';,/{}|\":<>?\~`.- ",
			nchars: "",
			allow: ""
		  }, p);	

		return this.each
			(
				function() 
				{

					if (p.nocaps) p.nchars += "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
					if (p.allcaps) p.nchars += "abcdefghijklmnopqrstuvwxyz";
					
					s = p.allow.split('');
					for ( i=0;i<s.length;i++) if (p.ichars.indexOf(s[i]) != -1) s[i] = "\\" + s[i];
					p.allow = s.join('|');
					
					var reg = new RegExp(p.allow,'gi');
					var ch = p.ichars + p.nchars;
					ch = ch.replace(reg,'');

					$(this).keypress
						(
							function (e)
								{
								
									if (!e.charCode) k = String.fromCharCode(e.which);
										else k = String.fromCharCode(e.charCode);
										
									if (ch.indexOf(k) != -1) e.preventDefault();
									if (e.ctrlKey&&k=='v') e.preventDefault();
									
								}
								
						);
						
					$(this).bind('contextmenu',function () {return false});
									
				}
			);

	};

	$.fn.numeric = function(p) {
	
		var az = "abcdefghijklmnopqrstuvwxyz";
		az += az.toUpperCase();

		p = $.extend({
			nchars: az
		  }, p);	
		  	
		return this.each (function()
			{
				$(this).alphanumeric(p);
			}
		);
			
	};
	
	$.fn.alpha = function(p) {

		var nm = "1234567890";

		p = $.extend({
			nchars: nm
		  }, p);	

		return this.each (function()
			{
				$(this).alphanumeric(p);
			}
		);
			
	};	

})(jQuery);
/*
// only lowercase alphabets are allowed
$('#text_input').filter_input({regex:'[a-z]'}); 

// only numbers are allowed
$('#text_input').filter_input({regex:'[0-9]'}); 

// only URL safe characters are allowed
$('#text_input').filter_input({regex:'[a-zA-Z0-9_]'}); 

// use live() for binding to elements - from version 1.1.0
$('.input').filter_input({regex:'[a-z]', live:true});*/
(function($){  
	
	$.fn.filter_replace = function(find, replace) {
		
		var defaults_find = 'ä';
		var defaults_replace = 'ae';
		
		// var find =  $.extend(defaults_find, find);
		// var replace =  $.extend(defaults_replace, replace);	
		return $(this).val(function(i, v){
			return v.replace(defaults_find,defaults_replace);
		});				

	};
	
	$.fn.add_replace = function(element, find, replace, before) {
		$(this).bind('keyup', function(){
			if(element.is(':disabled') == true)
			{
				element.val(before + $(this).val());
			}
						
			if(element.prev(':not(.has-text)'))
			{
				element.prev().addClass('has-text');
			}
		});
	};
	
})(jQuery);

(function($){  
  
    $.fn.extend({   

        filter_input: function(options) {  

          var defaults = {  
              regex:".*",
              live:false
          }  
                
          var options =  $.extend(defaults, options);  
          var regex = new RegExp(options.regex);
          
          function filter_input_function(event) {

            var key = event.charCode ? event.charCode : event.keyCode ? event.keyCode : 0;

            // 8 = backspace, 9 = tab, 13 = enter, 35 = end, 36 = home, 37 = left, 39 = right, 46 = delete
            if (key == 8 || key == 9 || key == 13 || key == 35 || key == 36|| key == 37 || key == 39 || key == 46) {

              if ($.browser.mozilla) {

                // if charCode = key & keyCode = 0
                // 35 = #, 36 = $, 37 = %, 39 = ', 46 = .
         
                if (event.charCode == 0 && event.keyCode == key) {
                  return true;                                             
                }

              }
            }


            var string = String.fromCharCode(key);
            if (regex.test(string)) {
              return true;
            }
            return false;
          }
          
          if (options.live) {
            $(this).live('keypress', filter_input_function); 
          } else {
            return this.each(function() {  
              var input = $(this);
              input.unbind('keypress').keypress(filter_input_function);
            });  
          }
          
        }

    });  
      
})(jQuery);