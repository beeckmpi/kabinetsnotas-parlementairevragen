<h2 style="margin-left: 40px;">Gebruiker Toevoegen</h2>
<div id="register_form">
	<?php
		echo form_open('user/register/signup');
        if(validation_errors() != '') {
    		echo '<div class="error" style="color: red; font-weight: bolder; margin-left: 40px; border-radius: 5px;  border: 1px solid #555; background: #eee; padding: 10px 5px 1px 10px">'.validation_errors().'</div>';
        }		
		$div_open_item = '<div class="form-item">';
		$div_close = '</div>';
		echo $div_open_item;
		echo form_label('Gebruikersnaam', 'reg_username');
		$data = array(
		              'name'        => 'reg_username',
		              'id'			=> 'reg_username',
		              'placeholder' => 'Gebruikersnaam',
		              'maxlength'   => '100',
		              'size'        => '45',
		            );
		
		echo form_input($data).'<br />';
        echo '<div style="margin-left:160px; width: 400px; font-size: 9px">De gebruikersnaam mag GEEN spaties bevatten. Deze bestaat normaal gezien uit de eerste 6 karakters van de achternaam en de eerste 2 karakters van de voornaam, dus bijvoorbeeld bij Beeckmans Pieter: beeckm (eerste 6 achternaam) en pi (eerste 2 voornaam): beeckmpi</div>';
		echo $div_close;
		echo $div_open_item;	
		echo form_label('Email-adres', 'email');
		$data = array(
		              'name'        => 'email',
		              'id'          => 'email',
		              'placeholder' => 'Email-adres',
		              'maxlength'   => '250',
		              'type'		=> 'email',
		              'size'        => '45',
		            );
		
		echo form_input($data).'<br />';
		echo $div_close;
		echo $div_open_item;	
		echo form_label('Voornaam', 'reg_first_name');
		$data = array(
		              'name'        => 'reg_first_name',
		              'id'			=> 'reg_first_name',
		              'placeholder' => 'Voornaam',
		              'maxlength'   => '100',
		              'size'        => '45',
		            );
		
		echo form_input($data);		
		echo $div_close;
		echo $div_open_item;	
		echo form_label('Initielen', 'reg_initials');
		$data = array(
		              'name'        => 'reg_initials',
		              'id'			=> 'reg_initials',
		              'placeholder' => '(optioneel)',
		              'maxlength'   => '100',
		              'size'        => '45',
		            );
		
		echo form_input($data);	
		echo $div_close;
		echo $div_open_item;	
		echo form_label('Naam', 'reg_name');
		$data = array(
		              'name'        => 'reg_name',
		              'id'			=> 'reg_name',
		              'placeholder' => 'Naam',
		              'maxlength'   => '100',
		              'size'        => '45',
		            );
		
		echo form_input($data);
		echo $div_close;
		echo $div_open_item;
		echo form_label('Afdeling', 'provincie');		
		if (!user_access(array('Administrators', 'Stafdienst'))){
			$disabled = 'disabled style="background-color: #ccc"';
		} else {
			$disabled = "";
		}
		$provincies[''] = '-- Selecteer --';
		asort($provincies);
		echo form_dropdown('provincie', $provincies, $user_data['location']['provincie'], 'id="provincies"'.$disabled);
		echo $div_close;
		echo $div_open_item;
		echo form_label('District', 'district');
	
		array_unshift($districten, array('value' => '', 'district' => '-- Selecteer --','class' => 'empty'));
		asort($districten[0]);
	/*	echo form_dropdown('provincie', $districten, $city);*/
		echo '<select name="district" id="districten">';
		if (!isset($selected)){
			$selected = '';
		}
		foreach ($districten as $key => $value){
			echo '<option value="'.$districten[$key]['value'].'" class="'.$districten[$key]['class'].'" '.$selected.'>'.$districten[$key]['district'].'</option>';
		}
		echo '</select>';
		echo $div_close;
		if (!user_access(array('Administrators', 'Stafdienst'))){
			$disabled = 'disabled style="background-color: #ccc"';
			$rol = 'Dossierbeheerder';
		} else {
			$disabled = '';
			$rol = '';
		}
		echo $div_open_item;
			echo form_label('Rol', 'rol');
			$rollen[''] = '-- Selecteer --';
			asort($rollen);
			echo form_dropdown('rol', $rollen, $rol, 'id="rol"'.$disabled);
		echo $div_close;
		
		echo $div_open_item;	
		echo form_submit(array('id' => 'registersubmit', 'class' => 'submit btn btn-success btn-sm', 'value' => 'Gebruiker toevoegen', 'name' => 'registersubmit'));
		echo $div_close;
		echo form_close();
	?>
</div>