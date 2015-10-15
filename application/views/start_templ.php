<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="utf-8">
		<title>Verrekeningen</title>		
		<script src="<?php echo base_url()?>media/js/jquery.min.js"></script>
        <script src="<?php echo base_url()?>media/js/jquery.ui/js/jquery-ui-1.10.3.custom.js"></script>
		<script src="<?php echo base_url()?>media/js/default.js"></script>
		<script src="<?php echo base_url()?>media/js/dossier.js"></script>
		<script src="<?php echo base_url()?>media/js/bootstrap.min.js"></script>
		<script src="<?php echo base_url()?>media/js/jquery.history.js"></script>
		<script src="<?php echo base_url()?>media/js/jquery.tinyscrollbar.min.js"></script>
		<link rel="stylesheet" href="<?php echo base_url()?>media/css/default.css" />			
		<link rel="stylesheet" href="<?php echo base_url()?>media/js/jquery.ui/css/ui-lightness/jquery-ui-1.10.3.custom.css" />			
		<link rel="stylesheet" href="<?php echo base_url()?>media/css/bootstrap.min.css" />	
	</head>
	<body>	
	    <header>
	      <h1>Secretariaat Opvolging</h1>
	      <div id="user_div">
			<div id="profile">
				<a href="<?php echo site_url()?>/user/profile/<?php echo $user_data['username']?>" style="padding:10px 0; height: 50px; font-weight: bold; ">
					<?php echo $user_data['first_name'].' '.$user_data['name']?>
				</a>
				<div class="profile-sub-links">
					<a href="<?php echo site_url()?>/user/logout" class="logout">Afmelden</a>
				</div>							
			</div>
		  </div>
		  <nav>
		  	<?php 
		  	$active_p = array('start' => '', 'verrekeningen' =>'', 'schuldvorderingen' => '', 'rapporten' => '');
		  	switch($active_page){
				case 'start':
						$active_p['start'] = 'actief';
				break;
				case 'verrekeningen':
						$active_p['verrekeningen'] = 'actief';
				break;
				case 'schuldvorderingen':
						$active_p['schuldvorderingen'] = 'actief';
				break;
				case 'rapporten':
						$active_p['rapporten'] = 'actief';
				break;
		  	}; 
			?>
		    	<ul>
				     <li><a href="<?php echo site_url()?>dossier/verrekening" class="<?php echo $active_p['verrekeningen']?>">Verrekeningen</a></li>
				     <li><a href="<?php echo site_url()?>dossier/schuldvorderingen" class="<?php echo $active_p['schuldvorderingen']?>">Schuldvorderingen</a></li>
				     <li><a href="<?php echo site_url()?>dossier/rapporten" class="<?php echo $active_p['rapporten']?>">Rapporten</a></li>
				     <li><a href="<?php echo site_url()?>/user/Userlist">Beheren</a></li>
		    	</ul>     
		    </nav>		
	    </header>		
		<section id="content_wrapper">			
	    	<ul id="start_applications">
	    		<ul>
	    			<li>	    				
	    				<a title="Voeg een dossier toe" href="<?php echo site_url()?>dossier/dossiers/toevoegen" >
	    					<img src="<?php echo base_url()?>media/images/dossier_aanmaken.png" /> 
	    					<div>DOSSIER AANMAKEN</div>	    				
	    				</a>
	    			</li>
	    			<li>
	    				<a href="<?php echo site_url()?>dossier/verrekening" title="Voeg een verrekening toe">
	    					<img src="<?php echo base_url()?>media/images/verrekeningen_lijst.png" /> 
		    				<div>VERREKENINGEN LIJST</div>
	    				</a>
	    			</li>
	    			<li>
	    				<a id="add_schuldvorderingen" href="<?php echo site_url()?>dossier/schuldvorderingen" title="Voeg een schuldvordering toe">
	    					<img src="<?php echo base_url()?>media/images/schuldvorderingen_lijst.png" /> 
		    				<div>SCHULDVORDERINGEN LIJST</div>
	    				</a>
	    			</li>
	    			<li>
	    				<a>RAPPORTEN AANMAKEN</a>
	    			</li>
	    			<li>
	    				<a href="<?php echo site_url()?>user/settings/account">
	    				ACCOUNT AANPASSEN
	    				</a>
	    			</li>
	    		</ul>
	    	</ul>	    	
		</section>
		<footer>			
		</footer>
	</body>
</html>