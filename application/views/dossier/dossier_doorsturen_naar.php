
	<div>
		<button class="btn btn-default btn-xs select-all" style=margin-bottom:15px>Alle diensten selecteren</button>
		<button class="btn btn-default btn-xs deselect-all" style=margin-bottom:15px>Selectie ongedaan maken</button>
	</div>
	<div class="" data-toggle="buttons">
	    <?php 
	       	$users = $doorgestuurd_naar['users'];
			$districten = $doorgestuurd_naar['districten'];
			$te_behandelen = explode(', ', $behandelen_door);
			foreach ($te_behandelen as $key => $value) {
				echo '<h4>'.$value.'</h4>';
				$checked = FALSE;
				$active = ' btn-default ';
				foreach ($districten[$value] as $dkey => $district){
					if(substr_count($doorsturen_naar, $district['district'])>=1){
						$checked = TRUE;
						$active = ' btn-primary active ';
					}
					echo '<label class="btn'.$active.'btn-xs btn-group-label" style="margin-right: 10px; margin-bottom: 10px">';				
					echo form_checkbox('doorgestuurd_naar["'.$district['district'].'"]', $district['district'], $checked);
					echo $district['district'];
					echo '</label>';
					$checked = FALSE;
					$active = ' btn-default ' ;
				}
				echo '<br />';
				foreach ($users[$value] as $dkey => $user){
					if(substr_count($doorsturen_naar, $user['first_name'].' '.$user['name'])>=1){
						$checked = TRUE;
						$active = ' btn-primary active ';
					}
					echo '<label class="btn'.$active.'btn-xs btn-group-label" style="margin-right: 10px; margin-bottom: 10px">';				
					echo form_checkbox('doorgestuurd_naar["'.$user['first_name'].' '.$user['name'].'"]', $user['first_name'].' '.$user['name'], $checked);
					echo $user['first_name'].' '.$user['name'];
					echo '</label>';
					$checked = FALSE;
					$active = ' btn-default ';
				}
			}			
		
		?>
	</div>
