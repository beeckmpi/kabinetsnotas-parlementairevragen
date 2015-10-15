
<h3>Profiel van <?php echo $user_data[0]['first_name'].' '.$user_data[0]['initials'].' '.$user_data[0]['name'];?></h3>
<div id="register_form">
	<div style="float: right;" class="menu_wrapper">
	<?php if (user_access(array('Administrators', 'Stafdienst')) || (user_access(array('Secretariaat')) && ($user_data[0]['location']['afkorting'] == $viewer_data[0]['location']['afkorting']) && $user_data[0]['user_role'][0]!='Administrators') || ($user_data[0]['username'] == $viewer_data[0]['username'])){ ?>
	<div class="edit-btn"><img src="<?php echo base_url()?>media/images/edit.png"/></div>	
	<ul class="edit-menu">	    
		<?php echo '<li>'.anchor('ajax/user/edit_account/'.$user_data[0]['username'], 'Bewerken', array('class' => 'editUser')).'</li>'; ?>	
		<?php if (user_access(array('Administrators', 'Stafdienst', 'Secretariaat'))){
		    echo '<li>'.anchor('ajax/user/resetPassword/'.$user_data[0]['username'], 'Reset wachtwoord', array('class' => 'resetPassword')).'</li>';
		} 
        if (user_access(array('Administrators'))){ ?>
		    <li><a href="<?php echo site_url()?>user/remove_user/<?php echo $user_data[0]['username']?>">Gebruiker Verwijderen</a></li>
        <?php } ?>
	</ul>
	<?php } ?>
	
	</div>
	<div class="form-item">
		<div class="label">Login:</div>
		<div class="item"><?php echo $user_data[0]['username'];?></div>
	</div>
	<div class="form-item">
		<div class="label">Naam:</div>
		<div class="item"><?php echo $user_data[0]['first_name'].' '.$user_data[0]['initials'].' '.$user_data[0]['name'];?> </div>
	</div>
	<div class="form-item">
		<div class="label">Email-adres:</div>
		<div class="item"><?php echo $user_data[0]['email'];?></div>
	</div>
	<div class="form-item">
		<div class="label">Afdeling:</div>
		<div class="item"><?php echo $user_data[0]['location']['provincie'];?></div>
	</div>
	<div class="form-item">
		<div class="label">District:</div>
		<div class="item"><?php echo $user_data[0]['location']['district'];?></div>
	</div>
	<div class="form-item">
		<div class="label">Rollen:</div>
		<?php foreach ($user_data[0]['user_role'] as $role) { ?>
		<div class="item"><?php echo $role;?></div>
		<?php } ?>
	</div>
</div>

