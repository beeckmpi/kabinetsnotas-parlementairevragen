<?php if (isset($edit)){ ?>
	<div style="font-weight: bold; font-size: 15px;">Delegatie bewerken</div>
<?php } else { ?>
	<div style="font-weight: bold; font-size: 15px;">Delegatie ambtenaar toevoegen</div>
<?php
	$edit = false;
	}
		echo form_open('ajax/beheren/save/delegatie/'.$edit);
		echo validation_errors();
		
		$div_open_item = '<div class="form-item">';
		$div_close = '</div>';
		
		echo $div_open_item;
		echo form_label('Delegatie', 'delegatie');
		$data = array(
		              'name'        => 'delegatie',
		              'id'			=> 'delegatie',
		              'placeholder' => 'Delegatie',
		              'maxlength'   => '100',
		              'size'        => '45',
		            );
		if (isset($la[0]['voornaam'])){
			 $data['value'] = $la[0]['voornaam'];
		}
		echo form_input($data).'<br />';
		echo $div_close;
		
		echo $div_open_item;	
		if (!$edit){
			echo form_submit(array('id' => 'registersubmit', 'class' => 'submit ajax', 'value' => 'Delegatie toevoegen', 'name' => 'registersubmit'));
		} else {
			echo form_submit(array('id' => 'registersubmit', 'class' => 'submit ajax', 'value' => 'Delegatie opslaan', 'name' => 'registersubmit'));
		}
		echo $div_close;
		echo form_close();
?>