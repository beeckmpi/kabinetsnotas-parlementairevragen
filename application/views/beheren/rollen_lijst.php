<div style="float: right;" class="menu_wrapper">
	<div class="edit-btn"><img src="<?php echo base_url()?>media/images/edit.png"/></div>
	<ul class="edit-menu">
		<li><a href="#" onclick="add_beheren('rol')" class="logout ajax">Rol toevoegen</a></li>	
	</ul>
</div>
<div style="margin: 5px 10px">
<?php 
	if (isset($rollen)){		
		echo $rollen;
	} else {
		echo 'Voeg rollen toe om een lijst te krijgen.';
	}
?>
</div>
