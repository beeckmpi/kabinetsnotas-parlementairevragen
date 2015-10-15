<!DOCTYPE html>
<html lang="en" class="no-js">
	<?php if (!isset($ajax)){ ?>
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Parlementaire Vragen en KabinetsNota's</title>	
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">
        <meta http-equiv="cache-control" content="no-cache, no-store">
        <meta http-equiv="pragma" content="no-cache">
		<link rel="shortcut icon" href="<?php echo base_url()?>favicon.ico">
		<link rel="icon" href="<?php echo base_url()?>favicon.ico" 
        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <link rel="stylesheet" href="<?php echo base_url()?>css/normalize.css">
        <link rel="stylesheet" href="<?php echo base_url()?>css/main.css">
       	<link rel="stylesheet" href="<?php echo base_url()?>media/css/bootstrap.min.css" />	
		<link rel="stylesheet" href="<?php echo base_url()?>media/css/default.css" />
		<link rel="stylesheet" href="<?php echo base_url()?>media/css/perfect-scrollbar.css" />
		<?php if (!isset($ajax)){ ?>
		<script src="https://apis.google.com/js/client:platform.js" async defer></script>
        <script src = "https://plus.google.com/js/client:plusone.js"></script>
        <script type="text/javascript" src="https://apis.google.com/js/api.js"></script>
		<script src="<?php echo base_url()?>media/js/jquery.min.js"></script>
        <script src="<?php echo base_url()?>media/js/jquery.ui/js/jquery-ui-1.10.3.custom.js"></script>
        <script src="<?php echo base_url()?>media/js/jquery.ui.datepicker.js"></script>
        <script src="<?php echo base_url()?>media/js/ckeditor/ckeditor.js"></script>
        <script src="<?php echo base_url()?>media/js/ckeditor/config.js"></script>
		<script src="<?php echo base_url()?>media/js/default.js"></script>
		<script src="<?php echo base_url()?>media/js/dossier.js?v=1"></script>
		<script src="<?php echo base_url()?>media/js/jquery.history.js"></script>
		<script src="<?php echo base_url()?>media/js/bootstrap.min.js"></script>
		<script src="<?php echo base_url()?>media/js/jquery.mousewheel.js"></script>
		<script src="<?php echo base_url()?>media/js/jquery.tinyscrollbar.min.js"></script>	
		<script src="<?php echo base_url()?>media/js/perfect-scrollbar.js"></script>
		<link rel="stylesheet" href="<?php echo base_url()?>media/js/jquery.ui/css/ui-lightness/jquery-ui-1.10.3.custom.css" />		
        <?php } ?>				
	</head>	
	<body>	
		<?php } ?>
	    <header id="top">
	      <span class="glyphicon glyphicon-align-left" id="open_menu"></span>
	      <span class="glyphicon glyphicon-align-right filter-openen" id="open_filter"></span>
	      <div id="page-title"><img src="<?php echo base_url()?>/media/images/logo_awv2.png" style="height:30px; margin-top: -8px; margin-right: 15px">Parlementaire Vragen en KabinetsNota's</div>
	      <ul id="top-options">
	      	<?php if (user_access(array('Administrators', 'Stafdienst'))){ ?>
	   	  	<li id="nieuwe_toevoegen"><a href="<?php echo site_url()?>dossier/dossiers/toevoegen/"><span class="glyphicon glyphicon-plus"></span> Nieuw Dossier</a></li>
	   	  	<li id="opslaan_li"><a href="<?php echo site_url()?>dossier/dossiers/toevoegen/" id="opslaan"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;Opslaan</a></li>
	   	  	<?php } ?>
	   	  	<li id="bewerken_opslaan_li"><a href="<?php echo site_url()?>dossier/dossiers/bewerken/" id="bewerken_opslaan"><span class="glyphicon glyphicon-floppy-save"></span>&nbsp;Bewerken voltooien</a></li>
	   	  	<li id="bewerken_li"><a href="<?php echo site_url()?>/#" id="bewerken"><span class="glyphicon glyphicon-pencil"></span>&nbsp;Bewerken</a></li>
	   	  	<?php if (user_access(array('Administrators', 'Stafdienst'))){ ?>
	   	  		<li id="verwijderen_li"><a href="<?php echo site_url()?>/#" id="verwijderen"><span class="glyphicon glyphicon-trash"></span>&nbsp;Verwijderen</a></li>
	   	  	<?php } ?>
	      </ul>
	      <div id="user_div">	
	      	<?php if (isset($user_image)) {?>
	      	<img src="<?php echo $user_image?>" style="position: absolute; right: 7px; top: 6px; height: 30px; border-radius: 35px; ">	      	
			<div id="profile" style="margin-right: 35px;">		
			<?php }  else { ?>
				<div id="profile">	
			 <?php } ?>		
				<a href="<?php echo site_url()?>user/profile/<?php echo $user_data['username']?>" style="">
					<?php echo $user_data['first_name'].' '.$user_data['name']?>
				</a>
				<div class="profile-sub-links">
					<?php if (isset($user_image)) {
						$logout = 'logoutGoogle';	
					} else {
						$logout = 'logout';
					}
					?>
						
					<a href="<?php echo site_url()?>user/logout" class="<?=$logout?>">Afmelden</a>
				</div>							
			</div>
		  </div>
	    </header>	
	    <aside id="left-bar">
			<nav>
			 	 <?php 
			   	if (isset($blocks)){
			  		foreach ($blocks as $key => $block){
			   			echo $block;
			  		}
			  	}			 
				?>
		   </nav>
		</aside>		
		<?php if (!isset($filter_view)){
			$filter_view = "normal";
		}?>
		<section id="dossiers_s" class="grid grid-2">
			<div id="loading_div"><img src="<?php echo base_url()?>media/images/loading_circle.gif"></div>
			<?php echo $this->load->view('overzicht'); ?>		
			<?php if (isset($view)){ echo $view;} ?>	
			<section id="lay_over" class="grid grid-2 lay-over">
		 	 	<div style="" id="lay_over_inside" >test</div>
			</section>
		</section> 
		
		<aside id="filter" style="display:<?php echo $filter_view ?>">			
			<section id="dossier_filter_" style="">
				<a href="<?php echo site_url()?>ajax/dossiers/csv" class="glyphicon glyphicon-download" id="download_csv" title="download csv"></a>
				<h4 style="margin-left: 20px;">Filter  <span class="aantal_dossiers"><span class="glyphicon glyphicon-chevron-right" style="font-size:12px; color: #777"></span> <span id="aantal_dossiers"></span> Dossier(s)</span></h4>
				<?php if (array_key_exists('voorkeuren', $user_data)){
					echo $this->load->view('dossier/dossier_filter', array('voorkeuren' => $user_data['voorkeuren'])); 
				} else {
					echo $this->load->view('dossier/dossier_filter', array('voorkeuren' => '')); 
				}
		   		?>		
		   	</section>	    			
		</aside>    			    	
		</section>
		<footer>			
		</footer>
		<section id="left-color">&nbsp;</section>
		<section id="right-color">&nbsp;</section>
		<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title" id="myModalLabel">Diensten Toevoegen</h4>
		      </div>
		      <div class="modal-body">
		      	
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>
		        <button type="button" class="btn btn-primary" id="save_modal" >Opslaan</button>
		      </div>
		    </div>
		  </div>
		</div>

    </body>
</html>