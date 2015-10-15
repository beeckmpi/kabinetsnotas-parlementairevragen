<div style="position: relative; height: 50px;">
	<strong>Richtlijnen:</strong>
	<ul style="position:absolute; top: -15px; left: 140px;border: 1px solid #888;font-size: small; list-style: &#x2192;">
		<li>CSV bestand verplicht.</lI>
		<li>Geen titels in de eerste rij.</li>
	</ul> 
</div>

<?php
	echo form_open_multipart('beheren/beheren/upload_win7/volledige_lijst', 'id="upload_overzicht_csv"');
	$div_open_item = '<div class="form-item">';
	$div_close = '</div>';
?>

		<ul class="upload_info">
			<li>De volgorde <strong>moet</strong> deze zijn: ...</li>
		</ul> 

<?
	echo $div_open_item;
	echo form_label('CMDB lijst Importeren', 'import_dossiers');
	$data = array(
		'name'        => 'import_provincies',
	    'id'		  => 'import_provincies',
	    'placeholder' => 'CSV met dossiers kiezen',
	    'class'		  => 'file_upload'
	);
	echo form_upload($data);
	echo $div_close;
	echo $div_open_item;
	echo form_label(' ', 'provincies_opladen');	
	echo form_submit('provincies_opladen', 'CSV met provincies uploaden');
	echo $div_close;
	echo '<div id="upload_dossiers_info" class="info">&nbsp;</div>';
	echo form_close();
	echo '<br />';
	echo form_open_multipart('beheren/beheren/upload_win7/computer_gegevens', 'id="upload_computer_gegevens_csv"');
	?>

		<div class="upload_info">
			De volgorde <strong>moet</strong> deze zijn: tag, geheugen, aankoopdatum, os
		</div> 

<?
	echo $div_open_item;
	echo form_label('Computer gegevens Importeren', 'import_computer_gegevens');
	$data = array(
		'name'        => 'import_computer_gegevens',
	    'id'		  => 'import_computer_gegevens',
	    'placeholder' => 'CSV met provincie kiezen',
	);
	echo form_upload($data);
	echo $div_close;
	echo $div_open_item;
	echo form_label(' ', 'computer_gegevens_opladen');	
	echo form_submit('computer_gegevens_opladen', 'CSV met computer gegevens uploaden');
	echo $div_close;
	echo '<div id="upload_computer_gegevens_info" class="info">&nbsp;</div>';
	echo form_close();
	echo '<br /><br />';
	
?>