<?php if (!user_access(array('Administrators'))  && $profile_data[0]['user_role'][0] == 'Administrators'){
	echo 'U heeft niet de juiste rechten om deze persoon te bewerken';
} else {
?>
<div style="float: right;" class="menu_wrapper">
	<div class="edit-btn"><img src="<?php echo base_url()?>media/images/edit.png"/></div>
	<ul class="edit-menu">
		<li><a href="<?php echo site_url()?>user/profile/<?php echo $profile_data[0]['username']?>">Profiel Bekijken</a></li>	
		<?php if (user_access(array('Administrators', 'Stafdienst', 'Secretariaat'))){
            echo '<li>'.anchor('ajax/user/resetPassword/'.$user_data[$key]['username'], 'Reset wachtwoord', array('class' => 'resetPassword')).'</li>';
        } ?>
        <?php if (user_access(array('Administrators'))){ ?>
            <li><a href="<?php echo site_url()?>user/remove_user/<?php echo $user_data[0]['_id']?>">Gebruiker Verwijderen</a></li>
        <?php } ?>
	</ul>
	<!--<input type="button" value="Bewerken" id="profile_edit" onclick="edit_profile('<?php echo $user_data[0]['username']?>')"/>
	<input type="button" value="Rollen aanpassen" id="profile_edit" onclick="edit_profile('<?php echo $user_data[0]['username']?>')"/>-->
	</div>
	<?php
		echo form_open('user/edit/'.$profile_data[0]['username']);
		echo validation_errors();
		$div_open_item = '<div class="form-item">';
		$div_close = '</div>';
		echo '<div id="account_form" class="form">';
		echo $div_open_item;		
		echo form_label('Gebruikersnaam', 'reg_username');
		$data = array(
		              'name'        => 'reg_username',
		              'id'			=> 'reg_username',
		              'placeholder' => 'Gebruikersnaam',
		              'maxlength'   => '100',
		              'size'        => '45',
		              'value'		=> $profile_data[0]['username']
		            );
		
		echo form_input($data);
		echo $div_close;
		echo $div_open_item;	
		echo form_label('Voornaam', 'reg_first_name');
		$data = array(
		              'name'        => 'reg_first_name',
		              'id'			=> 'reg_first_name',
		              'placeholder' => 'Voornaam',
		              'maxlength'   => '100',
		              'size'        => '45',
		              'value'		=> $profile_data[0]['first_name']
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
		              'value'		=> $profile_data[0]['initials']
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
		              'value'		=> $profile_data[0]['name']
		            );
		
		echo form_input($data);
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
		              'value' 		=> $profile_data[0]['email']
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
		echo form_dropdown('provincie', $provincies, $profile_data[0]['location']['provincie'], 'id="provincies"'.$disabled);
		echo $div_close;
		echo $div_open_item;
		echo form_label('District', 'district');
		if (isset($profile_data[0]['location']['district'])) {
			$city = $profile_data[0]['location']['district'];
		} else {
			$city = '';
		}
		array_unshift($districten, array('value' => '', 'district' => '-- Selecteer --','class' => 'empty'));
		asort($districten[0]);
	/*	echo form_dropdown('provincie', $districten, $city);*/
		echo '<select name="district" id="districten">';
		foreach ($districten as $key => $value){
			if ($city == $districten[$key]['value']){
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			echo '<option value="'.$districten[$key]['value'].'" class="'.$districten[$key]['class'].'" '.$selected.'>'.$districten[$key]['district'].'</option>';
		}
		echo '</select>';
		echo $div_close;
		if (!user_access(array('Administrators', 'Stafdienst'))){
			$disabled = 'disabled style="background-color: #ccc"';
			$rol = $profile_data[0]['user_role'][0];
		} else {
			$disabled = '';
			$rol = $profile_data[0]['user_role'][0];
		}
		echo $div_open_item;
			echo form_label('Rol', 'rol');
			$rollen[''] = '-- Selecteer --';
			asort($rollen);
			echo form_dropdown('rol', $rollen, $rol, 'id="rol"'.$disabled);
		echo $div_close;
		echo form_hidden('afkorting', $profile_data[0]['location']['afkorting']);
		echo form_submit(array('id' => 'registersubmit', 'class' => 'submit btn btn-success btn-sm', 'value' => 'Gegevens Opslaan', 'name' => 'registersubmit'));
		echo form_close();
		echo $div_close;
	}
	?>
