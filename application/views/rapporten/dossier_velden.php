<div id="dossier_opties" class="option_div">	
	<H3 style="margin-left: 150px">In te voegen velden Dossier</H3>
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
		echo form_label('Dossier', 'dossier');
		echo form_checkbox('dossier[dossier]', 'dossier', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Bestek', 'bestek');
		echo form_checkbox('dossier[bestek]', 'bestek', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Aannemer', 'aannemer');
		echo form_checkbox('dossier[aannemer]', 'aannemer', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Leidend ambtenaar', 'leidend_ambtenaar');
		echo form_checkbox('dossier[leidend_ambtenaar]', 'leidend_ambtenaar', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Dossierbeheerder', 'dossierbeheerder');
		echo form_checkbox('dossier[dossierbeheerder]', 'dossierbeheerder', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Omschrijving', 'extra_info');
		echo form_checkbox('dossier[extra_info]', 'extra_info', TRUE);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Totale som dossier', 'totaal');
		echo form_checkbox('dossier[totaal]', 'totaal', TRUE);
		echo $div_close;
	?>
</div>
	