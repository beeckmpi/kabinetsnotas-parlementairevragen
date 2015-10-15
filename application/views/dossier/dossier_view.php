<?php
	if (!isset($dossier)) {
		echo '<div id="view_dossier"><H4>Het dossier werd niet gevonden</h4> <a href="#" class="close_w" style="font-weight: bold">Terug naar dossiers_lijst</a> <div>';
	} else {
		if($dossier['datum_melding']->sec == 0){
			$dossier['datum_melding'] = '&nbsp;';
		} else {
			$dossier['datum_melding'] = date('d-m-Y', $dossier['datum_melding']->sec);
		}
		if(array_key_exists('datum_dtg', $dossier)){
			if($dossier['datum_dtg']->sec == 0){
				$dossier['datum_dtg'] = '&nbsp;';
			} else {
				$dossier['datum_dtg'] = date('d-m-Y', $dossier['datum_dtg']->sec);
			}
		}
		if(array_key_exists('datum_kabinet', $dossier)){
			if($dossier['datum_kabinet'] != ""){
				if($dossier['datum_kabinet']->sec == 0){
					$dossier['datum_kabinet'] = '&nbsp;';
				} else {
					$dossier['datum_kabinet'] = date('d-m-Y', $dossier['datum_kabinet']->sec);
				}
			}
		} 		
		if(array_key_exists('naar_staf_tegen', $dossier)){
			if (is_object($dossier['naar_staf_tegen'])){
				if($dossier['naar_staf_tegen']->sec == 0){
					$dossier['naar_staf_tegen'] = '&nbsp;';
				} else {
					$dossier['naar_staf_tegen'] = date('d-m-Y', $dossier['naar_staf_tegen']->sec);
				}
			} 
		}
		if (!user_access(array('Adminstrators', 'Stafdienst', 'Secretariaat', 'PCOEVT'))){
			if ($dossier['secretariaat'][$user['location']['afkorting']]["datum_secretariaat"]!='') {
				$dossier['secretariaat'][$user['location']['afkorting']]["datum_secretariaat"] = date('d-m-Y', $dossier['secretariaat'][$user['location']['afkorting']]["datum_secretariaat"]->sec);
			}
		}
		if($dossier['uitgeschreven_op']->sec == 0){
			$dossier['uitgeschreven_op'] = '&nbsp;';
		} else {
			$dossier['uitgeschreven_op'] = date('d-m-Y', $dossier['uitgeschreven_op']->sec);
		}
		if($dossier['herinnering_op']->sec == 0){
			$dossier['herinnering_op'] = '&nbsp;';
		} else {
			$dossier['herinnering_op'] = date('d-m-Y', $dossier['herinnering_op']->sec);
		} 
		if(array_key_exists('terug_herinnerd_op', $dossier)){
			if($dossier['terug_herinnerd_op']->sec == 0){
				$dossier['terug_herinnerd_op'] = '&nbsp;';
			} else {
				$dossier['terug_herinnerd_op'] = date('d-m-Y', $dossier['terug_herinnerd_op']->sec);
			}
		}	
		if(array_key_exists('antwoord_ontvangen', $dossier)){
			if (is_object($dossier['antwoord_ontvangen'])){
				if($dossier['antwoord_ontvangen']->sec == 0){
					$dossier['antwoord_ontvangen'] = '&nbsp;';
				} else {
					$dossier['antwoord_ontvangen'] = date('d-m-Y', $dossier['antwoord_ontvangen']->sec);
				}
			}
		} else {
			$dossier['antwoord_ontvangen'] = '';
		}
		
		if(!array_key_exists('kruispunt', $dossier)){
			$dossier['kruispunt'] = '&nbsp;';
		}

		if(!array_key_exists('gecoordineerd_door', $dossier)){
			$dossier['gecoordineerd_door'] = 'Stafdienst';
		}


		if(!array_key_exists('verduidelijking', $dossier)){
			$dossier['verduidelijking'] = '&nbsp;';
		}
		if(!array_key_exists('terug_herinnerd_op', $dossier)){
			$dossier['terug_herinnerd_op'] = '&nbsp;';
		}
		
		$jaar = substr($dossier['dossiernummer'], 0, 4);
		$dossier_nr = substr($dossier['dossiernummer'], 4, 8);
		if(array_key_exists('_id', $prev)){
			$prev_c = 'i_rm_p';
			$prev_l = '/secretariaat/index.php/dossier/dossiers/view/'.$prev['_id'];
		} else {
			$prev_c = 'i_rm_p_na';
			$prev_l = '#';
		}
		if(array_key_exists('_id', $next)){
			$next_c = 'i_rm_n';
			$next_l = '/secretariaat/index.php/dossier/dossiers/view/'.$next['_id'];
		} else {
			$next_c = 'i_rm_n_na';
			$next_l = '#';
		}
		$type = array(
			' ' => '--selecteer--',
			'email_kabinet' => 'KAB',
			'parlementaire_vragen' => 'PV'
			);	
		if(array_key_exists('beantwoord', $dossier)){ 
			if ($dossier['beantwoord'] == 'true') {
				$beantwoord_class= "beantwoord";
			} else {
				$beantwoord_class= "onbeantwoord";
				if(array_key_exists('secretariaat', $dossier)){
					if (array_key_exists($user['location']['afkorting'], $dossier['secretariaat'])){
						if (array_key_exists('datum_antwoord_binnen', $dossier['secretariaat'][$user['location']['afkorting']])){
							if ($dossier['secretariaat'][$user['location']['afkorting']]['datum_antwoord_binnen'] != ''){
								$beantwoord_class = 'secretariaat_beantwoord';
							}
						}
					}
				}
			}
		} else {
			$beantwoord_class= "onbeantwoord";
		}
		?>	
		

<div id="view_dossier">
	<div class=<?=$beantwoord_class?>>
	<h4>
		<?php if ($dossier['type'] != 'parlementaire_vragen'){ 
			if(array_key_exists('nummer_kab', $dossier) && $dossier['nummer_kab'] != ''){?>
				<?php echo $dossier['nummer_kab']?>
		<?php }} else { ?>		
			PV: <?php echo $dossier['nummer_pv']?>
		<?php } 
		if (user_access(array('Administrators', 'Stafdienst'))){ 
			if(array_key_exists('beantwoord', $dossier)){ 
				if ($dossier['beantwoord'] == 'true') { ?>
					<div style="position:absolute; right: 63px; top: 6px">
						<a class="glyphicon beantwoord glyphicon-thumbs-up" href="/secretariaat/index.php/ajax/dossiers/beantwoord/<?php echo $dossier['_id']?>" title="de pv is beantwoord"></a>
					</div>
			<?php } else { ?>
				<div style="position:absolute; right: 63px; top: 6px">
					<a class="glyphicon onbeantwoord glyphicon-thumbs-down" href="/secretariaat/index.php/ajax/dossiers/beantwoord/<?php echo $dossier['_id']?>" title="de pv is niet beantwoord"></a>
				</div>
			<?php }} else { ?>
				<div style="position:absolute; right: 63px; top: 6px">
					<a class="glyphicon onbeantwoord glyphicon-thumbs-down" href="/secretariaat/index.php/ajax/dossiers/beantwoord/<?php echo $dossier['_id']?>" title="de pv is niet beantwoord"></a>
				</div>			
		<?php }} ?>
		<div style="position:absolute; right: 10px; top: 18px">
			<a class="close_w" href="/secretariaat/index.php/dossier/dossiers/index" title="Volgend bericht (Ctrl+.)" id="rd_close">
				<div class="g_close" alt="">&nbsp;</div>
			</a>
		</div>
		<div style="position:absolute; right: 30px; top: 16px">
			<a href="<?php echo $next_l;?>" class="link_i" title="Volgend bericht" id="rd_next">
				<div class="<?php echo $next_c;?>" alt="">&nbsp;</div>
			</a>
		</div>
		<div style="position:absolute; right: 50px; top: 16px">
			<a href="<?php echo $prev_l;?>" class="link_i" title="Vorig bericht" id="rd_prev">
				<div class="<?php echo $prev_c;?>" alt="">&nbsp;</div>
			</a>
		</div>
		<div style="position:absolute; right: 100px; top: 11px;">
			<a href="#filter" class="link_i_o" id="filter_box" title="Filter" style="font-size: 14px; font-weight: bold; display: none">(Filter)</a>
			<div id="filter_details_box">
				<div id="filter_details"></div>
			</div>
		</div>
		
	</h4>
	<div id="notificatie_holder"><?php
		$number = 0; 
		if (array_key_exists('notificaties', $dossier)){
			
			foreach ($dossier['notificaties'] as $key => $notificatie){
				if (is_array($notificatie['aan'])){
					if (in_array($user['location']['afkorting'], $notificatie['aan']) || user_access(array('Administrators')) || ($user['username'] == $notificatie['username'])){
						if($notificatie['aan']!=null){
							$notificatie['created'] = date('d-m-Y H:i', $notificatie['created']->sec);
							$imploded = '';
							if (is_array($notificatie['aan'])){
								$imploded = 'naar <strong>'.implode(', ', $notificatie['aan']).'</strong>';
							}		
							if ($notificatie['boodschap'] == 'Opmerking') {
								$notificatie['boodschap'] = '';
							}	
							$number++;
						 ?>
							<article>
								<div><strong><?=$notificatie['door']?></strong>&nbsp;<?=$imploded?></div>
								<div style="font-size: 9px"><?=$notificatie['created']?></div>
								<div><?=$notificatie['boodschap']?></div>
							</article>
		<?php }}}}} ?></div>
	<?php if ($number != 0){?>
		<div style="position:absolute; right: 12px; top: 51px;">
			<a href="#notificatie" class="hiddenDiv" id="notificatie" title="notificatie" style="font-size: 17px;margin-left:5px"><?=$number?>  <i class="glyphicon glyphicon-comment"></i></a>			
		</div>
	<?php } ?>
	<div class="filter-show"></div>
	<?php if (array_key_exists('onderwerp', $dossier)) { 
	    if ($dossier['onderwerp']!='<p>Onderwerp'){
	    ?>
		<div class="form_view" style="max-width: 800px">
			<div class="label">Onderwerp</div>
			<div class="item"><?php echo $dossier['onderwerp'];?></div>
		</div>
	<?php }} ?>
	<?php if (array_key_exists('omschrijving', $dossier)) { 
	    if ($dossier['omschrijving'] != '<p>Omschrijving</p>'){
	    ?>
		<div class="form_view" style="max-width: 800px; margin: 0 0 5px;">
			<div class="label">Omschrijving</div>
			<div class="item"><div class="comment more"><?php echo $dossier['omschrijving']; ?>&nbsp;</div></div>
		</div>
	<?php }} ?>
	<div class="form-grid" style="width: 400px;">
	    <h5>Stafdienst</h5>
		<?php if (array_key_exists('datum_melding', $dossier)) { 
			if ($dossier['type']!= 'parlementaire_vragen'){
				$naam = 'Datum Melding';
			} else {
				$naam = 'Datum PV';
			}
		?>
		<div class="form_view">
			<div class="label"><?php echo $naam; ?></div>
			<div class="item"><?php echo $dossier['datum_melding'];?></div>
		</div>
		<?php }
		/*if (array_key_exists('gemeenteplaats', $dossier)) { ?>
		<div class="form_view">
			<div class="label">Gemeente</div>
			<div class="item"><?php echo $dossier['gemeenteplaats']; ?></div>
		</div>
		<?php 
		}
		if (array_key_exists('huisnummer', $dossier)) {
			if ($dossier['huisnummer'] != ''){
				$huisnummer= '</div><span style="font-weigh: normal"> Huisnummer</span>  <div class="item">'.$dossier['huisnummer'];
			} else {
				$huisnummer = '';
			}
		} else {
			$huisnummer = '';
		}
		if (array_key_exists('wegnummer', $dossier)) { ?>
		<div class="form_view">
			<div class="label">Gewestweg</div>
			<div class="item"><?php echo $dossier['wegnummer'].$huisnummer ?></div>
			<div style="margin-left: 130px; display: block; font-weight: bold;"><?php echo $dossier['wegbenaming']; ?></div>
		</div>
		<?php }
		if (array_key_exists('kruispunt', $dossier)) { ?>
		<div class="form_view">
			<div class="label">Kruispunt/straat</div>
			<div class="item"><?php echo $dossier['kruispunt']; ?></div>
		</div>
		<?php }*/
		if (array_key_exists('referte_dtg', $dossier)) { ?>
		<div class="form_view">
			<div class="label">Referte DTG</div>
			<div class="item"><?php echo $dossier['referte_dtg']; ?></div>
		</div>
		<?php }
		if (array_key_exists('datum_dtg', $dossier)) { ?>
		<div class="form_view">
			<div class="label">Datum DTG</div>
			<div class="item"><?php echo $dossier['datum_dtg']; ?></div>
		</div>
		<?php }
		if (array_key_exists('referentie_kab', $dossier)) { ?>
		<div class="form_view">
			<div class="label">Kabinet</div>
			<div class="item"><?php echo $dossier['referentie_kab']; ?></div>
		</div>
		<?php if (array_key_exists('parlementarier', $dossier) && $dossier['parlementarier'] != '') { ?>
		<div class="form_view">
			<div class="label">Parlementariër</div>
			<div class="item"><?php echo $dossier['parlementarier']; ?></div>
		</div>
		<?php }
		 if (array_key_exists('aanvrager', $dossier) && $dossier['aanvrager'] != '') { ?>
		<div class="form_view">
			<div class="label">Aanvrager</div>
			<div class="item"><?php echo $dossier['aanvrager']; ?></div>
		</div>
		<?php }
		if (array_key_exists('straat_nr', $dossier) && array_key_exists('postcode_gemeente', $dossier) && $dossier['straat_nr'] != '') { ?>
		<div class="form_view">
			<div class="label">Adres</div>
			<div class="item"><?php echo $dossier['straat_nr'];?></div>
			<div></div><div class="label">&nbsp;</div>
			<div class="item"><?php echo $dossier["postcode_gemeente"]; ?></div>
		</div>
		<?php }
		if (array_key_exists('emailadres', $dossier) && $dossier['emailadres'] != '') { ?>
		<div class="form_view">
			<div class="label">Email</div>
			<div class="item"><?php echo $dossier['emailadres']; ?></div>
		</div>
		<?php }
		if (array_key_exists('telefoon', $dossier) && $dossier['telefoon'] != '') { ?>
		<div class="form_view">
			<div class="label">Telefoon</div>
			<div class="item"><?php echo $dossier['telefoon']; ?></div>
		</div>
		<?php } ?>	
		<?php }
        if (array_key_exists('antwoord_ontvangen', $dossier)) { ?>
        <div class="form_view" style="margin-top: 15px">
            <div class="label">Antwoord ontvangen</div>
            <div class="item"><?php echo $dossier['antwoord_ontvangen']; ?>&nbsp;</div>
        </div>
        <?php } ?>
        <?php if (array_key_exists('te_behandelen_door', $dossier)) { 
            $dossier_tegen = 'Dienst(en) toevoegen';
            $dossier_tegen_str = '';
            if($dossier['te_behandelen_door'] != ''){
                $dossier_tegen = $dossier['te_behandelen_door'];
                $dossier_tegen_str = '';
                if (is_array($dossier_tegen)){
                    foreach($dossier_tegen as $key => $value){
                        $dossier_tegen_str .= $key.', ';
                    }
                    $dossier_tegen_str = substr($dossier_tegen_str, 0, -2);
                } else {
                    $dossier_tegen_str = $dossier_tegen;
                }
            }
            $dossier['te_behandelen_door'] = $dossier_tegen_str;
        ?>
        <div class="form_view">
            <div class="label">Antwoord door</div>
            <div class="item"><?php echo $dossier['te_behandelen_door']; ?>&nbsp;</div>
        </div>  
        <div class="form_view">
            <div class="label">Gecoördineerd door</div>
            <div class="item"><?php echo $dossier['gecoordineerd_door']; ?>&nbsp;</div>
        </div>     
        <?php } ?>
        <?php if (array_key_exists('doorsturen_naar', $dossier)) { 
            $dossier_tegen = '';
            $dossier_tegen_str = '';
            if($dossier['doorsturen_naar'] != ''){
                $dossier_tegen = $dossier['doorsturen_naar'];
                $dossier_tegen_str = '';
                if (is_array($dossier_tegen)){
                    foreach($dossier_tegen as $key => $value){
                        $dossier_tegen_str .= $key.', ';
                    }
                    $dossier_tegen_str = substr($dossier_tegen_str, 0, -2);
                } else {
                    $dossier_tegen_str = $dossier_tegen;
                }
            }
            $dossier['doorsturen_naar'] = $dossier_tegen_str;
        ?>
        <div class="form_view">
            <div class="label">Doorsturen Naar</div>
            <div class="item"><?php echo $dossier['doorsturen_naar']; ?>&nbsp;</div>
        </div>      
        <?php }
        if (user_access(array('Administrators', 'Stafdienst', 'Secretariaat', 'PCOEVT')) || ($dossier['beantwoord'] == 'true')){
            if (array_key_exists('herinnering_op', $dossier)) { ?>
            <div class="form_view">
                <div class="label">Herinnering op</div>
                <div class="item"><?php echo $dossier['herinnering_op']; ?>&nbsp;</div>
            </div>
            <?php }
            if (array_key_exists('uitgeschreven_op', $dossier)) { ?>
            <div class="form_view">
                <div class="label">Naar Kabinet tegen</div>
                <div class="item"><?php echo $dossier['uitgeschreven_op']; ?>&nbsp;</div>
            </div>
            
            <?php }
            if (array_key_exists('datum_kabinet', $dossier)) { ?>
            <div class="form_view">
                <div class="label">Datum naar Kabinet</div>
                <div class="item"><?php echo $dossier['datum_kabinet']; ?>&nbsp;</div>
            </div>
        <?php }} else { ?>
            <div class="form_view">
                <div class="label">Datum naar Secretariaat</div>
                <div class="item"><?php echo $dossier['secretariaat'][$user['location']['afkorting']]["datum_secretariaat"]; ?>&nbsp;</div>
            </div>
        <?php } ?>	
	</div>
	<div class="form-grid" style="width: 400px;">
		<h5>Secretariaat</h5>
		<?php  
		if (user_access(array('Administrators', 'Stafdienst', 'Secretariaat', 'PCOEVT')) || ($dossier['beantwoord'] == 'true')){
            if (array_key_exists('naar_staf_tegen', $dossier)) { ?>
            <div class="form_view" style="margin-bottom: 15px;">
                <div class="label">Naar Staf tegen</div>
                <div class="item"><?php echo $dossier['naar_staf_tegen']; ?>&nbsp;</div>
            </div>
        <?php }}
        if (!user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){
            $dossier['te_behandelen_door'] = $user['location']['afkorting'];
        }
        $te_behandelen_door = explode(', ', $dossier['te_behandelen_door']);
        if (array_key_exists('doorsturen_naar', $dossier) && $dossier['doorsturen_naar']!= ''){
            echo '<p>Dit wordt enkel getoond bij oude dossiers: doorgestuurd naar:<br /><strong>'.$dossier['doorsturen_naar'].'</strong></p>';
        }
        foreach ($te_behandelen_door as $key => $value) { 
            echo '<div style="margin-bottom: 10px">';
            if (user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){ ?>
                <div class="form_view">
                    <div class="label">Secretariaat</div>
                    <div class="item"><?php echo $value ?>&nbsp;</div>
                </div>
            <?php }    
            if (user_access(array('Administrators', 'Stafdienst', 'Secretariaat', 'PCOEVT'))){ 
                if (array_key_exists('doorsturen_naar', $dossier['secretariaat'][$value])) {    
              ?>
            <div class="form_view">
                <div class="label">Doorsturen naar</div>
                <div class="item"><?php echo $dossier['secretariaat'][$value]["doorsturen_naar_namen"]; ?>&nbsp;</div>
            </div> 
            <?php }} 
                if (array_key_exists('datum_secretariaat', $dossier['secretariaat'][$value])) {
                    if($dossier['secretariaat'][$value]["datum_secretariaat"] != ''){
                        if($dossier['secretariaat'][$value]["datum_secretariaat"]->sec == 0){
                            $dossier['secretariaat'][$value]["datum_secretariaat"] = '&nbsp;';
                        } else {
                            $dossier['secretariaat'][$value]["datum_secretariaat"] = date('d-m-Y', $dossier['secretariaat'][$value]["datum_secretariaat"]->sec);
                        }
                    }
            ?>
            <div class="form_view">
                <div class="label">Datum naar Secretariaat</div>
                <div class="item"><?php echo $dossier['secretariaat'][$value]["datum_secretariaat"]; ?>&nbsp;</div>
            </div>  
            <?php } if (user_access(array('Administrators', 'Stafdienst', 'Secretariaat', 'PCOEVT'))){ 
                if (array_key_exists('datum_antwoord_binnen', $dossier['secretariaat'][$value])) {
                    if($dossier['secretariaat'][$value]["datum_antwoord_binnen"] != ''){
                        if($dossier['secretariaat'][$value]["datum_antwoord_binnen"]->sec == 0){
                            $dossier['secretariaat'][$value]["datum_antwoord_binnen"] = '&nbsp;';
                        } else {
                            $dossier['secretariaat'][$value]["datum_antwoord_binnen"] = date('d-m-Y', $dossier['secretariaat'][$value]["datum_antwoord_binnen"]->sec);
                        }
                    }         
            ?>
            <div class="form_view">
                <div class="label">Antwoord naar Stafdienst </div>
                <div class="item"><?php echo $dossier['secretariaat'][$value]["datum_antwoord_binnen"]; ?>&nbsp;</div>
            </div>
            <?php }   
            if (array_key_exists('secretariaat_opmerking', $dossier['secretariaat'][$value])) {  ?>
            <div class="form_view">
                <div class="label">Opmerking</div>
                <div style="padding: .2em .6em .3em;"><?php echo $dossier['secretariaat'][$value]["secretariaat_opmerking"]; ?>&nbsp;</div>
            </div>
         </div>            
       <?php }}} ?>         
	</div>
	<div style="clear:both;"></div>
	<!--<div class="form-grid" style="width: 400px; border-top: 1px solid #bbb;margin-top: 15px;">
		
	</div>
	<div class="form-grid" style="width: 400px; border-top: 1px solid #bbb;margin-top: 15px; ">
		<?php if (array_key_exists('oorzaak', $dossier)) {
	 		if (!is_string($dossier['oorzaak'])){?>
		<div class="form_view">
			<div class="label">Oorzaak</div>
			<div class="item"><?php echo $dossier['oorzaak'][0]['naam']; ?></div>
		</div>
		<?php }}
		if (array_key_exists('suboorzaak', $dossier)) { 
			if (!is_string($dossier['suboorzaak'])){?>
		<div class="form_view">
			<div class="label">Suboorzaak</div>
			<div class="item"><?php echo $dossier['suboorzaak'][0]['naam']; ?></div>
		</div>
		<?php }}
		if (array_key_exists('suboorzaak2', $dossier)) { 
			if (!is_string($dossier['suboorzaak2'])){?>
		<div class="form_view">
			<div class="label">Suboorzaak2</div>
			<div class="item"><?php echo $dossier['suboorzaak2'][0]['naam']; ?></div>
		</div>	
		<?php }} ?>
	</div>-->
	<?php if (array_key_exists('bijlagen', $dossier)) { 
	if ($dossier['bijlagen'] != '') { ?> 
	<div id="bijlagen" class="form_view">
		<div class="label">Bijlagen</div>
		<ul class="item">
			<?php foreach ($dossier['bijlagen'] as $key => $bijlage) {?>
				<li style="position: relative">
					<a href="/secretariaat/index.php/dossier/dossiers/files/<?php echo $bijlage['_id']?>" class="detail_box_link">
						<span class="glyphicon glyphicon-download"></span> 
						<?php echo $bijlage['name']?>
					</a>
					
					<div class="detail_box">
						<?php if (array_key_exists('user', $bijlage)){?>
							<?php if (array_key_exists('user', $bijlage)){?>
								<div style"font-weight:bold"><?php echo $bijlage['user']?></div>
							<?php }?>
								<div style="font-size:x-small"><?php echo date('d-m-Y H:i', $bijlage['date']->sec)?></div>
							<?php if (array_key_exists('opmerking', $bijlage)){?>
							<p>
								<?php echo $bijlage['opmerking']?>
							</p>
							<?php }?>
						<?php } else { ?>
							<p>Voor oudere bestanden zijn er geen details opgenomen.</p>
						<?php } ?>
					</div>
				</li>				
			<?php }	?>
		</ul>		
	</div>
	<?php }} ?>
	<?php 
	$vinken = array('wachtende_chbx', 'ombudsman_chbx', 'kaartje_chbx', 'niet_voor_awv_chbx', 'ongegrond_chbx', 'afgehandeld_chbx');
	foreach ($vinken as $key){
		if (array_key_exists($key, $dossier)){			
			$dossier[$key] = '<span class="yes_k"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';			
		}
	}
	?>
	<div style="clear:both;max-width: 100%; margin-top: 15px;border-top: 1px solid #bbb;" id="button_ul">
		<ul style="width: 800px">
			<?php if (array_key_exists('wachtende_chbx', $dossier)) { ?>
			<li>Wachtende: <?php echo $dossier['wachtende_chbx']; ?></li>
			<?php }
			if (array_key_exists('ombudsman_chbx', $dossier)) { ?>
			<li>Ombudsman: <?php echo $dossier['ombudsman_chbx']; ?></li>
			<?php }
			if (array_key_exists('kaartje_chbx', $dossier)) { ?>
			<li>kaartje: <?php echo $dossier['kaartje_chbx']; ?></li>
			<?php }
			if (array_key_exists('niet_voor_awv_chbx', $dossier)) { ?>
			<li>Niet voor AWV: <?php echo $dossier['niet_voor_awv_chbx']; ?></li>
			<?php }
			if (array_key_exists('ongegrond_chbx', $dossier)) { ?>
			<li>Ongegrond: <?php echo $dossier['ongegrond_chbx']; ?></li>
			<?php }
			if (array_key_exists('afgehandeld_chbx', $dossier)) { ?>
			<li>Afgehandeld: <?php echo $dossier['afgehandeld_chbx']; ?></li>
			<?php } ?>
		</ul>
	</div>
	</div>
</div>
<?php } ?>