<div style="margin-bottom: 8px;"><span style="font-weight: bold;">Account aanmaken
<?php
echo form_open('user/register');
echo validation_errors();
$data = array(
              'name'        => 'reg_username',
              'id'			=> 'reg_username',
              'placeholder' => 'Gebruikersnaam',
              'maxlength'   => '100',
              'size'        => '45',
            );

echo form_input($data).'<br />';
$data = array(
              'name'        => 'email',
              'id'          => 'email',
              'placeholder' => 'Email-adres',
              'maxlength'   => '250',
              'type'		=> 'email',
              'size'        => '45',
            );

echo form_input($data).'<br />';
echo form_password(array('name' => 'password','placeholder' => 'Wachtwoord', 'size' => '45')).'<br />';
echo form_submit(array('id' => 'registersubmit', 'class' => 'submit', 'value' => 'Account aanmaken', 'name' => 'registersubmit'));
echo form_close();
?>