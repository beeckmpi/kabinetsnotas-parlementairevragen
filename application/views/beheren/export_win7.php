<div style="position: relative; height: 50px;">
	<ul style="position:absolute; top: -15px; left: 140px;border: 1px solid #888;font-size: small; list-style: &#x2192;">
		
	</ul> 
</div>

<?php
	echo form_open_multipart('', 'id=""');
	$div_open_item = '<div class="form-item">';
	$div_close = '</div>';
?>
		<ul class="upload_info">
			<li>Exporteer 1 grote CSV met alles in.</li>
		</ul> 

<?

	echo $div_open_item;
	echo form_label(' ', 'provincies_opladen');	
	echo form_button('volledig_overzicht_exporteren', 'Volledig overzicht exporteren');
	echo $div_close;
	echo '<div id="upload_dossiers_info" class="info">&nbsp;</div>';
	echo form_close();
	
?>