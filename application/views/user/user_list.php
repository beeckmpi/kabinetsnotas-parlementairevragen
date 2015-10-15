<?php if ($show_all){ ?>
	<div id="userList">
		<div style="float: right;" class="menu_wrapper">
			<div class="edit-btn"><img src="<?php echo base_url()?>media/images/edit.png"/></div>
			<ul class="edit-menu">
				<li><a href="<?php echo site_url()?>user/register" class="logout">gebruiker toevoegen</a></li>	
			</ul>
		</div> 
			<input placeholder="Naam" id="searchNaam" style="margin-right: 20px; width: 350px"> 
			<div style="float:right">
				Sorteren op:
				<select id="change_order">
					<option value="username" <?php echo $selected['username'];?>>Login</option>
					<option value="name" <?php echo $selected['name'];?>>Naam</option>
					<option value="location.provincie" <?php echo $selected['location.provincie'];?>>provincie</option>
					<option value="user_role.0" <?php echo $selected['user_role.0'];?>>Rol</option>
				</select>
			</div>
			<div id="userTable">
	<?php } 

		if ($count>=25){
	?>
		<ul class="pagination pagination-sm" id="userlist_pager">
		  <li><a href="#" id="page_prev" class="disabled">&laquo;</a></li>
		 <?php		 	
		 	$pages = ceil($count / 25);
			for ($i=1; $i<=$pages; $i++){
				if($i == 1){
					$active = "class=active";
				} else {
					$active = "";
				}
		 		echo '<li '.$active.'><a href="#" id="page_'.$i.'" class="custom_pager">'.$i.'</a></li>';		 						
		 	}		 	
		 ?>
		  <li><a href="#" id="page_next">&raquo;</a></li>
		</ul>
		<?php }?>
		
		<table id="users" style=" min-width: 520px;">
			<thead>
				<tr>
					<th class="sort" data-order="name">Naam</th>
					<th class="sort" data-order="username">Login</th>
					<th class="sort" data-order="location.provincie">Afdeling - District</th>
					<th class="sort" data-order="user_role.0">Rollen</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				if (array_key_exists(0, $user_data)){
					asort($user_data[0]);
					foreach ($user_data as $key => $name){
					    if($user_data[$key]['location']['district']!=''){
					       $district = $districten[$user_data[$key]['location']['district']];
                        } else {
                           $district = $user_data[$key]['location']['district'];
                        }
						echo '<td>'.anchor('user/profile/'.$user_data[$key]['username'], $user_data[$key]['name'].' '.$user_data[$key]['first_name'], 'klik voor meer').'</td>';
						echo '<td>'.anchor('user/profile/'.$user_data[$key]['username'], $user_data[$key]['username'], 'klik voor meer').'</td>';
						echo '<td>'.anchor('user/profile/'.$user_data[$key]['username'], $user_data[$key]['location']['provincie'].' - '.$district, 'klik voor meer').'</td>';
						foreach($user_data[$key]['user_role'] as $role){
							echo '<td>'.anchor('user/profile/'.$user_data[$key]['username'], $role, 'klik voor meer').'</td>';
						} 
						echo '<td style="position:relative">';
						if (!user_access(array('Administrators'))) {
							if (user_access(array('Secretariaat', 'Stafdienst'))){
								if ($user_data[$key]['user_role'][0] != 'Administrators'){
									echo '<a class="glyphicon glyphicon-cog" data-toggle="dropdown" style="font-size:13px"></a><ul class="dropdown-menu dropdown-menu-left" role="menu" aria-labelledby="dLabel">';						
									echo '<li>'.anchor('ajax/user/edit_account/'.$user_data[$key]['username'], 'Bewerken', array('class' => 'editUser')).'</li>';
									echo '<li>'.anchor('ajax/user/resetPassword/'.$user_data[$key]['username'], 'Reset wachtwoord', array('class' => 'resetPassword')).'</li></ul>';
								}
							}
						} else if (user_access(array('Administrators'))) {
							echo '<a class="glyphicon glyphicon-cog" data-toggle="dropdown" style="font-size:13px"></a><ul class="dropdown-menu dropdown-menu-left" role="menu" aria-labelledby="dLabel">';						
							echo '<li>'.anchor('ajax/user/edit_account/'.$user_data[$key]['username'], 'Bewerken', array('class' => 'editUser')).'</li>';
							echo '<li>'.anchor('ajax/user/resetPassword/'.$user_data[$key]['username'], 'Reset wachtwoord', array('class' => 'resetPassword')).'</li></ul>';
						}
						echo '</td></tr>';
					}
				}
				?>
			</tbody>
		</table>
		<?php if ($show_all){ ?>
			</div>
	</div>
<?php } ?>