// ----------------------------------------------------
// Base functions used in all or many files
// ----------------------------------------------------
// define functions 
;(function( $, window, document )
{
	// ----------------------------------------------------
	// center element
	$.fn.fs_center = function ()
	{
		// return element for chaining
		return this.each(function()
		{
			// cache selection
			var _this = $(this);
			var _window = $(window);
			// initial set position
			_this.css({'left':'50%', 'top':'50%', 'marginLeft':-_this.outerWidth()/2, 'marginTop':-_this.outerHeight()/2});
			// on load reajust position if nessesary
			_window.load(function(){
				_this.animate({'marginLeft':-_this.outerWidth()/2, 'marginTop':-_this.outerHeight()/2}, 200);
			});
			// adjust position on resize
			_window.resize(function(){
				_this.css({'marginLeft':-(_this.outerWidth()/2), 'marginTop':-_this.outerHeight()/2});	
			});
		});
	};
	// ----------------------------------------------------
	// set focus
	$.fn.fs_focus = function ()
	{
		// return element for chaining
		return this.each(function()
		{
			// cache selection
			var _this = $(this);
			// set focus and assign value to element to set focus to end
			_this.focus().val( _this.val() );
		});
	};
	// ----------------------------------------------------
	// debounced resize event (fires once every 100ms)
	$.fn.fs_resize = function( c, t )
	{
		onresize = function(){
			clearTimeout( t );
			t = setTimeout( c, 100)
		};
		return c;
	};
	// ----------------------------------------------------
	// get selected text
	$.fs_selected = function( node )
	{
		rangy.init();
		var range = rangy.createRange();
		// All DOM Range methods and properties supported
		range.selectNodeContents(document.body);
		// 
		// // Selection object based on those in Mozilla, WebKit and Opera
		// var sel = rangy.getSelection();
		// sel.removeAllRanges();
		// sel.addRange(range);
		// console.log(sel);
		// if text should be returned
		if( node !== true )	
		{


			return rangy.getSelection().expand("word");
			
			// if( document.selection )
			// 			{
			// 				selection = document.selection.createRange().text;
			// 			}
			// 			// check if highlighted or cursor
			// 			if( selection.isCollapsed == true)
			// 			{
			// 				var caret_position = $.fs_caret_position(selection);
			// 				// get parent editor
			// 				var editor = $(selection.getRangeAt(0).startContainer.parentNode).parents('div[contenteditable=true]');
			// 				var text = editor.text();
			// 				// // get text before caret
			// 				// 	var pre_text = text.substring(caret_position-1, caret_position);
			// 				// 	var post_text = text.substring(caret_position, caret_position+1);
			// 				// 	// find space before caret
			// 				// 	console.log('_'+pre_text+'_'+post_text+'_');
			// 				// 	if( pre_text == " " || pre_text == "" )
			// 				// 	{
			// 				// 		selection.modify("move", "backward", "word");
			// 				// 		selection.modify("extend", "backward", "word");
			// 				// 		// return false;
			// 				// 	}
			// 				// 	else if( post_text == " " || post_text == "" )
			// 				// 	{
			// 				// 		selection.modify("extend", "forward", "word");
			// 				// 		// return false;
			// 				// 	}
			// 				// 	else
			// 				// 	{
			// 				// 		selection.modify("move", "backward", "word");
			// 				// 		selection.modify("extend", "forward", "word");
			// 				// 		var range = document.createRange();
			// 				// 		console.log(caret_position);
			// 				// 		console.log(selection.anchorNode);
			// 				// 		range.collapse(true);
			// 				// 	}
			// 				// console.log(selection);
			// 				var range = selection.getRangeAt(0);  
			// 				var pre_text = text.substring(0, caret_position);
			// 				var post_text = text.substring(caret_position, text.length);
			// 				console.log(pre_text);
			// 				if( pre_text.indexOf(" ") > 0 )
			// 				{
			// 					var words = pre_text.split(" ");
			// 					var word_start = caret_position-words[words.length - 1].length;
			// 				}
			// 				else
			// 				{
			// 					var word_start = 0;
			// 				}
			// 				// get word end
			// 				var post_text = text.substring(caret_position, text.length);
			// 				// find space after caret
			// 				if( post_text.indexOf(" ") > 0 )
			// 				{
			// 					var words = post_text.split(" ");
			// 					var word_end = caret_position+words[0].length;
			// 				}
			// 				else
			// 				{
			// 					var word_end = caret_position;
			// 				}
			// 				var range = document.createRange();
			// 				range.setStart(selection.anchorNode, word_start);
			// 				range.setEnd(selection.anchorNode, word_end);
			// 				selection.removeAllRanges();
			// 				selection.addRange(range);
			// 				selection = range;
			// 				// console.log("range");
			// 				// console.log(range);
			// 				// alert(word_start+' '+word_end);
			// 				// return false;
			// 			}
			// 			else
			// 			{
			// 				return selection;
			// 			}
			// 			return selection;
			// return text
			
		}
		// if node should be returned
		else
		{	
			var range = rangy.getSelection();
			range.addRange();
			
			// return rangy.getSelection().createRangyRange.startContainer();
		}
	};
	// ----------------------------------------------------
	// get cursor position
	$.fs_caret_position = function( selection )
	{
		// define vars
		var list = selection.anchorNode.parentNode.childNodes;
		var node = selection.anchorNode;
		var length = list.length;
		// get cursor position
		for( var i = 0; i < length; i++ ) 
		{
			if( list[i] == node )
			{
				return i + selection.anchorOffset;
			}
		}
		// not found
		return -1; 
	};
	// ----------------------------------------------------
// add jquery to scope	
})( jQuery, window, document);
// ----------------------------------------------------
// once jquery is loaded and DOM is ready
$(function()
{
	var _body = $('body');
	var _window = $(window);
	if(_window.height() > _body.height())
	{
		_body.height(_window.height());
	}
	//
	$(window).load(function(){
		if(_window.height() > _body.height())
		{
			_body.height(_window.height());
		}
		else if(_window.height() < _body.height())
		{
			_body.attr('style','');
		}
	});
	
	$(window).fs_resize(function(){
		if(_window.height() > _body.height())
		{
			_body.height(_window.height());
		}
		else if(_window.height() < _body.height())
		{
			_body.height($(document).height());
		}
	});
	
});