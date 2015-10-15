<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index($redirect = '')
	{
		if (($this->session->userdata('logged_in') == FALSE))
		{
        	if(isset($_COOKIE['AWV_secretariaat_rm'])){
        		$this->load->model('Usermodel');
				$this->Usermodel->autologon();
			}
		}
		if (($this->session->userdata('logged_in') == TRUE))
		{
			redirect('dossier/dossiers/index', 'location');  
		} 
		else
		{
			$state = md5(rand());
			$this->session->set_userdata(array('state' => $state));
			$data = array(				
				'login_form' => $this->load->view('user/quick_login_form', array('error' => ' ', 'redirect' =>  $redirect, 'state' => $state), true)
			);
			$this->load->view('welcome_message', $data);
		}
		
	}
	public function redirect($redirect)
	{
		$data = array(				
			'login_form' => $this->load->view('user/quick_login_form', array('error' => ' ', 'redirect' =>  $redirect ), true)
		);
		$this->load->view('welcome_message', $data);
	}	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
?>
