$(document).ready(function(){		
	$('.login_error').css('visibility', 'visible').hide();
	$(document).on('submit', "#login", function(event)
	{
		event.preventDefault();
		var hrefl= $(this).attr('action');
		var records = $(this).serialize();
		$.post(hrefl+'/true', records, function(json){
			if (json.error_msg != undefined){
				$('.login_error').fadeIn().html(json.error_msg);				
			} else {
				window.location = '/secretariaat';		
			}
		});
	});
});