$(document).ready(function(){	
	var url = location.href;
	var index = url.split('/index.php/');
	var first = index[1].split('/');
	if (first[0] == 'dossier'){
		dossier_filter();
	}
	$('#opslaan, #bewerken, #bewerken_opslaan, #verwijderen').parents('li').css('display', 'none');
	var height = $("#filter").height();	
	var h4_h = $('#filter h4').outerHeight();
	var total_h = height-h4_h-20;
	var width = $("#filter").attr('width');
	$('#dossier_filter_').height(total_h).css('width', width).css('position', 'relative').css('overflow', 'hidden');	
	$('#dossier_filter_').perfectScrollbar({'suppressScrollX': false});
	$('#dossier_filter_').perfectScrollbar('update');
	$(document).on('click', 'ul.vertical_tabs li a',function(e){
		e.preventDefault();		
		$target = $(e.target);
		var hrefl = $target.attr('href');
		$active_tab = $target.html();
		$active_tab = $active_tab.toLowerCase();
		var title = 'Verrekening toevoegen';
		$('.vtab').css('display', 'none');
		$('#'+$active_tab).css('display', 'inherit');
		$('ul.vertical_tabs li').removeClass('active');
		$target.parent().addClass('active');
	});	
	$(document).on('click', 'ul.horizontal_tabs li a', function(e){
		e.preventDefault();		
		var parent_form = $(this).parents('form').attr('id');
		$target = $(e.target);
		var hrefl = $target.attr('href');
		$active_tab = $target.html();
		$active_tab = $active_tab.toLowerCase();
		var title = 'Verrekening toevoegen';
		$('.vtab').css('display', 'none');
		$('#'+ parent_form+' #'+$active_tab).css('display', 'inherit');
		$('ul.horizontal_tabs li').removeClass('active');
		$target.parent().addClass('active');
	});	
	$(document).on('click', '#edit_dossier, #add_dossier', function(event){
		event.preventDefault();
		var hrefl = $(this).attr('href');
		hrefl_ajax = hrefl.replace('dossier', 'ajax');
		ajax_page(hrefl_ajax, false, true, true, 'Verrekeningen', hrefl);		
	});
	$(document).on('click', '#add_verrekening, #verrekeningstabel a, #verrekeningen .edit-btn a',function(event){
		event.preventDefault();
		var hrefl = $(this).attr('href');
		hrefl_ajax = hrefl.replace('dossier', 'ajax');
		ajax_page(hrefl_ajax, false, true, false, 'Verrekeningen', hrefl);		
	});
	$(document).on('click', '#dossiers_zoeken ul li a', function(event){
		event.preventDefault();
		$('#dossiers_zoeken ul li').removeClass('actief');
		$(this).parent('li').addClass('actief');
		var hrefl = $(this).attr('href');
		hrefl_ajax = hrefl.replace('dossier', 'ajax');
		ajax_page(hrefl_ajax, false, true, true, 'Verrekeningen', hrefl);
	});	
	$('#lay_over').hide();	
	var hrefl = $('#nieuwe_toevoegen a').attr('href');
	$.get(hrefl, function(json){	
		$('#lay_over div').html(json.form);
		if($('select[name="type"] option:selected').val() == ' '){
			$('#form_view').hide();
		}
		dossier_calls();
	});
	
	
	$(document).on('change', '#inkomende_formulier select[name="type"]',function(event){
		var myTypes=new Array();
		myTypes['email_kabinet'] = 'EMAILKAB';
		myTypes['kabinetsnotas'] = 'KAB';
		myTypes['parlementaire_vragen'] = 'PV';
		myTypes['wegen'] = 'MPW';
		var mySelected = $('#inkomende_formulier select[name="type"] option:selected').val();
		if(mySelected != ' '){
			$('#form_view').show();
		} else {
			$('#form_view').hide();
		}
		$('.type_dossier').html(myTypes[mySelected]);
		$('#inkomende_formulier div.form-item, #inkomende_formulier div.form-item-inline, #inkomende_formulier div.form-item-inline-chbx').hide();
		$('*.'+mySelected).parent().show();
		$('.dossier_nummmer').show();
		$.get("/secretariaat/index.php/ajax/dossiers/new_dossier_nummer/"+mySelected, function(json){
			$('.dossier_nr').html(json.nummer)
		})
	});
	
	$(document).on('click', '#nieuwe_toevoegen', function(event){
		event.preventDefault();
		var hrefl = $(this).children('a').attr('href');
		if($('#lay_over').hasClass('opened')){	
			$(this).removeClass('open');
			$('#lay_over').removeClass('opened').slideUp('fast');		
			$('#top-options #opslaan').parents('li').css('display', 'none');
		} else {			
			$(this).addClass('open');
			$('#lay_over').addClass('opened').slideDown('fast');
			$('#top-options #opslaan').parents('li').css('display', 'inline');
		}		
		
	});	
	$(document).on('change', '.type_filter', function(){
		var hrefl = window.location.href;
		hrefl_arr = hrefl.split('view/verrekeningen/');
		last = hrefl_arr[1];
		dossier = last.split('/');
		hrefl = hrefl_arr[0]+'view/verrekeningen/'+dossier[0]+'/'+$('#type_filter option:selected').val()+'/'+$('#naar_ato option:selected').val()+'/'+$('#van_ato option:selected').val()+'/'+$('#naar_aannemer option:selected').val()+'/'+$('#van_aannemer option:selected').val()+'/'+$('#naar_pco option:selected').val()+'/'+$('#van_pco option:selected').val()+'/'+$('#naar_if option:selected').val()+'/'+$('input[name="investeerder"]').val();
		hrefl_ajax = hrefl.replace('dossier', 'ajax');
		ajax_page(hrefl_ajax, false, true, false, 'Verrekeningen', hrefl);
	});
	$(document).on('click', '.rapport', function(event){
		event.preventDefault();
		var hrefl = $(this).attr('href');
		var filter_geg = $('#filter_rapport').serialize();
		$.post(hrefl, filter_geg, function(json){
			window.location.replace(json.url);
		});
	});
	$(document).on('click', '.rapport-btn, #rapport_dossier', function(event){
		var $a= $(event.target); 
		event.preventDefault();
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
	$(document).on('click', '#filter_button', function(event){		
		if ($('#filter_div').css('visibility') == 'hidden'){
			$('#filter_div').css('visibility', 'visible');
		} else {
			$('#filter_div').css('visibility', 'hidden');
		}		
	});	
	
	$(document).on('click', '.selectBox', function(event){
		if ($('.selectList').css('display') == 'none'){
			$('.selectList').css('display', 'inherit');
		} else {
			$('.selectList').css('display', 'none');
		}
	});
	
	$(document).on('click', '.selectList li', function(event){
		var code = $(this).children('.code').html();
		var name = $(this).children('.name').html();
		$(this).parents('.betterSelect').children('.selectBox').html(code);
		$("#weg").text(name);
		$('input[name="wegnummer"]').val(code)
		$('input[name="wegbenaming"]').val(name)
		$(this).parent().css('display', 'none');
	});
	
	$(document).on('click', '#dossiers .link', function(event){
		event.preventDefault();
		var link = $(this).attr('href');
		var real_hrefl = link.replace('dossier/dossiers', 'ajax/dossiers');
		var form_geg = $('#dossier_filter').serialize();	
		$.post(real_hrefl, form_geg, function(json){
			$('#dossiers #table').css('display', 'none');
			$('#dossiers').append(json.dossier);
			$('#bewerken, #verwijderen').parents('li').css('display', 'inline');
			if (json.filter['filter_view']){				
				$.each(json.filter, function(key, value){
					if (key=='datum_melding_van'){
						$('#filter_details').append('<div class="filter_label">Ontvangst op (van):</div><b>'+value+'</b>');
						if (!json.filter['datum_melding_tot']){
							var currentTime = new Date()
							var month = currentTime.getMonth();
							var day = currentTime.getDate();
							var year = currentTime.getFullYear();
							$('#filter_details').append('<div class="filter_label">Ontvangst op (met):</div><b>'+day+'-'+month+'-'+year+'</b>');
						}
					} else if (key=='datum_melding_tot'){
						if (!json.filter['datum_melding_van']){
							var currentTime = new Date()
							var year = currentTime.getFullYear();
							$('#filter_details').append('<div class="filter_label">Ontvangst op (tot):</div><b>01-01-'+year+'</b>');
						}
						$('#filter_details').append('<div class="filter_label">Ontvangst op (tot):</div><b>'+value+'</b>');												
					} else if (key=='te_behandelen_door'){
						$('#filter_details').append('<div class="filter_label">Te behandelen door:</div><b>'+value+'</b>');
					} else if (key=='overgemaakt_aan'){
						$('#filter_details').append('<div class="filter_label">Overgemaakt aan sectie:</div><b>'+value+'</b>');
					}else if (key=='antwoord_tegen_tot'){
						$('#filter_details').append('<div class="filter_label">Antwoord tegen (tot):</div><b>'+value+'</b>');
					} else if (key=='antwoord_tegen_van'){
						$('#filter_details').append('<div class="filter_label">Antwoord tegen (tvanot):</div><b>'+value+'</b>');
					} 
				});				
				$('#filter_box').css('display', 'inherit');
				$('#filter_box').hover(function(){
					$('#filter_details_box').css('display', 'inherit');
				}, function(){
					$('#filter_details_box').css('display', 'none');
				});
				$(document).on('click', '#filter_box', function(e){
					e.preventDefault();
					$('#filter_details_box').css('display', 'inherit');
				})
			}			
			$('#bewerken').attr('href', '/secretariaat/index.php/dossier/dossiers/bewerken/'+json._id['$id']);
			$('#verwijderen').attr('href', '/secretariaat/index.php/dossier/dossiers/verwijderen/'+json._id['$id']);
			history.pushState(json, 'dossier', link);
		})
	});
	
	if($('#filter').css('display') == 'none'){
		$('#bewerken, #verwijderen').parents('li').css('display', 'inline');
		var link = window.location.href;
		var link_arr = link.split('view/');
		$('#bewerken').attr('href', '/secretariaat/index.php/dossier/dossiers/bewerken/'+link_arr[1]);
		$('#verwijderen').attr('href', '/secretariaat/index.php/dossier/dossiers/verwijderen/'+link_arr[1]);
	};
	$(document).on('click', "#bewerken", function(event){
		event.preventDefault();		
		var hrefl = $(this).attr('href');
		hrefl = hrefl.replace('dossier', 'ajax');		
		$('html').css('cursor', 'progress');
		$.get(hrefl, function(json){
			$('#view_dossier').html(json.dossier);
			$('#bewerken, #verwijderen, #opslaan').parents('li').css('display', 'none');
			$('#bewerken_opslaan').parents('li').css('display', 'inline');
			$('#form_view div.form-item, #form_view div.form-item-inline, #form_view div.form-item-inline-chbx').hide();
			$('*.'+json.type).parent().show();
			$('.dossier_nummer').show();
			$('html').css('cursor', 'auto');
			dossier_calls();
			history.pushState(json, 'bewerken', hrefl);			
		});
	});
	
	$(document).on('click', "#bewerken_opslaan", function(event){
		event.preventDefault();
		var inc_form = $('#bewerken_formulier').serialize();
		var hrefl = $('#bewerken_formulier').attr('action');
		$.post(hrefl, inc_form, function(json) {
			
		});
		var hrefl = $('#nieuwe_toevoegen a').attr('href');
			$.get(hrefl, function(json){
				$('.close_w').trigger('click');
				dossier_filter();
			});
	});
	$(document).on('click', "#verwijderen", function(event){
		event.preventDefault();
		var hrefl = $(this).attr('href');
		hrefl = hrefl.replace('dossier', 'ajax');		
		$('html').css('cursor', 'progress');
		$.get(hrefl, function(json){
			var hrefl = $('#nieuwe_toevoegen a').attr('href');
			$.get(hrefl, function(json){
				$('.close_w').trigger('click');
				dossier_filter();
			});
			$('html').css('cursor', 'auto');
		});
	});
	$(document).on('click', '#view_dossier .link_i', function(event){
		event.preventDefault();
		var hrefl = $(this).attr('href');
		var real_hrefl = hrefl.replace('dossier/dossiers', 'ajax/dossiers');	
		var form_geg = $('#dossier_filter').serialize();	
		$.post(real_hrefl, form_geg, function(json){
			$('#view_dossier').html(json.dossier);
			if (json.filter['filter_view'] == 'true'){
				$('#filter_box').css('display', 'inherit');
			}
			$('#bewerken').attr('href', '/secretariaat/index.php/dossier/dossiers/bewerken/'+json._id['$id']);
			$('#verwijderen').attr('href', '/secretariaat/index.php/dossier/dossiers/verwijderen/'+json._id['$id']);
			if (json.filter['filter_view']){				
				$.each(json.filter, function(key, value){
					if (key=='datum_melding_van'){
						$('#filter_details').append('<div class="filter_label">Ontvangst op (van):</div><b>'+value+'</b>');
						if (!json.filter['datum_melding_tot']){
							var currentTime = new Date()
							var month = currentTime.getMonth();
							var day = currentTime.getDate();
							var year = currentTime.getFullYear();
							$('#filter_details').append('<div class="filter_label">Ontvangst op (met):</div><b>'+day+'-'+month+'-'+year+'</b>');
						}
					} else if (key=='datum_melding_tot'){
						if (!json.filter['datum_melding_van']){
							var currentTime = new Date()
							var year = currentTime.getFullYear();
							$('#filter_details').append('<div class="filter_label">Ontvangst op (tot):</div><b>01-01-'+year+'</b>');
						}
						$('#filter_details').append('<div class="filter_label">Ontvangst op (tot):</div><b>'+value+'</b>');												
					} else if (key=='te_behandelen_door'){
						$('#filter_details').append('<div class="filter_label">Te behandelen door:</div><b>'+value+'</b>');
					} else if (key=='parlementarier'){
						$('#filter_details').append('<div class="filter_label">Parlementarier:</div><b>'+value+'</b>');
					}else if (key=='antwoord_tegen_tot'){
						$('#filter_details').append('<div class="filter_label">Antwoord tegen (van):</div><b>'+value+'</b>');
					} else if (key=='antwoord_tegen_van'){
						$('#filter_details').append('<div class="filter_label">Antwoord tegen (tot):</div><b>'+value+'</b>');
					} 
				});				
				$('#filter_box').css('display', 'inherit');
				$('#filter_box').hover(function(){
					$('#filter_details_box').css('display', 'inherit');
				}, function(){
					$('#filter_details_box').css('display', 'none');
				});
				$(document).on('click', '#filter_box', function(e){
					e.preventDefault();
					$('#filter_details_box').css('display', 'inherit');
				})
			}			
			history.pushState(json, 'dossier', hrefl);
		})
	});
	
	$(document).on('click', '.close_w', function(event){
		event.preventDefault();
		$('#view_dossier').remove();
		var hrefl = $(this).attr('href');
		$('#dossiers #table').css('display', 'inherit');
		$('#filter, #filter_openen_button').css('display', 'inherit');
		$('#bewerken, #verwijderen').parents('li').css('display', 'none');
		$('#bewerken_opslaan').parents('li').css('display', 'none');
		history.pushState('', 'dossier_overzicht', hrefl);
	});
	$(document).on('submit', '#dossier_toevoegen', function(event){
		event.preventDefault();
		var hrefl = $(this).attr('action');
		hrefl = hrefl.replace('dossier', 'ajax');		
		$('html').css('cursor', 'progress');
		var nieuw_nummer = $('input[name="dossier"]').val();
		if ($('input[name="dossier_id"]').val() != undefined){
			var origineel_nummer = $('input[name="dossier_id"]').val();
		} else{
			var origineel_nummer = undefined;
		}
		if (nieuw_nummer != origineel_nummer) {
			var go = preSubmitCheck('dossier_toevoegen', origineel_nummer, nieuw_nummer, '', false);
		}
		if ($('input[name="submit_form_perm"]').val() == 1){
			$.post(hrefl, $("#dossier_toevoegen").serialize(), function(json){
				if (json.isArray == false){
					hrelf_go = hrefl.split('/index.php');
					$('html').css('cursor', 'auto');
					window.location = hrefl_go[0];
				} else{
					var hrefl_go = hrefl.split('ajax');
					var loc = hrefl_go[0];
					if ((json.blocks == undefined) || (json.content == undefined) ||(json.information == undefined))
					{					
					window.location = loc;
					}
					$('#dossiers_zoeken').hide().html(json.blocks[0]).show();
					$('#verrekeningen').hide().html(json.content).show();
					$('#dossiers_informatie').hide().html(json.information).show();
					if (json.message == undefined){
						$('#message').hide();				
					} else {
						$('#message').html(json.message);
					}
					$('html').css('cursor', 'auto');
					verrekening_calls();
					$('#scrollbar1').tinyscrollbar();	
				}
			});
		} else {
			alert('De ingegeven dossier nummer wordt reeds gebruikt door een ander dossier. Verander het nummer of bewerk het reeds bestaande dossier.');
			$('html').css('cursor', 'auto');
		}
	});
	
	$(window).bind("popstate", function(event) {
	    var data = event.originalEvent.state;
	    if(data == ''){
	    	$('#view_dossier').remove();
			$('#dossiers #table').css('display', 'inherit');
			$('#filter, #filter_openen_button').css('display', 'inherit');
			$('title').html('Secretariaat Opvolging');
	    }	    
		if (data.dossier != undefined){
			if($('#view_dossier').html() == '' || $('#view_dossier').html() == undefined){		
				$('#dossiers #table').css('display', 'none');
				$('#filter, #filter_openen_button').css('display', 'none');
				$('#dossiers').append(data.dossier);
				$('#bewerken, #verwijderen').parents('li').css('display', 'inline');
				$('#bewerken').attr('href', '/secretariaat/index.php/dossier/dossiers/bewerken/'+data._id['$id']);
				$('#verwijderen').attr('href', '/secretariaat/index.php/dossier/dossiers/verwijderen/'+data._id['$id']);
				$('title').html('Secretariaat Opvolging');
			} else {
				$('#view_dossier').hide().html(data.dossier).show();
				$('title').html('Secretariaat Opvolging');
			}
		}
		
		$('#dossiers_zoeken ul li').removeClass('actief');		
		$('#dossiers_zoeken ul li #'+data.actief_dossier).parent('li').addClass('actief');
		if (data.message == undefined){
			$('#message').hide();				
		} else {
			$('#message').html(data.message);
		}		
	});
	$(document).on('click', '.filter-openen', function(event){
		event.preventDefault();
		if($('#filter').css('width') == '0px'){
			$('#filter').animate({					
			   	width: '+=240px',
			}, 300, function() {
				  // Animation complete.
			});
		} else {
			var section_w = $('#filter').css('width');
			$('#filter').animate({
			   	width: '-='+section_w,
			}, 300, function() {
			    // Animation complete.
			 });
		}
	});
	$(document).on('click', '#open_menu', function(event){
		event.preventDefault();
		if($('#left-bar').css('display') == 'none'){
			$('#left-bar').css('display', 'inherit');
			/*$('#filter').animate({					
			   	width: '+=240px',
			}, 300, function() {
				  // Animation complete.
			});*/
		} else {
			$('#left-bar').css('display', 'none');
			/*var section_w = $('#filter').css('width');
			$('#filter').animate({
			   	width: '-='+section_w,
			}, 300, function() {
			    // Animation complete.
			 });*/
		}
	});
	$(document).on('click', '.close', function(event){
		event.preventDefault();
		var section_w = $('#filter').css('width');
		$('#filter').animate({
		   	width: '-='+section_w,
		}, 300, function() {
		    // Animation complete.
		  });
	})
	$(document).on('change', 'select[name="oorzaak"]', function(event){
		var parent_form = $(this).parents('form').attr('id');
		var sel = $('#'+parent_form+' select[name="oorzaak"] option:selected').val();
		var hrefl = "/secretariaat/index.php/ajax/beheren/lijst/"+sel;
		var lijst = "";
		var select_text = $('#'+parent_form+' select[name="oorzaak"] option:selected').text();
		$('#'+parent_form+' input[name="oorzaak_value"]').val(select_text);		
		$('#'+parent_form +' select[name="suboorzaak2"]').html('');	
		$('#'+parent_form +' select[name="suboorzaak2"]').parent().css('display', 'none');
		$.get(hrefl, function(json){
			$.each(json, function(e, v) {
				lijst = lijst +'<option value="'+e+'">'+v+'</option>';							
			});
			if(lijst!= '<option value="0">--selecteer--</option>'){
				$('#'+parent_form +' select[name="suboorzaak"]').html(lijst);	
				$('#'+parent_form +' select[name="suboorzaak"]').parent().css('display', 'inherit');
			} else {
				$('#'+parent_form +' select[name="suboorzaak"]').html('');	
				$('#'+parent_form +' select[name="suboorzaak"]').parent().css('display', 'none');
			}		
		});
	});
	$(document).on('change', 'select[name="suboorzaak"]', function(event){
		var parent_form = $(this).parents('form').attr('id');
		var sel = $('#'+parent_form+' select[name="suboorzaak"] option:selected').val();
		var hrefl = "/secretariaat/index.php/ajax/beheren/lijst/"+sel+"/suboorzaken2";
		var lijst = "";
		var select_text = $('#'+parent_form+' select[name="suboorzaak"] option:selected').text();
		$('#'+parent_form+' input[name="suboorzaak_value"]').val(select_text);
		$.get(hrefl, function(json){
			$.each(json, function(e, v) {
				lijst = lijst +'<option value="'+e+'">'+v+'</option>';							
			});
			if(lijst!= '<option value="0">--selecteer--</option>'){
				$('#'+parent_form+' select[name="suboorzaak2"]').html(lijst);	
				$('#'+parent_form+' select[name="suboorzaak2"]').parent().css('display', 'inherit');
			} else {
				$('#'+parent_form+' select[name="suboorzaak2"]').html('');	
				$('#'+parent_form+' select[name="suboorzaak2"]').parent().css('display', 'none');
			}			
		});
	});
	$(document).on('change', 'select[name="suboorzaak2"]', function(event){
		var parent_form = $(this).parents('form').attr('id');
		var select_text = $('#'+parent_form+' select[name="suboorzaak2"] option:selected').text();
		$('#'+parent_form+' input[name="suboorzaak2_value"]').val(select_text);
	});
	$(document).on('click', '#top-options #opslaan', function(event){
		event.preventDefault();
		var inc_form = $('#inkomende_formulier').serialize();
		var hrefl = $('#inkomende_formulier').attr('action');
		$.post(hrefl, inc_form, function(json) {
			var hrefl = $('#nieuwe_toevoegen a').attr('href');
			$.get(hrefl, function(json){	
				$('#lay_over div').html("");
				$('#lay_over div').html(json.form);
				if($('select[name="type"] option:selected').val() == ' '){
					$('#form_view').hide();
				}
				dossier_calls();
			});
			$(this).removeClass('open');
			$('#lay_over').removeClass('opened').slideUp('fast');		
			$('#top-options #opslaan').parents('li').css('display', 'none');
		});
	});
	$(document).on('submit', '#inkomende_formulier', function(event){
		event.preventDefault();
		var hrefl = $(this).attr('action');
		var form_geg = $(this).serialize();
		$.post(hrefl, form_geg, function(e){
			
		})
	});
	$(document).on('change', '#dossier_filter select', function(event){
		dossier_filter();
		if($(this).attr('name')=='type'){
			var mySelected = $('#dossier_filter select[name="type"] option:selected').val();
			if (mySelected=='parlementaire_vragen') {
				$('#dossier_filter #nummer_PV').parent().css('display', 'inherit');
			} else {
				$('#dossier_filter #nummer_PV').parent().css('display', 'none');
			}
		}
	});
	$(document).on('change', '#dossier_filter .datepicker', function(event){
		dossier_filter();
	});
	$(document).on('keyup', '#dossier_filter .searchbox', function(event){
		dossier_filter();
	});
	$(document).on('change', '#bijlage', function(e){
		e.preventDefault();		
		var form = $(this).parents('form').attr('id');
		var hrefl= $(this).parents('form').attr('action');
		var formData = new FormData();
        var files = this.files;
        for (var i = 0, file; file = files[i]; ++i) { 
        	var reader = new FileReader();
        	reader.readAsDataURL(file); 
        	formData.append('bijlage', file)        	
			$.ajax({
				xhr: function()
				{
					var xhr = new window.XMLHttpRequest();
					xhr.upload.addEventListener("progress", function(evt){
						if (evt.lengthComputable) {  
							var percentComplete = (evt.loaded / evt.total)*100;
							$('#'+form +' #progressbar').html('Uploaden: '+ percentComplete.toFixed(0)+'%')
						}
					}, false);
					return xhr; 
				}, 
					url: hrefl+'/bijlagen',
					data: formData,
					processData: false,
					contentType: false,
					type: 'POST',
					dataType: 'json',
				}).done(function( data ) {
					var bijlagen = $('#'+form +' input[name="bijlagen"]').val();
					$('#'+form +' #bijlages').append('<li id="'+data._id['$id']+'" class="bijlage""><a href="/secretariaat/index.php/dossier/dossiers/files/'+data._id['$id']+'">'+data.name+'</a>&nbsp;&nbsp;</a><span class="glyphicon glyphicon-remove-circle remove_bijlage" id="remove_'+data._id['$id']+'"></span></li>');
					
					if(bijlagen == ""){
						$('#'+form +' input[name="bijlagen"]').val(data._id['$id']);
					} else {
						$('#'+form +' input[name="bijlagen"]').val(bijlagen+', '+data._id['$id']);
					}
					$('#'+form +' #progressbar').html('');
				});	
        }  		
	});
	$(document).on('click', '.remove_bijlage', function(event){
		var form = $(this).parents('form').attr('id');
		var id = $(this).attr('id');
		$(this).parent('li').hide();
		var id_arr = id.split('emove_');
		var remove_bijlagen = $('#'+form +' input[name="remove_bijlagen"]').val();
		if(remove_bijlagen == ""){
			$('#'+form +' input[name="remove_bijlagen"]').val(id_arr[1]);
		} else {
			$('#'+form +' input[name="remove_bijlagen"]').val(remove_bijlagen+', '+id_arr[1]);
		}
		var bijlagen = $('#'+form +' input[name="bijlagen"]').val();
		bijlagen = bijlagen.replace(', '+id_arr[1], '');
		bijlagen = bijlagen.replace(id_arr[1], '');
		if (bijlagen.charAt(0) == ','){
			bijlagen = bijlagen.substr(2);
		}
		$('#'+form +' input[name="bijlagen"]').val(bijlagen);
	});
	
	$(document).on('keyup', '#search_bar', function(event){
		event.preventDefault();
		var text = $(this).val();
		var href = '/secretariaat/index.php/ajax/dossiers/zoeken';
		$.post(href,{text: text}, function(json){
			
		})
	});
		
	$(document).on('click', '.onbeantwoord', function(event){
		event.preventDefault();
		var clicked = event.target;
		var href = $(this).attr('href');
		$.get(href, function(json){
			if (json.query == 'ok'){
				$(clicked).parents('tr').css('background-color', 'rgb(192,239,201)');
				$(clicked).removeClass('onbeantwoord').addClass('beantwoord');
				$(clicked).removeClass('glyphicon-thumbs-down').addClass('glyphicon-thumbs-up');
				$(clicked).attr('title', 'De pv is niet beantwoord');
				dossier_filter();
			}
		})
	});
	$(document).on('click', '.beantwoord', function(event){
		event.preventDefault();
		var clicked = event.target;
		var href = $(this).attr('href');
		$.get(href, function(json){
			if (json.query == 'ok'){
				$(clicked).parents('tr').css('background-color', '');
				$(clicked).removeClass('beantwoord').addClass('onbeantwoord');
				$(clicked).removeClass('glyphicon-thumbs-up').addClass('glyphicon-thumbs-down');
				$(clicked).attr('title', 'De pv is beantwoord');
				dossier_filter();
			}
		})
	})
	$('.datepicker').datepicker({ dateFormat: 'dd-mm-yy' });
	
	dossier_calls();	
	$( window ).resize(function() {
		$('#scrollbar2').tinyscrollbar();
		$('#dossier_filter_').perfectScrollbar('destroy');
		var height = $("#filter").height();	
		var h4_h = $('#filter h4').outerHeight();
		var total_h = height-h4_h-20;
		var width = $("#filter").attr('width');
		$('#dossier_filter_').height(total_h).css('width', width).css('position', 'relative').css('overflow', 'hidden');	
		$('#dossier_filter_').perfectScrollbar({'suppressScrollX': false});
		$('#dossier_filter_').perfectScrollbar('update');
		var t_h = $('#top').height();
		var w_h = $(window).height();
		var d_h = (w_h - t_h)-30;
		var d_w = $('#dossiers_s').width()-8;
		$('#dossiers_s').height(d_h);
		$('#table').perfectScrollbar('destroy');
		$('#table').height(d_h).width(d_w).css('position', 'relative').css('overflow', 'hidden');	
		$('#table').perfectScrollbar({
		  wheelSpeed: 30,
		  wheelPropagation: true,
		  minScrollbarLength: 50
		});
		$('#table').perfectScrollbar('update');
	});
});

function updateProgress(evt) 
{
   if (evt.lengthComputable) 
   {  //evt.loaded the bytes browser receive
      //evt.total the total bytes seted by the header
      //
     var percentComplete = (evt.loaded / evt.total)*100;  
     $('#progressbar').html('uploading '+percentComplete+'% ...');
   } 
}
function dossier_calls(){
	
	
	var mySelected = $('#dossier_filter select[name="type"] option:selected').val();
	if (mySelected=='parlementaire_vragen') {
		$('#dossier_filter #nummer_PV').parent().css('display', 'inherit');
	} else {
		$('#dossier_filter #nummer_PV').parent().css('display', 'none');
	}
	$('#dossier_filter .form-item-hidden').css('display', 'none');
	$('#dossier_filter *.'+mySelected).css('display', 'inherit');
	$('.datepicker').datepicker({ dateFormat: 'dd-mm-yy' });
	$('#loading_div').fadeOut();
	$('#table tr td').each(function(index){
		if ($(this).hasClass('beantwoord')){
			$(this).parents('tr').css('background', 'rgb(192,239,201)');
		}
	})
}

function dossier_filter()
{
	var form_geg = $('#dossier_filter').serialize();
	var hrefl = "/secretariaat/index.php/ajax/dossiers/filter/";
	var mySelected = $('#dossier_filter select[name="type"] option:selected').val();
	$('#dossier_filter .form-item-hidden').css('display', 'none');
	$('#dossier_filter *.'+mySelected).css('display', 'inherit');
	$('#loading_div').css('visibility', 'visible').show();
	$.post(hrefl, form_geg, function(json){		
		$('#table').html(json.table);
		dossier_calls();
		$('#table').perfectScrollbar('destroy');
		var t_h = $('#top').height();
		var w_h = $(window).height();
		var d_h = (w_h - t_h)-30;
		var d_w = $('#dossiers_s').width();
		$('#dossiers_s').height(d_h);
		$('#table').height(d_h).width(d_w).css('position', 'relative').css('overflow', 'hidden');	
		$('#table').perfectScrollbar({
			  wheelSpeed: 30,
			  wheelPropagation: true,
			  minScrollbarLength: 50
			});
		$('#table').perfectScrollbar('update');
	});
}

function ajax_page(hrefl, dossiers_zoeken, verrekeningen, informatie, title, url)
{
	$('html').css('cursor', 'progress');
	$.get(hrefl, function(json){		
		if (json.isArray == false){
			var hrefl_go = hrefl.split('/index.php');
			$('html').css('cursor', 'auto');
			window.location = hrefl_go[0];
		} else{
			var hrefl_go = hrefl.split('ajax');
			var loc = hrefl_go[0];
			if (dossiers_zoeken){
				if (json.blocks == undefined)
				{					
					window.location = loc;
				} else {
					$('#dossiers_zoeken').hide().html(json.blocks).show();
				}				
			}
			if (verrekeningen){
				if (json.content == undefined && json.tabel == undefined)
				{					
					window.location = loc;
				} else if (json.content != undefined)
				{
					$('#verrekeningen').hide().html(json.content).show();
				} else if (json.tabel != undefined)
				{
					$('#register_form').html(json.tabel)
				}				
			}
			if (informatie){
				if (json.information == undefined)
				{					
					window.location = loc;
				} else {
					$('#dossiers_informatie').hide().html(json.information).show();
				}				
			} if (bewerken) {
				
			}
			if (json.message == undefined){
				$('#message').hide();				
			} else {
				$('#message').html(json.message);
			}
			verrekening_calls();
			$('html').css('cursor', 'auto');
			history.pushState(json, title, url);
		}		
	})	
}


