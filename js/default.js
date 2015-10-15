$(document).ready(function(){	
	
	$(document).on('submit', "#login", function(event)
	{
		event.preventDefault();
		var hrefl= $(this).attr('action');
		var records = $(this).serialize();
		$.post(hrefl+'/true', records, function(json){
			if(json.redirect!=''){
				window.location.replace(json.redirect);
				$('#scrollbar2').tinyscrollbar();
			} else {
				$('#html').html(json.page);
				$('#scrollbar2').tinyscrollbar();
			}				
		})
	});
	
	$('body').append('<div id="overlay" />');
	$('<div id="overlay_content" />').appendTo('body').wrap('<div id="overlay_content_wrapper" />');	
	$(document).on('click', '#content section ul li .container', function(){
		var content = $(this).html();	
		$('#overlay, #overlay_content_wrapper').css('width', '100%').css('height', '100%');
		$('#overlay_content').html(content).prepend('<div class="close_overlay">X</div>').css('visibility', 'visible');
	});
	$(document).on('click', '#overlay_content_wrapper, .close_overlay', function(e){
		var $clicked=$(e.target);
		if($clicked.is('#overlay_content_wrapper') || $clicked.is('.close_overlay')) {
		    $('#overlay, #overlay_content_wrapper').fadeOut();
			$('#overlay_content').html('').css('visibility', 'hidden');
		} 
	})
	
	$('.leaf a').hover(function(e){
		$(this).siblings('.sub-menu-leaf').css('visibility', 'visible');
	}, function(e2){
		$(this).siblings('.sub-menu-leaf').css('visibility', 'hidden');
	});
	$('.sub-menu-leaf').hover(function(e){
		$(this).css('visibility', 'visible');
		$(this).siblings('a').addClass('active-btn');
	}, function(e2){
		$(this).css('visibility', 'hidden');
		$(this).siblings('a').removeClass('active-btn');
	});
	$(document).on('submit', '#upload_csv', function(event){
		event.preventDefault();
		var hrefl = $(this).attr('action');
		var type = $("select[name='type_upload'] option:selected").val();
		hrefl = hrefl.replace('dossier', 'ajax');		
		$('html').css('cursor', 'progress');
		
		var file = document.getElementById('importeren').files[0];		
		var reader = new FileReader();
		reader.readAsBinaryString(file, 'UTF-8');
		reader.onload = function(event){
			var result = event.target.result;
			var fileName = document.getElementById('importeren').files[0].name;
			$.post('/secretariaat/index.php/ajax/beheren/upload/'+type, {data: result, fileName: fileName}, function(json){
				if (json.session_expired != undefined)
				{					
					hrelf_go = hrefl.split('/');
					$('html').css('cursor', 'auto');
					window.location = hrefl_go[0]+'/'+hrefl_go[1]+'/'+hrefl_go[2];
				}
				$("#upload_csv .info").html(json.inhoud);
			});	
		}
	});
	
	$(document).on('submit', '#upload_overzicht_csv', function(event){
		event.preventDefault();
		var hrefl = $(this).attr('action');
		hrefl = hrefl.replace('dossier', 'ajax');		
		$('html').css('cursor', 'progress');
		
		var file = document.getElementById('import_provincies').files[0];		
		var reader = new FileReader();
		reader.readAsBinaryString(file, 'UTF-8');
		reader.onload = function(event){
			var result = event.target.result;
			var fileName = document.getElementById('import_provincies').files[0].name;
			$.post('/secretariaat/index.php/beheren/beheren/upload_win7/volledige_lijst', {data: result, fileName: fileName}, function(json){
				if (json.session_expired != undefined)
				{					
					hrelf_go = hrefl.split('/');
					$('html').css('cursor', 'auto');
					window.location = hrefl_go[0]+'/'+hrefl_go[1]+'/'+hrefl_go[2];
				}
				$("#upload_dossiers_csv .info").html(json.inhoud);
			});	
		}
	});
	$(document).on('submit', '#upload_computer_gegevens_csv', function(event){
		event.preventDefault();		
		$('html').css('cursor', 'progress');
		
		var file = document.getElementById('import_computer_gegevens').files[0];		
		var reader = new FileReader();
		reader.readAsBinaryString(file, 'UTF-8');
		reader.onload = function(event){
			var result = event.target.result;
			var fileName = document.getElementById('import_computer_gegevens').files[0].name;
			$.post('/secretariaat/index.php/beheren/beheren/upload_win7/computer_gegevens', {data: result, fileName: fileName}, function(json){
				if (json.session_expired != undefined)
				{					
					hrelf_go = hrefl.split('/');
					$('html').css('cursor', 'auto');
					window.location = hrefl_go[0]+'/'+hrefl_go[1]+'/'+hrefl_go[2];
				}
				$("#upload_computer_gegevens_info .info").html(json.inhoud);
			});	
		}
	});
	$(document).on('click', 'button[name="volledig_overzicht_exporteren"]', function(e)
	{
		e.preventDefault();
		$.getJSON('/secretariaat/index.php/beheren/beheren/export_win7/volledig_overzicht', function(json){
			if (json.session_expired != undefined)
			{					
				hrelf_go = hrefl.split('/');
				$('html').css('cursor', 'auto');
				window.location = hrefl_go[0]+'/'+hrefl_go[1]+'/'+hrefl_go[2];
			}
			if (json.url != undefined){
				window.location=json.url;
			}
		});			
	});
	$(document).on('click', '.edit-btn, .edit-btn a, .rapport-btn, #rapport_dossier', function(event){
		var $a= $(event.target); 
		if ($a.parents('.menu_wrapper').children('.edit-menu').css('visibility') == 'hidden'){
			$('.edit-menu').css('visibility', 'hidden');
			$a.parents('.menu_wrapper').children('.edit-menu').css('visibility', 'visible');
		} else {
			$a.parents('.menu_wrapper').children('.edit-menu').css('visibility', 'hidden');
		}
		$(document).bind('click', function(e) {
			var $clicked=$(e.target); // get the element clicked
		    if($clicked.parents().is('.edit-btn')) {
		    	
		    } else {		    	
		    	$('.edit-menu').css('visibility', 'hidden');
		    	
		    }
		});
	});
	$(document).on('click', ':button[name="select_all"]', function(event){
		var checkboxes = $(this).parent().siblings('div').children(':checkbox');
		$(checkboxes).each(function(index, cb){
			var checked = $(cb).attr('checked');			
			if($(this).attr('checked') == 'checked'){
				$(this).removeAttr('checked');	
			} else {
				$(this).attr('checked', 'checked');
			}
		})		
	});
	$(document).on('submit', '#te_behandelen_formulier', function(e){
		e.preventDefault();
		var form_string = $(this).serialize();
		var hrefl = $(this).attr("action");
		$.ajax({
			url: hrefl,
			data: form_string,
			type: "POST",
			success: function(json){
				if (json.session_expired != undefined)
				{					
					hrelf_go = hrefl.split('/');
					$('html').css('cursor', 'auto');
					window.location = hrefl_go[0]+'/'+hrefl_go[1]+'/'+hrefl_go[2];
				}
				
				$('#message').html(json.message).show();
				$('#'+json.tabel+' tbody').prepend(json.tr);
			}
		})
	});
	$(document).on('submit', '#parlementairen_formulier', function(e){
		e.preventDefault();
		var form_string = $(this).serialize();
		var hrefl = $(this).attr("action");
		$.ajax({
			url: hrefl,
			data: form_string,
			type: "POST",
			success: function(json){
				if (json.session_expired != undefined)
				{					
					hrelf_go = hrefl.split('/');
					$('html').css('cursor', 'auto');
					window.location = hrefl_go[0]+'/'+hrefl_go[1]+'/'+hrefl_go[2];
				}
				
				$('#message').html(json.message).show();
				$('#'+json.tabel+' tbody').prepend(json.tr);
			}
		})
	});
	$(document).on('submit', '#wegen_formulier', function(e){
		e.preventDefault();
		var form_string = $(this).serialize();
		var hrefl = $(this).attr("action");
		$.ajax({
			url: hrefl,
			data: form_string,
			type: "POST",
			success: function(json){
				if (json.session_expired != undefined)
				{					
					hrelf_go = hrefl.split('/');
					$('html').css('cursor', 'auto');
					window.location = hrefl_go[0]+'/'+hrefl_go[1]+'/'+hrefl_go[2];
				}
				
				$('#message').html(json.message).show();
				$('#'+json.tabel+' tbody').prepend(json.tr);
			}
		})
	});
	$(document).on('click', 'a.tb_deactive', function(e){
		e.preventDefault();
		var hrefl = $(this).attr("href");
		$link = $(this);
		$.getJSON(hrefl, function(json){
			if($('#activatie option:selected').val() == 'beide')
			{
				$link.parents('tr').children('td').children('.janee').text(json.janee);
				if (json.janee == 'ja'){
					$link.html('deactiveren');
					var url_c = $link.attr('href');
					var url_change = url_c.replace('/activeren/', '/deactiveren/')
					$link.attr('href', url_change);
				} else {
					$link.html('activeren');
					var url_c = $link.attr('href');
					var url_change = url_c.replace('/deactiveren/', '/activeren/')
					$link.attr('href', url_change);
				}
				
			} else {
				$link.parents('tr').hide();
			}
			
		});
	});
	$(document).on('change', '#activatie', function(e){
		var hrefl = $(this).data('url');
		
		var selected = $('#activatie option:selected').val(); 
		var hrefl_history = hrefl+'/'+selected;
		var hrefl_go = hrefl.replace('beheren/beheren', 'ajax/beheren')+'/'+selected;
		$.getJSON(hrefl_go, function(json){
			if(json.te_behandelen != undefined){
				$('#te_behandelen_lijst').html(json.te_behandelen);
				history.pushState(json, 'Overzicht te behandelen', hrefl_history);
			} else if(json.parlementairen != undefined){
				$('#parlementairen_lijst').html(json.parlementairen);
				history.pushState(json, 'Overzicht parlementairen', hrefl_history);
			} else {
				$('#wegen_lijst').html(json.wegen);
				history.pushState(json, 'Overzicht wegen ', hrefl_history);
			}
		});
		
	});
	
	$(window).bind("popstate", function(event) {
		 var data = event.originalEvent.state;
		 $('#te_behandelen_lijst').html(data.te_behandelen);
	});
	$(document).on('click', '.submit.ajax', function(e){
		e.preventDefault();
		var form_string = $(this).parents('form').serialize();
		var hrefl = $(this).parents('form').attr("action");
		$.ajax({
			url: hrefl,
			data: form_string,			
  			
			success: function(json){
				if (json.session_expired != undefined)
				{					
					hrelf_go = hrefl.split('/');
					$('html').css('cursor', 'auto');
					window.location = hrefl_go[0]+'/'+hrefl_go[1]+'/'+hrefl_go[2];
				}
				$('#overlay, #overlay_content_wrapper').css('width', '0%').css('height', '0%');
				$('#overlay_content').html('').css('visibility', 'hidden');
				$('#message').html(json.message).show();
				$('#'+json.tabel+' tbody').prepend(json.tr);
				if (json.select != undefined)
				{
					if (json.tabel == 'la')
					{
						json.tabel = "leidend_ambtenaar"
					}
					$("select[name='"+json.tabel+"']").append(json.select);
				}
			}
		})
	});
	
	$(document).on('focus', '.te_bepalen_edit, .te_bepalen_email_edit', function(event){
		$(this).siblings('.te_bepalen_save, .te_bepalen_cancel').css('display', 'inherit');
		var div= event.target; 
		var sel, range;
        if (window.getSelection && document.createRange) {
        	selection = window.getSelection();  
            range = document.createRange();
            range.selectNodeContents(div);
            sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        } else if (document.body.createTextRange) {
            range = document.body.createTextRange();
            range.moveToElementText(div);
            range.select();
        }
	});
	
	$(document).on('click', '.te_bepalen_save', function(e){
		var hrefl = $(this).parents('#te_behandelen').data('url');
		var te_behandelen_id = $(this).siblings('.te_bepalen_edit').data('id');
		var te_behandelen = $(this).siblings('.te_bepalen_edit').text();
		var te_behandelen_email = $(this).siblings('.te_bepalen_email_edit').text();
		var $clicked=$(e.target);
		$.post(hrefl+'/'+te_behandelen_id, {naam: te_behandelen, email: te_behandelen_email}, function(json){
			if (json.success){
				$clicked.siblings('.te_bepalen_orig').html(te_behandelen);
				$clicked.siblings('.te_bepalen_cancel').css('display', 'none');
		 		$clicked.css('display', 'none');
			}
		});
	});
	$(document).on('click', '.te_bepalen_cancel', function(e){
		var orig = $(this).siblings('.te_bepalen_orig').html();		
		 $(this).siblings('.te_bepalen_edit').html(orig);
		 $(this).siblings('.te_bepalen_save').css('display', 'none');
		 $(this).css('display', 'none');
	});
	
	$(document).on('focusout','.parlementarier_edit', function(e){
		if($(this).next('.parlementarier_orig').html() == $(this).html()){
			$(this).siblings('.parlementarier_save, .parlementarier_cancel').css('display', 'none');
		}		
	});
	$(document).on('focus', '.parlementarier_edit', function(event){
		$(this).siblings('.parlementarier_save, .parlementarier_cancel').css('display', 'inherit');
		var div= event.target; 
		var sel, range;
        if (window.getSelection && document.createRange) {
        	selection = window.getSelection();  
            range = document.createRange();
            range.selectNodeContents(div);
            sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        } else if (document.body.createTextRange) {
            range = document.body.createTextRange();
            range.moveToElementText(div);
            range.select();
        }
	});
	
	$(document).on('click', '.parlementarier_save', function(e){
		var hrefl = $(this).parents('#parlementairen').data('url');
		var parlementairen_id = $(this).siblings('.parlementarier_edit').data('id');
		var parlementairen = $(this).siblings('.parlementarier_edit').text();
		var $clicked=$(e.target);
		$.post(hrefl+'/'+parlementairen_id, {naam: parlementairen}, function(json){
			if (json.success){
				$clicked.siblings('.parlementarier_orig').html(parlementairen);
				$clicked.siblings('.parlementarier_cancel').css('display', 'none');
		 		$clicked.css('display', 'none');
			}
		});
	});
	$(document).on('click', '.parlementarier_cancel', function(e){
		var orig = $(this).siblings('.parlementarier_orig').html();		
		 $(this).siblings('.parlementarier_edit').html(orig);
		 $(this).siblings('.parlementarier_save').css('display', 'none');
		 $(this).css('display', 'none');
	});
	
	$(document).on('focusout', '.weg_edit', function(e){
		var $clicked=$(e.target);
		if ($clicked!=$('.weg_cancel, .weg_save')){
			$(this).siblings('.weg_save, .weg_cancel').css('display', 'none');
		} else {
			$(this).siblings('.weg_save, .weg_cancel').css('display', 'inherit');
		}		
	});
	
	$(document).on('focusin', '.weg_edit_code, .weg_edit_naam',function(event){
		$(this).siblings('.weg_save, .weg_cancel').css('display', 'inherit');
		var div= event.target; 
		var sel, range;
        if (window.getSelection && document.createRange) {
        	selection = window.getSelection();  
            range = document.createRange();
            range.selectNodeContents(div);
            sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        } else if (document.body.createTextRange) {
            range = document.body.createTextRange();
            range.moveToElementText(div);
            range.select();
        }
	});
	
	$(document).on('click', '.weg_save', function(e){
		var hrefl = $(this).parents('#wegen').data('url');
		var wegen_id = $(this).siblings('.weg_edit').data('id');
		var weg_code = $(this).siblings('.weg_edit_code').text();
		var weg_naam = $(this).siblings('.weg_edit_naam').text();
		var $clicked=$(e.target);
		$.post(hrefl+'/'+wegen_id, {code: weg_code, naam: weg_naam}, function(json){
			if (json.success){
				$clicked.siblings('.weg_orig_code').html(weg_code);
				$clicked.siblings('.weg_orig_naam').html(weg_naam);
				$clicked.siblings('.weg_cancel').css('display', 'none');
		 		$clicked.css('display', 'none');
			}
		});
	});
	$(document).on('click', '.weg_cancel', function(e){
		var code_orig = $(this).siblings('.weg_orig_code').text();		
		var naam_orig = $(this).siblings('.weg_orig_naam').text();	
		$(this).siblings('.weg_edit_code').text(code_orig);
		$(this).siblings('.weg_edit_naam').text(naam_orig);
		$(this).siblings('.weg_save').css('display', 'none');
		$(this).css('display', 'none');
	});
	
	$('.actief.toggle').next('.collapsed').css('display', 'inherit');
	
	$(document).on('click', '.toggle', function(event){
		event.preventDefault();
		$(this).next('.collapsed').slideToggle();
	});
	$(document).on('change', "select[name='dossierbeheerder']", function(event){
		if ($("select[name='dossierbeheerder'] option:selected").val() == 1){
			add_beheren('dossierbeheerder');
		}
	});
	$(document).on('change', "select[name='leidend_ambtenaar']", function(event){
		if ($("select[name='leidend_ambtenaar'] option:selected").val() == 1){
			add_beheren('la');
		}
	});
	$(document).on('change', '#provincies', function(e){
		var provincie = $(this).val();
		if ($(this).val() != ''){
			$('#districten').children(':not(.empty)').hide();
			$('#districten').children('.'+provincie.replace(' ', '_')).show();
			$('#districten').children('.empty').attr('selected', true);
		} else {
			$('#districten').children().show();
		}
	});
	$(document).on('change', '#districten', function(e){
		if ($(this).val() != ''){
			var provincie = $('#districten option:selected').attr('class').replace('_', ' ');
			$('#provincies option[value="'+provincie+'"]').attr('selected', true);
		}
	});	
	$(document).on('click', '#districten_tabel a', function(e){
		e.preventDefault();
		var hrefl = $(this).attr('href');
		edit_beheren(hrefl);
	});
	if ($('#message').val() != ''){
		$('#message').show();
	} else {
		$('#message').hide();
	}
	$(document).on('change', 'select[name="type_rapport"]', function(event){
		var hrefl = window.location.href;
		var hrefl_go = hrefl.split('dossier');
		$('#db_, #la_').css('display', 'none');
		hrefl = hrefl_go[0] + 'ajax/rapporten/lijsten/'+$('select[name="type_rapport"] option:selected').val();
		if ($('select[name="type_rapport"] option:selected').val() == 'per_la')
		{
			$('#la_').css('display', 'inherit');
		} 
		else if ($('select[name="type_rapport"] option:selected').val() == 'per_db')
		{
			$('#db_').css('display', 'inherit');
		} 
		$.get(hrefl, function(json){
			$('#ajax_load_form_items').html(json.lijsten);
		});
	});
	$(document).on('click', '.editabel', function(event) {
		
	});
	
});

function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;

	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";

	if(typeof(arr) == 'object') { //Array/Hashes/Objects
		for(var item in arr) {
			var value = arr[item];
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Strings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

function edit_profile(username) {	
	var title = 'Profiel bewerken';
	var hrefl = '/secretariaat/index.php/user/settings/'+username;
	$.get('/secretariaat/index.php/ajax/user/edit_account/'+username, function(json){
		if (json.session_expired != undefined)
		{					
			hrelf_go = hrefl.split('/');
			$('html').css('cursor', 'auto');
			window.location = hrefl_go[0]+'/'+hrefl_go[1]+'/'+hrefl_go[2];
		}
		$('h1 .subtitle .name').html(json.title);
		$('h1 .subtitle .title').html(json.sub_title);
		$('#message').html(json.message);
		$('#register_form').hide().html(json.content).fadeIn('slow');
		history.pushState(json, title, hrefl);
	})
}
function add_beheren(page){
	$.get('/secretariaat/index.php/ajax/beheren/add_beheren/'+page+'/', function(json){
		if (json.session_expired != undefined)
		{					
			hrelf_go = hrefl.split('/');
			$('html').css('cursor', 'auto');
			window.location = hrefl_go[0]+'/'+hrefl_go[1]+'/'+hrefl_go[2];
		}
		$('#overlay, #overlay_content_wrapper').hide().css('width', '100%').css('height', '100%').fadeIn();
		$('#overlay_content').html(json.content).prepend('<div class="close_overlay">X</div>').css('visibility', 'visible');
	})
}
function edit_beheren(page){
	$.get(page, function(json){
		if (json.session_expired != undefined)
		{					
			hrelf_go = hrefl.split('/');
			$('html').css('cursor', 'auto');
			window.location = hrefl_go[0]+'/'+hrefl_go[1]+'/'+hrefl_go[2];
		}
		$('#overlay, #overlay_content_wrapper').hide().css('width', '100%').css('height', '100%').fadeIn();
		$('#overlay_content').html(json.content).prepend('<div class="close_overlay">X</div>').css('visibility', 'visible');
	})
}

