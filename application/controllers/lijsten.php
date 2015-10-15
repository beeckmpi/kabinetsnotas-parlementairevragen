<?php
class Lijsten extends CI_Controller 
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
		$this->load->model('Beherenmodel');
		$this->load->model('Usermodel');		
								
		$username = $this->session->userdata('username');
		$profile_data = array('profile_data' => $this->Usermodel->profile($username));
		$lijsten = $this->Beherenmodel->oorzaken_lijst();
		$message = '';
		$user_data_arr = $this->Usermodel->profile();
		$user_data = array('user_data' => $user_data_arr['user']);
		$user_block = array(0 => $this->load->view('blocks/beheren_menu_block', array('active_page' => 'lijsten'), true));
		$data = array(		
			'message' => $message,
			'title' => $username,
			'sub_title' => 'Gebruikersgegevens',	
			'active_page' => 'beheren',
			'blocks' => $user_block,	
			'user_data' => $this->Usermodel->user_data($username, 'normal'),
			'content' =>  $this->load->view('oorzaken-list', $lijsten, true),
		);
		$this->load->view('user_settings_templ',$data);
	}
	public function toevoegen($active_tab = 'start')
	{
		$data_arr = array('form' => $this->load->view('dossier/dossier_form',array('active_tab' => $active_tab), true));	
		$this->output
			 ->set_content_type('application/json')
			 ->set_output(json_encode($data_arr));
	}
	public function opslaan()
	{
		$this->load->model('Beherenmodel');
		$edit = false;
		$this->Beherenmodel->save('lijsten', $_POST, $edit);
		$this->view('verrekeningen', str_replace('/', '_', $_POST['dossier']));
	}
}
