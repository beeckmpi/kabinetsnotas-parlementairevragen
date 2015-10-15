<div id=dossiers>
<?php 
	if (array_key_exists(0, $dossier_list)){
		foreach ($dossier_list as $key => $value){
			$id = $dossier_list[$key]['_id'];
			$nummer_pv = '';
			if($dossier_list[$key]['nummer_pv'] != ''){
				$nummer_pv = $dossier_list[$key]['nummer_pv'];
			}
			if (array_key_exists('nummer_kab', $dossier_list[$key])){
				if($dossier_list[$key]['nummer_kab'] != ''){
					$nummer_pv = $dossier_list[$key]['nummer_kab'];
				}
			} 
			$aan = $dossier_list[$key]['te_behandelen_door'];
			if($dossier_list[$key]['datum_melding']->sec == 0){
				$dossier_list[$key]['datum_melding'] = '';
			} else {
				$dossier_list[$key]['datum_melding'] = date('d-m-Y', $dossier_list[$key]['datum_melding']->sec);
			}				
			if($dossier_list[$key]['herinnering_op']->sec == 0){
				$herinnering_op = '';
			} else {
				$herinnering_op = date('d-m-Y', $dossier_list[$key]['herinnering_op']->sec);
			}
			if (is_object($dossier_list[$key]['naar_staf_tegen'])){
				if($dossier_list[$key]['naar_staf_tegen']->sec == 0){
					$naar_staf_tegen = '';
				} else {
					$naar_staf_tegen = date('d-m-Y', $dossier_list[$key]['naar_staf_tegen']->sec);
				}
			} else {
				if ($dossier_list[$key]['naar_staf_tegen'] != ''){
					$naar_staf_tegen = $dossier_list[$key]['naar_staf_tegen'];
				}
			}
			if($dossier_list[$key]['uitgeschreven_op']->sec == 0){
				$uitgeschreven_op = '';
			} else {
				$uitgeschreven_op = date('d-m-Y', $dossier_list[$key]['uitgeschreven_op']->sec);
			}
			
			if (strlen($dossier_list[$key]['onderwerp'])>90){
				$onderwerp = substr($dossier_list[$key]['onderwerp'], 0, 90).'...';
			} else {
				$onderwerp = $dossier_list[$key]['onderwerp'];
			}	
			if ($dossier_list[$key]['omschrijving'] != '' && array_key_exists('omschrijving', $dossier_list[$key])){
				if (strlen($dossier_list[$key]['omschrijving'])>500){
					$omschrijving = substr($dossier_list[$key]['omschrijving'], 0, 500).'...';
				} else if ($dossier_list[$key]['omschrijving'] == '<p>Omschrijving</p>') {
					$omschrijving = '';
				} else {
					$omschrijving = $dossier_list[$key]['omschrijving'];
				}
			} else {
				$omschrijving = '';
			} 
			$van = '';
			if (array_key_exists('parlementarier', $dossier_list[$key])){
				if ($dossier_list[$key]['type'] == 'parlementaire_vragen'){
					$van = '<strong>Parlementariër:</strong> '.$dossier_list[$key]['parlementarier'];
				} 
			} 
			if (array_key_exists('referentie_kab', $dossier_list[$key])){
				if ($dossier_list[$key]['type'] == 'email_kabinet'){
					$van = '<strong>Kabinet:</strong> '.$dossier_list[$key]['referentie_kab'];
				}
			}
			if(array_key_exists('beantwoord', $dossier_list[$key])){
				if($dossier_list[$key]['beantwoord'] != "true"){
					$beantwoord = anchor('ajax/dossiers/beantwoord/'.$dossier_list[$key]['_id'],'&nbsp;', 'class="onbeantwoord btn glyphicon glyphicon-thumbs-down '.$dossier_list[$key]['_id'].'_b" title="de pv is niet beantwoord" style="font-size:14px; padding: 2px 5px"');
					$beantwoord_class= 'onbeantwoord';
					if(array_key_exists('secretariaat', $dossier_list[$key])){
						if (array_key_exists($location, $dossier_list[$key]['secretariaat'])){
							if (array_key_exists('datum_antwoord_binnen', $dossier_list[$key]['secretariaat'][$location])){
								if ($dossier_list[$key]['secretariaat'][$location]['datum_antwoord_binnen'] != ''){
									$beantwoord_class = 'secretariaat_beantwoord';
								}
							}
						}
					}
				} else {
					$beantwoord = anchor('ajax/dossiers/beantwoord/'.$dossier_list[$key]['_id'],'&nbsp;', 'class="beantwoord btn glyphicon glyphicon-thumbs-up '.$dossier_list[$key]['_id'].'_b" title="de pv is beantwoord" style="font-size:14px; padding: 2px 5px"');
					$beantwoord_class= 'beantwoord';					
				}
			} else {
				$beantwoord = anchor('ajax/dossiers/beantwoord/'.$dossier_list[$key]['_id'],'&nbsp;', 'class="onbeantwoord btn glyphicon glyphicon-thumbs-down '.$dossier_list[$key]['_id'].'_b" title="de pv is niet beantwoord" style="font-size:14px; padding: 2px 5px"');
				$beantwoord_class= 'onbeantwoord';
			} 
			if (!user_access(array('Administrators', 'Stafdienst'))){ 
				$beantwoord = '';
			} 
			if ($dossier_list[$key]['type'] == 'parlementaire_vragen'){
				$voorvoegsel = 'PV';
			}else {
				$voorvoegsel = '';
			}
			
			$number = 0; 
			if (array_key_exists('notificaties', $dossier_list[$key])){
				foreach ($dossier_list[$key]['notificaties'] as $key => $notificatie){
					if($notificatie['aan']!=null){
						if (in_array($user_data['location']['afkorting'], $notificatie['aan']) || user_access(array('Administrators')) || ($user_data['username'] == $notificatie['username'])){
							$number++;
						}
					}
				}
			} 
	?>
	
			<article class=<?=$beantwoord_class?> style="position: relative">				
				<div style="position:absolute; right: 5px; top: 5px;"><?=$beantwoord?></div>
				<a href="<?php echo site_url()?>dossier/dossiers/view/<?=$id?>" class="link">
					<?php if ($number != 0){?>
					<div style="position:absolute; right: 50px; top: 10px;">
						<div style="font-size: 14px;margin-left:5px"><?=$number?>  <i class="glyphicon glyphicon-comment"></i></div>			
					</div>
					<?php }?>
					<div><strong><?=$voorvoegsel?> <?=$nummer_pv?>:</strong>&nbsp;<?=$onderwerp?></div>					
					<div><?=$van?></div>
					<ul class="list_details">
						<li><strong>A<span class="hiddenText">ntwoord </span>D<span class="hiddenText">oor</span>:</strong>&nbsp;<?=$aan?></li>
						<li><strong><span class="hiddenText">Te </span>H<span class="hiddenText">erinneren </span>O<span class="hiddenText">p</span>:</strong>&nbsp;<?=$herinnering_op?></li>
						<li><strong>N<span class="hiddenText">aar </span>S<span class="hiddenText">tafdienst</span>:</strong>&nbsp;<?=$naar_staf_tegen?></li>
						<li><strong>N<span class="hiddenText">aar </span>K<span class="hiddenText">abinet</span>:</strong>&nbsp;<?=$uitgeschreven_op?></li>
					</ul>
					<div><?=$omschrijving?></div>
				</a>
			</article>
	<?php
		}
	} else {
		echo '<h5>Geen inhoud gevonden die aan de filter voorwaarden voldoet.</h5>Het is aan te raden de filter (rechts) aan te passen.';
	} 
	?>
</div>
		