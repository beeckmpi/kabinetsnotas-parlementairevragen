<div style="position: relative; height: 50px;">
	<strong>Richtlijnen:</strong>
	<ul style="position:absolute; top: -15px; left: 140px;border: 1px solid #888;font-size: small; list-style: &#x2192;">
		<li>CSV bestand verplicht.</lI>
		<li>Geen titels in de eerste rij.</li>
	</ul> 
</div>

<?php
	echo form_open_multipart('beheren/beheren/upload/dossiers', 'id="upload_dossiers_csv"');
	$div_open_item = '<div class="form-item">';
	$div_close = '</div>';
?>

		<ul class="upload_info">
			
			<li>De volgorde <strong>moet</strong> deze zijn: dossier_naam, bestek, aannemer, leidend ambtenaar, dossierbeheerder, extra info.</li>
		</ul> 

<?
	echo $div_open_item;
	echo form_label('Dossiers Importeren', 'import_dossiers');
	$data = array(
		'name'        => 'import_dossiers',
	    'id'		  => 'import_dossiers',
	    'placeholder' => 'CSV met dossiers kiezen',
	    'class'		  => 'file_upload'
	);
	echo form_upload($data);
	echo $div_close;
	echo $div_open_item;
	echo form_label(' ', 'dossiers_opladen');	
	echo form_submit('dossiers_opladen', 'CSV met dossiers uploaden');
	echo $div_close;
	echo '<div id="upload_dossiers_info" class="info">&nbsp;</div>';
	echo form_close();
	echo '<br />';
	echo form_open_multipart('beheren/beheren/upload/verrekeningen', 'id="upload_verrekeningen_csv"');
	?>

		<div class="upload_info">
			De volgorde <strong>moet</strong> deze zijn: nummer, ten laste van, omschrijving, meer, min, btw (0 voor nee, 1 voor ja), termijnverlenging, eindverrekening, wijzigingsbevel, 
			opmerkingen, ontvangst verrekeningsvoorstel, ATO vereist (0 voor nee, 1 voor ja), ATO verzonden, ATO ontvangen, ATO opmerkingen, aan aannemer, terug van aannemer, opmerkingen aannemer
			IF vereist, IF opmerkingen, Medefinancier (Nihil indien Stafdienst), Stafdienst (Nihil indien Medefinancier), Goedkeurder, ambtshalve goedkeuring (0 voor nee, 1 voor ja),
			Goedkeuring d.d., goedkeuring aannemer, datum vastlegging, bedrag vastlegging
		</div> 

<?
	echo $div_open_item;
	echo form_label('Verrekeningen Importeren', 'import_verrekeningen');
	$data = array(
		'name'        => 'import_verrekeningen',
	    'id'		  => 'import_verrekeningen',
	    'placeholder' => 'CSV met verrekeningen kiezen',
	);
	echo form_upload($data);
	echo $div_close;
	echo $div_open_item;
	echo form_label(' ', 'verrekeningen_opladen');	
	echo form_submit('verrekeningen_opladen', 'CSV met verrekeningen uploaden');
	echo $div_close;
	echo '<div id="upload_verrekeningen_info" class="info">&nbsp;</div>';
	echo form_close();
	echo '<br /><br />';
	echo form_open_multipart('beheren/beheren/upload/schuldvorderingen', 'id="upload_schuldvorderingen_csv"');
	echo $div_open_item;
	echo form_label('Schuldvorderingen Importeren', 'import_schuldvorderingen');
	$data = array(
		'name'        => 'import_schuldvorderingen',
	    'id'		  => 'import_schuldvorderingen',
	    'placeholder' => 'CSV met schuldvorderingen kiezen',
	);
	echo form_upload($data);
	echo $div_close;	
	echo $div_open_item;
	echo form_submit('schuldvorderingen', 'CSV met schuldvorderingen uploaden');
	echo $div_close;
	echo '<div id="upload_schuldvorderingen_info" class="info">&nbsp;</div><br />';
	echo form_close();
?>