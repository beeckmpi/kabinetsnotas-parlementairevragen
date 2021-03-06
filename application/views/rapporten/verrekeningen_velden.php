<div id="verrekeningen_opties" class="option_div">	
	<H3 style="margin-left: 150px">In te voegen velden Verrekeningen</H3>
	<?php
		$data = array(
		    'name' => 'select_all',
		    'id' => 'select_all_dossier',
		    'class' => 'select-all',
		    'content' => 'Selectie omdraaien'
		);
	?>
	<div style="margin-left: 150px"><?php echo form_button($data)?></div>
	<?php
		$div_open_item = '<div class="form-item check-items">';
		$div_close = '</div>';	
		echo $div_open_item;
		echo form_label('Type', 'type');
		echo form_checkbox('verrekeningen[type]', 'type', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Nummer', 'nummer');
		echo form_checkbox('verrekeningen[nummer]', 'nummer', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Investeerder', 'investeerder');
		echo form_checkbox('verrekeningen[investeerder]', 'investeerder', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Omschrijving', 'omschrijving');
		echo form_checkbox('verrekeningen[omschrijving]', 'omschrijving', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Meer', 'meer');
		echo form_checkbox('verrekeningen[meer]', 'meer', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Min', 'min');
		echo form_checkbox('verrekeningen[min]', 'min', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Netto', 'netto');
		echo form_checkbox('verrekeningen[netto]', 'netto', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('BTW', 'btw');
		echo form_checkbox('verrekeningen[btw]', 'BTW', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Totaal (inc BTW)', 'totaal');
		echo form_checkbox('verrekeningen[totaal]', 'totaal', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Eindverrekening', 'eindverrekening');
		echo form_checkbox('verrekeningen[eindverrekening]', 'eindverrekening', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Termijnverlenging', 'termijnverlenging');
		echo form_checkbox('verrekeningen[termijnverlenging]', 'termijnverlenging', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Wijzigingsbevel', 'wijzigingsbevel');
		echo form_checkbox('verrekeningen[wijzigingsbevel]', 'wijzigingsbevel', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Opmerkingen', 'opmerkingen_wijzigingsbevel');
		echo form_checkbox('verrekeningen[opmerkingen_wijzigingsbevel]', 'Opmerkingen', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Verrekeningsvoorstel', 'ontvangst_verrekeningsvoorstel');
		echo form_checkbox('verrekeningen[ontvangst_verrekeningsvoorstel]', 'ontvangst_verrekeningsvoorstel', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('ATO vereist', 'vereist');
		echo form_checkbox('verrekeningen[vereist]', 'vereist', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('ATO verzonden', 'verzonden');
		echo form_checkbox('verrekeningen[ato_verzonden]', 'verzonden', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('ATO ontvangen', 'ontvangen');
		echo form_checkbox('verrekeningen[ato_ontvangen]', 'ontvangen', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('ATO opmerkingen', 'opmerkingen_ato');
		echo form_checkbox('verrekeningen[ato_opmerkingen]', 'opmerkingen_ato', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Aan aannemer', 'aan_aannemer');
		echo form_checkbox('verrekeningen[aan_aannemer]', 'aan_aannemer', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Terug van aannemer', 'terug_van_aannemer');
		echo form_checkbox('verrekeningen[terug_van_aannemer]', 'terug_van_aannemer', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Opmerkingen aannemer', 'opmerkingen_aannemer');
		echo form_checkbox('verrekeningen[opmerkingen_aannemer]', 'opmerkingen_aannemer', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('IF vereist', 'if_vereist');
		echo form_checkbox('verrekeningen[if_vereist]', 'if_vereist', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('IF opmerkingen', 'if_opmerkingen');
		echo form_checkbox('verrekeningen[if_opmerkingen]', 'if_opmerkingen', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Medefinancier', 'naar_medefinancier');
		echo form_checkbox('verrekeningen[naar_medefinancier]', 'naar_medefinancier', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Opmerking medefinancier', 'opmerking_medefinancier');
		echo form_checkbox('verrekeningen[opmerking_medefinancier]', 'opmerking_medefinancier', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Goedkeuring medefinancier', 'goedkeuring_medefinancier');
		echo form_checkbox('verrekeningen[goedkeuring_medefinancier]', 'goedkeuring_medefinancier', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Stafdienst', 'aan_stafdienst"');
		echo form_checkbox('verrekeningen[aan_stafdienst]', 'aan_stafdienst"', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Leidend ambtenaar', 'goedkeurder');
		echo form_checkbox('verrekeningen[goedkeurder]', 'goedkeurder', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Ambtshalve goedkeuring', 'ambtshalve_goedkeuring');
		echo form_checkbox('verrekeningen[ambtshalve_goedkeuring]', 'ambtshalve_goedkeuring', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Goedkeuring d.d.', 'goedkeuring_dd');
		echo form_checkbox('verrekeningen[goedkeuring_dd]', 'goedkeuring_dd', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Goedkeuring aannemer', 'goedkeuring_aannemer');
		echo form_checkbox('verrekeningen[goedkeuring_aannemer]', 'goedkeuring_aannemer', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Datum vastlegging', 'datum_vastlegging');
		echo form_checkbox('verrekeningen[datum_vastlegging]', 'datum_vastlegging', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Bedrag vastlegging', 'bedrag_vastlegging');
		echo form_checkbox('verrekeningen[bedrag_vastlegging]', 'bedrag_vastlegging', TRUE);
		echo $div_close;
	?>
</div>