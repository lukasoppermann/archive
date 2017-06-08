// ----------------------------------------------------
// Gui functions used for gui elements
// ----------------------------------------------------
// define functions 
;(function( $, window, document )
{
	// ----------------------------------------------------
	// button bar
	$.fn.fs_button_bar = function( opts )
	{
		// merge settings
		var settings = $.extend({
			'button' 	: '.button',
			'radio' 	: '.hidden-elements',
			'active' 	: 'active'
		}, opts);
		// return element for chaining
		return this.each(function()
		{
			// cache selection
			var _this = $(this);
			// define click event
			_this.on('click', settings.button, function(){
				// cache selection
				var t = $(this);
				var radio = _this.parents().find(settings.radio);
				// check if is enabled
				if( !t.hasClass('disabled') )
				{
					// change active state
					$(settings.button).removeClass(settings.active);
					t.addClass(settings.active);
					// change radio buttons
					radio.find('input').removeAttr('checked');
					radio.find('input[value='+t.data('value')+']').attr('checked', 'checked');
				}
			});
		});
	};
	// ----------------------------------------------------
	// button bar
	$.fn.fs_dropdown = function( opts )
	{
		// merge settings
		var settings = $.extend({
			'selected' 	: 'span.selected',
			'element' 	: 'li.option',
			'select' 		: '.hidden-elements',
			'active' 		: 'active',
			'selector'	: this.selector
		}, opts);
		// return element for chaining
		return this.each(function()
		{
			// cache selection
			var _this = $(this);
			// define click event
			_this.on('click', settings.element, function(){
				// cache selection
				var t = $(this);
				var selection = t.parent().siblings(settings.select);
				var previous = selection.find('option[selected=selected]').val();
				// change active state
				$(settings.element).removeClass(settings.active);
				t.addClass(settings.active);
				t.parent().siblings(settings.selected).text(selection.find('option[value='+t.data('value')+']').text());
				// change select buttons
				selection.find('option').removeAttr('checked');
				selection.find('option[value='+t.data('value')+']').attr('selected', 'selected');
				// create the event
				var changeEvent = new CustomEvent("dropdownChange", {
					detail: {
						selected: selection.find('option:selected').val(),
						active: 	t,
						previous: previous
					}
				});
				// fire event
				_this.trigger(changeEvent);
			});
		});
	}

	// ----------------------------------------------------
	// form_autosave
	$.fn.fs_form_autosave = function( opts )
	{
		// merge settings
		var settings = $.extend({
			'file' 		: '',
			'data' 		: {},
			callback  : function(){ }
		}, opts);
		// save function
		var save = function(_this, data){
			$.ajax({
				type : 'post',
				data: data,
				url: CI_BASE+settings.file,
				dataType: 'json'
			}).done(function( response )
			{
				if( response != null && (response.success == 'saved' || response.success == true) )
				{
					_this.parents('.form-element').addClass('saved');
					_this.addClass('saved');
					settings.callback.call(_this, response);
					setTimeout( function(){
						_this.parents('.form-element').removeClass('saved');
						_this.removeClass('saved');
					}, 3000);
				}
				else
				{
					_this.parents('.form-element').addClass('error');
					_this.addClass('error');
					setTimeout( function(){
						_this.parents('.form-element').removeClass('error');
						_this.removeClass('error');
					}, 3000);
				}
			});
		};
		// return element for chaining
		return this.each(function()
		{
			var fn;
			// cache selection
			var _this = $(this);
			// define event
			_this.on('blur', function(){
				save(_this, settings.data(_this));
			});
			_this.on('keyup', function(){
				clearTimeout( fn );
				fn = setTimeout( function(){
					save(_this, settings.data(_this));
				}, 1000);
			});

		});
	}
// ----------------------------------------------------
// add jquery to scope	
})( jQuery, window, document);