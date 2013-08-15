<?php
class Member extends MY_Controller {

	var $mod_title = 'Members';

	var $table_name = 'member';
	var $id_field = 'm_id';
	var $status_field = 'm_status';
	var $entry_field = 'm_entry';
	var $stamp_field = 'm_stamp';
	var $deletion_field = 'm_deletion';
	var $order_field = 'm_entry';
	var $order_dir = 'DESC';
	var $label_field = 'm_email';

	var $author_field = 'm_author';
	var $editor_field = 'm_editor';

	var $search_in = array('m_email', 'm_firstname','m_lastname');


	function __construct() {
		parent::__construct();
		$this->sci->init('admin');
		$this->_init();
		$this->session->validate(array('MEMBER_VIEW'), 'admin');
		$this->sci->assign('use_ajax' , TRUE); 
	}

	function enum_setting($maindata=array()) {
		return $maindata;
	}

	function iteration_setting($maindata=array()) {
		foreach( $maindata as $k=>$tmp ) {
			$this->db->where('m_id' , $tmp['m_referal_id'] );
			$res = $this->db->get('member');
			$referal = $res->row_array();
			$maindata[$k]['referal'] = $referal;
		}
		return $maindata;
	}

	function join_setting() {
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active");
	}

	function validation_setting() {
		$this->form_validation->set_rules('m_name', 'Name', 'trim|required|xss_clean');
	}

	function database_setter() {
		$m_name = $this->input->post('m_name');
		$this->db->set('m_name' , $m_name );

		$this->image_directory = 'userfiles/product_category/';
		$this->thumb_directory = 'userfiles/product_category/thumb/';
		$this->thumb_width = 125;
		$this->thumb_height = 125;

		if($_FILES['m_image']['name'] != '') {
			$filename = $this->_upload_image('m_image');
			$this->db->set('m_image' , $filename);
		}
	}


	function pre_add_edit() {
		show_error('function is disabled');
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}

	function view($m_id=0) {
		$this->db->where('m_id' , $m_id);
		$res = $this->db->get('member');
		$member = $res->row_array();

		$this->sci->assign('member' , $member);

		$fb_info = array();

		if($member['fb_raw_json'] != '') {
			$fb_info = json_decode($member['fb_raw_json']);
			$this->sci->assign('fb_info' , $fb_info);
			//print_r($fb_info);
		}

		if($member['m_referal_id'] != '0') {
			$this->db->where('m_id' , $member['m_referal_id']);
			$res = $this->db->get('member');
			$referal = $res->row_array();
			$this->sci->assign('referal' , $referal);
		}

		//get address
		$this->db->join('area_city ac' , 'ac.ac_id = member_address.ac_id' , 'left');
		$this->db->join('area_province ap' , 'ap.ap_id = member_address.ap_id' , 'left');
		$this->db->where('m_id' , $m_id);
		$this->db->where('madr_status' , 'Active');
		$res = $this->db->get('member_address');
		$addresses = $res->result_array();
		$this->sci->assign('addresses' , $addresses);


		$this->sci->da('view.htm');
	}
	
	
	
	
	///-------
	
	function ajax_load_member_view($m_id=0) { 
		$this->db->where('m_status' , 'Active');
		$this->db->where('member.m_id' , $m_id);
		$res = $this->db->get('member');
		$member = $res->row_array();
		//print_r($member);
		$this->sci->assign('member' , $member);
		 
		$this->sci->d('ajax_member/view.htm');
	}
	
	function ajax_load_member_edit($m_id=0) {
		$this->sci->assign('action' , 'edit'); 
		$this->sci->assign('m_id' , $m_id);
		
		$this->db->where('m_id' , $m_id); 
		$res = $this->db->get('member');
		$data = $res->row_array(); 
		$this->sci->assign('data' , $data);
		
		
		$this->load->library('form_validation'); 
		$this->sci->d('ajax_member/edit.htm');
	}
	
	
	function ajax_submit_member_edit() {
		$action = $this->input->post('action'); 
		$m_id = $this->input->post('m_id'); 
		$this->load->library('form_validation'); 
		//$this->form_validation->set_rules('m_login', 'User Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('m_firstname', 'First Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('m_lastname', 'Last Name', 'trim|xss_clean');
		$this->form_validation->set_rules('m_pass', 'Password', 'trim|xss_clean');
		$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'trim|xss_clean|matches[m_pass]'); 
		$this->form_validation->set_rules('m_mobile', 'Mobile Number', 'trim|xss_clean'); 
		$this->form_validation->set_rules('m_phone', 'Phone Number', 'trim|xss_clean'); 
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{
			$this->db->set('m_firstname' , $this->input->post('m_firstname'));
			$this->db->set('m_lastname' , $this->input->post('m_lastname')); 
			$this->db->set('m_mobile' , $this->input->post('m_mobile'));
			$this->db->set('m_phone' , $this->input->post('m_phone'));
			
			//$m_login = $this->input->post('m_login');
			//$this->db->set('m_email' , $m_login);
			//$this->db->set('m_login' , $m_login); 
			//$this->load->helper('member');
			//$m_registration_key = generate_registration_key($m_login);
			//$m_activation_key = generate_activation_key($m_login);
			//$this->db->set('m_registration_key' , $m_login);
			//$this->db->set('m_activation_key' , $m_login);  
			
			if($this->input->post('m_pass') != '') {
				$salt = $this->config->item('salt');
				$password = $this->input->post('m_pass');
				$password = md5($salt.$password);
				$this->db->set('m_pass' , $password);
			}
			
			$this->db->update('member');
			 
			$data['status'] = 'ok';
			$data['msg'] = 'ok'; 
		} 
		print json_encode($data);
	}
	




}
