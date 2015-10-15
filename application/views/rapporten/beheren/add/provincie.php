<div style="font-weight: bold; font-size: 15px;">Provincie toevoegen</div>
<?php
		echo form_open('ajax/save/provincie');
		echo validation_errors();
		echo form_label('Provincienaam', 'provincie');
		$div_open_item = '<div class="form-item">';
		$div_close = '</div>';
		echo $div_open_item;
		$data = array(
		              'name'        => 'provincie',
		              'id'			=> 'provincie',
		              'placeholder' => 'Provincienaam',
		              'maxlength'   => '100',
		              'size'        => '45',
		            );
		
		echo form_input($data).'<br />';
		echo $div_close;
		echo $div_open_item;	
		echo form_submit(array('id' => 'registersubmit', 'class' => 'submit ajax', 'value' => 'Provincie toevoegen', 'name' => 'registersubmit'));
		echo $div_close;
		echo form_close();
?>