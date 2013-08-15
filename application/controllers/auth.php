<?php

class Auth extends MY_Controller {

	var $mod_path;
	var $mod_url;

	var $mod_title = 'Login';

	function __construct() {
		parent::__construct();
		$this->sci->set_room('main');
		$this->_init();
		$this->load->model('mod_brand');
		$this->mod_path = 'auth/';
		$this->mod_url = site_url() . '';
		$this->salt = $this->config->item('salt');
	}

	function validate_member($encoded_lastpage='') {
		$decoded_lastpage = safe_base64_decode($encoded_lastpage);
		$this->session->set_lastpage($decoded_lastpage); 
		$this->session->validate_member(TRUE, $encoded_lastpage);
	}
	
	function validate_club($encoded_lastpage='') {
		$decoded_lastpage = safe_base64_decode($encoded_lastpage);
		$this->session->set_lastpage($decoded_lastpage); 
		$this->session->validate_club(TRUE, $encoded_lastpage);
	}

	function landing($last_page='') {
		$this->session->set_flashdata('show_auth_landing', TRUE);
		redirect(site_url()."home/landing");
	}

	function make_landing($brand_view_mode = 'list'){
		$this->sci->assign('brand_view_mode' , $brand_view_mode);

		$promo_onsale = $this->mod_brand->get_promo_onsale(6);
		$this->sci->assign('brand_onsale' , $promo_onsale);

		$brand_comingsoon = $this->mod_brand->get_promo_comingsoon(6);
		$this->sci->assign('brand_comingsoon' , $brand_comingsoon);
	}

	function index() {
		redirect(site_url().'admin');
	}
	
	
	function login( $encoded_lastpage='' ) {
		 
		$error_string = '';

		if($encoded_lastpage == '') {
			$lastpage = $this->session->get_lastpage();
		} else {
			$lastpage = safe_base64_decode($encoded_lastpage);
		} 

		$this->load->library('form_validation');
		$this->form_validation->set_rules("username", "Username", "required|trim|xss_clean" );
		$this->form_validation->set_rules("pass", "Password", "required|trim|xss_clean" );

		if ($this->form_validation->run() == FALSE) {
			//$error_string = validation_errors();
			$error_string = '<p class="red">Wrong username or password !</p>';
			$this->sci->assign('error_string', $error_string);
			$this->session->set_flashdata('error_string', $error_string);
			redirect(site_url()."club/index");
			//$this->sci->da('login.htm');
		}
		else {
			$username = $this->input->post('username');
			$password = $this->input->post('pass');

			$login_ok = $this->_do_login($username, $password);

			if ( $login_ok == 'error' || !$login_ok ) {
				
				redirect(site_url()."club/index"); 
				return FALSE;
			} 
			elseif( $login_ok == 'ok') {
				$redirect_target = ($lastpage!='') ? $lastpage : site_url();
				//redirect( $lastpage );
				$this->session->set_flashdata('error_string', $error_string);
				redirect(site_url()."club/index");
			}

		}
	}
	
	function _do_login($username , $password) {
		//get the submitted username from database
		$this->db->where('club_status', 'Active');
		$this->db->where('club_login' , $username);
		$res = $this->db->get('club');
		$result = $res->row_array(); 

		if(!$result) { //if no user is found
			return FALSE;
		} else {

			// check authentication of password submitted 
			if(  md5($this->salt.$password) == $result['club_pass']  ) {
				
				if($result['club_is_pending'] == 'No') { 
					$this->session->set_userdata('club_id' , $result['club_id']);
					$this->session->set_userdata('userinfo' , $result);
					return 'ok';
				} else {
					$error_string = '<p class="red">Club is pending confirmation</p>';
					$this->sci->assign('error_string', $error_string); 
					$this->session->set_flashdata('error_string', $error_string);
					return 'error';
				}

				
				
				//TODO : dont forget to turn on useraction !
				//$this->CI->useraction->login_ok($row->u_id , $loginname);
				
			} else {
				//$this->CI->useraction->login_ng($loginname);
				$error_string = '<p class="red">Wrong username or password !</p>';
				$this->sci->assign('error_string', $error_string); 
				$this->session->set_flashdata('error_string', $error_string);
				return 'error';
			}
		}
	}

	

	function _check_active($username) {
		$this->db->where( 'm_status', 'Active');
		$this->db->where( 'm_login' , $username);
		$res = $this->db->get( 'member');
		$result = $res->row_array();
		if(!$result) { return FALSE; }
		if($result['m_is_active'] == 'No') { return FALSE; } else { return TRUE; }
	}

	

	function logout() {
		$this->session->unset_userdata('club_id');
		session_destroy();
		redirect( site_url()."club/index");
	}

	function no_access() {
		$this->sci->da( 'no_access.htm');
	}

	function admin() {
		$this->session->validate(array('OPERATOR'));
		$this->sci->da( 'admin.htm');
	}

	function forgot_password() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'Username / Email', 'trim|required|callback_email_check');
		if($this->form_validation->run() == FALSE) {
			$this->sci->da('forgot_password.htm');
		} else {
			$email = $this->input->post('email');
			$this->db->where('m_login' , $email);
			$this->db->where('m_status' , 'Active');
			$res = $this->db->get('member');
			$member = $res->row_array();

			$this->send_password_change_email($member['m_id']);

			$this->sci->da('forgot_password_sent.htm');

		}
	}

	public function email_check($str) {
		$this->load->model('mod_member');
		if ($this->mod_member->check_email_registered($str) ) {
			return TRUE;
		} else {
			$this->form_validation->set_message('email_check', "$str is not a valid username / email");
			return FALSE;
		}
	}

	function send_password_change_email($m_id) {
		$this->db->where('m_id' , $m_id);
		$res = $this->db->get('member');
		$member = $res->row_array();

		//send email
		$this->load->library('email');
		$config['mailtype'] = 'html';
		$config['charset'] = 'iso-8859-1';
		$this->email->initialize($config);
		$this->email->from( $this->site_config['ec_email_from'], $this->site_config['ec_email_name'] );
		$this->email->to($member['m_login']);

		$this->email->subject( 'Password Change Requested' );
		$this->sci->assign('fullname' , $member['m_firstname'].' '.$member['m_lastname']);
		$this->sci->assign('key' , $member['m_registration_key']);
		$this->sci->assign('m_login_encoded' , safe_base64_encode($member['m_login']));

		$messagebody = $this->sci->fetch('auth/email_password_change.htm');
		$this->email->message($messagebody);
		$ok = $this->email->send();
		//echo $this->email->print_debugger();
		return $ok;
	}

	function change_password($email_encoded='', $registration_key='') {
		$email = safe_base64_decode($email_encoded);
		$this->db->where('m_registration_key' , $registration_key);
		$this->db->where('m_login' , $email);
		$this->db->where('m_status' , 'Active');
		$res = $this->db->get('member');
		$member = $res->row_array();

		if(!$member) {
			show_error('404');
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('password', 'Password', 'required|trim|xss_clean');
		$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'required|trim|xss_clean|matches[password]');
		if($this->form_validation->run() == FALSE) {
			$this->sci->da('change_password.htm');
		} else {
			$salt = $this->config->item('salt');
			$password = $this->input->post('password');
			$password = md5($salt.$password);
			$this->db->set('m_pass' , $password);
			$this->db->where('m_id' , $member['m_id']);
			$this->db->update('member');

			$this->sci->da('change_password_success.htm');

		}
	}

}
