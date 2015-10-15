<?php 
	$active_p = array('overzicht' => '', 'profiel' => '', 'lijsten' =>'', 'beheren' => '');
	switch($active_page){
		case 'overzicht':
			$active_p['overzicht'] = 'actief';
		break;
		case 'profiel':
			$active_p['profiel'] = 'actief';
		break;
		case 'lijsten':
			$active_p['lijsten'] = 'actief';
		break;
		case 'beheren':
			$active_p['beheren'] = 'actief';
		break;
	}; 
?>
	
   	<ul>
   		<!--<li><input type="text" placeholder="Zoeken" id="search_bar" size="20" style="width: 170px; margin-left: -10px; font-size: smaller"></li>-->
	     <li><a href="<?php echo site_url()?>" class="<?php echo $active_p['overzicht']?> toggle">Overzicht</a>
	     	<div id="user_settings_menu"  class="collapsed">
				<ul>
					<li><a href="<?php echo site_url()?>" id="">Lijst</a></li>
					<?php if (user_access(array('Administrators', 'Stafdienst'))){ ?>
					<!--<li><a href="<?php echo site_url()?>" id="">rapporten</a></li>
					<li><a href="<?php echo site_url()?>" id="">statistieken</a></li>-->		
					<li><a href="<?php echo site_url()?>beheren/beheren/te_behandelen_door" id="">Te behandelen door</a></li>		
					<li><a href="<?php echo site_url()?>beheren/beheren/parlementairen" id="">Parlementairen</a></li>
					<?php } ?>
				</ul>
			</div>	
	     </li>	    
	     <?php if (user_access(array('Secretariaat', 'Stafdienst'))){?>
	     	<li><a href="<?php echo site_url()?>user/Userlist" id="user_list">Gebruikers</a>
				<ul>
					<li><a href="<?php echo site_url()?>user/Userlist" id="user_list">Lijst</a></li>
					<li><a href="<?php echo site_url()?>user/register" id="add_user">Gebruiker toevoegen</a></li>
				</ul>
			</li>			
	     <?php } ?>
	     <li><a href="<?php echo site_url()?>user/profile/<?php echo $user_data['username']?>" style=""><?php echo $user_data['first_name'].' '.$user_data['name']?></a>
            <div id="user_settings_menu"  class="collapsed">
                <ul>
                    <li><a href="<?php echo site_url()?>user/settings/account" id="account">Mijn profiel</a></li>
                    <li><a href="<?php echo site_url()?>user/settings/password" id="password">wachtwoord</a></li>
                    <li><a href="<?php echo site_url()?>user/logout" class="logout">Afmelden</a></li>
                </ul>
            </div>  
         </li>
	     <?php if (user_access(array('Administrators'))){ ?>
	     <li><a href="<?php echo site_url()?>/user/Userlist" class="<?php echo $active_p['beheren']?> toggle" id="beheren_li_a">Beheren</a>
	     	<div id="beheren_list" class="collapsed">
			    <ul>
					<li><a href="<?php echo site_url()?>beheren/beheren/districten" id="distrist_list">Districten</a>
						<ul>
							<li><a href="<?php echo site_url()?>beheren/beheren/districten" id="add_user">Lijst</a></li>
						<li><a href="#" onclick="add_beheren('provincie')" class="logout ajax">Afdeling toevoegen</a></li>
							<li><a href="#" onclick="add_beheren('district')" class="logout ajax">District toevoegen</a></li>				
						</ul>
					</li>
					<li><a href="<?php echo site_url()?>beheren/beheren/rollen" id="add_user">Rollen</a>
						<ul>
							<li><a href="<?php echo site_url()?>beheren/beheren/rollen" id="add_user">Lijst</a></li>
							<li><a href="#" onclick="add_beheren('rol')" class="logout ajax">Rol toevoegen</a></li>	
						</ul>	
					</li>	
				</ul>
			</div>
		</li>
		<?php } ?>		  
	</ul> 