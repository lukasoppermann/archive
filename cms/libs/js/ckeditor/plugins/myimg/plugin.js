/*
My Link Plugin for CKEditor
*/
function getNextSibling(node, type){
    while((node = node.nextSibling) && (node.nodeName != type));
    return node;
}


CKEDITOR.plugins.add('myimg',
{
	init: function(editor)
	{
		var base_url = "http://veare.net/projects/cms";
		var pluginName = 'myimg';
		// CKEDITOR.dialog.add(pluginName, '/dialogs/'+pluginName+'.js');
		CKEDITOR.dialog.add( pluginName, function( editor )
		{
			// editor.getSelection().selectElement(editor.getSelection().getSelectedElement().getParent());
			return {
				title : 'Bild einfügen',
				minWidth : 300,
				minHeight : 150,
				resizable: CKEDITOR.DIALOG_RESIZE_NONE,
				onShow : function(){
					elem = this.getParentEditor().getSelection().getSelectedElement();
					if(elem)
					{
						var span = elem.getNext();
						var parent = span.getParent();
						old_src = elem.getAttribute("src");
						this.setupContent(span, elem, parent);
					}
				},
				onOk: function(){
					var caption = this.getValueOf('dialog','caption');
					// var src = this.getValueOf('dialog','src');
					var tmp_title = this.getValueOf('dialog','alt'); 
					var link  = this.getValueOf('dialog','link');  		
					var align  = this.getValueOf('dialog','align');
					if(tmp_title != null && tmp_title != 'Alternativer Text')
					{
						var title = " alt='"+tmp_title+"'";
					}
					else
					{
						var title = "";
					}
					if(editor.getSelection().getSelectedElement())
					{
						editor.getSelection().selectElement(editor.getSelection().getSelectedElement().getParent());
					}
					
					if(!elem)
					{
							var frame = $('#uploadframe'); 
							var src = frame.contents().find("#src").val();
							if(link == "")
							{
								editor.insertHtml('<div class="float-'+align+' image"><img class="'+align+'" src="'+ src +'"'+title+'/><span class="caption">'+ caption + '</span></div>');
							}
							else
							{
								editor.insertHtml('<div class="float-'+align+' image"><a href="'+link+'" class="image-link"><img class="'+align+'" src="'+ src +'"'+title+'/><span class="caption">'+ caption + '</span></a></div>');
							}
					}
					else
					{	
						var frame = $('#uploadframe'); 
						var src = frame.contents().find("#src").val();	
						if(!src)
						{
							src = old_src;			
						}
						if(CKEDITOR.env.webkit)
						{
							if(link == "")
							{
								editor.insertHtml('<a href="'+link+'" class="image-link"><img class="'+align+'" src="'+ src +'"'+title+'/><span class="caption">'+ caption + '</span></a>');
							}
							else
							{
								editor.insertHtml('<img class="'+align+'" src="'+ src +'"'+title+'/><span class="caption">'+ caption + '</span>');
							}
						}
						else
						{
							if(link == "")
							{
								editor.insertHtml('<div class="float-'+align+' image"><img class="'+align+'" src="'+ src +'"'+title+'/><span class="caption">'+ caption + '</span></div>');
							}
							else
							{
								editor.insertHtml('<div class="float-'+align+' image"><a href="'+link+'" class="image-link"><img class="'+align+'" src="'+ src +'"'+title+'/><span class="caption">'+ caption + '</span></a></div>');
							}
						}
					}
				},
				contents : [
				    {
				        id : 'dialog',
				        label : 'First Tab',
				        title : 'First Tab Title',
				        elements : [
							{
								type: 'html',
								html: '<iframe id="uploadframe" src="'+base_url+'/de/ajax_upload" width="100%"><p>iFrames müssen zugelassen sein.</p></iframe>'
							},
							{
				                type : 'text',
				                label : 'Link',
				                id : 'link'
				            },
							{
				                type : 'radio',
				                label : 'Ausrichtung',
				                id : 'align',
								items : [ [ 'Links', 'left' ], [ 'Rechts', 'right' ] ] ,
								'default' : 'right',
								onClick : function() {
									// this = CKEDITOR.ui.dialog.radio
									// alert( 'Current value: ' + this.getValue() );
								},
								setup: function(span, element){	
									if(element.getAttribute("class") != null)
									{
										this.setValue(element.getAttribute("class"));
									}	
									else
									{
										this.setValue('right');
									}
								}
				            },
							{
				                type : 'text',
				                label : 'Bildunterschrift',
				                id : 'caption',
								setup: function(span, element){								
									if(span.getText() != null){
										this.setValue(span.getText());
									}
								}
				            },
							{
				                type : 'text',
				                label : 'Alternativer Bildtext',
				                id : 'alt',
								setup: function(text, element){
									if(element != null && element.getAttribute('alt') != undefined && element.getAttribute('alt') != null){
										this.setValue(element.getAttribute('alt'));
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
					if ( element.is( 'img' ) )
						evt.data.dialog =  pluginName;
				}
			});
		editor.addCommand(pluginName, new CKEDITOR.dialogCommand(pluginName));
		editor.ui.addButton(pluginName,
		{
			label: 'Bild einfügen',
			command: pluginName
		});	

	}
});