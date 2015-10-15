<?php
echo form_open('user/login');
$data = array(
              'name'        => 'username',
              'id'          => 'username',
              'placeholder' => 'Gebruikersnaam',
              'maxlength'   => '100',
              'size'        => '20',
              'value' 		=> $_POST['username'],
            );

echo form_input($data).'<br />';
echo form_password(array('name' => 'password','placeholder' => 'Wachtwoord')).'<br />';
echo form_checkbox(array('name' => 'remember_me', 'value' => 1));
echo form_label('Onthoud me', 'remember_me').'<br />';
echo '<a href="'.base_url("user/forgot_password").'" class="forgot">Wachtwoord vergeten?</a>';
echo form_submit(array('value' => 'Sign In')).'<br />';
echo form_close();
?>