<?php if (isset($edit)){ ?>
	<div style="font-weight: bold; font-size: 15px;">Leidend ambtenaar bewerken</div>
<?php } else { ?>
	<div style="font-weight: bold; font-size: 15px;">Leidend ambtenaar toevoegen</div>
<?php
	$edit = false;
	}
		echo form_open('ajax/beheren/save/la/'.$edit);
		echo validation_errors();
		
		$div_open_item = '<div class="form-item">';
		$div_close = '</div>';
		echo $div_open_item;
		echo form_label('Titel', 'titel');
		$data = array(
		              'name'        => 'titel',
		              'id'			=> 'titel',
		              'placeholder' => 'Titel: ir. of Ing.',
		              'maxlength'   => '100',
		              'size'        => '20',
		              'required'	=> 'true'
		            );
		if (isset($la[0]['code'])){
			 $data['value'] = $la[0]['titel'];
		}
		echo form_input($data).'<br />';
		echo $div_close;
		echo $div_open_item;
		echo form_label('Voornaam', 'voornaam');
		$data = array(
		              'name'        => 'voornaam',
		              'id'			=> 'voornaam',
		              'placeholder' => 'Voornaam',
		              'maxlength'   => '100',
		              'size'        => '45',
		            );
		if (isset($la[0]['voornaam'])){
			 $data['value'] = $la[0]['voornaam'];
		}
		echo form_input($data).'<br />';
		echo $div_close;
		echo $div_open_item;
		echo form_label('Naam', 'naam');
		$data = array(
		              'name'        => 'naam',
		              'id'			=> 'naam',
		              'placeholder' => 'Naam',
		              'maxlength'   => '100',
		              'size'        => '45',
		            );
		if (isset($la[0]['naam'])){
			 $data['value'] = $la[0]['naam'];
		}
		echo form_input($data).'<br />';
		echo $div_close;
		echo $div_open_item;	
		if (!$edit){
			echo form_submit(array('id' => 'registersubmit', 'class' => 'submit ajax', 'value' => 'Leidend ambtenaar toevoegen', 'name' => 'registersubmit'));
		} else {
			echo form_submit(array('id' => 'registersubmit', 'class' => 'submit ajax', 'value' => 'Leidend ambtenaar opslaan', 'name' => 'registersubmit'));
		}
		echo $div_close;
		echo form_close();
?>