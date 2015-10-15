<?php
class Dossiers extends CI_Controller 
{
	public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        if (($this->session->userdata('logged_in') == FALSE))
		{
        	if(isset($_COOKIE['AWV_secretariaat_rm'])){
        		$this->load->model('Usermodel');
				$this->Usermodel->autologon();
			}
		}
    }
	public function index()
	{
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url().'index.php/'.current_url());
		}
		
		$this->load->model('Beherenmodel');
		$this->load->model('Dossiersmodel');
		$this->load->model('Usermodel');	
		$this->load->library('table');								
		$username = $this->session->userdata('username');
		$profile_data = array('profile_data' => $this->Usermodel->profile($username));
		$message = '';
		$user_data_arr = $this->Usermodel->profile();
		$user_data = array('user_data' => $user_data_arr['user']);
		$username = $this->session->userdata('username');
		$user = $this->Usermodel->user_data($username, 'normal');
		$data = array('type' => 'parlementaire_vragen');
		if (!user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){
			$user = $this->Usermodel->user_data($username, 'normal');
			$data['te_behandelen_door'] = $user['location']['afkorting'];
		};
		
		if (array_key_exists('voorkeuren', $user)){
			$data = array('type' => $user['voorkeuren']['type']);
			if ($user['voorkeuren']['lijst'] == 'tabel'){
				$dossier_list = $this->Dossiersmodel->dossier_info('list', $data, array('location'=> $user['location']['afkorting']), array(), $user_data_arr);	
			} else if ($user['voorkeuren']['lijst'] == 'details'){
				$dossier_data = $this->Dossiersmodel->dossier_info('data', $data);	
				$dossier_list = $this->load->view('dossier/list_view', array('dossier_list'=> $dossier_data, 'location' => $user['location']['afkorting'], 'user_data' => $user), true);
			} else {
				$dossier_list = $this->Dossiersmodel->dossier_info('list', $data, array('location'=> $user['location']['afkorting']), array(), $user_data_arr);	
			}
		} else {
			$dossier_list = $this->Dossiersmodel->dossier_info('list', $data, array('location'=> $user['location']['afkorting']), array(), $user_data_arr);	
		}		
		$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'overzicht','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
		$oorzaken = $this->Beherenmodel->oorzaken_lijst('select', 'oorzaken');
		$te_behandelen = $this->Beherenmodel->te_behandelen_lijst('ja', 'data');
		$parlementairen = $this->Beherenmodel->parlementairen_lijst('ja', 'data');
		$doorsturen_naar = $this->Beherenmodel->doorsturen_lijst($user['location']['afkorting'], 'data');
		$data = array(
			'message' => $message,
			'title' => 'Verrekeningen',
			'active_page' => 'overzicht',
			'sub_title' => 'Gebruikerslijst',
			'blocks' => $user_block,			
			'user_data' => $this->Usermodel->user_data($username, 'normal'),
			'user_image' => $this->session->userdata('image'),
			'content' => $dossier_list,
			'oorzaken' => $oorzaken,
			'filter_view' => "normal",
			'te_behandelen' => $te_behandelen,
			'parlementairen' => $parlementairen,
			
			'doorsturen_naar' => $doorsturen_naar
		);	
		$page = $this->load->view('dossiers_templ',$data);
	}
	public function toevoegen($active_tab = 'stafdienst')
	{
		$this->load->model('Beherenmodel');
		$oorzaken = $this->Beherenmodel->oorzaken_lijst('select', 'oorzaken');
		$wegen = $this->Beherenmodel->lijsten_lijst('select', 'wegen');
		$te_behandelen = $this->Beherenmodel->te_behandelen_lijst('ja', 'data');
		$parlementairen = $this->Beherenmodel->parlementairen_lijst('ja', 'data');
		$data_arr = array('form' => $this->load->view('dossier/dossier_form',array('active_tab' => $active_tab, 'oorzaken' => $oorzaken, 'wegen' => $wegen, 'te_behandelen' => $te_behandelen, 'parlementairen' => $parlementairen), true));	
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($data_arr));
	}
	public function view($dossier_id)
	{
		if ($this->session->userdata('logged_in') == FALSE)
		{
			$url = explode('index.php/', current_url());
			redirect(base_url().'index.php/welcome/redirect/'. preg_replace('"/"', '_', $url[1]));
		}
		$this->load->model('Dossiersmodel');
		$this->load->model('Usermodel');
		$this->load->model('Beherenmodel');
		$username = $this->session->userdata('username');
		$query = array('_id' => $dossier_id);
		
		$stop = false;
		$search_arr = array();
		$search_arr['next'] = array();
		$search_arr['prev'] = array();
		$username = $this->session->userdata('username');
		$user = $this->Usermodel->user_data($username, 'normal');
		$dossier = $this->Dossiersmodel->dossier_info('data', $query);
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
		$dossier_list = $this->Dossiersmodel->dossier_info('list', array(), array('location' => $user['location']['afkorting']));			
		$view = $this->load->view('dossier/dossier_view',$search_arr, true);
		$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'overzicht','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
		$data = array(
			'title' => 'Verrekeningen',
			'active_page' => 'overzicht',
			'blocks' => $user_block,			
			'user_data' => $this->Usermodel->user_data($username, 'normal'),
			'user_image' => $this->session->userdata('image'),
			'content' => $dossier_list,
			'view' => $view,
			'filter_view' => "none"
		);	
		$page = $this->load->view('dossiers_templ',$data);
	}
	public function opslaan()
	{
		$this->load->model('Dossiersmodel');
		$edit = false;
		$this->Dossiersmodel->save('dossiers', $_POST, $edit);
		$this->view('verrekeningen', str_replace('/', '_', $_POST['dossier']));
	}
	public function update_dossiers() {
		$this->load->model('Dossiersmodel');
		$this->Dossiersmodel->force_update();
	}
	
	public function bewerken($dossier_id){
		if (($this->session->userdata('logged_in') == FALSE))
		{
			$url = explode('index.php/', current_url());
			redirect(base_url().'index.php/welcome/redirect/'. preg_replace('"/"', '_', $url[1]));
		}
		$this->load->model('Dossiersmodel');
		$this->load->model('Usermodel');
		$this->load->model('Beherenmodel');
		$username = $this->session->userdata('username');
		$query = array('_id' => $dossier_id);
		$dossier = $this->Dossiersmodel->dossier_info('data', array('_id' => new MongoId($dossier_id)));
		$dossier = $dossier[0];
		$username = $this->session->userdata('username');
		$user = $this->Usermodel->user_data($username, 'normal');
		$dossier_list = $this->Dossiersmodel->dossier_info('list', array(), array('location' => $user['location']['afkorting']));	
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
		$view = $this->load->view('dossier/dossier_form',$search_arr, true);
		$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'overzicht','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
		$data = array(
			'title' => 'Verrekeningen',
			'active_page' => 'overzicht',
			'blocks' => $user_block,			
			'user_data' => $this->Usermodel->user_data($username, 'normal'),
			'content' => $dossier_list,
			'view' => $view,
			'filter_view' => "none"
		);	
		$page = $this->load->view('dossiers_templ',$data);
	}

	public function files($id)
	{
		$this->load->helper('download');
		$this->load->model('Dossiersmodel');
		$file = $this->Dossiersmodel->filesearch($id);
		force_download($file->file['filename'], $file->getBytes());	
		
	}
}
