$(document).ready(function(){		
	$('.login_error').css('visibility', 'visible').hide();
	$(document).on('submit', "#login", function(event)
	{
		event.preventDefault();
		var hrefl= $(this).attr('action');
		var records = $(this).serialize();
		var redirect = $(this).data('url');
		$.post(hrefl+'/true/'+redirect, records, function(json){
			if (json.error_msg != undefined){
				$('.login_error').fadeIn().html(json.error_msg);				
			} else {
				redirect = redirect.replace(/_/gi, '/');
				window.location = '/secretariaat/index.php/'+redirect;		
			}
		});
	});
});