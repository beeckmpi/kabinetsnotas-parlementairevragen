<?php
class Beheren extends CI_Controller 
{
	public function __construct()
    {
    	parent::__construct();
		if (($this->session->userdata('logged_in') == FALSE))
		{
        	if(isset($_COOKIE['AWV_secretariaat_rm'])){
        		$this->load->model('Usermodel');
				$this->Usermodel->autologon();
			} else {
				
			}
		}
		$username = $this->session->userdata('username');
	}
	
	public function districten($action = NULL, $district = NULL)
	{
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
		if (!isset($action)){
			$username = $this->session->userdata('username');
			$districten = array('districten' => $this->Beherenmodel->districten_lijst());			
			$username = $this->session->userdata('username');
			$profile_data = array('profile_data' => $this->Usermodel->profile($username));
			$selected = array('account' => '', 'password' => '', 'picture' => '', 'notifications' => '', 'security' => '', 'applications' => '');		
			$message = '';
			$user_data_arr = $this->Usermodel->profile();
			$user_data = array('user_data' => $user_data_arr['user']);
			$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'Beheren','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
			$data = array(				
				'message' => $message,
				'title' => 'Beheren '. $username,
				'active_page' => 'beheren',
				'sub_title' => 'Gebruikerslijst',
				'blocks' => $user_block,	
				'user_data' => $this->Usermodel->user_data($username, 'normal'),
				'selected' => $selected,
				'content' => $this->load->view('beheren/districten_lijst', $districten, true),
			);	
			$this->load->view('user_settings_templ',$data);
		}	
	}	
	public function uploaden($action = NULL, $district = NULL)
	{
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
		if (!isset($action)){
			$districten = array('districten' => $this->Beherenmodel->districten_lijst());			
			$username = $this->session->userdata('username');
			$profile_data = array('profile_data' => $this->Usermodel->profile($username));					
			$message = '';
			$user_data_arr = $this->Usermodel->profile();
			$user_data = array('user_data' => $user_data_arr['user']);
			$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'Beheren','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
			$data = array(				
				'message' => $message,
				'title' => 'Beheren '. $username,
				'active_page' => 'beheren',
				'blocks' => $user_block,	
				'sub_title' => 'Gebruikerslijst',	
				'user_data' => $this->Usermodel->user_data($username, 'normal'),
				'content' => $this->load->view('beheren/upload', $districten, true),
			);	
			$this->load->view('user_settings_templ',$data);
		}	
	}			
	
	
	public function rollen($action = Null, $rol = Null){
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
		if (!isset($action)){
			$this->load->library('table');
			$tmpl = array (
            	'table_open' => '<table border="0" cellpadding="4" cellspacing="0" style="width: auto; min-width: 520px;">',
            );
			$this->table->set_template($tmpl); 
			$rollen = array('rollen' => $this->table->generate($this->Beherenmodel->rollen_lijst()));			
			$username = $this->session->userdata('username');
			$profile_data = array('profile_data' => $this->Usermodel->profile($username));
			$selected = array('account' => '', 'password' => '', 'picture' => '', 'notifications' => '', 'security' => '', 'applications' => '');		
			$message = '';
			$user_data_arr = $this->Usermodel->profile();
			$user_data = array('user_data' => $user_data_arr['user']);
			$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'Beheren','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
			$data = array(				
				'message' => $message,
				'title' => 'Beheren '. $username,
				'active_page' => 'beheren',
				'sub_title' => 'Rollenlijst',
				'blocks' => $user_block,	
				'user_data' => $this->Usermodel->user_data($username, 'normal'),
				'selected' => $selected,
				'content' => $this->load->view('beheren/rollen_lijst', $rollen, true),
			);	
			$this->load->view('user_settings_templ',$data);
		}	
	} 

	public function te_behandelen_door($type = 'ja'){
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
		if (!isset($action)){
			$this->load->library('table');
			$tmpl = array (
            	'table_open' => '<table border="0" cellpadding="4" cellspacing="0" style="width: auto; min-width: 620px;" id="te_behandelen" data-url="'.site_url('ajax/beheren/te_behandelen/edit').'">',
            );
			$this->table->set_template($tmpl); 
			$username = $this->session->userdata('username');
			$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'overzicht','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
			
			$te_behandelen_lijst = array('te_behandelen' => $this->table->generate($this->Beherenmodel->te_behandelen_lijst($type)));	
			$oorzaken = $this->Beherenmodel->oorzaken_lijst('select', 'oorzaken');
			$te_behandelen = $this->Beherenmodel->te_behandelen_lijst('ja', 'data');
			$parlementairen = $this->Beherenmodel->parlementairen_lijst('ja', 'data');
			$data = array(			
				'title' => 'Beheren te behandelen door',
				'active_page' => 'beheren',
				'sub_title' => 'te behandelen door lijst',
				'user_data' => $this->Usermodel->user_data($username, 'normal'),
				'blocks' => $user_block,	
				'content' => $this->load->view('beheren/te_behandelen_lijst', $te_behandelen_lijst, true),
				'oorzaken' => $oorzaken,
				'filter_view' => "normal",
				'te_behandelen' => $te_behandelen,
				'parlementairen' => $parlementairen
			);	
			$page = $this->load->view('dossiers_templ',$data);
		}	
	}

	public function parlementairen($type = 'ja'){
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
		if (!isset($action)){
			$this->load->library('table');
			$tmpl = array (
            	'table_open' => '<table border="0" cellpadding="4" cellspacing="0" style="width: auto; min-width: 620px;" id="parlementairen" data-url="'.site_url('ajax/beheren/parlementairen/edit').'">',
            );
			$this->table->set_template($tmpl); 
			$username = $this->session->userdata('username');
			$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'overzicht','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
			
			$parlementairen_lijst = array('parlementairen' => $this->table->generate($this->Beherenmodel->parlementairen_lijst($type)));	
			$oorzaken = $this->Beherenmodel->oorzaken_lijst('select', 'oorzaken');
			$te_behandelen = $this->Beherenmodel->te_behandelen_lijst('ja', 'data');
			$parlementairen = $this->Beherenmodel->parlementairen_lijst('ja', 'data');
			$data = array(			
				'title' => 'Beheren te behandelen door',
				'active_page' => 'beheren',
				'sub_title' => 'te behandelen door lijst',
				'user_data' => $this->Usermodel->user_data($username, 'normal'),
				'blocks' => $user_block,	
				'content' => $this->load->view('beheren/parlementairen_lijst', $parlementairen_lijst, true),
				'oorzaken' => $oorzaken,
				'filter_view' => "normal",
				'te_behandelen' => $te_behandelen,
				'parlementairen' => $parlementairen
			);	
			$page = $this->load->view('dossiers_templ',$data);
		}
	}
	public function wegen($type = 'ja'){
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
		if (!isset($action)){
			$this->load->library('table');
			$tmpl = array (
            	'table_open' => '<table border="0" cellpadding="4" cellspacing="0" style="width: auto; min-width: 620px;" id="wegen" data-url="'.site_url('ajax/beheren/wegen/edit').'">',
            );
			$this->table->set_template($tmpl); 
			$username = $this->session->userdata('username');
			$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'overzicht','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
			
			$wegen = array('wegen' => $this->table->generate($this->Beherenmodel->wegen_lijst($type)));	
			$oorzaken = $this->Beherenmodel->oorzaken_lijst('select', 'oorzaken');
			$te_behandelen = $this->Beherenmodel->te_behandelen_lijst('ja', 'data');
			$parlementairen = $this->Beherenmodel->parlementairen_lijst('ja', 'data');
			$data = array(			
				'title' => 'Beheren te behandelen door',
				'active_page' => 'beheren',
				'sub_title' => 'te behandelen door lijst',
				'user_data' => $this->Usermodel->user_data($username, 'normal'),
				'blocks' => $user_block,	
				'content' => $this->load->view('beheren/wegen_lijst', $wegen, true),
				'oorzaken' => $oorzaken,
				'filter_view' => "normal",
				'te_behandelen' => $te_behandelen,
				'parlementairen' => $parlementairen
			);	
			$page = $this->load->view('dossiers_templ',$data);
		}
	}
	public function lijsten($type = Null, $rol = Null){
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
		if (!isset($action)){
			$this->load->library('table');
			$tmpl = array (
            	'table_open' => '<table border="0" cellpadding="4" cellspacing="0" style="width: auto; min-width: 520px;" id="'.$type.'">',
            );
			$this->table->set_template($tmpl); 
			$rollen = array('rollen' => $this->table->generate($this->Beherenmodel->lijsten_lijst($type)), 'type' => $type, 'id' => $type);			
			$username = $this->session->userdata('username');
			$profile_data = array('profile_data' => $this->Usermodel->profile($username));
			$selected = array('account' => '', 'password' => '', 'picture' => '', 'notifications' => '', 'security' => '', 'applications' => '');		
			$message = '';
			$user_data_arr = $this->Usermodel->profile();
			$user_data = array('user_data' => $user_data_arr['user']);
			$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'overzicht','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
			$data = array(				
				'message' => $message,
				'title' => 'Beheren '. $username,
				'active_page' => 'beheren',
				'sub_title' => $type.' Lijst',
				'blocks' => $user_block,	
				'user_data' => $this->Usermodel->user_data($username, 'normal'),
				'selected' => $selected,
				'content' => $this->load->view('beheren/lijsten_lijst', $rollen, true),
			);	
			$this->load->view('user_settings_templ',$data);
		}	
	}
	public function uploaden_win7($action = NULL, $district = NULL)
	{
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
		if (!isset($action)){
			$districten = array('districten' => $this->Beherenmodel->districten_lijst());			
			$username = $this->session->userdata('username');
			$profile_data = array('profile_data' => $this->Usermodel->profile($username));					
			$message = '';
			$user_data_arr = $this->Usermodel->profile();
			$user_data = array('user_data' => $user_data_arr['user']);
			$data = array(				
				'message' => $message,
				'title' => 'Beheren '. $username,
				'active_page' => 'beheren',
				'sub_title' => 'Gebruikerslijst',	
				'user_data' => $this->Usermodel->user_data($username, 'normal'),
				'content' => $this->load->view('beheren/upload_win7', $districten, true),
			);	
			$this->load->view('user_settings_templ',$data);
		}	
	}			
	
	public function upload_win7($type){
		$data = $_POST['data'];
		$fileName = $_POST['fileName'];
		$serverFile = $type.'_'.time().'_'.$fileName;
		$fp = fopen('D:\xampp\htdocs\verrekeningen\files\CSV\\'.$serverFile,'w'); //Prepends timestamp to prevent overwriting
		fwrite($fp, $data);
		fclose($fp);
		$fp = fopen('D:\xampp\htdocs\verrekeningen\files\CSV\\'.$serverFile,'r');		
		$this->load->model('Win7model');
		
		$row = 1;
		$inhoud = '';
		while (($csv_data = fgetcsv($fp, 100000, ";")) !== FALSE) {
			$num = count($csv_data);
			$dossier = array();
			$verrekening = array();
			$csv_data[0] = trim($csv_data[0]);
			if($type == 'volledige_lijst'){
				$dossier['tagnummer'] = (string)$csv_data[0];
				$dossier['ci_type'] = (string)$csv_data[1];
				$dossier['Productnaam'] = (string)$csv_data[2];
				$dossier['locatie'] = (string)$csv_data[3];
				$dossier['locatie_code'] = (string)$csv_data[4];
				$dossier['adres'] = (string)$csv_data[5];
				$dossier['nr'] = (int)$csv_data[6];
				$dossier['bus'] = (string)$csv_data[7];
				$dossier['postcode'] = (string)$csv_data[8];
				$dossier['gemeente'] = (string)$csv_data[9];
				$dossier['extra_locatie_info'] = (string)$csv_data[10];
				$dossier['aankoopdatum'] = (string)$csv_data[11];
				$dossier['gebruiker_naam_entiteitscode'] = (string)$csv_data[12];
				$dossier['gebruiker_voornaam_entiteitsnaam'] = (string)$csv_data[13];
				$dossier['eigenaar_entiteit'] = (string)$csv_data[14];
				$this->Win7model->save('overzicht', $dossier);
				$row++;
				$inhoud .= $csv_data[0].' werd succesvol aan de databank toegevoegd => ('.date('d-m-Y H:i:s').')<br />';
			
			} else if ($type == 'computer_gegevens'){
				$verrekening['tagnummer'] = (string)$csv_data[0];
				$verrekening['OS'] = $csv_data[1];
				$verrekening['geheugen'] = (int)$csv_data[2];
				$verrekening['extra_info'] = (string)$csv_data[3];
				$this->Win7model->save('computer_gegevens', $verrekening);
				$row++;
				$inhoud .= $csv_data[0].'-'.$csv_data[1].' werd succesvol aan de databank toegevoegd => ('.date('d-m-Y H:i:s').')<br />';
			}
		   
		}		
		fclose($fp);
		$inhoud .= 'Er zijn '.$row.' documenten toegevoegd aan de databank';
		$output_data['inhoud'] = $inhoud;
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($output_data));
	}
	public function export_win7($action = null){
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
		if (!isset($action)){
			$districten = array('districten' => $this->Beherenmodel->districten_lijst());			
			$username = $this->session->userdata('username');
			$profile_data = array('profile_data' => $this->Usermodel->profile($username));					
			$message = '';
			$user_data_arr = $this->Usermodel->profile();
			$user_data = array('user_data' => $user_data_arr['user']);
			$data = array(				
				'message' => $message,
				'title' => 'Beheren '. $username,
				'active_page' => 'beheren',
				'sub_title' => 'Gebruikerslijst',	
				'user_data' => $this->Usermodel->user_data($username, 'normal'),
				'content' => $this->load->view('beheren/export_win7', $districten, true),
			);	
			$this->load->view('user_settings_templ',$data);
		} 
		else if ($action == "volledig_overzicht")
		{
			$this->load->model('Win7model');
			$overzicht_data = $this->Win7model->overzicht_info('overzicht');
			$naam_bestand = 'overzicht_alles'.date('d-m-Y_H_i_s').'.csv';
			$fp = fopen('D:\xampp\htdocs\verrekeningen\files\CSV\\'.$naam_bestand,'w');
			$header = array('tagnummer', 'ci type', 'product naam', 'locatie', 'locatie_code', 'adres', 'nr', 'bus', 'postcode', 'gemeente', 'extra_locatie_info','aankoopdatum', 'naam', 'voornaam', 'entiteit', 'os', 'geheugen', 'extra informatie');
			fputcsv($fp, $header, ';');
			foreach ($overzicht_data as $nr => $value)
			{
				$overzicht_computers_gegevens = $this->Win7model->overzicht_info('computer_gegevens', $overzicht_data[$nr]['tagnummer']);
				if (isset($overzicht_computers_gegevens[0]))
				{
					if (!isset($overzicht_computers_gegevens[0]['extra_info']))
					{
						$overzicht_computers_gegevens[0]['extra_info'] = '';
					}
					$comp_arr = $overzicht_computers_gegevens[0];
					$data = array(
						0 => $overzicht_data[$nr]['tagnummer'],
						1 => $overzicht_data[$nr]['ci_type'],
						2 => $overzicht_data[$nr]['Productnaam'],
						3 => $overzicht_data[$nr]['locatie'],
						4 => $overzicht_data[$nr]['locatie_code'],
						6 => $overzicht_data[$nr]['adres'],
						7 => $overzicht_data[$nr]['nr'],
						8 => $overzicht_data[$nr]['bus'],
						9 => $overzicht_data[$nr]['postcode'],
						10 => $overzicht_data[$nr]['gemeente'],
						11 => $overzicht_data[$nr]['extra_locatie_info'],
						12 => $overzicht_data[$nr]['aankoopdatum'],
						13 => $overzicht_data[$nr]['gebruiker_naam_entiteitscode'],
						14 => $overzicht_data[$nr]['gebruiker_voornaam_entiteitsnaam'],
						15 => $overzicht_data[$nr]['eigenaar_entiteit'],
						16 => $comp_arr['OS'],
						17 => $comp_arr['geheugen'],
						18 => $comp_arr['extra_info'],							
					);		
					$overzicht_computers_gegevens = '';				
					fputcsv($fp, $data, ';');		
				};			
				
			}
			fclose($fp);
			$output_data = array('url' => base_url().'files/CSV/'.$naam_bestand);
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($output_data));
		}
	}
}
	