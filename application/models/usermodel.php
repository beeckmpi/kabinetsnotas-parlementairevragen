<?php
Class Usermodel extends CI_Model 
{
	var $autologon_cookie_name = 'AWV_secretariaat_rm';	
	function __Construct()
	{
		parent::__construct();
	}			
	function edit_user()
	{
		
	}
	function login($username = '', $password = '',$type = 'normal', $image = '')
	{
		try{
            if($type == 'normal'){
                if ($username == ''){
                    $username = $_POST['username'];
                    $password = hash("sha512", $_POST['password']);
                }   
            	$user = $this->mongo_db->where(array('username' => $username, 'password' => $password))->get('users');
            } else if ($type == "google") {
                $user = $this->mongo_db->where(array('email' => $username))->get('users');
            }
            if (empty($user)){
                return false;
            } else {
                $autologon_val = hash("sha512", 'remme_'.$user[0]['username'].'_'.$user[0]['email'].'_'.rand(0, 9999999));
                $cookie_time = time() + (3600 * 24 * 30);
                setcookie($this->autologon_cookie_name, 'user='.$user[0]['username'].'&hash='.$autologon_val, $cookie_time);
                if (isset($autologon_val)){
                   $autologon_val = 'autologon_val => '.$autologon_val.',';
                } else {
                   $autologon_val = '';
                }
                $data = array(
                    'username' => $user[0]['username'],
                    'email' => $user[0]['email'],
                    $autologon_val,
                    'rollen' => $user[0]['user_role'],
                    'logged_in' => TRUE,
                    'location' => $user[0]['location']['afkorting'],
                    'image' => $image        
                );
                $this->session->set_userdata($data);                    
                return true;    
            }          				
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}	
	function autologon(){
		parse_str($_COOKIE[$this->autologon_cookie_name]);		
		$exists = $this->mongo_db->where(array('username' => $user))->get('users');
		if (isset($exists[0]['autologon_val'])){
			if ($exists[0]['autologon_val'] == $hash){
				if ($this->login($user, $user[0]['password'])){
				    $data = array(
                        'username' => $exists[0]['username'],
                        'email' => $exists[0]['email'],
                        $autologon_val,
                        'rollen' => $exists[0]['user_role'],
                        'logged_in' => TRUE,
                        'location' => $exists[0]['location']['afkorting']
                    );
                    $this->session->set_userdata($data);
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}		
	} 
	function check_username($username){
		try {
			$exists = $this->mongo_db->where('username', $username)->get('users');
			if (empty($exists)){
				return true;
			} else {
				return false;
			}
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}
	function check_email($email){
		try {
			$exists = $this->mongo_db->where('email', $email)->get('users');
			if (empty($exists)){
				return true;
			} else {
				return false;
			}
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}
	function profile($username = '', $page = 0, $offset = 0, $sort = 'name', $search = '', $user_location = ''){
		try {
			if ($username != ''){
				$search = array('username' => $username);
				if ($user_location != ''){
					$search['location.afkorting'] = $user_location;
				}
				$return = $this->mongo_db->where($search)->get('users');
			} else {
				$pager = $page*$offset;				
				if($search != ''){
					$search = array('name' => array('$regex' => $search, '$options' => 'i'));
				} else {
					$search = array('name' => array('$exists' => true));
				}
				if ($user_location != ''){
					$search['location.afkorting'] = $user_location;
				}
				$count =$this->mongo_db->where($search)->order_by(array($sort => 1))->count('users');
				$user = $this->mongo_db->where($search)->order_by(array($sort => 1, 'name' => 1))->offset($pager)->limit(25)->get('users');							
				$return = array('user' => $user, 'count' => $count);
			}			
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
		return $return;
	}

	function user_account_save($username){
		try {					
			$user_data = $this->mongo_db->where('username', $username)->get('users');
			if (user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){
				$provincie = $this->mongo_db->where('provincie', $_POST['provincie'])->get('provincies');
				$user_data[0]['user_role'][0] = $_POST['rol'];
				$user_data[0]['location'] = array('provincie' => $_POST['provincie'], 'district' => $_POST['district'], 'afkorting' => $provincie[0]['afkorting']);
			} else {
				$user_data[0]['location'] = array('provincie' => $user_data[0]['location']['provincie'], 'district' => $_POST['district'], 'afkorting' =>$user_data[0]['location']['afkorting']);
			}			
			$user_data[0]['username'] = $_POST['reg_username'];
			$user_data[0]['email'] = $_POST['email'];
			$user_data[0]['name'] = $_POST['reg_name'];			
			$user_data[0]['first_name'] = $_POST['reg_first_name'];
			$user_data[0]['initials'] = $_POST['reg_initials'];			
			$this->mongo_db->where('username', $username)->update('users', $user_data[0]);
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}	
		return true;
	}
	function user_remove($username) {
		if ($this->mongo_db->where(array('username' => $username))->delete('users')){
			return true;
		}
	}
	function get_usersDistrict($district){
		try {
			if ($district != ''){
				$user_data = $this->mongo_db->where('location.district', $district)->order_by(array('username'))->get('users');
				return $user_data;
			}
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}
	function user_data($username = '', $type='normal')
	{
		try {
			if ($username != ''){
				$user_data = $this->mongo_db->where('username', $username)->order_by(array('username'))->get('users');
				if ($type == 'normal'){
					if (array_key_exists(0, $user_data)){
						return $user_data[0];
					} else {
						return '';
					}
				}
			} else {
				$user_data = $this->mongo_db->order_by(array('username' => 1))->get('users');
				if ($type == 'normal'){
					return $user_data;
				}
			}
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}
	function email($username = '')
	{
		try {
			if ($username != ''){
				$user_data = $this->mongo_db->where('username', $username)->order_by(array('username'))->get('users');
				if (array_key_exists(0, $user_data)){
					return $user_data[0]['email'];
				} else {
					return '';
				}				
			} 
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}
	function user_password_change($username, $type='normal', $password = '')
	{
		try {
			if ($type == "normal"){
				$user_data = $this->mongo_db->where('username', $username)->get('users');
				if (array_key_exists('old_password', $_POST)){
					if ($user_data[0]['password'] == hash("sha512", $_POST['old_password'])){
						if($_POST['new_password'] == $_POST['repeated_new_password']){
							$user_data[0]['password'] =hash("sha512", $_POST['new_password']); 
							$this->mongo_db->where('username', $username)->update('users', $user_data[0]);
						} else {
							return array('processed' => FALSE, 'error' => 'De nieuwe wachtwoorden komen niet overeen.');
						}
					} else {
						return array('processed' => FALSE, 'error' => 'Uw oud wachtwoord is niet correct');
					}
				} else if (user_access(array('Administrators', 'Stafdienst', 'PCOEVT'))){ 
					if($_POST['new_password'] == $_POST['repeated_new_password']){
						$user_data[0]['password'] =hash("sha512", $_POST['new_password']); 
						$this->mongo_db->where('username', $username)->update('users', $user_data[0]);
					} else {
						return array('processed' => FALSE, 'error' => 'De nieuwe wachtwoorden komen niet overeen.');
					}
				}
			} else if ($type == "reset"){
				if (user_access(array('Administrators', 'Stafdienst', 'Secretariaat'))){
					$user_data = $this->mongo_db->where('username', $username)->get('users'); 
					$user_data[0]['password'] =hash("sha512", $password); 
					$this->mongo_db->where('username', $username)->update('users', $user_data[0]);
				}
			}
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
		return array('processed' => TRUE, 'error' => '');
	}
	function set_config($username, $type, $list='table')
	{
		try {
			$data = array('voorkeuren' => array('type' => $type, 'lijst' => $list));
			$this->mongo_db->where(array('username' => $username))->update('users', array('$set' => $data));
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
		return array('processed' => TRUE, 'error' => '');
	}
	
	function user_password_reset($username, $password){
		try {
			
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}
	function update_users(){
		$user = $this->mongo_db->order_by(array('username' => 1))->get('users');
		foreach ($user as $key => $value){
			$provincie = $this->mongo_db->where('provincie', $user[$key]['location']['provincie'])->get('provincies');
			$user[$key]['location']['afkorting'] = $provincie[0]['afkorting'];
			$this->mongo_db->where('_id', $user[$key]['_id'])->update('users', $user[$key]);
		} 
	}
	
	function user_roles($rol = 'Administrators'){
		$rollen = $this->mongo_db->order_by(array('rol' => 1))->get('rollen');
		$rollen_arr = array();
		foreach ($rollen as $pkey => $pname){
			$p = $rollen[$pkey]['rol'];
            if ($p == 'Administrators'){
                if ($rol == 'Administrators'){
                    $rollen_arr[$p] = $p;
                }
            } else if ($p == 'Stafdienst') {
                if ($rol == 'Administrators' || $rol == 'Stafdienst'){
                    $rollen_arr[$p] = $p;
                }   
            } else {
                $rollen_arr[$p] = $p;   
            }					
		}
		return $rollen_arr;
	}
	
	function user_register($password)
	{
		$data = array(
			'username' 		=> $_POST['reg_username'], 
			'email' 		=> $_POST['email'], 
			'password' 		=> hash("sha512", $password),
			'user_role'		=> array(0 => $_POST['rol']),
			'name'     		=> $_POST['reg_name'],
			'initials'		=> $_POST['reg_initials'],
			'first_name' 	=> $_POST['reg_first_name'],
			'location' 		=> array('provincie' => $_POST['provincie'], 'district' => $_POST['district'])			
		);
		$provincie = $this->mongo_db->where('provincie', $_POST['provincie'])->get('provincies');
		$data['location']['afkorting'] = $provincie[0]['afkorting'];
		try {
			$this->mongo_db->insert('users', $data);
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
			return true;
		}
}
