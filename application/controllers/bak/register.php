<?php

class Register extends MY_Controller {

	var $mod_title = 'Home';

	function __construct() {
		parent::__construct();
		$this->sci->set_room('main');
		$this->_init();
	}

	//get terms and conditions
	function get_terms_and_condition() {
		$this->db->where('c_code' , 'terms-and-conditions');
		$this->db->where('c_status' , 'Active');
		$res = $this->db->get('content');
		$content_terms = $res->row_array();
		$this->sci->assign('content_terms' , $content_terms);
	}

	//get polling options
	function get_polling_options() {
		$polling_option = $this->mod_global->get_options('polling_option' , 'pollo_name' , 'pollo_name' , 'pollo_status  = "Active" and pollc_id = 1' );
		asort($polling_option);
		$this->sci->assign('polling_option' , $polling_option);
	}

	function index() {
		$this->db->where('c_code' , 'register-top');
		$res = $this->db->get('content');
		$register_top = $res->row_array();
		$this->sci->assign('register_top' , $register_top);
		
		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>";
		$breadcrumb[] = "Register";
		$this->sci->assign('breadcrumb' , $breadcrumb);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('club_name', 'Club Name', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('club_email', 'Club Email', 'trim|xss_clean'); 
		$this->form_validation->set_rules('club_desc', 'Club Description', 'trim'); 
		$this->form_validation->set_rules('club_contact', 'Club Contact', 'trim|xss_clean'); 
		$this->form_validation->set_rules('club_address', 'Club Address', 'trim|xss_clean');
		$this->form_validation->set_rules('club_intro', 'Club Intro', 'trim|xss_clean');
		$this->form_validation->set_rules('club_website', 'Club Website', 'trim|xss_clean');
		$this->form_validation->set_rules('club_facebook', 'Club Facebook', 'trim|xss_clean');
		$this->form_validation->set_rules('club_twitter', 'Club Twitter', 'trim|xss_clean');
		$this->form_validation->set_rules('club_milis', 'Club Milis', 'trim|xss_clean');
		$this->form_validation->set_rules('club_other1', 'Club other links 1', 'trim|xss_clean');
		$this->form_validation->set_rules('club_other2', 'Club other links 2', 'trim|xss_clean');
		$this->form_validation->set_rules('club_other3', 'Club other links 3', 'trim|xss_clean');
		$this->form_validation->set_rules('club_join_date', 'Club Join Date', 'trim|xss_clean');
		$this->form_validation->set_rules('club_est_date', 'Club Established Date', 'trim|xss_clean');
		
		//$this->form_validation->set_rules('m_login', 'User Name', 'required|trim|xss_clean');
		//$this->form_validation->set_rules('m_login', 'Username', 'required|valid_email|trim|xss_clean|callback_email_check');
		//$this->form_validation->set_rules('m_firstname', 'First Name', 'required|trim|xss_clean');
		//$this->form_validation->set_rules('m_lastname', 'Last Name', 'trim|xss_clean');
		//$this->form_validation->set_rules('m_pass', 'Password', 'required|trim|xss_clean');
		//$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'required|trim|xss_clean|matches[m_pass]'); 
		//$this->form_validation->set_rules('m_mobile', 'Mobile Number', 'trim|xss_clean'); 
		//$this->form_validation->set_rules('m_phone', 'Phone Number', 'trim|xss_clean');
		
		$this->form_validation->set_rules('club_login', 'Username', 'required|trim|xss_clean');
		$this->form_validation->set_rules('club_admin_name', 'Admin Name', 'required|trim|xss_clean'); 
		$this->form_validation->set_rules('club_pass', 'Password', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'trim|required|xss_clean|matches[club_pass]');

		//TODO: validate email
		

		if($this->form_validation->run() == FALSE) {
			//$this->get_terms_and_condition();
			//$this->get_polling_options();
			$this->sci->da('register.htm');
		} else {
			

			//$this->load->helper('member');
			//$m_registration_key = generate_registration_key($registrant_email);
			//$m_activation_key = generate_activation_key($registrant_email);
			//$this->db->set('m_registration_key' , $m_registration_key);
			//$this->db->set('m_activation_key' , $m_activation_key);
			
			$this->db->set('club_name' , $this->input->post('club_name') ); 
			$this->db->set('club_email' , $this->input->post('club_email') ); 
			$this->db->set('club_desc' , $this->input->post('club_desc') ); 
			$this->db->set('club_contact' , $this->input->post('club_contact') ); 
			$this->db->set('club_address' , $this->input->post('club_address') );
			$this->db->set('club_intro' , $this->input->post('club_intro') );
			$this->db->set('club_website' , $this->input->post('club_website') );
			$this->db->set('club_facebook' , $this->input->post('club_facebook') );
			$this->db->set('club_twitter' , $this->input->post('club_twitter') );
			$this->db->set('club_milis' , $this->input->post('club_milis') );
			$this->db->set('club_other1' , $this->input->post('club_other1') );
			$this->db->set('club_other2' , $this->input->post('club_other2') );
			$this->db->set('club_other3' , $this->input->post('club_other3') ); 
			$this->db->set('club_est_date' , $this->input->post('club_est_date') );
			$this->db->set('club_join_date' , date('Y-m-d') );
			
			$this->db->set('club_login' , $this->input->post('club_login') );
			$this->db->set('club_admin_name' , $this->input->post('club_admin_name') );
			
			if($this->input->post('club_pass') != '') {
				$salt = $this->config->item('salt');
				$password = $this->input->post('club_pass');
				$password = md5($salt.$password); 
				$this->db->set('club_pass' , $password );
			} 
			$this->db->set('club_entry' , date('Y-m-d H:i:s'));
			$this->db->set('club_is_pending' , 'Yes');
			
			if($_FILES['club_logo']['name'] != '' ) {
				$filename = $this->_upload_image('club_logo');
				$this->db->set('club_logo' , $filename);
			}
			if($_FILES['club_mainphoto']['name'] != '' ) {
				$filename = $this->_upload_image('club_mainphoto');
				$this->db->set('club_mainphoto' , $filename);
			}
			
			$ok = $this->db->insert('club');
			$insert_id = $this->db->insert_id();
			
			
			//$this->db->set('m_firstname' , $this->input->post('m_firstname'));
			//$this->db->set('m_lastname' , $this->input->post('m_lastname'));
			//$this->db->set('m_phone' , $this->input->post('m_phone'));
			//$this->db->set('m_mobile' , $this->input->post('m_mobile'));
			//
			//$m_login = $this->input->post('m_login');
			//$this->db->set('m_email' , $m_login);
			//$this->db->set('m_login' , $m_login); 
			//
			//$this->load->helper('member');
			//$m_registration_key = generate_registration_key($m_login);
			//$m_activation_key = generate_activation_key($m_login);
			//$this->db->set('m_registration_key' , $m_login);
			//$this->db->set('m_activation_key' , $m_login); 
			//$this->db->set('m_is_active' , 'Yes');
			//
			//$salt = $this->config->item('salt');
			//$password = $this->input->post('m_pass');
			//$password = md5($salt.$password);
			//$this->db->set('m_pass' , $password);
			//$this->db->set('m_entry' , 'NOW()', FALSE); 
			//$this->db->set('club_id' , $insert_id);
			//$ok = $this->db->insert('member');
			redirect(site_url().'register/success/' );
		}
	}

	
	
	function send_email($m_id) {
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

		$this->email->subject( 'Thank You For Your Registration' );
		$this->sci->assign('fullname' , $member['m_firstname'].' '.$member['m_lastname']);
		$this->sci->assign('key' , $member['m_activation_key']);

		$messagebody = $this->sci->fetch('register/email_register.htm');
		$this->email->message($messagebody);
		$ok = $this->email->send();
		//echo $this->email->print_debugger();
		return $ok;
	}

	function success($m_id=0) {
		$this->db->where('c_code' , 'register-success');
		$res = $this->db->get('content');
		$register_success = $res->row_array();
		$this->sci->assign('register_success' , $register_success);
		$this->sci->assign('m_id' , $m_id);
		$this->sci->da('success.htm');
	}

	function activation($m_activation_key='') {
		if($m_activation_key=='') { show_error(); return FALSE; }

		$this->db->where('m_activation_key' , $m_activation_key);
		$this->db->where('m_status' , 'Active');
		$res = $this->db->get('member');
		$member = $res->row_array();

		if(!$member){ show_error(''); return FALSE; }

		$this->db->set('m_is_active' , 'Yes');
		$this->db->where('m_id' , $member['m_id']);
		$ok = $this->db->update('member');
		if(!$ok) { show_error(''); return FALSE; } else {
			redirect(site_url().'register/activated');
		}
	}

	function activated() {
		$this->sci->da('activated.htm');
	}


	//registration page by invitation
	function by_invitation($invitation_key='', $email='') {

		$new_email = safe_base64_decode($email);
		$this->sci->assign('new_email' , $new_email);

		$salt = $this->config->item('salt');

		$this->db->where('m_registration_key' , $invitation_key);
		$res = $this->db->get('member');
		$referral = $res->row_array();
		$this->sci->assign('referral' , $referral);

		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>";
		$breadcrumb[] = "Register";
		$this->sci->assign('breadcrumb' , $breadcrumb);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('m_sex', 'Sex', 'required');
		$this->form_validation->set_rules('accept_terms', 'Terms Acceptance', 'required|trim|xss_clean');
		$this->form_validation->set_rules('m_firstname', 'First Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('m_lastname', 'Last Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('m_pass', 'Password', 'required|trim|xss_clean');
		$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'required|trim|xss_clean|matches[m_pass]');
		$this->form_validation->set_rules('m_mobile', 'Mobile Number', 'trim|xss_clean|required');
		$this->form_validation->set_rules('dob_day', 'DOB Day', 'trim|xss_clean|required|greater_than[0]');
		$this->form_validation->set_rules('dob_month', 'DOB Month', 'trim|xss_clean|required|greater_than[0]');
		$this->form_validation->set_rules('dob_year', 'DOB Year', 'trim|xss_clean|required|greater_than[0]');

		//TODO: validate email
		$this->form_validation->set_rules('m_email', 'Email', 'required|valid_email|trim|xss_clean|callback_email_check');

		if($this->form_validation->run() == FALSE) {
			$this->get_terms_and_condition();
			$this->sci->da('by_invitation.htm');
		} else {
			$this->db->trans_start();
				$this->db->set('m_sex' , $this->input->post('m_sex'));
				$this->db->set('m_firstname' , $this->input->post('m_firstname'));
				$this->db->set('m_lastname' , $this->input->post('m_lastname'));
				$this->db->set('m_poll' , 'Invitation Email');

				$this->db->set('m_referal_id' , $referral['m_id'] );
				//TODO: check referral id with email

				$registrant_email = $this->input->post('m_email');
				$this->db->set('m_email' , $registrant_email);
				$this->db->set('m_login' , $registrant_email);

				$this->load->helper('member');
				$m_registration_key = generate_registration_key($registrant_email);
				$m_activation_key = generate_activation_key($registrant_email);
				$this->db->set('m_registration_key' , $m_registration_key);
				$this->db->set('m_activation_key' , $m_activation_key);

				$salt = $this->config->item('salt');
				$password = $this->input->post('m_pass');
				$password = md5($salt.$password);
				$this->db->set('m_pass' , $password);

				$this->db->set('m_mobile' , $this->input->post('m_mobile'));
				$day =  $this->input->post('dob_day');
				$month =  $this->input->post('dob_month');
				$year =  $this->input->post('dob_year');
				if($day!=0 && $month!=0 && $year!=0) {
					$dob = $year."-".$month."-".$day;
					$this->db->set('m_birthday' , $dob);
				}
				$this->db->set('m_entry' , 'NOW()', FALSE);

				$ok = $this->db->insert('member');
				$insert_id = $this->db->insert_id();

				//subscribe to newsletter
				$this->load->model('mod_newsletter');
				$this->mod_newsletter->subscribe($registrant_email);

				$this->send_email($insert_id);

			$this->db->trans_complete();


			$_POST['m_id'] = $insert_id;
			redirect(site_url().'register/success/'.$insert_id );
		}
	}

	//callback if email already registered
	public function email_check($str) {
		$this->load->model('mod_member');
		if ($this->mod_member->check_email_registered($str) ) {
			$this->form_validation->set_message('email_check', '%s is already registered');
			return FALSE;
		} else {
			return TRUE;
		}
	}




}
