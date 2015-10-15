<?php
class beheren extends CI_Controller 
{
		
	public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        if (!$this->session->userdata('logged_in'))
		{
        	if(isset($_COOKIE['AWV_secretariaat_rm'])){
        		$this->load->model('Usermodel');
				if (!$this->Usermodel->autologon()){
					$data = array('session_expired' => true);
					$this->output
					 ->set_content_type('application/json')
					 ->set_output(json_encode($data));
				}
			} else {
				$data = array('session_expired' => true);
				$this->output
				->set_content_type('application/json')
				->set_output(json_encode($data));
			}
			exit;
		}
    }

	public function add_beheren($page){
		$this->load->model('Usermodel');
		$this->load->model('Beherenmodel');
		if ($page == 'district'){
			$form_data = array('provincies' => $this->Beherenmodel->provincies_lijst());
		} else {
			$form_data = array('provincies' => array('0' => 'test'));
		}
		
		$data = array(		
			'content' => $this->load->view('beheren/add/'.$page, $form_data, true),
		);	
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($data));
	}
	public function edit($page, $arg1 = Null, $arg2 = Null){
		
		$this->load->model('Usermodel');
		$this->load->model('Beherenmodel');
		if ($page == 'district'){
			$form_data = array('provincies' => $this->Beherenmodel->provincies_lijst(), 'districtgegevens' => $this->Beherenmodel->district_gegevens($arg1, $arg2), 'edit' => 'true');
		} else {
			$form_data = array('provincies' => array('0' => 'test'));
		}
		
		$data = array(		
			'content' => $this->load->view('beheren/add/'.$page.'', $form_data, true),
		);	
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($data));
	}
	
	public function lijst($oorzaak_id, $type = 'suboorzaken'){
		$this->load->model('Beherenmodel');
		$oorzaak = $this->Beherenmodel->oorzaken_id('_id', $oorzaak_id);
		if($type == 'suboorzaken'){
			$data = $this->Beherenmodel->oorzaken_lijst('select', $type, $oorzaak[0]['naam']);			
		} else {
			$data = $this->Beherenmodel->oorzaken_lijst('select', $type, $oorzaak[0]['parent'], $oorzaak[0]['naam']);
		}
		
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($data));
	}
	public function getDistricten($afkorting){
		$this->load->model('Beherenmodel');
		$provincie = $this->Beherenmodel->getProvincie($afkorting);
		$data = $this->Beherenmodel->getDistricten($provincie[0]['provincie']);
		/*foreach ($districten as $key){
			$data[$key]['code'] = $districten[$key]['code'];
			$data[$key]['district'] = $districten[$key]['code'];
		}*/
		
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($data));		
	}
	public function te_behandelen_door($type = 'ja'){
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
		$this->load->library('table');
		$tmpl = array (
          	'table_open' => '<table border="0" cellpadding="4" cellspacing="0" style="width: auto; min-width: 620px;" id="te_behandelen" data-url="'.site_url('ajax/beheren/te_behandelen/edit').'">',
        );
		$this->table->set_template($tmpl); 
		$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'overzicht'), true));
		$username = $this->session->userdata('username');
		$te_behandelen = array('te_behandelen' => $this->table->generate($this->Beherenmodel->te_behandelen_lijst($type)));	
			
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($te_behandelen));
	}
	public function te_behandelen($action, $id = '', $edit = false){
		$this->load->model('Beherenmodel');
		
		if ($action == 'add'){
			$data = array('naam' => $_POST['naam'], 'email' => $_POST['email'], 'actief' => 'ja');
			if ($this->Beherenmodel->save('te_behandelen', $data, $edit)){
				$json = array(
					'message' => '<span style="font-size: 12px">'.$data['naam'].'</span> is toevoegd',
					'tabel' => 'te_behandelen',
					'tr' => '<tr style="background:#FFD2BC;"><td>'.$data['naam'].'</td><td>'.$data['actief'].'</td><td><a href='.site_url('ajax/beheren/te_behandelen/deactiveren/'.$data['_id']).' class="tb_deactive">deactiveren</a></td></tr>'
				);
			}
		} else if ($action == 'edit'){
			$data = array('_id' => $id, '$set' => array('naam' => $_POST['naam'], 'email' => $_POST['email']));
			if ($this->Beherenmodel->save('te_behandelen', $data, true)){
				$json = array(
					'success' => true,
				);
			}
		} else if ($action == "deactiveren") {
			$data = array('_id'=> $id, '$set' => array('actief' => 'nee'));
			if ($this->Beherenmodel->save('te_behandelen', $data, true)){
				$json = array(
					'_id' => $id,
					'janee' => 'nee'
				);
			}
		} else if ($action == "activeren") {
			$data = array('_id'=> $id, '$set' => array('actief' => 'ja'));
			if ($this->Beherenmodel->save('te_behandelen', $data, true)){
				$json = array(
					'_id' => $id,
					'janee' => 'ja'
				);
			}
		}

		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($json));
	}
	public function parlementairen_door($type = 'ja'){
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
		$this->load->library('table');
		$tmpl = array (
          	'table_open' => '<table border="0" cellpadding="4" cellspacing="0" style="width: auto; min-width: 620px;" id="parlementairen" data-url="'.site_url('ajax/beheren/parlementairen/edit').'">',
        );
		$this->table->set_template($tmpl); 
		$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'overzicht'), true));
		$username = $this->session->userdata('username');
		$parlementairen = array('parlementairen' => $this->table->generate($this->Beherenmodel->parlementairen_lijst($type)));	
			
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($parlementairen));
	}
	public function parlementairen($action, $id = '', $edit = false){
		$this->load->model('Beherenmodel');
		
		if ($action == 'add'){
			$data = array('naam' => $_POST['naam'], 'actief' => 'ja');
			if ($this->Beherenmodel->save('parlementairen', $data, $edit)){
				$json = array(
					'message' => '<span style="font-size: 12px">'.$data['naam'].'</span> is toevoegd',
					'tabel' => 'parlementairen',
					'tr' => '<tr style="background:#FFD2BC;"><td>'.$data['naam'].'</td><td>'.$data['actief'].'</td><td><a href='.site_url('ajax/beheren/parlementairen/deactiveren/'.$data['_id']).' class="tb_deactive">deactiveren</a></td></tr>'
				);
			}
		} else if ($action == 'edit'){
			$data = array('_id' => $id, '$set' => array('naam' => $_POST['naam']));
			if ($this->Beherenmodel->save('parlementairen', $data, true)){
				$json = array(
					'success' => true,
				);
			}
		} else if ($action == "deactiveren") {
			$data = array('_id'=> $id, '$set' => array('actief' => 'nee'));
			if ($this->Beherenmodel->save('parlementairen', $data, true)){
				$json = array(
					'_id' => $id,
					'janee' => 'nee'
				);
			}
		} else if ($action == "activeren") {
			$data = array('_id'=> $id, '$set' => array('actief' => 'ja'));
			if ($this->Beherenmodel->save('parlementairen', $data, true)){
				$json = array(
					'_id' => $id,
					'janee' => 'ja'
				);
			}
		}

		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($json));
	}

	public function wegen_door($type = 'ja'){
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
		$this->load->library('table');
		$tmpl = array (
          	'table_open' => '<table border="0" cellpadding="4" cellspacing="0" style="width: auto; min-width: 620px;" id="wegen" data-url="'.site_url('ajax/beheren/wegen/edit').'">',
        );
		$this->table->set_template($tmpl); 
		$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'overzicht'), true));
		$username = $this->session->userdata('username');
		$wegen = array('wegen' => $this->table->generate($this->Beherenmodel->wegen_lijst($type)));	
			
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($wegen));
	}
	public function wegen($action, $id = '', $edit = false){
		$this->load->model('Beherenmodel');
		
		if ($action == 'add'){
			$data = array('code' => $_POST['code'], 'naam' => $_POST['naam'], 'actief' => 'ja');
			if ($this->Beherenmodel->save('wegen', $data, $edit)){
				$json = array(
					'message' => '<span style="font-size: 12px">'.$_POST['code'].' '.$data['naam'].'</span> is toevoegd',
					'tabel' => 'wegen',
					'tr' => '<tr style="background:#FFD2BC;"><td><div contentEditable="true" class="weg_edit weg_edit_code" data-id="'.$data['_id'].'">'.$data['code'].'</div><div contentEditable="true" class="weg_edit weg_edit_naam" data-id="'.$data['_id'].'" style="display:inline">'.$data['naam'].'</div><div class="weg_orig_code">'.$data['code'].'</div><div class="weg_orig_naam">'.$data['naam'].'</div><button class="weg_save">Bewaren</button><button class="weg_cancel">Annuleren</button></td><td>ja</td><td><a href='.site_url('ajax/beheren/wegen/deactiveren/'.$data['_id']).' class="tb_deactive">deactiveren</a></td></tr>'
				);
			}
		} else if ($action == 'edit'){
			$data = array('_id' => $id, '$set' => array('code' => $_POST['code'], 'naam' => $_POST['naam']));
			if ($this->Beherenmodel->save('wegen', $data, true)){
				$json = array(
					'success' => true,
				);
			}
		} else if ($action == "deactiveren") {
			$data = array('_id'=> $id, '$set' => array('actief' => 'nee'));
			if ($this->Beherenmodel->save('wegen', $data, true)){
				$json = array(
					'_id' => $id,
					'janee' => 'nee'
				);
			}
		} else if ($action == "activeren") {
			$data = array('_id'=> $id, '$set' => array('actief' => 'ja'));
			if ($this->Beherenmodel->save('wegen', $data, true)){
				$json = array(
					'_id' => $id,
					'janee' => 'ja'
				);
			}
		}

		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($json));
	}
	public function save($form, $edit = false) {
		$this->load->model('Beherenmodel');
		if ($form == 'provincie'){
			$data = array(
				'provincie' 	=> $_GET['provincie'],	
				'actief'		=> 'ja'					
			);						
			if ($this->Beherenmodel->save('provincies', $data, $edit)){
				$json = array(
					'message' => 'De provincie <span style="font-size: 12px">'.$data['provincie'].'</span> is toevoegd',
					'tabel' => 'provincies',
					'tr' => '<tr style="background:#FFD2BC;"><td><input type="checkbox" value="" name=""></td><td>'.$data['provincie'].'</td><td>'.$data['actief'].'</td></tr>'
				);
			}			
		} else if ($form == 'district'){
			$data = array(				
				'code' 			=> $_GET['code'], 
				'district' 		=> $_GET['district'], 
				'provincie' 	=> $_GET['provincie'],
				'actief' 		=> 'ja',						
			);
			if (array_key_exists('_id', $_GET)){
					$data['_id'] = $_GET['_id'];
				}
			if ($this->Beherenmodel->save('districten', $data, $edit)){
				$json = array(
					'message' => 'Het district <span style="font-size: 12px">'.$data['district'].'</span> is toevoegd',
					'tabel' => 'districten',
					'tr' => '<tr style="background:#FFD2BC;"><td><input type="checkbox" value="" name=""></td><td><a href="districten/'.$data['district'].'">'.$data['code'].'</a></td><td><a href="districten/'.$data['district'].'">'.$data['district'].'</a></td><td><a href="districten/'.$data['district'].'">'.$data['provincie'].'</a></td><td><a href="districten/'.$data['district'].'">'.$data['actief'].'</a></td></tr>'
				);
			}
		} else if ($form == 'rol'){
			$data = array(
				'rol' 	=> $_GET['rol'],	
				'actief'		=> 'ja'					
			);						
			if ($this->Beherenmodel->save('rollen', $data, $edit)){
				$json = array(
					'message' => 'De rol <span style="font-size: 12px">'.$data['rol'].'</span> is toevoegd',
					'tabel' => 'provincies',
					'tr' => '<tr style="background:#FFD2BC;"><td><input type="checkbox" value="" name=""></td><td>'.$data['rol'].'</td><td>'.$data['actief'].'</td></tr>'
				);
			}			
		} else if ($form == 'la'){
			$data = array(
				'type'		=>  "la",
				'titel' 	=> 	$_GET['titel'],	
				'voornaam' 	=> 	$_GET['voornaam'],	
				'naam' 		=>	$_GET['naam'],	
				'actief'	=> 'ja'					
			);						
			if ($this->Beherenmodel->save('lijsten', $data, $edit)){
				$json = array(
					'message' => '<span style="font-size: 12px">'.$data['titel'].' '.$data['voornaam'].' '.$data['naam'].'</span> is toevoegd',
					'tabel' => 'la',
					'tr' => '<tr style="color:#FFD2BC; font-size: 16px; font-weight: bold"><td><input type="checkbox" value="" name=""></td><td>'.$data['titel'].' '.$data['voornaam'].' '.$data['naam'].'</td><td>'.$data['actief'].'</td></tr>',
					'select' => '<option selected="selected">'.$data['titel'].' '.$data['voornaam'].' '.$data['naam'].'</option>'
				);
			}			
		} else if ($form == 'dossierbeheerder'){
			$data = array(
				'type'		=>  "dossierbeheerder",
				'voornaam' 	=> 	$_GET['voornaam'],	
				'naam' 		=>	$_GET['naam'],	
				'actief'	=> 'ja'					
			);							
			if ($this->Beherenmodel->save('lijsten', $data, $edit)){
				$json = array(
					'message' => 'De dossierbeheerder <span style="font-size: 12px">'.$data['voornaam'].' '.$data['naam'].'</span> is toevoegd',
					'tabel' => 'dossierbeheerder',
					'tr' => '<tr style="color:#FFD2BC; font-size: 16px; font-weight: bold"><td><input type="checkbox" value="" name=""></td><td>'.$data['voornaam'].' '.$data['naam'].'</td><td>'.$data['actief'].'</td></tr>',
					'select' => '<option selected="selected">'.$data['voornaam'].' '.$data['naam'].'</option>'
				);
			}			
		} else if ($form == 'delegatie'){
			$data = array(
				'type'		=>  "delegatie",
				'delegatie' =>	$_GET['delegatie'],	
				'actief'	=> 'ja'				
			);						
			if ($this->Beherenmodel->save('lijsten', $data, $edit)){
				$json = array(
					'message' => 'De delegatie <span style="font-size: 12px">'.$data['delegatie'].'</span> is toevoegd',
					'tabel' => 'delegatie',
					'tr' => '<tr style="color:#FFD2BC; font-size: 16px; font-weight: bold"><td><input type="checkbox" value="" name=""></td><td>'.$data['delegatie'].'</td><td>'.$data['actief'].'</td></tr>'
				);
			}			
		}
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($json));
	}

	public function upload($type){
		$data = $_POST['data'];
		$fileName = $_POST['fileName'];
		$serverFile = $type.'_'.time().'_'.$fileName;
		$fp = fopen('D:\xampp\htdocs\secretariaat\files\CSV\\'.$serverFile,'w'); //Prepends timestamp to prevent overwriting
		fwrite($fp, $data);
		fclose($fp);
		$fp = fopen('D:\xampp\htdocs\secretariaat\files\CSV\\'.$serverFile,'r');
		$this->load->model('Dossiersmodel');
		$row = 1;
		$inhoud = '';
		$oorzaak_num = -1;
		$suboorzaak_num = -1;
		$_oorzaken = array();
		while (($csv_data = fgetcsv($fp, 100000, ";")) !== FALSE) {
			$num = count($csv_data);
			$oorzaken = array();
			if ($type == 'dossiers'){
				$oorzaken['type'] = $csv_data[0];
				$oorzaken['dossiernummer'] = (int)$csv_data[1];
				$oorzaken['gemeenteplaats'] = $csv_data[2];
				$oorzaken['wegnummer'] = $csv_data[3];
				$oorzaken['huisnummer'] = $csv_data[4];		
				$oorzaken['wegbenaming'] = $csv_data[5];
				$oorzaken['straatnaam'] = $csv_data[6];	
				$oorzaken['te_behandelen_door'] = $csv_data[7];
				$oorzaken['overgemaakt_aan'] = $csv_data[8];	
				$oorzaken['referentie_sectie'] = $csv_data[9];
				$oorzaken['te_herinneren_op'] = new MongoDate(strtotime($csv_data[10]));	
				$oorzaken['herinnering_op'] = new MongoDate(strtotime($csv_data[11]));
				$oorzaken['antwoord_tegen'] = new MongoDate(strtotime($csv_data[12]));	
				$oorzaken['uitgeschreven_op'] = new MongoDate(strtotime($csv_data[13]));
				$oorzaken['wachtende'] = $csv_data[14];	
				$oorzaken['ombudsman'] = '';	
				$oorzaken['kaartje'] = $csv_data[15];
				$oorzaken['niet_voor_awv'] = $csv_data[16];	
				$oorzaken['ongegrond'] = $csv_data[17];	
				$oorzaken['afgehandeld'] = $csv_data[18];
				$oorzaken['aanvrager'] = $csv_data[19];	
				$oorzaken['aanvrager_adres'] = $csv_data[20];	
				$oorzaken['aanvrager_gemeente'] = $csv_data[21];		
				$oorzaken['email'] = $csv_data[22];
				$oorzaken['telefoon'] = $csv_data[23];	
				$oorzaken['oorzaak'] = $csv_data[24];
				$oorzaken['suboorzaak'] = $csv_data[25];	
				$oorzaken['suboorzaak2'] = $csv_data[26];
				$oorzaken['datum_melding'] = new MongoDate(strtotime($csv_data[27]));
				$this->Dossiersmodel->save('dossiers', $oorzaken);
				$row++;
				$inhoud .= $csv_data[1].' werd succesvol aan de databank toegevoegd => ('.date('d-m-Y H:i:s').')<br />';
			} else if($type == 'oorzaken'){
				if($csv_data[0]!='') {					
					$oorzaken['naam'] = $csv_data[0];
					$oorzaken['parent'] = '';
					$oorzaken['topparent'] = '';
					$oorzaak = $csv_data[0];	
					$this->Dossiersmodel->save('oorzaken', $oorzaken);
					$oorzaken = array();		
				} 
				if($csv_data[1]!=''){
					$oorzaken['naam'] = $csv_data[1];
					$oorzaken['parent'] = $oorzaak;
					$oorzaken['topparent'] = '';
					$suboorzaak = $csv_data[1];	
					$this->Dossiersmodel->save('oorzaken', $oorzaken);
					$oorzaken = array();
				} 
				if($csv_data[2]!=''){
					$oorzaken['naam'] = $csv_data[2];
					$oorzaken['parent'] = $suboorzaak;
					$oorzaken['topparent'] = $oorzaak;
					$this->Dossiersmodel->save('oorzaken', $oorzaken);
					$oorzaken = array();
				}
				
				$inhoud .= $csv_data[0].' werd succesvol aan de databank toegevoegd => ('.date('d-m-Y H:i:s').')<br />';
			} else if ($type == 'suboorzaken'){
				$this->load->model('Beherenmodel');				
				$oorzaken['naam'] = $csv_data[1];				
				$oorzaken['parent'] = $csv_data[0];
				$oorzaken['topparent'] = '';
				$this->Dossiersmodel->save('oorzaken', $oorzaken);
				$row++;
				$inhoud .= $csv_data[0].' werd succesvol aan de databank toegevoegd => ('.date('d-m-Y H:i:s').')<br />';			
			} else if ($type == 'suboorzaken2'){
				$this->load->model('Beherenmodel');				
				$oorzaken['naam'] = $csv_data[2];
				$oorzaken['parent'] = $csv_data[1];
				$oorzaken['topparent'] = $csv_data[0];
				$this->Dossiersmodel->save('oorzaken', $oorzaken);
				$row++;
				$inhoud .= $csv_data[0].' werd succesvol aan de databank toegevoegd => ('.date('d-m-Y H:i:s').')<br />';			
			} else if ($type == 'wegen'){
				$this->load->model('Beherenmodel');	
				$oorzaken['naam'] = $csv_data[1];
				$oorzaken['code'] = $csv_data[0];
				$this->Dossiersmodel->save('wegen', $oorzaken);
				$row++;
				$inhoud .= $csv_data[0].' werd succesvol aan de databank toegevoegd => ('.date('d-m-Y H:i:s').')<br />';			
			}	   
		}		
		fclose($fp);
		
		$inhoud .= 'Er zijn '.$row.' documenten toegevoegd aan de databank';
		$output_data['inhoud'] = $inhoud;
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($output_data));
	}
}
?>