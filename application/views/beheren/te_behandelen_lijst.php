<?php
echo '<div style="position:absolute;right:10px; z-index: 100"><div class="form-item-inline">Actief: <br />'.form_dropdown('Activatie', array('ja' => 'ja', 'nee' => 'nee', 'beide' => 'beide'), 'ja', 'id="activatie" data-url="'.site_url('beheren/beheren/te_behandelen_door').'"').'</div></div>';
echo form_open('ajax/beheren/te_behandelen/add', 'id="te_behandelen_formulier" name="te_behandelen_formulier" style="position: relative"');
	$div_open_item = '<div class="form-item">';
	$div_close = '</div>';
	echo $div_open_item;
			echo 'Naam sectie/personeelslid<br />';
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
			echo 'Email adres(sen)<br />';
			$data = array(
			              'name'        => 'email',
			              'id'			=> 'email',
			              'placeholder' => 'email adres(sen)',
			              'maxlength'   => '100',
			              'size'        => '60'
			            );		
			echo form_input($data);		
		echo $div_close;
		echo $div_open_item;	
			echo form_submit(array('id' => 'toevoegen_te_behandelen', 'class' => '', 'value' => 'Toevoegen', 'name' => 'registersubmit'));		
		echo $div_close;
echo form_close();

?>
<div style="margin: 15px 10px; clear:both" id="te_behandelen_lijst">
<?php 
	if (isset($te_behandelen)){		
		echo $te_behandelen;
	} else {
		echo 'Voeg mensen toe om een lijst te krijgen.';
	}
?>
</div>
