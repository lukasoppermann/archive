// ----------------------------------------------------
// Dialog wysiwyg
//
// dependencies
// fs.base.js 
// - fs_selected()
//
// TODO:
// ----------------------------------------------------
// define functions 
;(function( $, window, document )
{
	// methods
	var methods = {
		// settings object
		settings: {},
		// initialize
		init: function( settings )
		{
			return this.each(function()
			{
				// Extend default options with those provided
				methods.settings = $.extend({}, $.fn.fs_wysiwyg.defaults, settings);
				// chache selection
				_this 				= $(this);
				// make field editiable
				_this.attr("contenteditable","true").wrap('<div class="wysiwyg-container"></div>').parents('.wysiwyg-container');
				// .append('<div class="textarea wysiwyg-html" contenteditable="true"></div>');
				if(_this.html() == '')
				{
					_this.html('<p>Your content here</p>');
				}
				// $('body').on('keyup', '.wysiwyg-html', function()
				// 				{
				// 				
				// 					var string = $(this).html();
				// 					var regex = /&lt;(\w+)&gt;/gi;  
				// 					var matches = {start:[],end:[]};
				// 					// var match = regex.exec(string);
				// 					while (match = regex.exec( string ))
				// 					{
				// 						matches.start.push(match[1]);
				// 					}
				// 					var regex = /&lt;\/(\w+)&gt;/gi;  
				// 					while (match = regex.exec( string ))
				// 					{
				// 						matches.end.push(match[1]);
				// 					}
				// 					
				// 					$.each(matches.start, function(i,val)
				// 					{
				// 						pos = $.inArray(val, matches.end);
				// 						if( pos > -1 )
				// 						{
				// 							
				// 						}
				// 					});
				// 					
				// 					// console.log(jQuery.inArray("p", matches.end));
				// 					
				// 					var string = $(this).html();
				// 					// var reg = /&lt;(\w+)&gt;(.*)&lt;\/\1&gt;/gi;
				// 					// var count = matches.start.length;
				// 					// var str = string;
				// 					// for( i = 0; i < count; i++ )
				// 					// {
				// 					// 	reg = new RegExp('&lt;'+matches.start[i]+'&gt;(.*)&lt;\/'+matches.start[i]+'&gt;', 'gi')
				// 					// 	match = reg.exec( string );
				// 					// 	str = str.replace('p','<p></p>');
				// 					// 	console.log(match[0]);
				// 					// 							console.log(str);
				// 					// }
				// 					regex = /&lt;(\w+)&gt;(.*)&lt;\/\1&gt;/gi;
				// 					regex = /&lt;(\w+)&gt;/gi;
				// 					var i = 0;
				// 					while( match = regex.exec(string) )
				// 					{
				// 						i++;
				// 						console.log(i);
				// 						// string = string.replace(/&lt;(\w+)&gt;(.*)&lt;\/\1&gt;/gi,'<$1>$2</$1>');
				// 						string = string.replace(/&lt;(\w+)&gt;([^\1]*?)&lt;\/\1&gt;/gi,'<$1>$2</$1>');
				// 						
				// 						console.log(string);	
				// 						console.log(match);	
				// 					}
				// 					string = string.replace(/&lt;(\w+)&gt;([^\1]*?)&lt;\/\1&gt;/gi,'<$1>$2</$1>');
				// 					_this.html(string);
				// 					// _this.html($(this).html().replace(/&lt;(\w+)&gt;(.*)&lt;\/\1&gt;/gi,'<$1>$2</$1>'));	
				// 					
				// 					// _this.html(match);
				// 										// 
				// 										// var text = "First line\nsecond line";  
				// 										// var regex = /(\S+) line\n?/y;  
				// 										// 
				// 										// var match = regex.exec(text);  
				// 										// print(match[1]);  // prints "First"  
				// 										// print(regex.lastIndex); // prints 11
				// 					
				// 				});
				 
				// add buttons
				// _this.before('<div class="wysiwyg-buttons"><button class="button bold" data-wysiwyg-button="custom" data-wysiwyg-tag="strong">b</button><button class="button h2" data-wysiwyg-tag="heading" data-wysiwyg-tag-value="h2">H2</button></div>');
				
				//
				methods.buttons( _this );
			});
		},
		disable: function()
		{
			return this.each(function()
			{
				// disable
				$(this).attr("contenteditable","false");
			});
		},
		enable: function( settings )
		{
			if( parseInt(Object.keys(methods.settings).length) >= 1)
			{
				return this.each(function()
				{
					$(this).attr("contenteditable","true");
				});
			}
			else
			{
				methods.init.apply(this, settings);
			}
		},
		buttons: function(wysiwyg, disable)
		{
			// cache selection 
			var _buttons = _this.prev('.wysiwyg-buttons');
			// get buttons
			if( _buttons.length == 0)
			{
				var buttons = '';
				var count = $(methods.settings.buttons).size();
				$.each(methods.settings.buttons, function(i, button)
				{
					if( (!button.active && button.active != false || button.active == true || button.active == 'true') && 
						button.tag && (button.tag != undefined || button.tag != null) )
					{
						// check label
						if( !button.label )
						{
							if( !button.value )
							{
								button.label = button.tag;
							}
							else
							{
								button.label = button.value;	
							}
						}
						// check value
						if( !button.value )
						{
							button.value = '';
						}
						// add buttons
						buttons += '<button data-name="'+i+'" class="button '+button.tag+' '+button.value+'">'+button.label+'</button>'; 
					}
				});
				
				_this.before('<div class="wysiwyg-buttons">'+buttons+'</div>');
				_buttons = _this.prev('.wysiwyg-buttons');
				
				if (!--count) methods.button_events();
			}
			else
			{
				methods.button_events();
			}
		},
		button_events: function()
		{
			// Button events
			$('.wysiwyg-buttons').on('click', '.button', function( event )
			{
				event.preventDefault();
				// cache selection
				var _this = $(this);
				var _wysiwyg = _this.parents('.wysiwyg-container').find('.wysiwyg');
				var btn = methods.settings.buttons[_this.data('name')];
				// check for button type
				if( !btn.fn )
				{
					methods.cmd(btn.tag, false, btn.value);
				}
				else 
				{
					methods.fns[btn.fn](btn, _this.parents('.wysiwyg-container').find('.wysiwyg'));
				}
				// 
				_wysiwyg.focus();
			});
		},
		cmd: function(type, arg)
		{
			if( arg == null ) 
			{
				arg = null;
			}
			// execute command
			return methods.settings.document.execCommand(type, false, arg);
		},
		query: function(type)
		{
			return methods.settings.document.queryCommandValue(type);
		},
		fns: 
		{
			block: function( btn )
			{
				var element = $.fs_selected(true);
				console.log($.fs_selected());
				//
				if( methods.query('formatBlock') == btn.value)
				{
					methods.cmd('formatBlock','p');
				}
				else
				{
					if( element[0].tagName.toLowerCase() == btn.value )
					{
						element.replaceWith('<p>'+element.html()+'</p>');
					}
					else if( element.find(btn.value).length > 0 )
					{
						element.find(btn.value).replaceWith(element.find(btn.value).html());
					}
					else if( element.parents(btn.value).length > 0 )
					{
						element.parents(btn.value).replaceWith('<p>'+element.parents(btn.value).html()+'</p>');
					}
					else
					{
						methods.cmd('formatBlock', btn.value);
					}
				}
			},
			inline: function( btn )
			{
				var element = $.fs_selected(true);
				// console.log(element);
				if( element[0].tagName.toLowerCase() == btn.tag )
				{
					element.replaceWith(element.html());
				}
				else if( element.find(btn.tag).length > 0 )
				{
					element.find(btn.tag).replaceWith(element.find(btn.tag).html());
				}
				else
				{
					var selected = $.fs_selected();
					if(selected != false)
					{
						methods.cmd('insertHTML', '<'+btn.tag+'>'+selected+'</'+btn.tag+'>');
					}
				}
			},
			html: function( btn, wysiwyg )
			{
				var wysiwyg_html = wysiwyg.siblings('.wysiwyg-html');
				var string = wysiwyg.html().replace(/>/g,"&gt;").replace(/</g,"&lt;");
				wysiwyg_html.html(string);
			},
			clean: function( btn, wysiwyg )
			{
				var string = wysiwyg.html().replace(/(<br\s+?\/?>){2,}/gi,"<br />").replace(/<(\w+)>(?:<br\s*\/?>)*<\/\w+>/gi,"");
				wysiwyg.html(string);
				string = string.replace(/>/g,"&gt;").replace(/</g,"&lt;");
				wysiwyg.siblings('.wysiwyg-html').html(string);
			}
		}
	};

	$.fn.fs_wysiwyg = function( method )
	{
		// Method calling logic
		if ( methods[method] ) 
		{
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} 
		else if ( typeof method === 'object' || ! method ) 
		{
			return methods.init.apply( this, arguments );
		}
		else
		{
			$.error( 'Method ' +  method + ' does not exist on jQuery.fs_dialog' );
		}
	}

	// default options
	$.fn.fs_wysiwyg.defaults = {
		document: document,
		buttons: {
			h2: 				{label:'H2', tag: 'heading', value: 'h2', fn: 'block'},
			h3: 				{label:'H3', tag: 'heading', value: 'h3', fn: 'block'},
			bold: 				{label:'<strong>B</strong>', tag: 'strong', fn: 'inline'},
			italic: 			{'label':'<em>i</em>', tag: 'i'},
			// quote: 				{label:'quote', tag: 'formatblock', value: 'blockquote', fn: 'block'},
			link: 				{label:'link', tag: 'heading', fn: 'inline'},
			subscript: 			{label:'X<sub>2</sub>', tag: 'subscript',},
			superscript: 		{label:'X<sup>2</sup>', tag: 'superscript'},
			bullet_list: 		{label:'list', tag: 'insertUnorderedList'},
			numbered_list: 		{label:'ordered list', tag: 'insertOrderedList'},
			remove_format: 		{label:'remove format', tag: 'removeFormat'},
			// html: 				{label:'html', tag: 'html', fn: 'html'},
			// clean: 				{label: 'clean', tag: 'clean', fn: 'clean'}
		}
	};
// add jquery to scope	
})( jQuery, window, document);
// ----------------------------------------------------
// once jquery is loaded and DOM is ready
$(function()
{
	$('.wysiwyg').fs_wysiwyg();
});