<div id="schuldvorderingen_opties" class="option_div">		
	<H3 style="margin-left: 150px">In te voegen velden Schuldvorderingen</H3>
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
		echo form_label('Nummer', 'nummer');
		echo form_checkbox('schuldvorderingen[nummer]', 'nummer', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Ontvangst Schuldv.', 'ontvangst_schuldvordering');
		echo form_checkbox('schuldvorderingen[ontvangst_schuldvordering]', 'ontvangst_schuldvordering', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Bedrag', 'bedrag');
		echo form_checkbox('schuldvorderingen[bedrag]', 'bedrag', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Factuurdatum', 'factuurdatum');
		echo form_checkbox('schuldvorderingen[factuurdatum]', 'factuurdatum', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Factuur bedrag', 'factuur_bedrag');
		echo form_checkbox('schuldvorderingen[factuur_bedrag]', 'factuur_bedrag', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Opmerkingen', 'opmerkingen');
		echo form_checkbox('schuldvorderingen[opmerkingen]', 'opmerkingen', TRUE);
		echo $div_close;
	?>
</div>
	