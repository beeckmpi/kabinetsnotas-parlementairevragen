<?php
class User extends CI_Controller 
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
	public function user($username)
	{
		$this->load->model('Usermodel');
		$profile_data = $this->Usermodel->profile($username);
		if (empty($profile_data)){
			$data = array(				
					'form' => '<div id="big_text">Er is iets foutgelopen!!</div>
								<div id="sub_text">De pagina is momenteel niet beschikbaar.</div>',
				);
			echo $this->load->view('user/user_on_index',$data, true);
		} else {
			
			$profile_data = array('profile_data' => $profile_data);
			$data = array(				
					'form' => $this->load->view('user/profile', $profile_data, true),
				);
			echo $this->load->view('user/user_on_index',$data, true);
		}
	}
	
	public function edit_account($username){
		$this->load->model('Usermodel');
		$this->load->model('Beherenmodel');
		$logged_in_user = $this->session->userdata('username');
        $rollen = $this->session->userdata('rollen');
		if ($username==''){
			$username = $logged_in_user;
		}
		$profile_data = array(
			'profile_data' => $this->Usermodel->profile($username),
			'provincies' => $this->Beherenmodel->provincies_lijst(), 
			'districten' => $this->Beherenmodel->districten_lijst('select'),
			'rollen' 	 => $this->Usermodel->user_roles($rollen[0]),
			'user_data' => $this->Usermodel->profile($logged_in_user),
		);		
		$edit = $this->uri->segment(4, 0);	
		$message = '';
		$selected = array('account' => '', 'password' => '', 'picture' => '', 'notifications' => '', 'security' => '', 'applications' => '');		
		$data = array(		
			'message' => $message,
			'title' => $username,
			'sub_title' => 'Account bewerken',		
			'user_data' => $this->Usermodel->user_data($logged_in_user, 'normal'),
			'selected' => $selected,
			'content' => $this->load->view('user/settings/account', $profile_data, true),
		);	
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($data));
	}

	public function userList($order ='name', $page = 0, $name = ''){
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');
		$username = $this->session->userdata('username');
		$profile_data = array('profile_data' => $this->Usermodel->profile($username));		
		$message = '';
		$username = $this->session->userdata('username');
		$data = '';
		if (!user_access(array('Administrators'))) {
			if (user_access(array('Secretariaat'))){
			$user = $this->Usermodel->user_data($username, 'normal');
			$data = $user['location']['afkorting'];
			}
		}
		$user_data_arr = $this->Usermodel->profile('', $page-1, 25, $order, $name, $data);
		$districten_arr = $this->Beherenmodel->getDistricten();
        $districten = array();
         foreach ($districten_arr as $key => $value) {
            $districten[$districten_arr[$key]['code']] = $districten_arr[$key]['district'];
        }
		$selected = array();
		$selected = array('username' => '', 'name' => '', 'location.provincie' => '', 'user_role.0' => '');
		foreach ($selected as $key => $value){
			if ($order == $key){
				$selected[$order] = 'selected=selected';
			}
		}
		$user_data = array('user_data' => $user_data_arr['user'], 'selected' => $selected, 'show_all' => false, 'count' => $user_data_arr['count'], 'districten' => $districten);
		$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'Beheren','user_data' => $this->Usermodel->user_data($username, 'normal')), true));
		$data = array(				
			'content' => $this->load->view('user/user_list', $user_data, true),		
			'order' => $order	
		);	
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($data));
	}
	
	
	public function user_settings($type, $user=''){
		$this->load->model('Usermodel');
		$logged_in_user = $this->session->userdata('username');
		if ($user==''){
			$user = $logged_in_user;
		}
		$username= $this->session->userdata('username');
		$profile_data = array('profile_data' => $this->Usermodel->profile($username));
		
		$edit = $this->uri->segment(4, 0);	
		$message = '';
		$selected = array('account' => '', 'password' => '', 'picture' => '', 'notifications' => '', 'security' => '', 'applications' => '');
		if ($type == 'account'){
			
			if ($username == ''){
				$username = $this->uri->segment(1, 0);
			}
			$user_data = array('user_data' => $this->Usermodel->profile($username));		
			if (empty($user_data)){
				$data = array(				
						'content' => '<div id="big_text">Er is een fout!!</div>
									<div id="sub_text">De pagina is niet beschikbaar momenteel.</div>',
					);
				$this->load->view('def_templ',$data);
			} else {			
				$data = array(		
						'message' => $message,
						'title' => $username,
						'sub_title' => 'Gebruikersgegevens',								
						'content' => $this->load->view('user/profile', $user_data, true),
					);
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($data));
			}	
		} else if ($type == 'password') {
			if (!empty($_POST['old_password'])){
				$save_password =$this->Usermodel->user_password_change($username);
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
					'sub_title' => 'Wachtwoord',		
					'user_data' => $this->Usermodel->user_data($username, 'normal'),
					'selected' => $selected,
					'content' => $this->load->view('user/settings/password', $profile_data, true),
				);	
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($data));
		} 
	}

	public function createToken(){
		// Create a state token to prevent request forgery.
		// Store it in the session for later validation.
		$state = md5(rand());
		$app['session']->set('state', $state);
		// Set the client ID, token state, and application name in the HTML while
		// serving it.
		return $app['twig']->render('index.html', array(
		    'CLIENT_ID' => CLIENT_ID,
		    'STATE' => $state,
		    'APPLICATION_NAME' => APPLICATION_NAME
		));
	}
	
	public function gplusAuth(){
		
	}

	public function resetPassword($username)
	{
		$this->load->model('Usermodel');
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 8; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
		$password = implode($pass);
		$save_password = $this->Usermodel->user_password_change($username, 'reset', $password);
		$username_editor = $this->session->userdata('username');
		$editor_data = $this->Usermodel->user_data($username_editor, 'normal');
		$user_data = $this->Usermodel->user_data($username, 'normal');
		$data = array('email' => $user_data['email'], 'success' => 'yes');
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
			<p>Uw wachtwoord is gereset door <strong>'.$editor_data['first_name'].' '.$editor_data['name'].'</strong>.</p>
			<p>Uw aanmeldgegevens zijn nu:<br />
			<ul><li>Gebruikersnaam: '.$user_data['username'].'</li><li>Wachtwoord: '.$password.'</li></ul>
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
		$this->email->to($user_data['email']); 			
		$this->email->subject('Parlementaire Vragen en KabinetsNota\'s: Wachtwoord reset');
		$this->email->message($message);				
		$this->email->send();
		
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}
	
}
?>