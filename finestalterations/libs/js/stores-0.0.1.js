$(document).ready(function(){
	var latlng;

	var map = new GMaps({
	  	div: '#map',
	  	lat: -37.843676,
	  	lng: -144.994491,
		zoomControl: false,
		panControl: false,
		mapTypeControl: false,
		streetViewControl: false,
		scaleControl: false,
		overviewMapControl: false
	});

	GMaps.geocode({
	  address: $('<div>'+$('.address').html().replace("<br>", " ")+'</div>').text().trim(),
	  callback: function(results, status) {
	    if (status == 'OK') {
			latlng = results[0].geometry.location;
			map.setCenter(latlng.lat()+0.0015, latlng.lng()-0.001);
			map.drawOverlay({
				lat: latlng.lat()+0.0015,
				lng: latlng.lng()-0.001,
			  	content: '<a href="'+$('.address').attr('href')+'" target="_blank" class="marker"><img src="'+CI_ROOT+'media/layout/finestalterations_marker.png" /></a>'
			});
			$('#map').animate({'opacity':1});
	    }
	  }
	});
	
	// -----------------------------
	// inqury form
	$('#inquiry_form').on('focus', 'input, textarea', function(){
		if( $(this).val() == $(this).attr('placeholder') )
		{
			$(this).val("");
		}
	});
	$('#inquiry_form').on('blur', 'input, textarea', function(){
		if( $(this).val() == "" )
		{
			$(this).val($(this).attr('placeholder'));
		}
	});

	$('#inquiry_form').on('click', '#submit', function(){
		$('#inquiry_form').find('.error').removeClass('error');
		$.ajax({
			type : 'post',
			data: $('#inquiry_form').serialize()+"&store_id="+$(this).parents('form').data('store'),
			url: CI_BASE+'ajax/ticket',
			dataType: 'json',
			success: function(response)
			{
				if(response.success == true)
				{
					$('#inquiry_form').find('input, textarea').val('');
					$('#inquiry_form').addClass('success');
					setTimeout( function(){
						$('#inquiry_form').removeClass('success');
					}, 3000);
				}
				else
				{
					$.each(response.fields, function(i){
						$('[name='+i+']').addClass('error');
					});
				}
			}
		});
	});
});