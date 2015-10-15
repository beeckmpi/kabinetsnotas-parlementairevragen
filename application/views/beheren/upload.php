<div style="position: relative; height: 50px;">
	<strong>Richtlijnen:</strong>
	<ul style="position:absolute; top: -15px; left: 140px;border: 1px solid #888;font-size: small; list-style: &#x2192;">
		<li>CSV bestand verplicht.</lI>
		<li>Geen titels in de eerste rij.</li>
	</ul> 
</div>

<?php
	echo form_open_multipart('beheren/beheren/upload/oorzaken', 'id="upload_csv"');
	$div_open_item = '<div class="form-item">';
	$div_close = '</div>';
?>

		<ul class="upload_info">
			
			<li>De volgorde <strong>moet</strong> deze zijn: naam, parent.</li>
		</ul> 

<?php
	echo form_open_multipart('beheren/beheren/upload/', 'id="upload_csv"');
	echo '<div class="form-item">';
		echo form_label('Import type', 'type_upload');
		$type = array(
			' ' => '--selecteer--',
			'dossiers' => 'Dossiers',
			'oorzaken' => 'Oorzaken',
			'suboorzaken' => 'Suboorzaken', 
			'suboorzaken2' => 'Suboorzaken2',
			'wegen' => 'Wegen'
		);
		asort($type);
	echo form_dropdown('type_upload', $type);
	echo $div_close;
	echo $div_open_item;
	echo form_label('Importeren bestand (*.csv)', 'importeren');
	$data = array(
		'name'        => 'importeren',
	    'id'		  => 'importeren',
	    'placeholder' => 'importeren',
	    'class'		  => 'file_upload'
	);
	echo form_upload($data);
	echo $div_close;
	echo $div_open_item;
	echo form_label(' ', 'importeren');	
	echo form_submit('importeren', 'importeren');
	echo $div_close;
	echo '<div id="upload_info" class="info">&nbsp;</div>';
	echo form_close();
	echo '<br />';

?>