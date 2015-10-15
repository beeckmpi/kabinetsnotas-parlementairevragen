
<div id="user_settings_form">
	<div id="register_form">
		<?php 
			if (isset($content) && $content != ''){
		    	echo $content; 
			} else { ?>
				<p>
					U kan verschillende rapporten aanmaken. In elke rapport kan u zelf kiezen welke informatie u wilt krijgen.
				</p>
				<p>
					Per rapport kan u ook kiezen of u deze in .csv (Excel) of PDF wil verkrijgen.
				</p>
			<?php } ?>
	</div>
</div>