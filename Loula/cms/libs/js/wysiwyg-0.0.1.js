$(document).ready(function() {
	if ($.browser.webkit) 
	{
		$('textarea.wysiwyg').height(500);
		$('textarea.wysiwyg').tinymce({
			// Location of TinyMCE script
			script_url : CI_ROOT+'libs/js/tiny_mce/tiny_mce_gzip.php',
			// General options
			theme : "advanced",
			skin : "default",
			entity_encoding : "raw",
			plugins : "",
			setup : function(ed) {
			      // ed.onInit.add(function() {
			      //     	$(window).resize();
			      // });
			},

			// Theme options
			theme_advanced_buttons1 : "bold,italic,|,formatselect,|,sub,sup,|,charmap,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,blockquote,|,undo,redo,|,link,unlink,image,removeformat,code,fullscreen",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_buttons4 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : false,

			// Example content CSS (should be your site CSS)
			content_css : CI_ROOT+'libs/css/tinymce_content-0.0.1.css',

			// Drop lists for link/image/media/template dialogs
			// template_external_list_url : "lists/template_list.js",
			// external_link_list_url : "lists/link_list.js",
			// external_image_list_url : "lists/image_list.js",
			// media_external_list_url : "lists/media_list.js",

		});
		
  	}
 	else
	{
		$('textarea.wysiwyg').tinymce({
			// Location of TinyMCE script
			script_url : CI_ROOT+'libs/js/tiny_mce/tiny_mce_gzip.php',

			// General options
			theme : "advanced",
			skin : "default",
			plugins : "autolink,lists,style,advimage,advlink,iespell,inlinepopups,searchreplace,paste,fullscreen,noneditable,xhtmlxtras,advlist,autoresize",
			entity_encoding : "raw",
			mode: "none",
			setup : function(ed) {
			      	ed.onInit.add(function() {
			          	$(window).resize();
						// hide_tinymce();
			      	});
			},
			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,formatselect,|,sub,sup,|,charmap,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,blockquote,|,undo,redo,|,link,unlink,image,removeformat,code,fullscreen",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_buttons4 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : false,

			// Example content CSS (should be your site CSS)
			content_css : CI_ROOT+'libs/css/tinymce_content-0.0.1.css',

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js",

		});
	}
// ------------------------------------------------------------------
// hide and show tinymce on click	
	// add Activator Element 
	$('.form-element.mceEditor').append('<div id="mceActivator"></div>');
	// needed so that toggling does not untoggle editor
	$('.form-element.mceEditor').click(function(event){
		event.stopPropagation();
	});
	//
	// $('#mceActivator').click(show_tinymce);
	// $('#page_wrapper').click(hide_tinymce);
// close jQuery onLoad
});
// ---------------------
// hide tinymce controls
function show_tinymce()
{
	// hide controls
	$("tr.mceFirst, tr.mceLast").animate({
		opacity: 1.0,
		height: 'toggle'
  	}, 250);
	// remove class hidden from elemens
	$("td.mceIframeContainer").find("iframe").contents().find("body").removeClass('hidden');
	$("table.mceLayout").removeClass('hidden');
	// hide Activator used for toggling
	$("#mceActivator").hide();
}
// ---------------------
// show tinymce controls
function hide_tinymce()
{
	// check if editor is really hidden
	if(!$('.form-element.mceEditor').find("table.mceLayout").hasClass('hidden'))
	{
		// show controls
		$("tr.mceFirst, tr.mceLast").animate({
			opacity: 0.0,
			height: 'toggle'
	  	}, 250);
		// add class to elements
		$("td.mceIframeContainer").find("iframe").contents().find("body").addClass('hidden');
		$("table.mceLayout").addClass('hidden');
		// show & resize Activator used for toggling
		$("#mceActivator").height($("td.mceIframeContainer").height()).show();
	}
 }