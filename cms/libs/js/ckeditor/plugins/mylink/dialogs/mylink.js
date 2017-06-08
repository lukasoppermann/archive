/*
My Link Plugin for CKEditor
*/
( function(){
	var mylink_fn = function(editor){

		return {
			title: 'Link hinzufügen',
			minWidth: 300,
			minHeight: 200
		}
	}
	CKEDITOR.dialog.add('inserHTML', function(editor){
		return mylink_fn(editor);
	});
	
})();