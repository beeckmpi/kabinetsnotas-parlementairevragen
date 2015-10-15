<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	if ( ! function_exists('user_access'))
	{
		function user_access($roles = array())
		{
			$CI =& get_instance();
			$user = $CI->session->userdata('username');
			$user_geg = $CI->mongo_db->where(array('username' => $user))->get('users');
			if (in_array('Administrators', $user_geg[0]['user_role'])){
				return true;
			} else {
				foreach ($roles as $role)
				{
					if (in_array($role, $user_geg[0]['user_role'])){
						return true;
					}
				}
			}
			return false;
		}
	}

