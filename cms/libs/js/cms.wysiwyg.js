$(document).ready(function(){
	// -----------------
	// check if wysiwyg textareas are on this page
	if($("textarea.wysiwyg").length != 0)
	{	
		CKEDITOR.config.resize_enabled = false;
		var config = {
			toolbar:
			[
		    ['Source'],
		    ['Cut','Copy'],
		    ['Undo','Redo','-','Find','Replace'],
		    ['Subscript','Superscript','SpecialChar'],
			['Maximize','ShowBlocks'],
		    ['NumberedList','BulletedList'],
		    // ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			['Format','Bold','Italic','FontSize','-','mylink','Unlink','-','myimg']
		    

			]
		};
		config.format_tags = 'p;h2;h3';		
		
		// -----------------
		// execute wysiwyg script
		$('textarea.wysiwyg').ckeditor(config);
	
		$("file_upload").uploadify();
	}
	
	
});