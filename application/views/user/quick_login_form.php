<div class="login_error"><?php echo $error; ?></div>
<?php
echo form_open('user/login', 'id="login" data-url="'.$redirect.'"');
$data = array(
              'name'        => 'username',
              'id'          => 'username',
              'placeholder' => 'Gebruikersnaam',
              'maxlength'   => '150',
              'size'        => '29',
            );

echo '<div style="margin: 0px 0px 5px;">'.form_input($data).'</div>';
echo form_hidden('redirect', $redirect);
echo '<div style="float:left">'.form_password(array('name' => 'password','placeholder' => 'Wachtwoord')).'</div>';
echo form_submit(array('value' => 'Aanmelden', 'class' => 'login_submit')).'<br />';
echo '<div style="clear: both"></div><div style="font-size: 10px">Indien nog geen account hebt of u bent uw wachtwoord vergeten, contacteer dan eerst uw secretariaat, indien ze het niet kunnen oplossen, laat hen dat een mailtje sturen naar de <A HREF="mailto:ICTWegenAntwerpen@mow.vlaanderen.be">administrators</A></div><br />';

echo form_close();
?>

