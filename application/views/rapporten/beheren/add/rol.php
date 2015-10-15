<div style="font-weight: bold; font-size: 15px;">Rol toevoegen</div>
<?php
		echo form_open('ajax/beheren/save/rol');
		echo validation_errors();
		echo form_label('Rol', 'rol');
		$div_open_item = '<div class="form-item">';
		$div_close = '</div>';
		echo $div_open_item;
		$data = array(
		              'name'        => 'rol',
		              'id'			=> 'rol',
		              'placeholder' => 'Rol',
		              'maxlength'   => '100',
		              'size'        => '45',
		            );
		
		echo form_input($data).'<br />';
		echo $div_close;
		echo $div_open_item;	
		echo form_submit(array('id' => 'registersubmit', 'class' => 'submit ajax', 'value' => 'Rol toevoegen', 'name' => 'registersubmit'));
		echo $div_close;
		echo form_close();
?>