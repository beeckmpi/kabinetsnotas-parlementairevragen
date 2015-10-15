<?php
class start extends CI_Controller 
{
	public function __construct()
    {
    	parent::__construct();
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
			redirect(base_url());
		}		
		$this->load->model('Usermodel');
		$username = $this->session->userdata('username');
		$profile_data = array('profile_data' => $this->Usermodel->profile($username));		
		$message = '';
		$user_data_arr = $this->Usermodel->profile();
		$user_data = array('user_data' => $user_data_arr['user']);
		
		$data = array(				
			'message' => $message,
			'title' => 'START',
			'active_page' => 'start',
			'user_data' => $this->Usermodel->user_data($username, 'normal'),
		);	
		$this->load->view('start_templ',$data);	
	}	
}
?>