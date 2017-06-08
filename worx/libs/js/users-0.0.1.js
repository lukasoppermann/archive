// ----------------------------------------------------
// once jquery is loaded and DOM is ready
$(function()
{
	save_changes = function()
	{
		$('.dialog').on('click', '.save', function(){
			var checked = true;
			var _this = $(this);
			$.ajax({
				type : 'post',
				data: {'user':_this.parents('.dialog').find('input[name="user"]').val(),'id':_this.parents('.dialog').find('input[name="id"]').val()},
				url: CI_BASE+'users/check_username/'
			}).done(function( response )
			{
				if( response == 'FALSE' )
				{
					checked = false;
					_this.removeClass('success').addClass('error');
				}
				// check email
				$.ajax({
					type : 'post',
					data: {'email':_this.parents('.dialog').find('input[name="email"]').val()},
					url: CI_BASE+'users/check_email/'
				}).done(function( response )
				{
					if( response == 'FALSE' )
					{
						checked = false;
						_this.removeClass('success').addClass('error');
					}
					// if everything checked
					if(checked === true)
					{
						$.ajax({
							type : 'post',
							data: $('.dialog form').serialize()+'&ajax=ajax',
							dataType: 'json',
							url: CI_BASE+'users/edit'
						}).done(function( response )
						{
							if( response != null && response.error != null && response.error == 'password' )
							{
								alert('Password must be at least 6 Characters long and only contain letters and digits. It must match the retype password field.')
								_this.parents('.dialog').find('input[name="pass"]').addClass('error');
							}
							else
							{
								// close dialog box
								$('.dialog').fs_dialog('hide');	
								document.location.href = document.location.href;
							}
						}).fail(function()
						{
							alert('There has been an error');
							// close dialog box
							$('.dialog').fs_dialog('hide');
						});
					}
				});
			});
		});
		
		$('.dialog').on('click', '.delete', function()
		{
			$.ajax({
				type : 'post',
				data: {'id':$(this).parents('.dialog').find('input[name="id"]').val()},
				url: CI_BASE+'users/delete/'
			}).done(function( response )
			{
				$('.dialog').fs_dialog('hide');
				document.location.href = document.location.href;
			});
		});
	}
	
	$('.user-card').click(function(){
		var id = $(this).data('user-id');
		$('.dialog').fs_dialog('show', 'ajax', {url:CI_BASE+'users/get_user_data', data:{ajax: 'ajax', 'id':id},'fns':{'success':save_changes}, 'dataType':'text', 'refresh':true}, '', {'close_label':'&times;',overlay_close:true});
	});
	
	$('.add-user-card').click(function(){
		$('.dialog').fs_dialog('show', 'ajax', {url:CI_BASE+'users/get_user_data', data:{ajax: 'ajax', 'id':null},'fns':{'success':save_changes}, 'dataType':'text', 'refresh':true}, '', {'close_label':'&times;',overlay_close:true});
	});
	
	$('body').on('blur', '.dialog input[name="user"]', function()
	{
		var _this = $(this);
		$.ajax({
			type : 'post',
			data: {'user':_this.val(),'id':_this.parents('.dialog').find('input[name="id"]').val()},
			url: CI_BASE+'users/check_username/'
		}).done(function( response )
		{
			if( response == 'TRUE' )
			{
				_this.removeClass('error').addClass('success');
			}
			else
			{
				_this.removeClass('success').addClass('error');
			}
		}).fail(function()
		{
			alert('There has been an error');
			// close dialog box
			$('.dialog').fs_dialog('hide');
		});
	});
	
	$('body').on('blur', '.dialog input[name="email"]', function()
	{
		var _this = $(this);
		$.ajax({
			type : 'post',
			data: {'email':_this.val()},
			url: CI_BASE+'users/check_email/'
		}).done(function( response )
		{
			if( response == 'TRUE' )
			{
				_this.removeClass('error').addClass('success');
			}
			else
			{
				_this.removeClass('success').addClass('error');
			}
		}).fail(function()
		{
			alert('There has been an error');
			// close dialog box
			$('.dialog').fs_dialog('hide');
		});
	});

	// $('.dialog').fs_dialog('position');
});