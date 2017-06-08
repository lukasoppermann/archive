// ----------------------------------------------------
// once jquery is loaded and DOM is ready
$(function()
{
	$('.page-content .autosave').fs_form_autosave({
		file:'settings/save', 
		data : function( _this ){
			return {	'type' 	:_this.parents('form').data('type'),
						'key' 	:_this.attr('name'),
						'value' :_this.val()
					}
		}
	});
	$('.page-content .autosave-store').fs_form_autosave({
		file:'settings/save', 
		data : function( _this ){
			return {	
						'id'    	:_this.parents('.store').data('id'),
						'type' 		:_this.parents('form').data('type'),
						'key' 		:_this.attr('name'),
						'alphanum' 	:_this.data('alphanum'),
						'required' 	:_this.data('required'),
						'value' 	:_this.val().split("\n").join('<br />')
					}
		}
	});
	// -------------------------------------
	// collapse
	$('.collapse').on('click', 'h2', function(){
		$(this).next('.collapsable').slideToggle(400, function(){
			$(this).parents('.collapse').toggleClass('collapsed');
		});
	});
	// -------------------------------------
	// add_store
	$('#add_store').on('click', function(){
		
		$('[name="new_store"]').find('h2').data('text', $('[name="new_store"]').find('h2').text()).text('Saving ...');
		$('#add_store').hide();
		
		$.ajax({
			type : 'post',
			data: $('[name="new_store"]').serialize().split('%0D%0A').join('%3Cbr+%2F%3E'),
			url: CI_BASE+'settings/new_store/',
			dataType: 'json'
		}).done(function( response )
		{
			if( response.success == 'true' )
			{
				$('[name="new_store"]').find('input, textarea').val('');
				window.location = window.location;
			}
			else
			{
				$('.required').removeClass('required');
				$('[name="new_store"]').find('[name=name], [name=phone], [name=email], [name=address]').each(function(){
					if( $(this).val() == '' )
					{
						$('[name="new_store"]').find('h2').text($('[name="new_store"]').find('h2').data('text'));
						$('#add_store').show();
						$(this).addClass('required');
					}
				});
			}
		});
	});
	// -------------------------------------
	// delete store
	$('[name="store"]').on('click', '.delete', function(){
		var store = $(this).parents('.store');
		$.ajax({
			type : 'post',
			data: {id: store.data('id')},
			url: CI_BASE+'settings/delete_store/',
			dataType: 'json'
		}).done(function( response )
		{
			store.animate({'opacity':0,'height':0}, 400, function(){
				$(this).remove();
			});
		});
	});
	// -------------------------------------
	// sort store
	$('.stores').sortable({
	    items: '.store'
	}).bind('sortupdate', function(e, ui)
	{
		// define object
		var data = {};
		// build object
		$(this).find('.store').each(function(i, e)
		{
			data[$(e).data('id')] = i+1;
		});
		// send ajax request
		$.ajax({
			url: CI_ROOT+'settings/sort_store',            
		        type: "POST",
		        dataType: 'json',
		        data: {items: data, ajax: 'true'}
		});
	});
	// -------------------------------------
	// social settings
	// -----------------
	// dropdown
	$('.follow').on('change', function(){
		// get current follow id
		var current_follow = $(this).find('option:selected').val();
		var type = $(this).parents('form').data('type');
		// save to db
		$.ajax({
			url: CI_ROOT+'settings/save_follow',
			type: "POST",
			dataType: 'json',
			data: {type: type, id: current_follow}
		});
	});
	// checkbox
	$('.post_to').on('change', function(){
		// get current follow id
		var _form = $(this).parents('form'); 
		var post_to = new Array();
		// loop through selected
		_form.find('.post_to:checked').each(function(){
			post_to.push($(this).val());
		});
		//
		var type = _form.data('type');
		// save to db
		$.ajax({
			url: CI_ROOT+'settings/save_post_to',
			type: "POST",
			dataType: 'json',
			data: {type: type, ids: post_to}
		});
	});
	// -------------------------------------
});