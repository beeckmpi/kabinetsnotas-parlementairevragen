<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		 <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Parlementaire Vragen en KabinetsNota's</title>	
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">
        <script src="https://apis.google.com/js/client:platform.js" async defer></script>
        <script src = "https://plus.google.com/js/client:plusone.js"></script>
        <script src="<?php echo base_url()?>media/js/jquery.min.js"></script>
        <script src="<?php echo base_url()?>media/js/jquery.ui/js/jquery-ui-1.10.3.custom.js"></script>
		<script src="<?php echo base_url()?>media/js/login.js"></script>
		<script src="<?php echo base_url()?>media/js/bootstrap.min.js"></script>
		<script src="<?php echo base_url()?>media/js/jquery.history.js"></script>
        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <link rel="stylesheet" href="<?php echo base_url()?>css/normalize.css">
        <link rel="stylesheet" href="<?php echo base_url()?>css/main.css">				
		<link rel="stylesheet" href="<?php echo base_url()?>media/css/default.css" />
		<link rel="stylesheet" href="<?php echo base_url()?>media/js/jquery.ui/css/ui-lightness/jquery-ui-1.10.3.custom.css" />			
		<link rel="stylesheet" href="<?php echo base_url()?>media/css/bootstrap.min.css" />			
	</head>
	<body>		
	    <header id="top">
	      <div id="page-title"><img src="<?php echo base_url()?>/media/images/logo_awv2.png" style="height:30px; margin-top: -8px; margin-right: 15px">Parlementaire Vragen en KabinetsNota's</div>
	    </header>		
	
		<section id="dosiers_informatie" style="margin: 120px auto 0px auto; width: 560px;">
		    <div style="display:inline-block; width: 47%">
    			<header>Aanmelden</header>
    			<div style="font-size: smaller; margin-bottom: 10px;">U kan zich aanmelden met uw gebruikersnaam en wachtwoord.</div>		
    			<div id="login_form"><?php echo $login_form ?></div>
			</div>
			<div style="display:inline-block; width: 47%; margin-left: 5%; vertical-align: top">
    			<div>Of<p style="font-size: smaller">U kan u aanmelden met uw Wegen en Verkeer Google account.</p></div>
    			<span id="signinButton">
    			  <span
    			    class="g-signin"
    			    data-callback="signInCallback"
    			    data-clientid="829591700839-foca2nr8jja2183nbr2h5h98irujam4q.apps.googleusercontent.com"
    			    data-cookiepolicy="single_host_origin"
    			    data-scope="https://www.googleapis.com/auth/plus.profile.emails.read">
    			  </span>
    			</span>
    			<div id="results"></div>
            </div>
		</section>	    	
		<section id="dosiers_informatie" style="margin: 10px auto; width: 480px; display:none">
			<h5>Extra Informatie</h5>		
			<div style="font-size: smaller">
				
			</div>
		</section>	
		<script type="text/javascript">function signInCallback(authResult) {
			  if (authResult['code']) {
			  	var href = window.location.href;
			  	var href_first = href.split('index.php/');
			  	var redirect = $('#login').data('url');
				$.get('https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='+authResult['access_token'], function(info){					 
				    // Send the code to the server
				    $.get('https://www.googleapis.com/plus/v1/people/me?access_token='+authResult['access_token'], function(user){				       
				    	$.ajax({
					      type: 'POST',
					      url: href_first[0]+'index.php/user/gplusAuth/<?php echo $state ?>/'+redirect,
					      success: function(json) {
							if (json.error_msg != undefined){
								$('.login_error').fadeIn().html(json.error_msg);				
							} else {					
								redirect = redirect.replace(/_/gi, '/');
								window.location = '/secretariaat/index.php/'+redirect;		
							}
					      },
					      data: user
					    });
				    })
				    
				});
			    // Hide the sign-in button now that the user is authorized, for example:
			    $('#signinButton').attr('style', 'display: none');
			   
			  } else if (authResult['error']) {
			    // There was an error.
			    // Possible error codes:
			    //   "access_denied" - User denied access to your app
			    //   "immediate_failed" - Could not automatially log in the user
			    // console.log('There was an error: ' + authResult['error']);
			  }
			}
		  </script>

		<script>
			
		</script>
		<footer>
			
		</footer>
	</body>
</html>