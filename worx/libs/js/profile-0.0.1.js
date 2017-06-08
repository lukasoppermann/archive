$('#profile_form').on('click', '#save', function(){
	$('.error').removeClass('error');
	$.ajax({
		type: 'POST',
		data: $(this).parents('form').serialize(),
		url: CI_ROOT+'profile/save/'+$(this).parents('form').data('page'),
		dataType: 'json'
	}).done(function(r)
	{
		if(r.errors)
		{
			$.each(r.errors, function(i){
				$('[name='+i+']').addClass('error');
			});
		}
		else
		{
			$('#save').text('saved');
			$('#profile_form').addClass('saved')
			setTimeout( function(){
				$('#save').text('save changes');
			}, 3000);
		}
	});
});