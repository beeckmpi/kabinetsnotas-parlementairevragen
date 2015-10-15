<?php
	echo validation_errors();
	
	
	echo form_open('ajax/rapporten/rapport', 'id="rapporten_toevoegen"');
	/*if (isset($schuldvorderingen_data)) {
		$nummer = $schuldvorderingen_data[0]['nummer'];
		$ontvangst_schuldvordering = $schuldvorderingen_data[0]['ontvangst_schuldvordering'];
		$opmerkingen = htmlspecialchars_decode($schuldvorderingen_data[0]['opmerkingen'], ENT_QUOTES);
		$bedrag = $schuldvorderingen_data[0]['bedrag'];
		$factuurdatum = $schuldvorderingen_data[0]['factuurdatum'];
		$factuur_bedrag= $schuldvorderingen_data[0]['factuur_bedrag'];
		echo form_hidden(array('schuldvorderingen_id' => $schuldvorderingen_data[0]['_id']));
		$submit = 'Schuldvordering opslaan';
	} else {
		$nummer = $ontvangst_schuldvordering = $bedrag = $factuurdatum = $factuur_bedrag = $opmerkingen = '';
		$submit = 'Schuldvordering toevoegen';
		
	}*/
	$div_open_item = '<div class="form-item">';
	$div_close = '</div>';
	echo $div_open_item;
	echo form_label('Naam Rapport*', 'naam_rapport');
	$data = array(
	              'name'        => 'naam_rapport',
	              'id'			=> 'naam_rapport',
	              'placeholder' => 'Naam rapport',
	              'maxlength'   => '100',
	              'size'        => '51',
	              'required'	=> 'true',
	            );		
	echo form_input($data);		
	echo $div_close;
	echo $div_open_item;	
	echo form_label('Opmerkingen', 'opmerkingen');
	$data = array(
	              'name'        => 'opmerkingen',
	              'id'			=> 'opmerkingen',
	              'placeholder' => 'Opmerkingen',
	              'rows'		=> '4',
	              'cols'		=> '48',             
	            );	
	echo form_textarea($data);		
	echo $div_close;
	echo $div_open_item;
	echo form_label('Extensie Rapport*', 'extensie');
	$extensie = array(
		' ' => '--selecteer--',
		'CSV' => 'CSV', 
		'PDF' => 'PDF',
		
	);
	asort($extensie);
	echo form_dropdown('extensie', $extensie);
	echo $div_close;
	echo $div_open_item;
	echo form_label('Type Rapport*', 'type_rapport');
	$type = array(
		' ' => '--selecteer--',
		'naar_aannemer' => 'Verrekeningen naar de aannemer en nog niet terug', 
		'per_la' => 'Verrekeningen per Leidend ambtenaar',
		'per_db' => 'Verrekeningen per dossierbeheerder',
		'ao_goedgekeurde_dossiers' => 'Algemeen overzicht - goedgekeurde dossiers',
		'ao_wijzigingsbevelen' => 'Algemeen overzicht - wijzigingsbevelen',
		'ao_goedgekeurde_dossiers' => 'Algemeen overzicht - goedgekeurde dossiers',
		'verrekeningen_per_dossier_in_omloop' => 'Verrekeningen per dossier in omloop',
		'totaalbedragen_per_dossier' => 'Totaalbedragen per dossier',
	);
	asort($type);
	echo form_dropdown('type_rapport', $type);
	echo $div_close;
	echo '<div id="dossier_" style="display:inherit" class="form-item">';
	$years = array();
	for($i = date('Y'); $i>=2005; $i--){
		$years[$i] = $i;
	}
	echo form_label('Jaar', 'jaar');
	array_unshift($years, 'Alle Jaren');
	echo form_dropdown('jaar', $years, '', 'id="jaar" style="width: 292px"');
	echo $div_close;
	echo '<div id="dossier_" style="display:inherit" class="form-item">';
	echo form_label('Dossier', 'dossiernaam');
	array_unshift($dossier, 'Alle Dossiers');
	echo form_dropdown('dossiernaam', $dossier, '', 'id="dossier" style="width: 292px"');
	echo $div_close;
	echo '<div id="la_" style="display:none" class="form-item">';
	echo form_label('Leidend ambtenaar', 'la');
	array_unshift($la, 'Groepeer per leidend ambtenaar');
	echo form_dropdown('leidend_ambtenaar', $la, '', 'id="la" style="width: 292px"');
	echo $div_close;
	echo '<div id="db_" style="display:none" class="form-item">';
	echo form_label('Dossierbeheerder', 'dossierbeheerder');
	array_unshift($dossierbeheerders, 'Groepeer per dossierbeheerder');	
	array_push($dossierbeheerders, '++ toevoegen ++');	
	echo form_dropdown('dossierbeheerder', $dossierbeheerders, '', 'id="dossierbeheerder" style="width: 292px"');
	echo $div_close;
	echo '<div  class="form-item" id="ajax_load_form_items"></div>';
	echo $div_open_item;	
	echo form_submit(array('id' => 'registersubmit', 'class' => 'submit', 'value' => 'Rapport aanmaken', 'name' => 'registersubmit'));
	echo $div_close;
	
	echo form_close();
	echo $div_close;
?>