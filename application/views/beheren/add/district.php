<?php if (isset($edit)){ ?>
	<div style="font-weight: bold; font-size: 15px;">District bewerken</div>
<?php } else { ?>
	<div style="font-weight: bold; font-size: 15px;">District toevoegen</div>
<?php
	$edit = false;
	}
		echo form_open('ajax/beheren/save/district/'.$edit);
		echo validation_errors();
		
		$div_open_item = '<div class="form-item">';
		$div_close = '</div>';
		if (isset($districtgegevens[0]['_id'])){
			echo form_hidden('_id', $districtgegevens[0]['_id']);
		}
		echo $div_open_item;		
		echo form_label('Districtcode', 'code');
		$data = array(
		              'name'        => 'code',
		              'id'			=> 'code',
		              'placeholder' => 'Districtscode',
		              'maxlength'   => '100',
		              'size'        => '15',
		              'required'	=> 'true'
		            );
		if (isset($districtgegevens[0]['code'])){
			 $data['value'] = $districtgegevens[0]['code'];
		}
		echo form_input($data).'<br />';
		echo $div_close;
		echo $div_open_item;
		echo form_label('Districtnaam', 'district');
		$data = array(
		              'name'        => 'district',
		              'id'			=> 'district',
		              'placeholder' => 'Districtsnaam',
		              'maxlength'   => '100',
		              'size'        => '45',
		            );
		if (isset($districtgegevens[0]['district'])){
			 $data['value'] = $districtgegevens[0]['district'];
		}
		echo form_input($data).'<br />';
		echo $div_close;
		echo $div_open_item;	
		echo form_label('Provincie', 'country');
		
		$provincies[''] = '-- Selecteer --';
		asort($provincies);
		if (isset($districtgegevens[0]['provincie'])){
			$default = $districtgegevens[0]['provincie'];
		} else {
			$default = 'SELECT';
		}
		echo form_dropdown('provincie', $provincies, $default);
		echo $div_close;
		echo $div_open_item;	
		if (!$edit){
			echo form_submit(array('id' => 'registersubmit', 'class' => 'submit ajax', 'value' => 'District toevoegen', 'name' => 'registersubmit'));
		} else {
			echo form_submit(array('id' => 'registersubmit', 'class' => 'submit ajax', 'value' => 'District opslaan', 'name' => 'registersubmit'));
		}
		echo $div_close;
		echo form_close();
?>