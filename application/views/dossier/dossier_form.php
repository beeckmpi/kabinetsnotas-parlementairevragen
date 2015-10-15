
<?php
	echo validation_errors();	
	$view = array(
		'stafdienst' => array('view' => 'none', 'actief' => ''),
		'secretariaat' => array('view' => 'none', 'actief' => ''),
		'bijlagen' => array('view' => 'none', 'actief' => '')			
	);
	if (!user_access(array('Administrators', 'Stafdienst', 'Secretariaat', 'PCOEVT'))){
		unset($view['stafdienst']);
	}
	
	if(!isset($dossier)){
		$dossier = array(
			'type'					=> '', 
			'onderwerp'				=> '',
			'dossiernummer' 		=> '', 
			'gemeente' 				=> '', 
			'gemeenteplaats' 		=> '', 
			'wegnummer' 			=> '',  
			"huisnr"				=> '', 
			"wegbenaming"			=> '', 
			"straatnaam"			=> '', 
			'referte_dtg' 			=> '',
			'referentie_kab'		=> '',
			'datum_dtg' 			=> '',
			'datum_kabinet' 		=> '',
			'aard' 					=> '',
			'nummer_pv'				=> '',
			'nummer_kab'			=> '',
			"doorgestuurd_naar" 	=> '',
			"gecoordineerd_door" 	=> '',
			"doorsturen_naar"		=> '',
			"te_behandelen_door"	=> '', 
			"overgemaakt_aan"		=> '', 
			"referentie"			=> '', 
			"te_herinneren_op"		=> '', 
			"herinnering_op"		=> '', 
			"terug_herinnerd_op"	=> '',
			"antwoord_ontvangen"	=> '', 
			"uitgeschreven_op"		=> '', 
			"wachtende"				=> '', 
			"ombudsman"				=> '', 
			"kaartje"				=> '', 
			"niet_voor_awv"			=> '', 
			"ongegrond"				=> '', 
			"afgehandeld"			=> '', 
			"parlementarier" 		=> '',
			"aanvrager"				=> '', 
			"straat_nr"				=> '', 
			"postcode_gemeente"		=> '', 
			"emailadres"			=> '',
			"telefoon"				=> '', 
			"omschrijving"			=> '',
			"oorzaak"				=> '', 
			"suboorzaak"			=> '', 
			"suboorzaak2"			=> '', 
			"datum_melding"			=> '',
			"bijlage_opmerking"		=> '',
			"beantwoord"			=> "false"
		);
		unset($view['secretariaat']);
	}
	foreach ($view as $key => $value){
		if ($active_tab == $key){
			$view[$key]['view'] = 'inherit';
			$view[$key]['actief'] = 'active';
		}
	}
	$parlementarier = '';
	if (isset($dossier['parlementarier'])){
		$parlementarier = $dossier['parlementarier'];
	}
	
	if($dossier['dossiernummer'] != ''){
		$dossiernummer["jaar"] = substr($dossier['dossiernummer'], 0, 4);
		$dossiernummer["nummer"] = substr($dossier['dossiernummer'], 4, 8);
	} else { 
		$dossiernummer["jaar"] = date('Y');
		$dossiernummer["nummer"] = '';
	};
	$voorlopig = '(voorlopig)';
	if($dossier['type'] != ''){
		$voorlopig = '';
		$type_afk = array('email_kabinet' => 'EMAILKAB', 'fietspaden' => 'MPF',  'kabinetsnotas' => 'KAB', 'wegen' => 'MPW', 'parlementaire_vragen' => 'PV');
	} else {
		$type_afk = array('' => '');
	}
	if($dossier['datum_kabinet'] == '' || $dossier['datum_kabinet']->sec == 0){
		$dossier['datum_kabinet'] = '';
	} else {
		$dossier['datum_kabinet'] = date('d-m-Y', $dossier['datum_kabinet']->sec);
	}
	if($dossier['datum_melding'] == '' || $dossier['datum_melding']->sec == 0){
		$dossier['datum_melding'] = '';
	} else {
		$dossier['datum_melding'] = date('d-m-Y', $dossier['datum_melding']->sec);
	}
	if(array_key_exists('naar_staf_tegen', $dossier)){
		if (is_object($dossier['naar_staf_tegen'])){
			if($dossier['naar_staf_tegen']->sec == 0){
				$dossier['naar_staf_tegen'] = '';
			} else {
				$dossier['naar_staf_tegen'] = date('d-m-Y', $dossier['naar_staf_tegen']->sec);
			}
		} 
	} else {
		$dossier['naar_staf_tegen'] = '';
	}
	if($dossier['uitgeschreven_op'] == '' || $dossier['uitgeschreven_op']->sec == 0){
		$dossier['uitgeschreven_op'] = '';
	} else {
		$dossier['uitgeschreven_op'] = date('d-m-Y', $dossier['uitgeschreven_op']->sec);
	}	
	if($dossier['herinnering_op'] == '' || $dossier['herinnering_op']->sec == 0){
		$dossier['herinnering_op'] = '';
	} else {
		$dossier['herinnering_op'] = date('d-m-Y', $dossier['herinnering_op']->sec);
	}
	if(array_key_exists('terug_herinnerd_op', $dossier)){
		if($dossier['terug_herinnerd_op'] == '' || $dossier['terug_herinnerd_op']->sec == 0){
			$dossier['terug_herinnerd_op'] = '';
		} else {
			$dossier['terug_herinnerd_op'] = date('d-m-Y', $dossier['terug_herinnerd_op']->sec);
		}
	}
	if(array_key_exists("antwoord_ontvangen", $dossier) && is_object($dossier['antwoord_ontvangen'])){
		if($dossier['antwoord_ontvangen'] == '' || $dossier['antwoord_ontvangen']->sec == 0){
			$dossier['antwoord_ontvangen'] = '';
		} else {
			$dossier['antwoord_ontvangen'] = date('d-m-Y', $dossier['antwoord_ontvangen']->sec);
		}
	} else {
		$dossier['antwoord_ontvangen'] = '';
	}
	if(!array_key_exists('kruispunt', $dossier)){
		$dossier['kruispunt'] = '';
	}
	if(!array_key_exists('doorgestuurd_naar', $dossier)){
		$dossier['doorgestuurd_naar'] = '';
	}
	if(!array_key_exists('doorsturen_naar', $dossier)){
		$dossier['doorsturen_naar'] = '';
	}
	if(!array_key_exists('verduidelijking', $dossier)){
		$dossier['verduidelijking'] = '';
	}
	if(!array_key_exists('referentie_kab', $dossier)){
		$dossier['referentie_kab'] = '';
	}
	if(!array_key_exists('terug_herinnerd_op', $dossier)){
		$dossier['terug_herinnerd_op'] = '';
	}
	if(!array_key_exists('gecoordineerd_door', $dossier)){
		$dossier['gecoordineerd_door'] = '';
	}
	if(array_key_exists('_id', $dossier)){
		$hrefl = 'opslaan/'.$dossier['_id'];
		$id = 'bewerken_formulier';
		$edit = '_edit';
	} else {
		$hrefl = 'opslaan';
		$id = 'inkomende_formulier';
		$edit = '';
	}
	echo form_open('ajax/dossiers/'.$hrefl, 'id="'.$id.'" name="'.$id.'" style="position: relative"');
	$div_open_item = '<div class="form-item">';
	$div_close = '</div>';
	if (user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){ 
	 	$disabled = '';
		$disabledSec = '';
		$rol = 'Staf';
	} else if (user_access(array('Secretariaat'))) {
		$disabled = 'disabled';
		$disabledSec = '';
		$rol = 'secretariaat';
	} else {
		$disabled = 'disabled';
		$disabledSec = 'disabled';
		$rol = 'dossierbeheerder';
	}
	
	?>
	<h4>Dossier 
		<?php if(array_key_exists('_id', $dossier)){ ?>Bewerken
		<div style="position:absolute; right: 10px; top: 10px">
			<a class="close_w" href="#" title="Sluiten" id="rd_close">
				<div class="g_close" alt="">&nbsp;</div>
			</a>
		</div>
		<?php } else { ?>Toevoegen<?php }?>
	</h4>
	<?php
		if(array_key_exists('beantwoord', $dossier)){
	 		echo form_hidden('beantwoord', $dossier['beantwoord']);
		} else {
			echo form_hidden('beantwoord', "false");
		} 
	?>
	<?php
	echo form_hidden('rol_gebruiker', '');
	if ($dossier['type']!=''){
		echo form_hidden('type', $dossier['type']);
	} else {
		echo '<div class="form-item-inline-select">';
		echo '<span style="font-weight:bold; margin-right: 15px">Type Rapport</span>';
		$type = array(
			' ' => '--selecteer--',
			'email_kabinet' => 'Kabinetsnota\'s',
			'parlementaire_vragen' => 'Parlementaire vragen',
		);
		asort($type);
		echo form_dropdown('type', $type, $dossier['type']);
		echo $div_close;
	}
	if(array_key_exists('dossiernummer', $dossier)){
		echo form_hidden('dossiernummer', $dossier['dossiernummer']);
	}	
	$dossier_nr = '';
	if(array_key_exists('nummer_kab', $dossier) && $dossier['nummer_kab']!=''){
		 $dossier_nr = $dossier['nummer_kab'];
	} else if (array_key_exists('nummer_pv', $dossier) && $dossier['nummer_pv']!=''){	
		$dossier_nr = 'PV: '.$dossier['nummer_pv'];
	} 	 
	echo '<div class="form-item-inline dossier_nummmer" style="font-weight: bold; font-size: 15px">'.$dossier_nr;
	echo $div_close;
	
	echo '<div style="position:relative; zoom:1.0" id="form_view">'; 
	?>
	<ul style="" class="horizontal_tabs">
		<?php foreach ($view as $key => $value) {?>
		<li class="<?php echo $value['actief']; ?>"><a href="#"><?php echo $key ?></a></li>		
		<?php } ?>
	</ul>
	<?php
	if (user_access(array('Administrators', 'Stafdienst', 'Secretariaat', 'PCOEVT'))){
	echo '<div id="stafdienst" class="vtab"  style="display:'.$view['stafdienst']['view'].'">';
	echo $div_open_item;
		echo form_label('Nummer PV*', 'nummer_pv');
		$data = array(
		              'name'        => 'nummer_pv',
		              'id'			=> 'nummer_pv'.$edit,
		              'placeholder' => 'Nummer PV',
		              'maxlength'   => '100',
		              'size'        => '40',
		              'class' 		=> 'parlementaire_vragen '.$disabled,
		              'value'		=>	$dossier['nummer_pv']
		            );		
		echo form_input($data);		
	echo $div_close;
	echo $div_open_item;
		echo form_label('Nummer KAB*', 'nummer_kab');
		$data = array(
		              'name'        => 'nummer_kab',
		              'id'			=> 'nummer_kab'.$edit,
		              'placeholder' => 'Nummer KAB',
		              'maxlength'   => '100',
		              'Style'		=> 'width: 250px',
		              'class' 		=> 'email_kabinet '.$disabled,
		              'value'		=>	$dossier['nummer_kab']
		            );		
		echo form_input($data);		
	echo $div_close;
	echo $div_open_item;
		echo form_label('Onderwerp*', 'onderwerp');
		$data = array(
		              'name'        => 'onderwerp',
		              'id'			=> 'onderwerp'.$edit,
		              'placeholder' => 'Onderwerp',
		              'maxlength'   => '150',
		              'size'        => '50',
	             	  'class' 		=> 'email_kabinet parlementaire_vragen '.$disabled,
		              'value'		=>	$dossier['onderwerp']
	            );		
		echo form_input($data);		
	echo $div_close;
	echo $div_open_item;	
		echo form_label('Omschrijving', 'omschrijving');
		/*$data = array(
		              'name'        => 'omschrijving',
		              'id'			=> 'omschrijving',
		              'placeholder' => 'Omschrijving',
		              'rows'		=> '3',
		              'Style'		=> 'width: 664px',
		              'class' 		=> 'parlementaire_vragen email_kabinet '.$disabled,
		              'value'		=> $dossier['omschrijving'],       
		            );	
		echo form_textarea($data);	*/
		if(array_key_exists('_id', $dossier)){				
			$omschrijving = 'bewerken_omschrijving';
			if ($dossier['omschrijving'] == ''){
				$omschrijving_text = 'Omschrijving';
			} else {
				$omschrijving_text = $dossier['omschrijving'];
			}
		} else {
			$omschrijving = 'omschrijving';
			$omschrijving_text = 'Omschrijving';
		}
		if ($disabled != 'disabled'){
			echo '<div contenteditable="true" id="'.$omschrijving.'" class="parlementaire_vragen email_kabinet '.$disabled.'">'.$omschrijving_text.'</div>';	
		} else {
			echo '<div id="'.$omschrijving.'" class="parlementaire_vragen email_kabinet '.$disabled.'">'.$omschrijving_text.'</div>';
		}
	echo $div_close;	
	if ($disabled != 'disabled'){
 ?>
	<script>
		 // Turn off automatic editor creation first.	    
	    <?php if(array_key_exists('_id', $dossier)){ ?>
	    	CKEDITOR.disableAutoInline = true;
	    	CKEDITOR.inline( 'bewerken_omschrijving' );
	    <?php }?>
	</script>
<?php
	}
	echo '<div class="form-grid" id="left_geg">';
		echo $div_open_item;
			if ($dossier['type']!= 'parlementaire_vragen'){
				$naam = 'Datum Melding';
			} else {
				$naam = 'Datum PV';
			}
			echo form_label($naam.'*', 'datum_melding');
			$data = array(
			              'name'        => 'datum_melding',
			              'id'			=> 'datum_melding'.$edit,
			              'placeholder' => $naam,
			              'maxlength'   => '100',
			              'class'		=> 'datepicker email_kabinet parlementaire_vragen '.$disabled,
			              'value'		=> $dossier['datum_melding'],
			              'Style'		=> 'width: 250px',
			              'required'	=> true
			            );		
			echo form_input($data);		
		echo $div_close;
		echo $div_open_item;
			echo form_label('Kabinet', 'referentie_kab');
			$data = array(
			              'name'        => 'referentie_kab',
			              'id'			=> 'referentie_kab',
			              'placeholder' => 'Kabinet',
			              'maxlength'   => '100',
			              'Style'		=> 'width: 250px',
			              'class' 		=> 'email_kabinet '.$disabled,
			              'value'		=>	$dossier['referentie_kab'],
			            );		
			echo form_input($data);		
		echo $div_close;
		echo $div_open_item;
			echo form_label('Parlementariër', 'parlementarier');		
			$parlementairen[''] = '--selecteren--';		
			asort($parlementairen);
			echo form_dropdown('parlementarier', $parlementairen, $parlementarier, 'class="parlementaire_vragen '.$disabled.'" style="width: 250px"');
		echo $div_close;
		echo $div_open_item;
			echo form_label('Aanvrager', 'aanvrager');
			$data = array(
			              'name'        => 'aanvrager',
			              'id'			=> 'aanvrager',
			              'placeholder' => 'Aanvrager',
			              'maxlength'   => '100',
			              'Style'		=> 'width: 250px',
		             	  'class' 		=> 'email_kabinet '.$disabled,
			              'value'		=>	$dossier['aanvrager'],
			            );		
			echo form_input($data);		
		echo $div_close;
		echo $div_open_item;
			echo form_label('Adres', 'straat_nr');
			$data = array(
			              'name'        => 'straat_nr',
			              'id'			=> 'straat_nr',
			              'placeholder' => 'Straat en huisnummer',
			              'maxlength'   => '100',
			              'Style'		=> 'width: 250px',
		             	  'class' 		=> 'email_kabinet '.$disabled,
			              'value'		=>	$dossier['straat_nr'] ,
			            );		
			echo form_input($data);		
		echo $div_close;
		echo $div_open_item;
			echo form_label('', 'postcode_gemeente');
			$data = array(
			              'name'        => 'postcode_gemeente',
			              'id'			=> 'postcode_gemeente',
			              'placeholder' => 'Postcode & Gemeente',
			              'maxlength'   => '100',
			              'Style'		=> 'width: 250px',
		             	  'class' 		=> 'email_kabinet '.$disabled,
			              'value'		=>	$dossier['postcode_gemeente'],
			            );		
			echo form_input($data);		
		echo $div_close;
		echo $div_open_item;
				echo form_label('Emailadres', 'emailadres');
				$data = array(
				              'name'        => 'emailadres',
				              'id'			=> 'emailadres',
				              'placeholder' => 'Emailadres',
				              'maxlength'   => '100',
				              'type'		=> 'email',
				              'Style'		=> 'width: 250px',
			             	  'class' 		=> 'email_kabinet '.$disabled,
				              'value'		=>	$dossier['emailadres'],
				            );		
				echo form_input($data);		
			echo $div_close;				
		
			echo $div_open_item;
				echo form_label('Aard', 'aard');
				$aard = array(
					' ' => '--selecteer--',
					'pv_nr' 				=> 'P.V. nr.',
					'interpellatie' 		=> 'Interpellatie', 
					'vraag_om_uitleg'		=> 'Vraag om uitleg',
				);
				asort($aard);
				echo form_dropdown('aard', $aard, $dossier['aard'], 'class="parlementaire_vragen '.$disabled.'" style="width: 250px"');
			echo $div_close;
			
			
		echo '</div><div class="form-grid" id="right_geg">';
		
			echo $div_open_item;
				echo form_label('Antwoord door', 'te_behandelen_door');		
				/*e $te_behandelen[''] = '--selecteren--';		
				asort($te_behandelen);
				echo form_dropdown('te_behandelen_door', $te_behandelen, $dossier['te_behandelen_door'], 'class="wegen email_kabinet kabinetsnotas parlementaire_vragen"');*/
				$dossier_tegen = 'Dienst(en) toevoegen';
				$dossier_tegen_str = '';
				if($dossier['te_behandelen_door'] != ''){
					$dossier_tegen = $dossier['te_behandelen_door'];					
					if (is_array($dossier_tegen)){
						foreach($dossier_tegen as $key => $value){
							$dossier_tegen_str .= $key.', ';
						}
						$dossier_tegen_str = substr($dossier_tegen_str, 0, -2);
					} else {
						$dossier_tegen_str = $dossier_tegen;
					}
					$dossier['te_behandelen_door'] = $dossier_tegen_str;
					echo form_hidden('te_behandelen_door', $dossier_tegen_str);	
				} else {
					$dossier_tegen_str = 'Dienst(en) toevoegen';
					echo form_hidden('te_behandelen_door', '');	
				}				
				
				echo '<div class="input_selector te_behandelen_door_input  email_kabinet kabinetsnotas parlementaire_vragen '.$disabled.'" style="width:250px">'.$dossier_tegen_str.'<a></a></div>';
			echo $div_close;
			echo $div_open_item;
				echo form_label('Gecoördineerd door', 'gecoordineerd_door');		
				$gecoordineerd_door_lijst['stafdienst'] = 'Stafdienst';
				$gecoordineerd_door_lijst['EVT'] = 'EVT';
				$gecoordineerd_door_lijst['PCO'] = 'PCO';
				echo form_dropdown('gecoordineerd_door', $gecoordineerd_door_lijst, $dossier['gecoordineerd_door'], 'class="gecoordineerd_door email_kabinet kabinetsnotas parlementaire_vragen '.$disabled.'" style="width: 250px"');
			echo $div_close;
			if(array_key_exists('antwoord_ontvangen', $dossier)){
				$ontvangen = $dossier['antwoord_ontvangen']; 
			} else {
				$ontvangen = '';
			}
			echo $div_open_item;
				echo form_label('Antwoord ontvangen', 'antwoord_ontvangen');
				$data = array(
				              'name'        => 'antwoord_ontvangen',
				              'id'			=> 'antwoord_ontvangen'.$edit,
				              'placeholder' => 'Antwoord ontvangen',
				              'maxlength'   => '100',
				              'Style'		=> 'width: 250px',
				              'class'		=> 'datepicker email_kabinet parlementaire_vragen '.$disabled,
				              'value'		=> $ontvangen
				            );		
				echo form_input($data);		
			echo $div_close;
			echo $div_open_item;
				echo form_label('Naar staf tegen', 'naar_staf_tegen');
				$data = array(
				              'name'        => 'naar_staf_tegen',
				              'id'			=> 'naar_staf_tegen'.$edit,
				              'placeholder' => 'Naar staf tegen',
				              'maxlength'   => '100',
				              'Style'		=> 'width: 250px',
				              'class'		=> 'datepicker email_kabinet kabinetsnotas parlementaire_vragen '.$disabled,
				              'value'		=> $dossier['naar_staf_tegen'],
				            );		
				echo form_input($data);		
			echo $div_close;
			
			echo $div_open_item;
				echo form_label('Herinnering op', 'herinnering_op');
				$data = array(
				              'name'        => 'herinnering_op',
				              'id'			=> 'herinnering_op'.$edit,
				              'placeholder' => 'Herinnering op',
				              'maxlength'   => '100',
				              'Style'		=> 'width: 250px',
				              'class'		=> 'datepicker email_kabinet parlementaire_vragen '.$disabled,
				              'value'		=> $dossier['herinnering_op'],
				            );		
				echo form_input($data);		
			echo $div_close;
			echo $div_open_item;
				echo form_label('Naar Kabinet tegen', 'uitgeschreven_op');
				$data = array(
				              'name'        => 'uitgeschreven_op',
				              'id'			=> 'uitgeschreven_op'.$edit,
				              'placeholder' => 'Naar Kabinet tegen',
				              'maxlength'   => '100',
				              'Style'		=> 'width: 250px',
				              'class'		=> 'datepicker email_kabinet parlementaire_vragen '.$disabled,
				              'value'		=> $dossier['uitgeschreven_op'],
				            );		
				echo form_input($data);		
			echo $div_close;
			echo $div_open_item;
				echo form_label('Datum naar Kabinet', 'datum_kabinet');
				$data = array(
				              'name'        => 'datum_kabinet',
				              'id'			=> 'datum_kabinet'.$edit,
				              'placeholder' => 'Datum naar Kabinet',
				              'maxlength'   => '100',
				              'Style'		=> 'width: 250px',
				              'class'		=> 'datepicker email_kabinet parlementaire_vragen '.$disabled, 
				              'value'		=>	$dossier['datum_kabinet'], 
				            );		
				echo form_input($data);		
			echo $div_close;
		echo $div_close;
		echo '<div></div>';}
	if (array_key_exists('secretariaat', $view)){
		echo '</div><div id="secretariaat" class="vtab" style="display:'.$view['secretariaat']['view'].'">';
		if (!user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){
		    $dossier['te_behandelen_door'] = $location;
		}
		$te_behandelen_door = explode(', ', $dossier['te_behandelen_door']);
		if (array_key_exists('doorsturen_naar', $dossier) && $dossier['doorsturen_naar']!= ''){
			echo '<p>Dit wordt enkel getoond bij oude dossiers: doorgestuurd naar:<br /><strong>'.$dossier['doorsturen_naar'].'</strong></p>';
		}
		foreach ($te_behandelen_door as $key => $value) {
			echo '<div style="margin-bottom: 10px">';
			if (user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){
				echo $div_open_item;
					echo form_label('Afdeling', 'secretariaat_locatie');
					echo '<div class="input_view email_kabinet kabinetsnotas parlementaire_vragen"style="width: 400px; margin-left: 160px;">'.$value.'</div>';
				echo $div_close;
			} 
			echo '<div class="doorsturen_naar form-item">';
				echo form_label('Doorsturen naar', 'secretariaat['.$value.'][doorgestuurd_naar]');		
				/*$doorgestuurd_naar[''] = '--selecteren--';		
				asort($doorgestuurd_naar);
				cho form_dropdown('doorgestuurd_naar', $doorgestuurd_naar, $dossier['doorgestuurd_naar'], 'class="wegen email_kabinet kabinetsnotas parlementaire_vragen"');*/
				
				if(array_key_exists('secretariaat', $dossier))	{
					echo '<input type=hidden name="secretariaat['.$value.'][doorsturen_naar]" value="'.$dossier['secretariaat'][$value]['doorsturen_naar'].'" class="hidden_doorsturen">';
					echo '<input type=hidden name="secretariaat['.$value.'][doorsturen_naar_namen]" value="'.$dossier['secretariaat'][$value]['doorsturen_naar_namen'].'" class="hidden_doorsturen_namen">';
					if($dossier['secretariaat'][$value]['doorsturen_naar_namen'] != ''){
						$doorsturen_naar_str = $dossier['secretariaat'][$value]['doorsturen_naar_namen'] ;
					} else {
						$doorsturen_naar_str = 'Doorsturen naar';
					}	
				} else {
					echo '<input type=hidden name="secretariaat['.$value.'][doorsturen_naar]" value="" class="hidden_doorsturen">';
					echo '<input type=hidden name="secretariaat['.$value.'][doorsturen_naar_namen]" value="" class="hidden_doorsturen_namen">';
					$doorsturen_naar_str = 'Doorsturen naar';
				}
				echo '<div class="input_selector doorgestuurd_naar_input  email_kabinet kabinetsnotas parlementaire_vragen '.$disabledSec.'"><a>'.$doorsturen_naar_str.'</a></div>';
			echo $div_close;
			echo $div_open_item;
				if(array_key_exists('secretariaat', $dossier))	{
					if($dossier['secretariaat'][$value]['datum_secretariaat'] == '' || $dossier['secretariaat'][$value]['datum_secretariaat']->sec == 0){
						$datum_secretariaat = '';
					} else {
						$datum_secretariaat = date('d-m-Y', $dossier['secretariaat'][$value]['datum_secretariaat']->sec);
					}	
				} else {
					$datum_secretariaat = '';
				} 
				echo form_label('Datum naar Secretariaat', 'secretariaat['.$value.'][datum_secretariaat]');
				$data = array(
					         'name'         => 'secretariaat['.$value.'][datum_secretariaat]',
					         'id'			=> 'datum_secretariaat_'.$value.$edit,
					         'placeholder'  => 'Naar Secretariaat tegen',
					         'maxlength'    => '100',
					         'size'         => '30',
					         'Style'		=> 'width: 400px; margin-left: 160px;',
					         'class'		=> 'datepicker email_kabinet parlementaire_vragen '.$disabledSec, 
					         'value'		=>	$datum_secretariaat, 
					    );		
				echo form_input($data);		
			echo $div_close;
			echo $div_open_item;
				if(array_key_exists('secretariaat', $dossier))	{
					if($dossier['secretariaat'][$value]['datum_antwoord_binnen'] == '' || $dossier['secretariaat'][$value]['datum_antwoord_binnen']->sec == 0){
						$datum_antwoord_binnen = '';
					} else {
						$datum_antwoord_binnen = date('d-m-Y', $dossier['secretariaat'][$value]['datum_antwoord_binnen']->sec);
					}	
				} else {
					$datum_antwoord_binnen = '';
				} 
				echo form_label('Antwoord naar stafdienst', 'secretariaat['.$value.'][datum_antwoord_binnen]');
				$data = array(
					         'name'         => 'secretariaat['.$value.'][datum_antwoord_binnen]',
					         'id'			=> 'datum_antwoord_binnen_'.$value.$edit,
					         'placeholder'  => 'Antwoord naar stafdienst',
					         'maxlength'    => '100',
					         'size'         => '30',
					         'Style'		=> 'width: 400px; margin-left: 160px;',
					         'class'		=> 'datepicker email_kabinet parlementaire_vragen '.$disabledSec, 
					         'value'		=>	$datum_antwoord_binnen, 
					    );		
				echo form_input($data);	
				echo '<div style="margin-left:160px; width: 400px; font-size: 9px">Enkel de datum invullen wanneer het effectief naar de stafdienst is verstuurd, niet wanneer het naar de stafdienst moet. Die informatie staat onder het tabblad "stafdienst".</div>';
			echo $div_close;
			echo $div_open_item;	
				if(array_key_exists('_id', $dossier)){				
					$secretariaat_opmerking = 'secretariaat_bewerken_opmerking_'.$value;
				} else {
					$secretariaat_opmerking = 'secretariaat_opmerking_'.$value;
				}
				if(array_key_exists('secretariaat', $dossier))	{
					$secretariaat_opmerking = $dossier['secretariaat'][$value]['secretariaat_opmerking'];	
				} else {
					$secretariaat_opmerking = '';
				} 
				echo form_label('Opmerkingen', 'secretariaat['.$value.'][secretariaat_opmerking]');				
				$data = array(
				              'name'        => 'secretariaat['.$value.'][secretariaat_opmerking]',
				              'id'			=> $secretariaat_opmerking,
				              'placeholder' => 'Opmerking',
				              'rows'		=> '10',
				              'cols'		=> '42',			              
				              'Style'		=> 'width: 400px; margin-left: 160px;',
				              'value'		=>	$secretariaat_opmerking,
				              'class' 		=> 'parlementaire_vragen email_kabinet '.$disabledSec        
				            );	
				echo form_textarea($data);	
			echo $div_close;
			echo '</div>';
		}
	}
	echo '</div><div id="bijlagen" class="vtab" style="display:'.$view['bijlagen']['view'].';max-width:800px;">';
	if(array_key_exists('_id', $dossier)){
		$bijlage = 'bijlage_bewerken';
		$bijlage_opmerking = 'bijlage_bewerken_opmerking';
		$bijlage_versie = 'bijlage_bewerken_versie';
	} else {
		$bijlage = 'bijlage';
		$bijlage_opmerking = 'bijlage_opmerking';
		$bijlage_versie = 'bijlage_versie';
	}
	echo $div_open_item;	
		if (!user_access(array('Administrators', 'Stafdienst', 'Secretariaat', 'PCOEVT'))){
			echo '<H4>Informatie Secretariaat</h4>';
			if ($dossier['secretariaat'][$user['location']['afkorting']]["datum_secretariaat"]!='') {
				$dossier['secretariaat'][$user['location']['afkorting']]["datum_secretariaat"] = date('d-m-Y', $dossier['secretariaat'][$user['location']['afkorting']]["datum_secretariaat"]->sec);
			} else {
				$dossier['secretariaat'][$user['location']['afkorting']]["datum_secretariaat"] = 'Er is nog geen datum ingevuld.';
			}
			echo '<p style="font-size:larger"><strong>Datum naar Secretariaat:</strong> '.$dossier['secretariaat'][$user['location']['afkorting']]["datum_secretariaat"].'</p>';
			if ($dossier['secretariaat'][$user['location']['afkorting']]["secretariaat_opmerking"]!='') {
				echo '<p><strong style="font-size:larger">Opmerking</strong>: '.$dossier['secretariaat'][$user['location']['afkorting']]["secretariaat_opmerking"].'</p>';
			}
		}
		echo "<h4 for='bijlage' style=''>Bijlage toevoegen</h4>";
		if (!user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){
			$loc = $location;
		} else {
			$loc = 'Algemeen';
		}
		echo '<input type=hidden value="'.$loc.'" id=bijlage_locatie name="bijlage_locatie" />';
		$data = array(
		   'name'        => 'bijlage',
		   'id'			 => $bijlage,
		   'placeholder' => 'Bijlage',
		   'Style'		 => 'clear:both; margin-left:0px; width: 230px; display:inline-block',
		   'class' 		 => 'parlementaire_vragen email_kabinet bijlage',
		);
		echo form_upload($data);
		/*$versie = array('' => '--selecteer versie--', 'goedkeuren' => 'Goed te keuren', 'definitieve_versie' => 'Definitieve versie');
		echo form_dropdown($bijlage_versie, $versie, '', 'class="parlementaire_vragen" style="display:inline-block; width: 150px;clear:both; margin-left:0px;" id="'.$bijlage_versie.'"');*/		
	echo $div_close;
	echo $div_open_item;				
	   $data = array(
	       'name'        => 'bijlage_opmerking',
		   'id'			 => $bijlage_opmerking,
		   'placeholder' => 'Opmerking',
		   'rows'		 => '2',
		   'cols'		 => '42',			              
		   'Style'		 => 'clear:both; margin-left:0px',
		   'class' 		 => 'parlementaire_vragen email_kabinet'          
		);	
		echo form_textarea($data);	
		echo $div_close;
		echo '<div style="display: none; margin-left:0px" class="bijlage_buttons">';
		echo form_button(array('id' => 'bijlage_annuleren', 'content' => 'Annuleren', 'name' => 'bijlage_annuleren', 'style' => 'padding:4px 6px;line-height: 1; font-size:11px', 'class' => 'btn btn-link btn-sm bijlage_annuleren'));
		echo form_button(array('id' => 'bijlage_opslaan', 'content' => 'Bijlage toevoegen', 'name' => 'bijlage_opslaan', 'style' => 'margin-left: 4px; padding:4px 6px;line-height: 1; font-size:11px' , 'class' => 'btn btn-primary btn-sm bijlage_opslaan'));
		echo $div_close;
		$bijlage_hidden = '';
		if (array_key_exists('bijlagen', $dossier)){
			if ($dossier['bijlagen'] != '') {
				foreach ($dossier['bijlagen'] as $key => $bijlage) {
					if($bijlage_hidden == ''){
						$bijlage_hidden = $bijlage['_id'];
					} else {
						$bijlage_hidden .= ', '.$bijlage['_id'];
					}					
				}
			}				  	
		}
		echo form_hidden('bijlagen',$bijlage_hidden);
		echo form_hidden('remove_bijlagen');
		echo '<div id="progressbar" style="margin-left: 100px;"></div>';
		echo '<h4 style="">Bijlagen</h4>';
		?>
		<table id="bijlages" style="color: #222; font-size: 12px; min-width: 300px;">
			<thead>
				<tr>
					<th width="25%">Bestand</th>
					<th width="37%">Opmerking</th>
					<th width="10%">Gebruiker</th>
					<th width="18%">Datum</th>
					<th></th>
				</tr>
			</thead>
		<?php if (array_key_exists('bijlagen', $dossier))
			{
				if ($dossier['bijlagen'] != '') {
					if (user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){
						if (array_key_exists('locatie', $bijlage)) {
							foreach ($dossier['bijlagen'] as $key => $bijlage) {
								$locatie[$key] = $bijlage['locatie'];
								$bijlage_date[$key] = $bijlage['date'];
							} 
							array_multisort($locatie, SORT_ASC, $bijlage_date, SORT_DESC, $dossier['bijlagen']);
						} else {
							foreach ($dossier['bijlagen'] as $key => $bijlage) {
								$bijlage_date[$key] = $bijlage['date'];
								$name[$key] = $bijlage['name'];							
							} 
							array_multisort($bijlage_date, SORT_DESC, $name, SORT_ASC, $dossier['bijlagen']);
						}
					} else {
						foreach ($dossier['bijlagen'] as $key => $bijlage) {
							$bijlage_date[$key] = $bijlage['date'];
							$name[$key] = $bijlage['name'];							
						} 
						array_multisort($bijlage_date, SORT_DESC, $name, SORT_ASC, $dossier['bijlagen']);
					}
					$bijlage_locatie = '1';
					foreach ($dossier['bijlagen'] as $key => $bijlage) {
						$bijlage_tonen = TRUE;						
						if (array_key_exists('locatie', $bijlage)) {
							if (!user_access(array('Administrators', 'Stafdienst', 'PCOEVT')) && ($bijlage['locatie'] != 'Algemeen' && $bijlage['locatie'] != $location && $bijlage['locatie'] != null)){
								$bijlage_tonen = FALSE;
							}
						}
						if ($bijlage_tonen) {						
							if (user_access(array('Administrators', 'Stafdienst'))){
								if (array_key_exists('locatie', $bijlage)) {
									if ($bijlage['locatie'] != $bijlage_locatie){
										$bijlage_locatie = $bijlage['locatie'];
										if ($bijlage_locatie == 'Algemeen' || $bijlage_locatie == ''){
											echo '<tr><td colspan=6 style="font-weight:bold">Bijlagen Stafdienst</td></tr>';
										} else {
											echo '<tr><td colspan=6 style="font-weight:bold">Bijlagen '.$bijlage_locatie.'</td></tr>';
										}
									}
								} else {
									echo '<tr><td colspan=6 style="font-weight:bold">Bijlagen Stafdienst</td></tr>';
								}
							}
						?>
						<tr>
							<td>
								<?php if (array_key_exists('name', $bijlage)){?>
								<a href="/secretariaat/index.php/dossier/dossiers/files/<?php echo $bijlage['_id']?>"><?php echo $bijlage['name']?>&nbsp;&nbsp;</a>
								<?php } ?>
							</td>
							<td>
								<?php if (array_key_exists('opmerking', $bijlage)){?>
									<a href="/secretariaat/index.php/dossier/dossiers/files/<?php echo $bijlage['_id']?>"><?php echo $bijlage['opmerking']?>&nbsp;&nbsp;</a>
								<?php } ?>
							</td>
							<td>
								<?php if (array_key_exists('user', $bijlage)){?>
									<a href="/secretariaat/index.php/dossier/dossiers/files/<?php echo $bijlage['_id']?>"><?php echo $bijlage['user']?>&nbsp;&nbsp;</a>
								<?php } ?>
							</td>
							<td>
								<?php if (array_key_exists('date', $bijlage)){?>
									<a href="/secretariaat/index.php/dossier/dossiers/files/<?php echo $bijlage['_id']?>"><?php echo date('d-m-Y H:i', $bijlage['date']->sec)?>&nbsp;&nbsp;</a>
								<?php } ?>
							</td>
							<td>
								<span class="glyphicon glyphicon-remove-circle remove_bijlage" id="remove_<?php echo $bijlage['_id']?>"></span>
							</td>
						</tr>				
			<?php }}	?>			
	<?php } } 
	echo '</table></div>';
	?>
	<div class="data_modal_te_behandelen data_modal_hide">
		<div>
			<button class="btn btn-default btn-xs select-all" style=margin-bottom:15px>Alle diensten selecteren</button>
			<button class="btn btn-default btn-xs deselect-all" style=margin-bottom:15px>Selectie ongedaan maken</button>
		</div>
		<div class="" data-toggle="buttons">
		    <?php 	    	
		       	asort($te_behandelen);
				foreach ($te_behandelen as $key => $value) {
					$test = FALSE;	
					$active = 'btn-default';
					if(substr_count($dossier['te_behandelen_door'], $te_behandelen[$key])>=1){
						$test = TRUE;
						$active = 'btn-primary active';
					} 
					echo '<label class="btn '.$active.' btn-xs btn-group-label" style="margin-right: 10px;">';					
					echo form_checkbox('te_behandelen_door_chkbx['.$key.']', $te_behandelen[$key], $test);				
					echo $te_behandelen[$key];
					echo '</label>';
				}			
			?>
		</div>
		<?php 
			if(array_key_exists('_id', $dossier)){				
				$type_form = 'bewerken_formulier';
			} else {
				$type_form = 'inkomende_formulier';
			}
		?>
		<input type="hidden" id="type_form" value="<?=$type_form?>">
	</div>
	<div class="data_modal_notificaties data_modal_hide">
		<p>Klik de personen of locaties aan waarnaar u een notificatie wil sturen dat het dossier is aangepast.<br />Indien u geen notificaties wil sturen, selecteer niemand en klik op 'Notificaties versturen'</p>
		<div style="display: inline-block; width: 49%; min-width: 390px;">
			<?php if (user_access(array('Administrators', 'Secretariaat', 'PCOEVT'))) {?>
				<div class="stafdienst" data-toggle="buttons">
					<h4>Stafdienst of gecoördineerd door PCO of EVT</h4>
				    <?php 	    	
				       echo '<label class="btn btn-default btn-xs btn-group-label" style="margin-right: 10px;">';					
					   echo form_checkbox('notificatie_chbx[Stafdienst]', 'Stafdienst', FALSE);		
					   echo 'Stafdienst</label>';	
                        echo '<label class="btn btn-default btn-xs btn-group-label" style="margin-right: 10px;">';                  
                       echo form_checkbox('notificatie_chbx[EVT]', 'EVT', FALSE);     
                       echo 'EVT</label>';
                        echo '<label class="btn btn-default btn-xs btn-group-label" style="margin-right: 10px;">';                  
                       echo form_checkbox('notificatie_chbx[PCO]', 'PCO', FALSE);     
                       echo 'PCO</label>';				   
					?>
				</div>
			
			<?php } 
			if (user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))) {?>
				<div class="secretariaat" data-toggle="buttons" style="clear:both;">
					<h4>Secretariaat</h4>
				    <div>
						<button class="btn btn-default btn-xs select-all-d" style=margin-bottom:15px>Iedereen selecteren</button>
						<button class="btn btn-default btn-xs deselect-all-d" style=margin-bottom:15px>Selectie ongedaan maken</button>
					</div>
				</div>
			<?php } 
			if (user_access(array('Secretariaat', 'PCOEVT', 'Administrators'))) { ?>
			<div class="dossierbeheerders" data-toggle="buttons">			
				<h4>Dossierbeheerders</h4>
				<div>
					<button class="btn btn-default btn-xs select-all-d" style=margin-bottom:15px>Iedereen selecteren</button>
					<button class="btn btn-default btn-xs deselect-all-d" style=margin-bottom:15px>Selectie ongedaan maken</button>
				</div>			
			</div>
			<?php } 
			if (user_access(array('Dossierbeheerder')) && !user_access(array('Stafdienst', 'Secretariaat'))) { ?>
			<div class="stafdienst" data-toggle="buttons">
				<h4>Secretariaat</h4>
				<?php 	    	
					echo '<label class="btn btn-default btn-xs btn-group-label" style="margin-right: 10px;">';					
					echo form_checkbox('notificatie_chbx['.$location.']', $location, FALSE);		
					echo $location.'</label>';
				?>
			</div>
			<?php } ?>
			<input type=hidden value="<?php echo $loc ?>" id=location_personeel />
		</div>
		<div style="display: inline-block; width: 49%; vertical-align: top">
			<?php 
			echo $div_open_item;	
				echo '<h4>Opmerking</h4>';
				echo '<p>Deze opmerking wordt in de verstuurde mail verwerkt.</p>';
				if(array_key_exists('_id', $dossier)){				
					$opmerking = 'bewerken_opmerking_notificatie';
				} else {
					$opmerking = 'opmerking_notificatie';
				}
				echo '<div contenteditable="true" id="'.$opmerking.'" class="parlementaire_vragen email_kabinet opmerking'.$disabled.'">Opmerking</div>';	
				echo '<div style="font-size: 70%; font-style:italic">U kan text vet zetten door deze te selecteren en ctrl+b in te duwen op uw keyboard.<br /> U kan de text cursief zetten door deze te selecteren en dan ctrl+i in te duwen op uw keyboard.</div>';
			echo $div_close;
			?>	
			
		</div>
	</div>
	<div class="data_modal_doorsturen_naar data_modal_hide">
		<div>
			<button class="btn btn-default btn-xs select-all" style=margin-bottom:15px>Alle diensten selecteren</button>
			<button class="btn btn-default btn-xs deselect-all" style=margin-bottom:15px>Selectie ongedaan maken</button>
		</div>
		<div class="">
		    <?php 
		    if ($dossier['te_behandelen_door'] != ''){
		    	if (!user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){
		    	 	$dossier['te_behandelen_door'] = $location;
					$adm = '';
				} elseif (user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){
					$adm = '_adm';
				}
		       	$users = $doorgestuurd_naar['users'];
				$districten = $doorgestuurd_naar['districten'];
				$te_behandelen_door = explode(', ', $dossier['te_behandelen_door']);
				if (array_key_exists('secretariaat', $dossier)){
					$doorsturen_naar = $dossier['secretariaat'];
				}
				$checked = FALSE;
				$active = ' btn-default ' ;
				foreach ($te_behandelen_door as $key => $value) {
					echo '<div id="personeel_'.$value.'" class="personeel'.$adm.'" data-toggle="buttons"><h4>'.$value.'</h4>';
					foreach ($districten[$value] as $dkey => $district){
						if (array_key_exists('secretariaat', $dossier)){
							if(substr_count($doorsturen_naar[$value]['doorsturen_naar'], $district['district'])>=1){
								$checked = TRUE;
								$active = ' btn-primary active ';
							}
						}
						echo '<label class="btn'.$active.'btn-xs btn-group-label" style="margin-right: 10px; margin-bottom: 10px">';			
						echo form_checkbox('doorgestuurd_naar_chbx["'.$district['district'].'"]', $district['district'], $checked);
						echo $district['district'];
						echo '</label>';
						$checked = FALSE;
						$active = ' btn-default ' ;
					}
					echo '<br />';
					foreach ($users[$value] as $dkey => $user){
						if (array_key_exists('secretariaat', $dossier)){
							if((substr_count($doorsturen_naar[$value]['doorsturen_naar'], $user['first_name'].' '.$user['name'])>=1) || (substr_count($doorsturen_naar[$value]['doorsturen_naar'], $user['username'])>=1)){
								$checked = TRUE;
								$active = ' btn-primary active ';
							}
						}
						echo '<label class="btn'.$active.'btn-xs btn-group-label" style="margin-right: 10px; margin-bottom: 10px">';			
						echo form_checkbox('doorgestuurd_naar_chbx["'.$user['username'].'"]', $user['username'], $checked);
						echo $user['first_name'].' '.$user['name'];
						echo '</label>';
						$checked = FALSE;
						$active = ' btn-default ' ;
					}
					echo '</div>';
				}			
			}
			if (!user_access(array('Administrators', 'Stafdienst'))){
			  $loc = $location;
			} else {
			  $loc = '';
			}
			?>
			<input type=hidden value="<?php echo $loc ?>" id=location_personeel />
			<?php 
				if(array_key_exists('_id', $dossier)){				
					$type_form = 'bewerken_formulier';
				} else {
					$type_form = 'inkomende_formulier';
				}
			?>
			<input type="hidden" id="type_form" value="<?=$type_form?>">
		</div>
	</div>
<?php 	
	echo '</div><div id="formInfo">* Verplicht in te vullen velden.</div>';
	echo '<div id="form_messages">Fouten: <ul id="messages"></ul></div>';
	echo form_close(); 
?>




