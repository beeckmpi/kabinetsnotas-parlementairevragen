<div style="float: right;" class="menu_wrapper">
	<div class="edit-btn"><img src="<?php echo base_url()?>media/images/edit.png"/></div>
	<ul class="edit-menu">
		<li><a href="#" onclick="add_beheren('district')" class="logout ajax">District toevoegen</a></li>	
		<li><a href="#" onclick="add_beheren('provincie')" class="logout ajax">Provincie toevoegen</a></li>	
	</ul>
</div>

<?php 
	if (isset($districten)){		
?>
	<table id="districten_tabel" style="">
		<thead>
			<tr>
				<th style="width: 30px"><?php print form_checkbox() ?></th>
				<th style="width: 15%">Districtsnummer</th>
				<th style="width: 35%">Districtsnaam</th>
				<th style="width: 35%">Afdeling</th>
				<th style="width: 10%">Actief</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				
				foreach ($districten as $key => $provincie){
										
					foreach ($provincie as $district => $district_values){
						echo '<tr><td>'.form_checkbox().'</td>';							
						foreach ($district_values as $district_labels => $district_value){							
							if ($district_labels != '_id'){
								echo '<td>'.anchor('ajax/beheren/edit/district/'.$district_values['code'].'/'.$district_values['district'], $district_value, 'klik voor meer').'</td>';
							}						
						}	
						echo '</tr>';					
					}	
													
				}
					
			?>
		</tbody>
	</table>
<?php 
	} else {
		echo 'Voeg een provincie en district toe om een lijst te krijgen.';
	}
?>
