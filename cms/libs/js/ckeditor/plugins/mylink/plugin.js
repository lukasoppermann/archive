/*
My Link Plugin for CKEditor
*/
CKEDITOR.plugins.add('mylink',
{
	init: function(editor)
	{
		var pluginName = 'mylink';
		// CKEDITOR.dialog.add(pluginName, '/dialogs/'+pluginName+'.js');
		CKEDITOR.dialog.add( pluginName, function( editor )
		{
			return {
				title : 'Link einfügen',
				minWidth : 300,
				minHeight : 150,
				resizable: CKEDITOR.DIALOG_RESIZE_NONE,
				onShow : function(){
					var elemn = this.getParentEditor().getSelection();
					var elem = this.getParentEditor().getSelection().getSelectedElement();
					var text = this.getParentEditor().getSelection().getNative();
					this.setupContent(text, elem, elemn);
				},
				onOk: function(){
					var linktext = this.getValueOf('dialog','linktext');
					var url = this.getValueOf('dialog','url');
					var tmp_title = this.getValueOf('dialog','title'); 	
					if(tmp_title != null && tmp_title != 'Beschreibender Linktitel')
					{
						var title = " title='"+tmp_title+"'";
					}
					else
					{
						var title = "";
					}
					editor.insertHtml('<a href="'+ url +'"'+title+'>'+ linktext +'</a>');
					
				},
				contents : [
				    {
				        id : 'dialog',
				        label : 'First Tab',
				        title : 'First Tab Title',
				        elements : [
				            {
				                type : 'text',
				                label : 'Linktext',
				                id : 'linktext',
								setup: function(text, element){
									this.setValue(text);
								}
				            },
							{
				                type : 'text',
				                label : 'Link URL',
				                id : 'url',
				                'default' : 'http://www.',
								setup: function(text, element, elemn){								
									if(element != null){
										this.setValue(elemn.getAttribute('href'));
									}
								}
				            },
							{
				                type : 'text',
				                label : 'Link-Titel',
				                id : 'title',
				                'default' : 'Beschreibender Linktitel',
								setup: function(text, element){
									if(element != null && element.getAttribute('title') != undefined && element.getAttribute('title') != null){
										this.setValue(element.getAttribute('title'));
									}
									else
									{
										this.setValue('Beschreibender Linktitel');
									}
								}
				            }
				        ]
				     }
				]
			};
		});
		editor.on( 'doubleclick', function( evt )
			{
				var element = CKEDITOR.plugins.link.getSelectedLink( editor ) || evt.data.element;

				if ( !element.isReadOnly() )
				{
					if ( element.is( 'a' ) )
						evt.data.dialog =  pluginName;
				}
			});
		editor.addCommand(pluginName, new CKEDITOR.dialogCommand(pluginName));
		editor.ui.addButton(pluginName,
		{
			label: 'Link',
			command: pluginName
		});	

	}
});