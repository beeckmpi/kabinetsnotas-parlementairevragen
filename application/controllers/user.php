<?php
class User extends CI_Controller 
{		
	private $id;
	private $username;
	private $password;
	private $emailadress;
	private $profile;
	private $account;
	
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
	public function getUsername()
	{
		return $this->username;
	}
	public function setUsername($username)
	{
		$this->username = $username;
	}
	public function getPassword()
	{
		return $this->password;
	}
	public function setPassword($password)
	{
		$this->password = hash("sha512", $password);
	}
	public function checkUsername()
	{
		$this->load->model('Usermodel');
		$username_validation = $this->Usermodel->check_username($_POST['reg_username']);
		$this->json->json_encode($username_validation);
	}
	public function checkEmail()
	{
		$this->load->model('Usermodel');
		$username_validation = $this->Usermodel->check_email($_POST['email']);
	}
	public function index()
	{
		$this->load->view('login_form', array('error' => ' '));
	}
	public function login($ajax = null, $redirect = null)
	{		
		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');		
		$this->load->model('Usermodel');
		if ($this->form_validation->run()){
			if($this->Usermodel->login()){
				if ($ajax == 'null'){
					redirect(base_url());
				} else {
					$this->load->model('Beherenmodel');
					$this->load->model('Usermodel');					
					$this->load->model('Dossiersmodel');		
												
					$username = $this->session->userdata('username');
					$profile_data = array('profile_data' => $this->Usermodel->profile($username));
					$message = '';
					$user_data_arr = $this->Usermodel->profile();
					$user_data = array('user_data' => $user_data_arr['user']);
					$data = array('type' => 'parlementaire_vragen');
					if (!user_access(array('Administrators', 'Stafdienst'))){
						$user = $this->Usermodel->user_data($username, 'normal');
						$data['te_behandelen_door'] = $user['location']['afkorting'];
					};
					$user = $this->Usermodel->user_data($username, 'normal');
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
						'content' => $dossier_list,
						'oorzaken' => $oorzaken,
						'filter_view' => "normal",
						'te_behandelen' => $te_behandelen,
						'parlementairen' => $parlementairen,
						'doorsturen_naar' => $doorsturen_naar,
						'ajax' => true,
						'redirect' => $_POST['redirect']
					);	
					$page = $this->load->view('dossiers_templ',$data, true);	
					$data_arr = array('page' => $page);
					$this->output
						 ->set_content_type('application/json')
						 ->set_output(json_encode($data_arr));	
				}
				
			} else {
				if ($ajax == null){
					$data = array(				
						'login_form' => $this->load->view('user/quick_login_form', array('error' => 'Er is een fout opgetreden' ), true)
					);
					$this->load->view('welcome_message', $data);
				} else {
					$data_arr = array('error_msg' => 'U kan zich niet aanmelden, de login of het wachtwoord is niet correct. Gelieve opnieuw te proberen of contact op te nemen met de <a href="mailto:ICTWegenAntwerpen@mow.vlaanderen.be">administrators</a>');	
					$this->output
						 ->set_content_type('application/json')
						 ->set_output(json_encode($data_arr));
				}	
			}
		} else {
			if ($ajax == null){
					$data = array(				
						'login_form' => $this->load->view('user/quick_login_form', array('error' => 'Er is een fout opgetreden' ), true)
					);
					$this->load->view('welcome_message', $data);
				} else {
					$data_arr = array('error_msg' => 'U kan niet aanmelden, de login of het wachtwoord is niet correct');	
					$this->output
						 ->set_content_type('application/json')
						 ->set_output(json_encode($data_arr));
				}	
		}
	}
	public function logout(){
		$this->session->sess_destroy();
		redirect(base_url());
	}
	public function remove_user($username){
		if (($this->session->userdata('logged_in') == TRUE))
		{			
			$this->load->model('Usermodel');
			if ($this->Usermodel->user_remove($username)){
				$this->userlist();
			}
			
		}
	}
	public function profile($user = '', $message = ''){
		if (($this->session->userdata('logged_in') == TRUE) || (user_access(array('Administrators', 'Secretariaat'))))
		{			
			$this->load->model('Usermodel');			
			$username = $this->session->userdata('username');
			$data = '';
			$user_ = $username;
			if ($user==''){
				$user = $username;
			}				
			$user_data = array('user_data' => $this->Usermodel->profile($user, 0, 0, 'name', '', ''),'viewer_data' => $this->Usermodel->profile($user_, 0, 0, 'name', '', ''));	
			if (empty($user_data)){
				$data = array(				
						'content' => '<div id="big_text">Er is een fout!!</div>
									<div id="sub_text">De pagina is niet beschikbaar momenteel.</div>',
					);
				$this->load->view('def_templ',$data);
			} else {			
				$selected = array('account' => '', 'password' => '', 'picture' => '', 'notifications' => '', 'security' => '', 'applications' => '');
				$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'overzicht','user_data' => $this->Usermodel->user_data($username, 'normal')), true));				
				$data = array(		
						'message' => $message,
						'title' => $username,
						'sub_title' => 'Gebruikersgegevens',	
						'active_page' => 'beheren',
						'blocks' => $user_block,	
						'user_data' => $this->Usermodel->user_data($user_, 'normal'),
						'selected' => $selected,	
						'user_image' => $this->session->userdata('image'),
						'content' => $this->load->view('user/profile', $user_data, true),
					);
				$this->load->view('dossiers_templ',$data);
			}
		} else {
			redirect(base_url());
		}
	}
	public function userList(){
		if (($this->session->userdata('logged_in') == FALSE) || (!user_access(array('Afdelingshoofd', 'Secretariaat', 'Stafdienst'))))
		{
			redirect(base_url());
		}
		$this->load->model('Usermodel');
        $this->load->model('Beherenmodel');
		$username = $this->session->userdata('username');
		$profile_data = array('profile_data' => $this->Usermodel->profile($username));		
		$message = '';
		$data = '';
		$username = $this->session->userdata('username');
		if (!user_access(array('Administrators'))) {
			if (user_access(array('Secretariaat'))){
			$user = $this->Usermodel->user_data($username, 'normal');
			$data = $user['location']['afkorting'];
			}
		}
        $districten_arr = $this->Beherenmodel->getDistricten();
        $districten = array();
        foreach ($districten_arr as $key => $value) {
            $districten[$districten_arr[$key]['code']] = $districten_arr[$key]['district'];
        }
		$user_data_arr = $this->Usermodel->profile('', 0, 0, 'name', '', $data);
		$user_data = array('user_data' => $user_data_arr['user'], 'show_all' => true, 'districten' => $districten, 'selected' => array('username' => '', 'name' => 'selected=selected', 'location.provincie' => '', 'user_role.0' => ''), 'count' => $user_data_arr['count']);
		$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'Beheren','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
		$data = array(				
			'message' => $message,
			'title' => $username,
			'sub_title' => 'Gebruikerslijst',		
			'blocks' => $user_block,
			'active_page' => 'beheren',
			'user_data' => $this->Usermodel->user_data($username, 'normal'),
			'user_image' => $this->session->userdata('image'),
			'content' => $this->load->view('user/user_list', $user_data, true)			
		);	
		$this->load->view('user_settings_templ',$data);
	}
	public function edit($user='', $type='profile'){
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Usermodel');
		$this->load->model('Beherenmodel');
		$username = $this->session->userdata('username');
		if ($user==''){
			$user = $username;
		}		
		$profile_data = array(
			'profile_data' => $this->Usermodel->profile($user), 
			'provincies' => $this->Beherenmodel->provincies_lijst(), 
			'districten' => $this->Beherenmodel->districten_lijst('select'),
			'rollen' 	 => $this->Usermodel->user_roles()
		);		
		$edit = $this->uri->segment(4, 0);	
		$message = '';
		$selected = array('account' => '', 'password' => '', 'picture' => '', 'notifications' => '', 'security' => '', 'applications' => '');
		$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'beheren'), true));
		if ($type == 'profile'){
			if (!empty($_POST['reg_username'])){
				
				if($this->Usermodel->user_account_save($user) == true){
					$message = 'De instellingen zijn opgeslaan.';
					$this->profile($user, $message);	
				}
			} else {
				$selected['account'] = 'selected';			
				$data = array(		
						'message' => $message,
						'title' => $username,
						'active_page' => 'beheren',
						'sub_title' => 'Mijn profiel',	
						'blocks' => $user_block,	
						'user_image' => $this->session->userdata('image'),
						'user_data' => $this->Usermodel->user_data($username, 'normal'),
						'selected' => $selected,
						'content' => $this->load->view('user/settings/account', $profile_data, true),
					);	
				$edit= '';		
				$this->load->view('dossiers_templ',$data);
			}
		}	
	}
	public function settings($type, $user=''){
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Usermodel');
		$username = $this->session->userdata('username');
		if ($user==''){
			$user = $username;
		} 
		
		$user_data = array('profile_data' => $this->Usermodel->profile($user)); 
		
		$edit = $this->uri->segment(4, 0);	
		$message = '';
		$selected = array('account' => '', 'password' => '', 'picture' => '', 'notifications' => '', 'security' => '', 'applications' => '');
		$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'overzicht','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
		if ($type == 'account'){
			$this->profile($username);	
		} else if ($type == 'password') {
			if (!empty($_POST['new_password'])){
				$save_password =$this->Usermodel->user_password_change($user);
				if($save_password['processed'] == true){
					$message = 'Uw wachtwoord is gewijzigd';
				} else {
					$message = $save_password['error'];
				}
			}
			$selected['password'] = 'selected';		
			$data = array(		
					'message' => $message,
					'title' => $username,
					'sub_title' => 'wachtwoord',
					'active_page' => 'beheren',	
					'blocks' => $user_block,
					'user_image' => $this->session->userdata('image'),	
					'user_data' => $this->Usermodel->user_data($username, 'normal'),
					'selected' => $selected,
					'content' => $this->load->view('user/settings/password', $user_data, true),
				);	
				$this->load->view('dossiers_templ',$data);
		} 
		
	}
	public function register()
	{
		if (($this->session->userdata('logged_in') == FALSE))
		{
			redirect(base_url());
		}
		$this->load->model('Usermodel');
		$this->load->model('Beherenmodel');
		$username = $this->session->userdata('username');
        $rollen = $this->session->userdata('rollen');
		$profile_data = array('profile_data' => $this->Usermodel->profile($username));
		$selected = array('account' => '', 'password' => '', 'picture' => '', 'notifications' => '', 'security' => '', 'applications' => '');
		$edit = $this->uri->segment(4, 0);	
		$message = '';
		$sign_up = $this->uri->segment(3, 0);
		$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'Beheren','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
		$form_data = array(
			'error' 	 => "",
			'provincies' => $this->Beherenmodel->provincies_lijst(), 
			'districten' => $this->Beherenmodel->districten_lijst('select'),
			'rollen' 	 => $this->Usermodel->user_roles($rollen[0])
		);	
		if(empty($sign_up)){
			$data = array(				
				'message' => $message,
				'title' => $username,
				'sub_title' => 'Gebruikers account aanmaken',	
				'active_page' => 'beheren',
				'blocks' => $user_block,
				'user_data' => $this->Usermodel->user_data($username, 'normal'),
				'selected' => $selected,
				'content' => $this->load->view('user/register_form', $form_data, true),
			);			
			$this->load->view('user_settings_templ',$data);
		} else {
			$this->load->library('form_validation');
			$this->load->model('Usermodel');		
			$this->form_validation->set_rules('reg_username', 'Username', 'required|min_length[8]|max_length[8]');
			$this->form_validation->set_rules('email', 'Email', 'required');
			$username_validation = $this->Usermodel->check_username($_POST['reg_username']);
			$email_validation = $this->Usermodel->check_email($_POST['email']);
			if ($this->form_validation->run() && $username_validation == TRUE && $email_validation == TRUE){
				$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
			    $pass = array(); //remember to declare $pass as an array
			    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
			    for ($i = 0; $i < 8; $i++) {
			        $n = rand(0, $alphaLength);
			        $pass[] = $alphabet[$n];
			    }
				$password = implode($pass);
				$username = $this->session->userdata('username');
				$user_data = $this->Usermodel->user_data($username, 'normal');
				if (!user_access(array('Administrators', 'Stafdienst'))){
					$_POST['provincie'] = $user_data['location']['provincie'];
					$_POST['rol'] = 'Dossierbeheerder';
				}
				if($this->Usermodel->user_register($password) == true){					
					$profile_data = array('profile_data' => $this->Usermodel->profile($username));		
					$message = '';									
					$new_user_data = $this->Usermodel->user_data($_POST['reg_username'], 'normal');
					$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'Beheren','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
					$data = array(				
						'message' => $message,
						'title' => $username,
						'sub_title' => 'Gebruikerslijst',		
						'blocks' => $user_block,
						'active_page' => 'beheren',
						'user_image' => $this->session->userdata('image'),
						'user_data' => $this->Usermodel->user_data($username, 'normal'),
						'content' => '<div style="margin-left: 40px;"><div id="big_text">De gebruiker is geregistreerd!</div>
								<div id="sub_text"><a class="hapi" href="'.base_url().'index.php/'.$_POST['reg_username'].'">Klik hier om het profiel te zien</a></div></div>',
					);	
					$message = 
						'<style type="text/css">
							p {
								font-family:"Segoe UI", arial; 
								color: #555;
								font-size: 13px;
							}
							ul {
								font-family:"Segoe UI", arial; 
								color: #555;
								font-size: 13px;
							}
						</style>
						<p>Beste,</P>
						<p><strong>'.$user_data['first_name'].' '.$user_data['name'].'</strong> heeft voor u een account aangemaakt op de applicatie Parlementaire Vragen en Kabinetsnota\'s.</p>
						<p>Uw aanmeldgegevens zijn:<br />
						<ul><li>Gebruikersnaam: '.$new_user_data['username'].'</li><li>Wachtwoord: '.$password.'</li></ul>
						<p>Dit wachtwoord werd door de server gegenereerd en enkel naar u gestuurd, u kan uw wachtwoord aanpassen via "wachtwoord" in de menubalk links.</p>
						<p>U kan inloggen via deze url: <a href="'.site_url().'">'.site_url().'</a><br />		
						U kan deze link het beste met Mozilla Firefox openen. </p>
						<p></p>
						<p>Met vriendelijke groeten, 
						<br />
						<br /> 
						De stafdienst</p>';				
					$this->load->library('email');			
					$this->email->from('pv_kb_stafdienst@mow.vlaanderen.be', 'KabinetsNota\'s en Parlementaire Vragen');
					$this->email->to($new_user_data['email']); 				
					$this->email->subject('Parlementaire Vragen en KabinetsNota\'s: Account');
					$this->email->message($message);				
					$this->email->send();
					$this->load->view('user_settings_templ',$data);
				}
			} else {
				$data = array(				
					'message' => $message,
					'title' => $username,
					'sub_title' => 'wachtwoord',	
					'blocks' => $user_block,	
					'user_data' => $this->Usermodel->user_data($username, 'normal'),
					'selected' => $selected,
					'content' => $this->load->view('user/register_form', $form_data, true),
				);
				$this->load->view('user_settings_templ',$data);
			}
		}
	}
	public function gplusAuth($state){		
		$this->load->model('Usermodel');
		if($this->Usermodel->login($_POST['emails'][0]['value'], '', 'google', $_POST['image']['url'])){
			$data_arr = array('login' => TRUE);
			$this->output
				 ->set_content_type('application/json')
				 ->set_output(json_encode($data_arr));
		} else {
			$data_arr = array('error_msg' => 'Uw Google account is nog niet toegevoegd aan de site of u gebruikt uw persoonlijke google account. Enkel mow.vlaanderen.be google accounts werken.');	
			$this->output
				 ->set_content_type('application/json')
				 ->set_output(json_encode($data_arr));
		}		
	}
	public function update_users(){
		$this->load->model('Usermodel');
		$update_users =$this->Usermodel->update_users();
	}
}
?>