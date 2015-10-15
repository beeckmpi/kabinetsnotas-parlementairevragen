<?php 
	echo form_open('ajax/dossiers/filter', 'id="dossier_filter" name="dossier_filter" style="position: relative"'); 
	$div_open_item = '<div class="form-item">';
	$div_close = '</div>';	
	echo $div_open_item;
	$lijst = 'tabel';
	$type_lijst = 'parlementaire_vragen';
	if (is_array($voorkeuren)){
		if($voorkeuren['lijst'] != null){
			$lijst = $voorkeuren['lijst'];
			$type_lijst = $voorkeuren['type'];
		}
	}
		echo "<div><strong>Type lijst:</strong></div>";
		$type = array(
			' ' => '--selecteer--',
			'details' => 'Detailweergave',
			'tabel' => 'Tabel'
		);		
		asort($type);
	echo form_dropdown('lijst', $type, $lijst);
	echo $div_close;
	echo $div_open_item;
		echo "<div><strong>Type Rapport:</strong></div>";
		$type = array(
			' ' => '--selecteer--',
			'email_kabinet' => 'Kabinetsnota\'s',
			'parlementaire_vragen' => 'Parlementaire vragen'
		);		
		asort($type);
	echo form_dropdown('type', $type, $type_lijst);
	echo $div_close;
	echo $div_open_item;
			echo "<div><strong>Nummer PV:</strong></div>";
			$data = array(
			          'name'        => 'nummer_PV',
			          'id'			=> 'nummer_PV',
			          'placeholder' => 'Nummer PV',
			          'maxlength'   => '100',
			          'size'        => '20',
			          'class' 		=> 'searchbox'
		            );		
		echo form_input($data);		
	echo $div_close;
	echo $div_open_item;
			echo "<div><strong>Nummer KAB:</strong></div>";
			$data = array(
			          'name'        => 'nummer_kab',
			          'id'			=> 'nummer_kab',
			          'placeholder' => 'Nummer KAB',
			          'maxlength'   => '100',
			          'size'        => '20',
			          'class' 		=> 'searchbox'
		            );		
		echo form_input($data);		
	echo $div_close;
	echo $div_open_item;
			echo "<div><strong>Onderwerp:</strong></div>";
			$data = array(
			          'name'        => 'onderwerp',
			          'id'			=> 'onderwerp',
			          'placeholder' => 'Onderwerp',
			          'maxlength'   => '100',
			          'size'        => '20',
			          'class' 		=> 'searchbox'
		            );		
		echo form_input($data);		
	echo $div_close;
	echo $div_open_item;
		echo "<div><strong>Datum naar Kabinet</strong></div>";
		$datum_kabinet_ingevuld = array(
			'' => '--selecteer--',
			'ingevuld' => 'Ingevuld',
			'leeg' => 'Leeg',
		);		
	echo form_dropdown('datum_kabinet_ingevuld', $datum_kabinet_ingevuld, 'datum_kabinet_ingevuld');
	echo $div_close;
	echo $div_open_item;
			echo "<div><strong>Datum PV/Melding (van):</strong></div>";
			$data = array(
			          'name'        => 'datum_melding_van',
			          'id'			=> 'datum_melding_van',
			          'placeholder' => 'Datum',
			          'maxlength'   => '100',
			          'size'        => '20',
			          'class'		=> 'datepicker',
		              'value'		=> date('d-m-Y', strtotime('-1 year', time()))
		            );		
		echo form_input($data);		
	echo $div_close;
	echo $div_open_item;
		echo "<div><strong>Datum PV/Melding (tot):</strong></div>";
		$data = array(
		              'name'        => 'datum_melding_tot',
		              'id'			=> 'datum_melding_tot',
		              'placeholder' => 'Datum',
		              'maxlength'   => '100',
		              'size'        => '20',
		              'class'		=> 'datepicker',
		              'value'		=> date('d-m-Y')
		            );		
		echo form_input($data);		
	echo $div_close;
	if (user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){
		echo $div_open_item;
			echo "<div><strong>Antwoord door:</strong></div>";	
			$te_behandelen[''] = '--selecteren--';		
			asort($te_behandelen);
			echo form_dropdown('te_behandelen_door', $te_behandelen);
		echo $div_close;
	}
	
	echo $div_open_item;
		echo "<div><strong>Dossierbehandelaar:</strong></div>";	
		$doorsturen_naar[''] = '--selecteren--';		
		asort($doorsturen_naar);
		if (!user_access(array('Administrators', 'Stafdienst', 'Secretariaat', 'PCOEVT'))){
			$dossierbeheerder = $user_data['username'];
		} else {
			$dossierbeheerder = '';
		}
		echo form_dropdown('doorsturen_naar', $doorsturen_naar, $dossierbeheerder);
	echo $div_close;
	echo $div_open_item;
		echo "<div><strong>Naar staf tegen (van)</strong></div>";
		$data = array(
	       'name'        	=> 'naar_staf_tegen_van',
	        'id'			=> 'naar_staf_tegen_van',
	        'placeholder' 	=> 'Naar staf tegen(van)',
	        'maxlength'   	=> '100',
	        'size'        	=> '20',
	        'class'			=> 'datepicker',
	        'value'			=> ''
	    );		
		echo form_input($data);		
	echo $div_close;
	echo $div_open_item;
		echo "<div><strong>Naar staf tegen (tot)</strong></div>";
		$data = array(
	       'name'        	=> 'naar_staf_tegen_tot',
	        'id'			=> 'naar_staf_tegen_tot',
	        'placeholder' 	=> 'Naar staf tegen (van)',
	        'maxlength'   	=> '100',
	        'size'        	=> '20',
	        'class'			=> 'datepicker',
	        'value'			=> ''
	    );		
		echo form_input($data);		
	echo $div_close;
	echo '<div class="">';
		echo "<div><strong>Beantwoord:</strong></div>";
		$afgehandeld = array(
			' ' => '--selecteer--',
			'true' => 'ja',
			'false' => 'nee'
		);		
		asort($afgehandeld);
	echo form_dropdown('beantwoord', $afgehandeld, 'Beantwoord');
	echo $div_close;
	echo '<div class="form-item form-item-hidden parlementaire_vragen">';
		echo "<div><strong>Parlementariër</strong></div>";
		$parlementairen[''] = '--selecteren--';		
		asort($parlementairen);
		echo form_dropdown('parlementarier', $parlementairen);
	echo $div_close;	
	echo '<div class="form-item" style="display:none">';
		echo "<div><strong>SubOorzaak</strong></div>";
		$suboorzaken = array('' => '--selecteer--');
		asort($suboorzaken);
		echo form_dropdown('suboorzaak', $suboorzaken);
		echo form_hidden('suboorzaak_value');
	echo $div_close;		
	echo '<div class="form-item" style="display:none">';
		echo "<div><strong>Suboorzaak2</strong></div>";
		$suboorzaken2 = array('' => '--selecteer--');
		asort($suboorzaken2);
		echo form_dropdown('suboorzaak2', $suboorzaken2);
		echo form_hidden('suboorzaak2_value');
	echo $div_close;
	echo form_close();
?>