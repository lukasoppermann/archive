/*
 * cms.settings.js - javascript for cms settings page
 */
$(document).ready(function(){
	$(".form-wrapper").each(function(){
		width_box = $(this).width();
		width_span = $(this).children(":first").width();
		width = width_box - width_span - 30;
		$(this).children(".input-field").width(width);
	})
	$(".form-wrapper").click(function(event){
		if( $(".form-wrapper").hasClass("active") )
		{
			$(".form-wrapper").removeClass("active");
		}
     $(this).addClass("active");
   });
	$('select').selectmenu();
});
