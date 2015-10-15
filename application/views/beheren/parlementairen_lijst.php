<?php
echo '<div style="position:absolute;right:10px; z-index: 100"><div class="form-item-inline">Actief: <br />'.form_dropdown('Activatie', array('ja' => 'ja', 'nee' => 'nee', 'beide' => 'beide'), 'ja', 'id="activatie" data-url="'.site_url('beheren/beheren/parlementairen_door').'"').'</div></div>';
echo form_open('ajax/beheren/parlementairen/add', 'id="parlementairen_formulier" name="parlementairen_formulier" style="position: relative"');
	$div_open_item = '<div class="form-item-inline">';
	$div_close = '</div>';
	echo $div_open_item;
			echo 'Naam parlementslid<br />';
			$data = array(
			              'name'        => 'naam',
			              'id'			=> 'naam',
			              'placeholder' => 'Naam',
			              'maxlength'   => '100',
			              'size'        => '60'
			            );		
			echo form_input($data);		
		echo $div_close;
		echo $div_open_item;	
			echo form_submit(array('id' => 'toevoegen_parlementairen', 'class' => '', 'value' => 'Toevoegen', 'name' => 'registersubmit'));		
		echo $div_close;
echo form_close();

?>
<div style="margin: 15px 10px; clear:both" id="parlementairen_lijst">
<?php 
	if (isset($parlementairen)){		
		echo $parlementairen;
	} else {
		echo 'Voeg mensen toe om een lijst te krijgen.';
	}
?>
</div>
