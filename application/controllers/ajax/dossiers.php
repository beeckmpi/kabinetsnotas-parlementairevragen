<?php
class Dossiers extends CI_Controller 
{
	public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in'))
		{
        	if(isset($_COOKIE['AWV_secretariaat_rm'])){
        		$this->load->model('Usermodel');
				if (!$this->Usermodel->autologon()){
					$data = array('session_expired' => true);
					$this->output->set_content_type('application/json')->set_output(json_encode($data));
				} 
			} else {
				$data = array('session_expired' => true);
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			}
			exit;
		}
    }
	public function index()
	{
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');							
		$username = $this->session->userdata('username');
		$profile_data = array('profile_data' => $this->Usermodel->profile($username));
		$message = '';
		$user_data_arr = $this->Usermodel->profile();
		$user_data = array('user_data' => $user_data_arr['user']);
		$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'overzicht','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
		$data = array(
			'message' => $message,
			'title' => 'Verrekeningen',
			'active_page' => 'overzicht',
			'sub_title' => 'Gebruikerslijst',
			'blocks' => $user_block,			
			'user_data' => $this->Usermodel->user_data($username, 'normal'),
			'user_image' => $this->session->userdata('image'),
			'content' => '',
		);	
		$page = $this->load->view('dossiers_templ',$data);
	}
	public function toevoegen($active_tab = 'start')
	{
		$this->load->model('Beherenmodel');
		$oorzaken = $this->Beherenmodel->oorzaken_lijst('select', 'oorzaken');
		
		$wegen = $this->Beherenmodel->lijst_lijst('select', 'wegen');
		$te_behandelen = $this->Beherenmodel->te_behandelen_lijst('ja', 'data');
		$parlementairen = $this->Beherenmodel->parlementairen_lijst('ja', 'data');
		$data_arr = array('form' => $this->load->view('dossier/dossier_form',array('active_tab' => $active_tab, 'oorzaken' => $oorzaken, 'wegen' => $wegen, 'te_behandelen' => $te_behandelen, 'parlementairen' => $parlementairen), true));	
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($data_arr));
	}
	public function verwijderen($id)
	{
		$this->load->model('Dossiersmodel');
		$geg = $this->Dossiersmodel->remove($id);
		if ($geg) {
			$remove = true;
			$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($remove));
		}
	}
	public function opslaan($_id = null, $bijlage = null)
	{
		$this->load->model('Dossiersmodel');	
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
        $this->load->library('email');  
		if($_id=='bijlagen' || $bijlage == 'bijlagen'){
			$now = date('d-m-Y h:i');
			$_POST['datum_bijlage'] = new MongoDate(strtotime($now));
			$_POST['user'] = $this->session->userdata('username');			
			$geg = $this->Dossiersmodel->save('bijlagen', $_POST);	
			if($geg){
				$data_arr = array('_id' => $geg, 'name' => $_FILES['bijlage']['name'], 'location' => $_POST['locatie'], 'opmerking' => $_POST['opmerking'], 'user' => $_POST['user'], 'date' => $now);
			} else {
				$data_arr = array('fout' => 'fouten!!');
			}
			$this->output
				 ->set_content_type('application/json')
				 ->set_output(json_encode($data_arr));
			//return $data_arr;
		} else {
			if (user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){ 
				if($_POST['te_behandelen_door'] == 'Dienst(en) toevoegen'){
					$_POST['te_behandelen_door'] = '';
				}
                if (user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){
				    $_POST['datum_melding'] =  new MongoDate(strtotime($_POST['datum_melding']));
                }
				if (array_key_exists('datum_dtg', $_POST)){
					$_POST['datum_dtg'] =  new MongoDate(strtotime($_POST['datum_dtg']));
				}
				if (array_key_exists('datum_kabinet', $_POST)){
					$_POST['datum_kabinet'] =  new MongoDate(strtotime($_POST['datum_kabinet']));
				}
				if (array_key_exists('naar_staf_tegen', $_POST)){
					$_POST['naar_staf_tegen'] =  new MongoDate(strtotime($_POST['naar_staf_tegen']));
				}
				$_POST['herinnering_op'] =  new MongoDate(strtotime($_POST['herinnering_op']));
				//$_POST['terug_herinnerd_op'] =  new MongoDate(strtotime($_POST['terug_herinnerd_op']));
				//$_POST['antwoord_tegen'] =  new MongoDate(strtotime($_POST['antwoord_tegen']));
				if (array_key_exists('antwoord_ontvangen', $_POST)){
					if ($_POST['antwoord_ontvangen']!=''){
						$_POST['antwoord_ontvangen'] =  new MongoDate(strtotime($_POST['antwoord_ontvangen']));
					}
				}
				$_POST['uitgeschreven_op'] =  new MongoDate(strtotime($_POST['uitgeschreven_op']));
			}
			if (array_key_exists('naar_secretariaat', $_POST)){
				$_POST['naar_secretariaat'] =  new MongoDate(strtotime($_POST['naar_secretariaat']));
			} 
			$username = $this->session->userdata('username');
			$user = $this->Usermodel->user_data($username, 'normal');
			if ($_POST['bijlagen'] != '' && array_key_exists('bijlagen', $_POST)){
				$_POST['bijlagen'] = explode(', ', $_POST['bijlagen']);
				foreach ($_POST['bijlagen'] as $key => $value){
					$this->load->model('Dossiersmodel');
					$file = $this->Dossiersmodel->filesearch($value);
					if (array_key_exists('locatie', $file->file)){
						$locatie_pers = $file->file['locatie'];
					} else {
						$locatie_pers = 'Algemeen';
					}
					if (array_key_exists('user', $file->file)){
						$user_pers = $file->file['user'];
					} else {
						$user_pers = '';
					}
					if (array_key_exists('opmerking', $file->file)){
						$opmerking_pers = $file->file['opmerking'];
					} else {
						$opmerking_pers = '';
					}
					$_POST['bijlagen'][$key] = array('_id' => $value, 'name' => $file->file['filename'], 'locatie' => $locatie_pers, 'opmerking' => $opmerking_pers, 'user' => $user_pers,'date' => $file->file['uploadDate']);
				}
			}			
			if ($_POST['remove_bijlagen'] != ''){
				$remove_bijlagen = explode(', ', $_POST['remove_bijlagen']);
				foreach ($remove_bijlagen as $key => $value){
					$this->load->model('Dossiersmodel');
					$this->Dossiersmodel->removeFile($value,$_id);
				}
				unset($_POST['remove_bijlagen'], $_POST['te_behandelen_door_chkbx'], $_POST['doorgestuurd_naar_chbx']);
			}
			$username = $this->session->userdata('username');
			if (array_key_exists('secretariaat', $_POST)){
				foreach($_POST['secretariaat'] as $provincie => $value){
					if ($_POST['secretariaat'][$provincie]['datum_secretariaat']!=''){
						$_POST['secretariaat'][$provincie]['datum_secretariaat'] = new MongoDate(strtotime($_POST['secretariaat'][$provincie]['datum_secretariaat']));
					}
					if ($_POST['secretariaat'][$provincie]['datum_antwoord_binnen']!=''){
						$_POST['secretariaat'][$provincie]['datum_antwoord_binnen'] = new MongoDate(strtotime($_POST['secretariaat'][$provincie]['datum_antwoord_binnen']));
					}
				}
			}
			if ($_id != null){
				$_POST['dossier_id'] = $_id;											
				$_POST['bewerkt_door'] = $username;
				$_POST['laatst_bewerkt'] =  new MongoDate(strtotime(date('d-m-Y h:i')));
				$dossier_orig = $this->Dossiersmodel->dossier_info('data', array('_id' => $_id));
				$te_behandelen_door = explode(', ', $_POST['te_behandelen_door']);
				foreach ($te_behandelen_door as $key => $location){
					if (array_key_exists('secretariaat', $dossier_orig[0])){
						if (array_key_exists($location, $dossier_orig[0]['secretariaat'])){
							if (!array_key_exists($location, $_POST['secretariaat'])){
								$_POST['secretariaat'][$location] = $dossier_orig[0]['secretariaat'][$location];
							} 							
						} else {
							$_POST['secretariaat'][$location] = array('doorsturen_naar' => '', 'doorsturen_naar_namen' => '', 'datum_secretariaat' => '', 'datum_antwoord_binnen' => '', 'secretariaat_opmerking' => '');
						}
					} 
				}
				if (array_key_exists('aangemaakt_door', $dossier_orig[0])){
					$aangemaakt_door = $_POST['aangemaakt_door'] = $dossier_orig[0]['aangemaakt_door'];
					$_POST['aangemaakt_op'] = $dossier_orig[0]['aangemaakt_op'];
				} else {
					$aangemaakt_door = '';
				}
				$date = new DateTime();
				$now = $date->getTimestamp();
				if(!array_key_exists('notificatie_chbx', $_POST)){
					$notficaties_chbx = array('');
				} else {
					$notficaties_chbx = $_POST['notificatie_chbx'];
				}
				$notificatie = array('created' => new MongoDate(strtotime(date('d-m-Y h:i'))), 'door' => $user['name'].' '.$user['first_name'], 'username' => $username, 'aan' => $notficaties_chbx, 'boodschap' => $_POST['opmerking_notificatie']);
				$dossier_id = $this->Dossiersmodel->save('dossiers', $_POST, true);
				$this->Dossiersmodel->add_notificatie($_id, $notificatie);
				$dossier = $this->Dossiersmodel->dossier_info('data', array('_id' => $_id));
				$user_data = $this->Usermodel->user_data($username, 'normal');				
				$type = array(
					' ' => '--selecteer--',
					'email_kabinet' => 'EMAILKAB',
					'fietspaden' => 'MPF', 
					'parlementaire_vragen' => 'PV',
					'kabinetsnotas' => 'KAB'
				);
				$dossier_nr = $type[$dossier[0]['type']].'/'.substr($dossier[0]['dossiernummer'], 0, 4).'/'.substr($dossier[0]['dossiernummer'], 4, 7);
				if($dossier[0]['type'] == 'parlementaire_vragen'){
					$dossier_id = 'Pv '.$dossier[0]['nummer_pv'];
				} else {
					$dossier_id = $dossier[0]['nummer_kab'];
				}
				if ($_POST['opmerking_notificatie']!= '' && $_POST['opmerking_notificatie']!= 'Opmerking'){
					$opmerking = '<p>Opmerking '.$user_data['first_name'].' '.$user_data['name'].':<p><p style="color:red; font-weight:bold">'.$_POST['opmerking_notificatie'].'</p>';
				} else {
					$opmerking = '';
				}
				$url = '<a href="'.site_url().'dossier/dossiers/view/'.$dossier[0]['_id'].'">'.site_url().'dossier/dossiers/view/'.$dossier[0]['_id'].'</a>';				
				$message = 
				'<style type="text/css">
					p {
						font-family:"Segoe UI", arial; 
						color: #555;
						font-size: 13px;
					}
				</style>
				<p>Beste,</P>
				<p><strong>'.$dossier_id.'</strong> werd aangepast in het opvolgingssysteem door <strong>'.$user_data['first_name'].' '.$user_data['name'].'</strong>.</p>
				'.$opmerking.'
				<p>U kan het dossier hier '.$url.' terug vinden.<br /> 
				U kan deze link het beste met Mozilla Firefox openen. <br />
				Indien u nog niet ingelogd bent op de applicatie is het wel aangeraden om u eerst in te loggen</P>
				<p><i>Opgelet!! Stuur geen antwoord op deze mail, dit is een automatisch mailsysteem en uw mail zal niet aankomen.</i></p>
				<p></p>
				<p>Met vriendelijke groeten, 
				<br />
				<br /> 
				De stafdienst</p>';			
				if (array_key_exists('notificatie_chbx', $_POST)) {
					$email = '';
					foreach ($_POST['notificatie_chbx'] as $key => $value){						
						$user = $this->Usermodel->user_data($value, 'normal');						
						if (is_array($user)){
							$user_location = $user['location']['afkorting'];
							if (array_key_exists($user_location, $email)){
								$email[$user_location] = $email[$user_location].', '.$user['email'];
							} else {
								$email[$user_location] = $user['email'];
							}							
						} else {
							$user_location = $user_data['location']['afkorting'];
							$te_behandelen = $this->Beherenmodel->te_behandelen($value);
							$dossier['emails'][$user_location]['email'] = $email;
							if (is_array($te_behandelen)){
								$email[$value] = $te_behandelen[$value][0]['email'];
							}
						}						
						$afdeling_user = $this->Beherenmodel->te_behandelen($user_location);
						$cc= $afdeling_user[$user_location][0]['email'];
					}	
					if ($email != ''){
						foreach ($email as $key => $value){
							$dossier['emails'][$value]['email'] = $email;		
							$this->email->from('pv_kb_stafdienst@mow.vlaanderen.be', 'KabinetsNota\'s en Parlementaire Vragen');
							$this->email->to($value); 
							$this->email->cc($cc); 				
							$this->email->subject($dossier_id);
							$this->email->message($message);
                            if(!$this->email->send()){
                                $email_send = false;
                            } else {
                                $email_send = true;
                            }
						}
					}
				} else {
				    $email_send  = true;
				}			
			} else {
				$te_behandelen_door = explode(', ', $_POST['te_behandelen_door']);
				foreach ($te_behandelen_door as $location => $information){
					$_POST['secretariaat'][$information] = array('doorsturen_naar' => '', 'doorsturen_naar_namen' => '', 'datum_secretariaat' => '', 'datum_antwoord_binnen' => '', 'secretariaat_opmerking' => '');
				}
				$nummer = $this->new_dossier_nummer($_POST['type'], true);
				$_POST['dossiernummer'] = date('Y').$nummer['nummer'];
				$_POST['aangemaakt_door'] = $username;
				$_POST['aangemaakt_op'] =  new MongoDate(strtotime(date('d-m-Y h:i')));
				$dossier_id = $this->Dossiersmodel->save('dossiers', $_POST, false);
				$dossier = $this->Dossiersmodel->dossier_info('data', array('_id' => $dossier_id));
				if((!empty($_POST['te_behandelen_door'])) && $_POST['te_behandelen_door'] != 'Dienst(en) toevoegen'){
					$te_behandelen = $this->Beherenmodel->te_behandelen($_POST['te_behandelen_door']);
					foreach ($te_behandelen as $key => $value){
						if (array_key_exists('email', $value[0])){
							$type = array(
								' ' => '--selecteer--',
								'email_kabinet' => 'EMAILKAB',
								'fietspaden' => 'MPF', 
								'wegen' => 'Wegen',
								'parlementaire_vragen' => 'PV',
								'kabinetsnotas' => 'KAB'
							);
							$dossier_nr = $type[$dossier[0]['type']].'/'.substr($dossier[0]['dossiernummer'], 0, 4).'/'.substr($dossier[0]['dossiernummer'], 4, 7);
							if($dossier[0]['type'] == 'parlementaire_vragen'){
								$dossier_id = 'Pv '.$dossier[0]['nummer_pv'];
							} else {
								$dossier_id = $dossier[0]['nummer_kab'];
							}
							$url = '<a href="'.site_url().'dossier/dossiers/view/'.$dossier[0]['_id'].'">'.site_url().'dossier/dossiers/view/'.$dossier[0]['_id'].'</a>';
							
							$message = 
							'<style type="text/css">
								p {
									font-family:"Segoe UI", arial; 
									color: #555;
									font-size: 13px;
								}
							</style>
							<p>Beste,</P>
							<p><strong>'.$dossier_id.'</strong> werd toegevoegd aan het opvolgingssysteem</p>
							<p>U kan het dossier hier '.$url.' terug vinden.<br /> 
							U kan deze link het beste met Mozilla Firefox openen. <br />
							Indien u nog niet ingelogd bent op de applicatie is het wel aangeraden om u eerst in te loggen</P>
							<p></p>
							<p>Met vriendelijke groeten, 
							<br />
							<br /> 
							De stafdienst</p>';							
												
							$this->email->from('pv_kb_stafdienst@mow.vlaanderen.be', 'KabinetsNota\'s en Parlementaire Vragen');
							$this->email->to($value[0]['email']); 
							//$this->email->cc();							
							$this->email->subject($dossier_id);
							$this->email->message($message);								
							if(!$this->email->send()){
                                $email_send = false;
                            } else {
                                $email_send = true;
                            }
						} else {
						    $email_send = true;
						}		
					}
				}
			}		
			$this->output
				 ->set_content_type('application/json')
				 ->set_output(json_encode(array('dossier' => $dossier, 'email_send' => $email_send)));			
		}	
	}
	public function doorsturen_naar_list()
	{
		$this->load->model('Beherenmodel');
		$behandelen_door = $_POST['te_behandelen'];
		$doorsturen_naar = $_POST['doorsturen_naar'];
		$doorsturen = $this->Beherenmodel->doorsturen_lijst($behandelen_door);
		$search_arr['doorgestuurd_naar'] = $doorsturen;
		$search_arr['doorsturen_naar'] = $doorsturen_naar;
		$search_arr['behandelen_door'] = $behandelen_door;
		$view = $this->load->view('dossier/dossier_doorsturen_naar',$search_arr, true);
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode(array('view' => $view)));
	}	
	public function new_dossier_nummer($type, $local = false)
	{
		if ($type == 'undefined'){
			$nummer = '';
		} else {
			$this->load->model('Dossiersmodel');
			$dossier = $this->Dossiersmodel->dossier_info('nummer', $type);
			if(array_key_exists(0, $dossier)){
				(int)$nummer = substr($dossier[0]['dossiernummer'], 4, 8);
				(int)$jaar = substr($dossier[0]['dossiernummer'], 0, 4);
				if ($jaar == date('Y')){
					$nummer++;
					if($nummer <= 9){
						$nummer = '00'.$nummer;
					} else if ($nummer <= 99){
						$nummer = '0'.$nummer;
					} 
				} else {
					$nummer = '001';
				}
			} else {
				$nummer = '001';
			}
		}		
		$data_arr = array('nummer' => $nummer);
		if ($local){
			return $data_arr;
		} else {
			$this->output
				 ->set_content_type('application/json')
				 ->set_output(json_encode($data_arr));
		}
	}	
	public function bewerken($dossier_id)
	{
		$this->load->model('Dossiersmodel');
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
		$dossier = $this->Dossiersmodel->dossier_info('data', array('_id' => new MongoId($dossier_id)));
		$dossier = $dossier[0];
		$search_arr = array();
		$search_arr['dossier'] = $dossier;	
		if (user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){ 	
			$search_arr['active_tab'] = 'stafdienst';
		} elseif (user_access(array('Secretariaat'))){
			$search_arr['active_tab'] = 'secretariaat';
		} else {
			$search_arr['active_tab'] = 'bijlagen';
		} 
		$oorzaken = $this->Beherenmodel->oorzaken_lijst('select', 'oorzaken');		
		$search_arr['oorzaken'] = $oorzaken;	
		$te_behandelen = $this->Beherenmodel->te_behandelen_lijst('ja', 'data');
		$parlementairen = $this->Beherenmodel->parlementairen_lijst('ja', 'data');
		$username = $this->session->userdata('username');
		$user = $this->Usermodel->user_data($username, 'normal');
		$search_arr['location'] = $user['location']['afkorting'];
		if ($dossier['te_behandelen_door'] != ''){
			if (!user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){				
				$dossier['te_behandelen_door'] = $user['location']['afkorting'];
			}
			$doorsturen = $this->Beherenmodel->doorsturen_lijst($dossier['te_behandelen_door']);
		} else {
			$doorsturen = '';
		}
		$search_arr['doorgestuurd_naar'] = $doorsturen;
		$search_arr['te_behandelen'] = $te_behandelen;
		$search_arr['parlementairen'] = $parlementairen;
		$search_arr['user'] = $user;
		$data_arr = array('dossier' => $this->load->view('dossier/dossier_form',$search_arr, true), '_id' => $dossier['_id'], 'type' => $search_arr['dossier']['type']);	
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($data_arr));
	}
	public function view($dossier_id)
	{
		$this->load->model('Dossiersmodel');
		$this->load->model('Usermodel');
		$query = $this->filter(TRUE);		
		$dossier = $this->Dossiersmodel->dossier_info('data', $query);
		$stop = false;
		$search_arr = array();
		$search_arr['next'] = array();
		$search_arr['prev'] = array();
		$username = $this->session->userdata('username');
		$user = $this->Usermodel->user_data($username, 'normal');
		$search_arr['user'] = $user;
		foreach ($dossier as $key) {
			if ($stop){
				$search_arr['next'] = $key;
				break;
			}
			if ($key['_id'] == new MongoId($dossier_id)){
				$search_arr['dossier'] = $key;
				$stop = true;
			}
			if(!$stop){				
				$search_arr['prev'] = $key;				
			} 
		}
		$filter = array('filter_view' => false);
		foreach ($_POST as $key => $value){
			switch($key){
				case 'datum_melding_van':
					if ($value != '01-01-'.date('Y')){
						$filter['datum_melding_van'] = $value;
						$filter['filter_view'] = true;
					};
					break;
				case 'datum_melding_tot':
					if ($value != date('d-m-Y')){
						$filter['datum_melding_tot'] = $value;
						$filter['filter_view'] = true;
					};
					break;
				default:
					if ($value != ''){
						$filter[$key] = $value;
						if ($key != 'type'){
							$filter['filter_view'] = true;
						}
					}
			} 
		}		
		$data_arr = array('dossier' => $this->load->view('dossier/dossier_view',$search_arr, true), '_id' => $search_arr['dossier']['_id'], 'filter' => $filter);	
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($data_arr));
	}
	public function csv()
	{
		$this->load->model('Dossiersmodel');
		$this->load->model('Usermodel');
		$username = $this->session->userdata('username');
		$user = $this->Usermodel->user_data($username, 'normal');
        $location = $user['location']['afkorting'];
		$this->load->model('Dossiersmodel');
		$query = $this->filter(TRUE);		
		$dossier = $this->Dossiersmodel->dossier_info('data', $query);
		$date = date('d-m-Y-h-i-s');	
		$dossier_vars = array('nummer_pv', 'te_laat', 'onderwerp', 'datum_melding', 'referentie_kab', 'parlementarier', 'omschrijving', 'antwoord_ontvangen', 'te_behandelen_door', 'doorgestuurd_naar', 'naar_staf_tegen', 'herinnering_op', 'uitgeschreven_op',  'datum_kabinet', 'datum_antwoord_binnen', 'bijlagen');			
		$fp = fopen(FILEPATH. 'CSV/rapport_'.$date .'.csv', 'w' );				
		$dossier_id = '';
		$header = array();        
		$vertalen = array(
			"beantwoord"=> "Beantwoord",
			"te_laat" => "Te laat",
			"type"=> "Type",
			"dossiernummer"=> "Dossiernummer",
			"onderwerp"=> "Onderwerp",
			"datum_melding"=> "Datum PV",
			"datum_antwoord_binnen" => "Naar stafdienst",
			"gemeente"=> "Gemeente",
			"wegnummer"=> "Wegnummer",
			"wegbenaming"=> "Wegbenaming",
			"huisnr"=> "Huisnr",
			"kruispunt"=> "kruispunt",
			"referentie_kab"=> "Referentie kabinet",
			"parlementarier"=> "Parlementarier",
			"aanvrager"=> "aanvrager",
			"straat_nr"=> "Straat",
			"postcode_gemeente"=> "Gemeente",
			"emailadres"=> "Email adres",
			"telefoon"=> "Telefoon",
			"omschrijving"=> "Omschrijving",
			"aard"=> "Aard",
			"nummer_pv"=> "PV",
			"antwoord_tegen"=> "Antwoord tegen",
			"te_behandelen_door"=> "Te behandelen door",
			"doorgestuurd_naar"=> "Doorsturen naar",
			"antwoord_ontvangen"=> "Antwoord ontvangen",
			"referentie"=> "Referentie",
			"naar_staf_tegen"=> "Naar Staf tegen",
			"herinnering_op"=> "Herinnering op",
			"uitgeschreven_op"=> "Uitgeschreven op",
			"datum_kabinet"=> "Datum Kabinet",
			"bijlage_opmerking"=> "Bijlage Opmerking",
			"bijlagen"=> "Bijlagen",
			"aangemaakt_door"=> "Aangemaakt door",
			"aangemaakt_op"=> "Aangemaakt op"
		);
		foreach ($dossier_vars as $key => $dossier_value) {
			array_push($header, $vertalen[$dossier_value]);
		}		
		fputcsv($fp, $header, ';');
		foreach ($dossier as $key => $pv){
			$bijlage = '';
			if (array_key_exists('bijlagen', $pv)){
				if(is_array($pv['bijlagen'])){
					foreach ($pv['bijlagen'] as $k => $b){
						if ($bijlage != ''){
							$bijlage .= ', '.$pv['bijlagen'][$k]['name'];
						} else {
							$bijlage = $pv['bijlagen'][$k]['name'];
						}				
					}
				} else {
					$bijlage = $pv['bijlagen'];
				}
			}
			if (!array_key_exists('antwoord_ontvangen', $pv) || $pv['antwoord_ontvangen'] === '' || !is_object($pv['antwoord_ontvangen'])){
				if (array_key_exists('antwoord_ontvangen', $pv) && $pv['antwoord_ontvangen'] != ''){
					$antwoord_ontvangen = $pv['antwoord_ontvangen'];
				}
				$antwoord_ontvangen = '';
			} else {
				$antwoord_ontvangen = date('d-m-Y', $pv['antwoord_ontvangen']->sec);
			}
			if (!array_key_exists('doorgestuurd_naar', $pv)){
				$pv['doorgestuurd_naar'] = '';
			}
			if (!array_key_exists('naar_staf_tegen', $pv) || $pv['naar_staf_tegen'] === '' || !is_object($pv['naar_staf_tegen'])){
				$naar_staf_tegen = '';
			} else {
				$naar_staf_tegen = date('d-m-Y', $pv['naar_staf_tegen']->sec);				
			}
            $antwoord_tegen = "";
            if ($pv['antwoord_tegen']!=''){
                $antwoord_tegen = date('d-m-Y', $pv['antwoord_tegen']->sec);
            } 
            $naar_stafdienst = "";
            if ($pv['secretariaat'][$location]['datum_antwoord_binnen']!=''){
                $naar_stafdienst = date('d-m-Y', $pv['secretariaat'][$location]['datum_antwoord_binnen']->sec);
            } 
			$te_laat = 'Onberekenbaar';
			if (array_key_exists('antwoord_ontvangen', $pv) && array_key_exists('naar_staf_tegen', $pv)){
				if (is_object($pv['antwoord_tegen']) && is_object($pv['naar_staf_tegen'])){
					if ($pv['naar_staf_tegen']->sec >= $pv['antwoord_ontvangen']->sec){
						$te_laat = 'Nee';
					} else {
						$te_laat = 'Ja';
					}
				}
			}
			fputcsv($fp, 
				array(
					$pv['nummer_pv'], $te_laat, utf8_decode($pv['onderwerp']), date('d-m-Y', $pv['datum_melding']->sec), $pv['referentie_kab'], utf8_decode($pv['parlementarier']), utf8_decode($pv['omschrijving']), $antwoord_tegen, 
					$pv['te_behandelen_door'], 
					$pv['doorgestuurd_naar'],
					$naar_staf_tegen, 
					date('d-m-Y', $pv['herinnering_op']->sec), 
					date('d-m-Y', $pv['herinnering_op']->sec), 
					date('d-m-Y', $pv['uitgeschreven_op']->sec),
					$naar_stafdienst,
					$bijlage
				), ';');
		}
		fclose($fp);
		$data = array('url' => base_url().'files/CSV/rapport_'.$date .'.csv');
		$this->output
				 ->set_content_type('application/json')
				 ->set_output(json_encode($data));
	}
	public function beantwoord($id)
	{
		$this->load->model('Dossiersmodel');
		if ($this->Dossiersmodel->beantwoord($id)){
			$this->output
				 ->set_content_type('application/json')
				 ->set_output(json_encode(array('query' => 'ok')));
		} else {
			$this->output
				 ->set_content_type('application/json')
				 ->set_output(json_encode(array('query' => 'nok')));
		}
	}	
	public function zoeken()
	{
		$this->load->model('Dossiersmodel');
		$tekst = $_POST['text'];
		$resultaat = $this->Dossiersmodel->zoeken($tekst);
		
		$this->output
				 ->set_content_type('application/json')
				 ->set_output(json_encode($resultaat));
	}
	public function filter($intern = FALSE)
	{
		$this->load->model('Dossiersmodel');
		$this->load->model('Usermodel');
		$username = $this->session->userdata('username');
		$user_pref = $this->Usermodel->set_config($username, $_POST['type'], $_POST['lijst']);
		$user = $this->Usermodel->user_data($username, 'normal');
		$location = array('location' => $user['location']['afkorting']);
		$query = array();
		if (!user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){
			$query['te_behandelen_door'] = array('$regex' => '.*'.$user['location']['afkorting'].'.*');
		} else {
			if ($_POST['te_behandelen_door'] != ''){
				$query['te_behandelen_door']= array('$regex' => '.*'.$_POST['te_behandelen_door'].'.*');
			}
		}
		if ($_POST['type'] != ''){
			$query['type'] = $_POST['type'];
		}
		if ($_POST['nummer_PV'] != ''){
			$query['nummer_pv'] = new MongoRegex('/'.$_POST['nummer_PV'].'/');
		}
		if ($_POST['nummer_kab'] != ''){
			$query['nummer_kab'] = new MongoRegex('/'.$_POST['nummer_kab'].'/');
		}
		if ($_POST['onderwerp'] != ''){
			$query['onderwerp']['$regex'] = new MongoRegex('/'.str_replace('_', '/', $_POST['onderwerp']).'/i');
		}
		if ($_POST['doorsturen_naar'] != ''){
			$query['secretariaat.'.$location['location'].'.doorsturen_naar'] = array('$regex' => new MongoRegex('/'.$_POST['doorsturen_naar'].'/'));
		}
		if ($_POST['datum_kabinet_ingevuld'] != ''){
			if ($_POST['datum_kabinet_ingevuld'] == 'ingevuld'){
				$query['datum_kabinet']['$gt'] = new MongoDate(strtotime('10 September 2000'));
			} else {
				$query['datum_kabinet']['$lt'] = new MongoDate(strtotime('10 September 2000'));
			}
		}		
		if ($_POST['beantwoord'] != ' '){
			$query['beantwoord'] = $_POST['beantwoord'];
		}
		if ($_POST['datum_melding_van'] != ''){
			$query['datum_melding']['$gt'] = new MongoDate(strtotime($_POST['datum_melding_van'].'-1 day'));
		}
		if ($_POST['datum_melding_tot'] != ''){
			$query['datum_melding']['$lt'] = new MongoDate(strtotime($_POST['datum_melding_tot'].'+1 day'));
		}		
		if ($_POST['parlementarier'] != ''){
			$query['parlementarier'] = $_POST['parlementarier'];
		}
		if ($_POST['parlementarier'] != ''){
			$query['parlementarier'] = $_POST['parlementarier'];
		}
		if ($_POST['naar_staf_tegen_van'] != ''){
			$query['naar_staf_tegen']['$gt'] = new MongoDate(strtotime($_POST['naar_staf_tegen_van'].'-1 day'));
		}
		if ($_POST['naar_staf_tegen_tot'] != ''){
			$query['naar_staf_tegen']['$lt'] = new MongoDate(strtotime($_POST['naar_staf_tegen_tot'].'+1 day'));
		}		
		if (!$intern){
			if (array_key_exists('voorkeuren', $user)){
				if ($user['voorkeuren']['lijst'] == 'tabel'){
					$dossier_list = $this->Dossiersmodel->dossier_info('list', $query, $location);	
				} else if ($user['voorkeuren']['lijst'] == 'details'){
					$dossier_data = $this->Dossiersmodel->dossier_info('data', $query);	
					$dossier_list = $this->load->view('dossier/list_view', array('dossier_list'=> $dossier_data, 'user_data' => $user, 'location' => $user['location']['afkorting']), true);
				}
			} else { 
				$dossier_list = $this->Dossiersmodel->dossier_info('list', $query);
			}			
			$dossier_count = $this->Dossiersmodel->dossier_info('count', $query);
			$data_arr = array('table' => $dossier_list, 'start' => strtotime($_POST['datum_melding_van']), 'count' => $dossier_count);	
			$this->output
				 ->set_content_type('application/json')
				 ->set_output(json_encode($data_arr));
		} else {
			return $query;
		}		
	}
}
