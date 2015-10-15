$(document).ready(function(){	
	$('#user_settings_menu ul li a').click(function(e){
		e.preventDefault();
		var $a= $(e.target); 
		var title = $a.html();
		var hrefl = $a.attr('href');
		var href_arr = hrefl.split('index.php');
		var type = $(this).attr('id');
		$('#user_settings_menu ul li').removeClass('active');
		$(this).parent().addClass('active');
		var ajax_hrefl = href_arr[0]+'index.php/ajax/user/user_settings/'+type;
		
		$.get(ajax_hrefl, function(json){
			$('h1 .subtitle .name').html(json.title);
			$('h1 .subtitle .title').html(json.sub_title);
			$('#message').html(json.message);
			$('#register_form').hide().html(json.content).fadeIn('slow');
			history.pushState(json, title, hrefl);
		});
	});
	$(window).bind("popstate", function(event) {
	    var data = event.originalEvent.state;
	    $('h1 .subtitle .name').html(data.title);
	    $('h1 .subtitle .title').html(data.sub_title);
		$('#message').html(data.message);
		$('#register_form').html(data.content);
	});
});