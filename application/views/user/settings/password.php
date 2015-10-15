
<h3>Wachtwoord wijzigen voor <?php echo $profile_data[0]['username'] ?></h3>

<?php
$div_open_item = '<div class="form-item">';
$div_close = '</div>';
echo form_open('user/settings/password/'.$profile_data[0]['username']);
if(user_access(array('Administrators'))){
	echo $div_open_item;
	echo form_button(array('id' => 'gen_password', 'value' => 'genereer wachtwoord', 'content' => 'Genereer Wachtwoord', 'class' => 'btn btn-default btn-sm', 'style' => 'display:inline-block; margin-left: 10px'));
	echo '<div id=generated_password style="display:inline-block; margin-left: 11px; padding-top:4px; font-size: 16px; font-weight: bold; vertical-align:middle"></div>';
	echo $div_close;
}
if (!user_access(array('Administrators'))){
	 
	echo $div_open_item;
	echo form_label('Oud wachtwoord', 'old_password');
	echo form_password(array('name' => 'old_password','placeholder' => 'Oud wachtwoord', 'size' => '45')).'<br />';
	echo "<div class='form_helper'>het wachtwoord dient minstens uit 8 tekens te bestaan</div>";
	echo $div_close;
}	

echo $div_open_item;
echo form_label('Nieuw wachtwoord', 'password');
echo form_password(array('name' => 'new_password','placeholder' => 'Nieuw wachtwoord', 'size' => '45')).'<br />';
echo $div_close.$div_open_item;
echo form_label('Herhaal het nieuwe wachtwoord', 'repeat_password');
echo form_password(array('name' => 'repeated_new_password','placeholder' => 'Herhaal het nieuwe wachtwoord', 'size' => '45')).'<br />';
echo $div_close.$div_open_item;
echo form_submit(array('id' => 'passwordsubmit', 'class' => 'btn btn-primary btn-sm', 'value' => 'Opslaan', 'name' => 'passwordsubmit'));
echo $div_close;
echo form_close();
?>
