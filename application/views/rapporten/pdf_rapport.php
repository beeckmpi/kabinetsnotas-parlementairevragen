<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="utf-8">
		<title>Uren</title>				
		<link rel="stylesheet" href="<?php echo base_url()?>media/css/print.css" type"text/css" />
		<script type="text/php"> 
			
		</script>
	</head>
	<body>
		<h2>Rapport</h2>
		<h3><?php echo $rapport_type; ?></h3>
		<div id="rapport_gegevens">
			<div>Naam: <b><?php echo $naam; ?></b></div>
			<div>Opgemaakt op <b><?php echo date('d-m-Y H:i'); ?></b> door <b><?php echo $user ?></b></div>
			<?php if (!empty($opmerkingen)) {?>
			<div>Opmerkingen: <b><?php echo $opmerkingen; ?></b></div>
			<?php } ?>
		</div>		
		<div class="table">
		<?php
			$vertalen = array(
								'bestek'							=> 'Bestek',
								'aannemer'							=> 'Aannemer',
								'leidend_ambtenaar'					=> 'Leidend ambtenaar',
								'dossierbeheerder'					=> 'Dossierbeheerder',
								'extra_info'						=> 'Extra informatie',
								'type' 								=> 'Type',
								'omschrijving'						=> 'Omschrijving',
								'investeerder' 						=> 'Investeerder',
								'meer' 								=> 'Meer',
								'min'								=> 'Min',
								'netto'								=> 'Netto',
								'BTW'								=> 'BTW',
								'totaal'							=> 'Totaal',
								'eindverrekening'					=> 'Eindverrekening',
								'termijnverlenging'					=> 'Termijnverlenging',
								'wijzigingsbevel'					=> 'Wijzigingsbevel',
								'Opmerkingen'						=> 'Opmerkingen wijzigingsbevel:',
								'ontvangst_verrekeningsvoorstel' 	=> 'Ontvangst Verrekeningsvoorstel',
								'vereist'							=> 'ATO Advies Vereist',
								'verzonden'							=> 'Verzonden naar ATO',
								'ontvangen'							=> 'Advies ATO ontvangen',
								'opmerkingen_ato'					=> 'Opmerkingen ATO',
								'aan_aannemer'						=> 'Aan aannemer',
								'terug_van_aannemer'				=> 'Terug van Aannemer',
								'opmerkingen_aannemer'				=> 'Opermerkingen aannemer',
								'naar_medefinancier'				=> 'Voor goedkeuring naar de medefinancierder',
								'opmerking_medefinancier'			=> 'Opmerkingen medefinancierder',
								'goedkeuring_medefinancier'			=> 'Goedkeuring medefinancierder',
								'if_vereist'						=> 'IF Vereist',
								'if_opmerkingen'					=> 'Opmerkingen IF',								
								'aan_stafdienst"'					=> 'Voor goedkeuring naar de stafdienst',
								'goedkeurder'						=> 'Voor goedkeuring naar de goedkeurder/delegator',
								'ambtshalve_goedkeuring'			=> 'Ambtshalve goedkeuring',
								'goedkeuring_dd'					=> 'Goedkeuring d.d.',
								'goedkeuring_aannemer'				=> 'Goedkeuring Aannemer',
								'datum_vastlegging'					=> 'Datum vastlegging',
								'bedrag_vastlegging'				=> 'Bedrag Vastlegging'								
							);
			$goedkeuring = array(0 => 'nee', 1 => 'ja', 'nihil' => 'nee', '' => 'nee');
			foreach ($verrekeningen as $key => $value){
				if (!isset($dossier_id)){
					$dossier_id = '';
				}				
				if (isset($verrekeningen[$key]['meer'])){
					$meer = $verrekeningen[$key]['meer'];
				} else {
					$meer = 0;
				}
				if (isset($verrekeningen[$key]['min'])){
					$min = $verrekeningen[$key]['min'];
				} else {
					$min = 0;
				}
				if (isset($verrekeningen[$key]['btw_calc'])){
					$btw_calc = $verrekeningen[$key]['btw_calc'];
				} else {
					$btw_calc = 0;
				}
				$verrekeningen[$key]['netto'] = $netto = $meer - $min;
				if ($btw_calc == 1){
					$verrekeningen[$key]['BTW'] = $btw = $netto * 0.21;
				} else {
					$verrekeningen[$key]['BTW'] = $btw = 0;
				}				
				$verrekeningen[$key]['totaal'] = $netto + $btw;
				$row = array();
				if($verrekeningen[$key]['dossier']!= $dossier_id){
					$dossier_id = $verrekeningen[$key]['dossier'];
					foreach ($dossiers as $dkey => $dvalue){							
						if ($dossiers[$dkey]['dossier'] == $dossier_id ){									
							echo '</div><br /><br /><div class="table"><div class="dossier">Dossier: <b>'.$dossiers[$dkey]['dossier'].'</b></div><div class="dossier_info">';
							foreach ($form['dossier'] as $dossier_value) {
								if (isset($dossiers[$dkey][$dossier_value])){
									if ($dossier_value != 'dossier'){									
										echo '<div class="dossier_geg">'.$vertalen[$dossier_value].': <b>'.$dossiers[$dkey][$dossier_value].'</b></div>';
									}		
								}															
							}
							echo '</div>';
							if (isset($form['verrekeningen']['nummer'])){
								echo '<div style="position: relative"><div class="nummer"><b>'.$verrekeningen[$key]['nummer'].'</b></div><div class="verrekening-block">';
								foreach ($form['verrekeningen'] as $verrekening_value) {								
									if ($verrekening_value != 'nummer'){
										if(isset($verrekeningen[$key][$verrekening_value])){
											if ((is_float($verrekeningen[$key][$verrekening_value])) || (is_int($verrekeningen[$key][$verrekening_value])))	{
												$verrekeningen[$key][$verrekening_value] = '&euro; '.number_format($verrekeningen[$key][$verrekening_value], 2, ',', '.');
											}										
											if ($verrekening_value=='vereist'||$verrekening_value=='if_vereist'||$verrekening_value=='ambtshalve_goedkeuring'){
												$verrekeningen[$key][$verrekening_value] = $goedkeuring[$verrekeningen[$key][$verrekening_value]];
											}
											echo '<div class="verrekeningen_geg">'.$vertalen[$verrekening_value].': <b>'.$verrekeningen[$key][$verrekening_value].'</b></div>';
										} else {
											echo '<div class="verrekeningen_geg">'.$vertalen[$verrekening_value].':<b> </b></div>';
										}								
									}								
								}							
								echo '</div></div>';
							}
						} 
					}					
				} else {
					if (isset($form['verrekeningen']['nummer'])){
						echo '<div style="border-top: 1px solid #000; position: relative"><div class="nummer" style="width: 45px">'.$verrekeningen[$key]['nummer'].'</div><div class="verrekening-block" >';
						foreach ($form['verrekeningen'] as $verrekening_value) {													
							if ($verrekening_value != 'nummer'){
								if(isset($verrekeningen[$key][$verrekening_value])){
									if ((is_float($verrekeningen[$key][$verrekening_value])) || (is_int($verrekeningen[$key][$verrekening_value])))	{
										$verrekeningen[$key][$verrekening_value] = '&euro; '.number_format($verrekeningen[$key][$verrekening_value], 2, ',', '.');
									} else if ($verrekening_value=='vereist'||$verrekening_value=='if_vereist'||$verrekening_value=='ambtshalve_goedkeuring'){
										$verrekeningen[$key][$verrekening_value] = $goedkeuring[$verrekeningen[$key][$verrekening_value]];
									}										
									echo '<div class="verrekeningen_geg">'.$vertalen[$verrekening_value].': <b>'.$verrekeningen[$key][$verrekening_value].'<b></div>';
								} else {
									echo '<div class="verrekeningen_geg">'.$vertalen[$verrekening_value].':  </div>';
								}								
							}						
						}
						echo '</div></div>';
					}
				}
			}
		?>	
		</div>	
	</body>
</html>