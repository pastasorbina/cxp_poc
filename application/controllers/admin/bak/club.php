<?php
class Club extends MY_Controller {

	var $mod_title = 'Manage Clubs';

	var $table_name = 'club';
	var $id_field = 'club_id';
	var $status_field = 'club_status';
	var $entry_field = 'club_entry';
	var $stamp_field = 'club_stamp';
	var $deletion_field = 'club_deletion';
	var $order_field = 'club_entry';
	var $order_dir = 'DESC';
	var $label_field = 'club_name';

	var $author_field = 'club_author';
	var $editor_field = 'club_editor';

	var $search_in = array('club_name');

	var $template_add = 'edit.htm';
	var $template_edit = 'edit.htm';


	function __construct() {
		parent::__construct();
		$this->sci->init('admin');
		$this->_init();
		$this->session->validate(array('ADMIN'), 'admin');
		$this->sci->assign('use_ajax' , FALSE);
		$this->config->set_item('global_xss_filtering', FALSE);
	}

	function iteration_setting($maindata=array()) {
		return $maindata;
	}

	function join_setting() {
		//$this->db->join('member m' , 'm.club_id = club.club_id' , 'left');
		$this->db->select('*, club.club_id as club_id');
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active");
		$this->db->where('club_is_pending' , 'No');
	}

	function validation_setting($action="add") {
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
		
		$this->form_validation->set_rules('club_login', 'Username', 'required|trim|xss_clean');
		$this->form_validation->set_rules('club_admin_name', 'Admin Name', 'required|trim|xss_clean'); 
		$this->form_validation->set_rules('club_pass', 'Password', 'trim|xss_clean');
		if($this->input->post('club_pass') != '') {
			$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'trim|required|xss_clean|matches[club_pass]');
		}
	}

	function database_setter($action='add') { 
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
		
		$this->db->set('club_login' , $this->input->post('club_login') );
		$this->db->set('club_admin_name' , $this->input->post('club_admin_name') );
		
		if($this->input->post('club_pass') != '') {
			$salt = $this->config->item('salt');
			$password = $this->input->post('club_pass');
			$password = md5($salt.$password); 
			$this->db->set('club_pass' , $password );
		}
		 
		$club_join_date =  $this->input->post('club_join_date');
		if($club_join_date == '') {
			$this->db->set('club_join_date' , date('Y-m-d') );
		} else {
			$this->db->set('club_join_date' , $club_join_date);
		}
		
		if($_FILES['club_logo']['name'] != '' ) {
			$filename = $this->_upload_image('club_logo');
			$this->db->set('club_logo' , $filename);
		}
		if($_FILES['club_mainphoto']['name'] != '' ) {
			$filename = $this->_upload_image('club_mainphoto');
			$this->db->set('club_mainphoto' , $filename);
		}
		if($action == "add") {
			$this->db->set('club_entry' , 'NOW()', FALSE);
		}
	}
	
	//function validation_setting_member($action="add") {
	//	$this->form_validation->set_rules('m_login', 'User Name', 'required|trim|xss_clean');
	//	$this->form_validation->set_rules('m_firstname', 'First Name', 'required|trim|xss_clean');
	//	$this->form_validation->set_rules('m_lastname', 'Last Name', 'trim|xss_clean');
	//	$this->form_validation->set_rules('m_pass', 'Password', 'required|trim|xss_clean');
	//	$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'required|trim|xss_clean|matches[m_pass]'); 
	//	$this->form_validation->set_rules('m_mobile', 'Mobile Number', 'trim|xss_clean'); 
	//	$this->form_validation->set_rules('m_phone', 'Phone Number', 'trim|xss_clean'); 
	//}
	//
	//function database_setter_member($action="add") { 
	//	$this->db->set('m_firstname' , $this->input->post('m_firstname'));
	//	$this->db->set('m_lastname' , $this->input->post('m_lastname'));
	//	$this->db->set('m_phone' , $this->input->post('m_phone'));
	//	$this->db->set('m_mobile' , $this->input->post('m_mobile'));
	//
	//	$m_login = $this->input->post('m_login');
	//	$this->db->set('m_email' , $m_login);
	//	$this->db->set('m_login' , $m_login); 
	//
	//	$this->load->helper('member');
	//	$m_registration_key = generate_registration_key($m_login);
	//	$m_activation_key = generate_activation_key($m_login);
	//	$this->db->set('m_registration_key' , $m_login);
	//	$this->db->set('m_activation_key' , $m_login);
	//	
	//	$this->db->set('m_is_active' , 'Yes');
	//
	//	$salt = $this->config->item('salt');
	//	$password = $this->input->post('m_pass');
	//	$password = md5($salt.$password);
	//	$this->db->set('m_pass' , $password);
	//	if($action == "add") {
	//		$this->db->set('m_entry' , 'NOW()', FALSE);
	//	} 
	//}


	function pre_add_edit() { 
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}
	
	function add_club() {
		$this->sci->assign('ajax_action' , 'add');
		$this->pre_add_edit();
		$this->pre_add();

		$this->load->library('form_validation');
		$this->validation_setting('add'); 
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('add.htm');
		}else{
			$this->database_setter('add');  
			$ok = $this->db->insert('club');
			$insert_id = $this->db->insert_id();
			
			//$this->database_setter_member('add');
			$this->db->set('club_id' , $insert_id);
			$ok = $this->db->insert('member');
			
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url.'add_club');
			} else {
				$this->session->set_confirm(1); 
				redirect($this->mod_url."view/$insert_id");
			}
		}
	}
	
	function edit_club($club_id=0) {
		$this->sci->assign('ajax_action' , 'edit');
		$this->pre_add_edit();
		$this->pre_edit();
		
		$this->db->where('club.club_id' , $club_id);
		$this->db->join('member m' , 'm.club_id = club.club_id' , 'left');
		$res = $this->db->get('club');
		$data = $res->row_array();
		$this->sci->assign('data' , $data);

		$this->load->library('form_validation');
		$this->validation_setting('edit');
		
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('edit.htm');
		}else{
			$this->database_setter('edit');
			$this->db->where('club_id' , $club_id);
			$ok = $this->db->update('club'); 
			
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url.'edit_club');
			} else {
				$this->session->set_confirm(1); 
				redirect($this->mod_url."view/$club_id");
			}
		}
	}
	
	
	function add() {
		$this->sci->assign('ajax_action' , 'add');
		$this->pre_add_edit();
		$this->pre_add();

		$this->load->library('form_validation');
		$this->validation_setting('add');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da($this->template_add);
		}else{
			$this->database_setter('add');
			$this->db->set($this->entry_field , 'NOW()', FALSE );
			$ok = $this->db->insert($this->table_name);
			$insert_id = $this->db->insert_id();
			$this->post_add($insert_id);
			$this->post_add_edit($insert_id);
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url.'add');
			} else {
				$this->session->set_confirm(1);
				$insert_id = $this->db->insert_id();
				redirect($this->mod_url."view/$insert_id");
			}
		}
	}

	/**
	 * Edit
	 * @access public
	 *
	 */
	function edit( $id=0 ) {
		$this->sci->assign('ajax_action' , 'edit');
		$this->pre_add_edit();
		$this->join_setting();
		$this->db->where($this->id_field , $id);
		$res = $this->db->get($this->table_name);
		$data = $res->row_array();
		$this->sci->assign('data' , $data);
		$this->pre_edit($id);

		$this->load->library('form_validation');
		$this->validation_setting('edit');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da($this->template_edit);
		}else{
			$this->database_setter('edit');
			$this->db->where($this->id_field , $id);
			$ok = $this->db->update($this->table_name);
			$this->post_edit($id);
			$this->post_add_edit($id);
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."edit/$id");
			} else {
				$this->session->set_confirm(1);
				redirect($this->mod_url."view/$id");
			}
		}
	}
	
	
	function view($club_id=0) {
		$this->db->where('club_id' , $club_id); 
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		
		if($club) {
			$this->db->where('m_id' , $club['club_id']);
			$res = $this->db->get('member');
			$member = $res->row_array();
			$this->sci->assign('member' , $member);
		}
		
		$this->sci->da('view.htm');
	}
	
	
	function list_pending() {
		$this->session->set_bread('list');
		$this->db->join('member m' , 'm.club_id = club.club_id' , 'left');
		$this->db->select('*, club.club_id as club_id');
		$this->db->where('club_is_pending' , 'Yes');
		$res = $this->db->get('club');
		$maindata = $res->result_array();
		$this->sci->assign('maindata' , $maindata);
		$this->sci->da('list_pending.htm');
	}
	
	function approve($club_id=0) {
		$this->db->where('club_id' , $club_id);
		$this->db->set('club_is_pending' , 'No');
		$this->db->update('club');
		$this->session->set_confirm(1);
		redirect($this->session->get_bread('list'));
	}
	function disapprove($club_id=0) {
		$this->db->where('club_id' , $club_id);
		$this->db->set('club_is_pending' , 'Yes');
		$this->db->update('club');
		$this->session->set_confirm(1);
		redirect($this->session->get_bread('list'));
	}
	
	
	
	
	
	
	
	
	
	function ajax_load_club_files($club_id=0) {
		$this->db->join('club c' , 'c.club_id = club_files.club_id' , 'left'); 
		$this->db->where('clubf_status' , 'Active');
		$this->db->where('club_files.club_id' , $club_id);
		$res = $this->db->get('club_files');
		$maindata = $res->result_array();
		$this->sci->assign('maindata' , $maindata);
		 
		$this->sci->d('club_files/list.htm');
	}
	
	function _clubf_rules() {
		$this->form_validation->set_rules('clubf_title', 'Title', 'trim|xss_clean'); 
		$this->form_validation->set_rules('clubf_date', 'Date', 'trim'); 
		$this->form_validation->set_rules('club_id', 'Club ID', 'trim'); 
	}

	function _clubf_db() { 
		$c_title = $this->input->post('clubf_title');
		$this->db->set('clubf_title' , $c_title);  
		$this->db->set('club_id' , $this->input->post('club_id') );  

		$c_date = $this->input->post('clubf_date');
		$c_date = $c_date ? $c_date : date('Y-m-d H:i:s');
		$this->db->set('clubf_date' , $c_date );
		
		if($_FILES['clubf_file']['name'] != '' ) {
			$filename = $this->_upload_image('clubf_file', TRUE);
			$this->db->set('clubf_file' , $filename);
		}
	}
	
	function list_club_files($club_id=0) {
		$this->sci->assign('club_id' , $club_id); 
		
		$this->db->where('club_id' , $club_id);
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		
		$this->db->where('club_id' , $club_id);
		$this->db->where('clubf_status' , 'Active');
		$res = $this->db->get('club_files');
		$maindata = $res->result_array();
		$this->sci->assign('maindata' , $maindata);
		
		$this->sci->da('club_files/list.htm');
	}
	
	 function add_club_files($club_id=0) {
		$this->sci->assign('ajax_action' , 'add');
		$this->sci->assign('club_id' , $club_id); 
		
		$this->db->where('club_id' , $club_id);
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		  
		$this->load->library('form_validation');
		$this->_clubf_rules('add');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da("club_files/edit.htm");
		}else{
			$this->_clubf_db('add');
			$this->db->set("clubf_entry" , 'NOW()', FALSE );
			$ok = $this->db->insert("club_files");
			$insert_id = $this->db->insert_id(); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."add_club_files/$club_id");
			} else {
				$this->session->set_confirm(1); 
				redirect($this->mod_url."list_club_files/$club_id");
			}
		}
	}
	
	function edit_club_files($clubf_id=0) {
		$this->sci->assign('ajax_action' , 'edit');
		$this->sci->assign('clubf_id' , $clubf_id);
		
		$this->db->where('clubf_id' , $clubf_id);
		$res = $this->db->get('club_files');
		$content = $res->row_array();
		$this->sci->assign('content' , $content);
		
		$club_id = $content['club_id'];
		$this->db->where('club_id' , $club_id);
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		  
		$this->load->library('form_validation');
		$this->_clubf_rules('edit');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da("club_files/edit.htm");
		}else{
			$this->_clubf_db('edit');
			$this->db->where('clubf_id' , $clubf_id);
			$ok = $this->db->update("club_files");
			$insert_id = $this->db->insert_id(); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."edit_club_files/$clubf_id");
			} else {
				$this->session->set_confirm(1); 
				redirect($this->mod_url."list_club_files/$club_id");
			}
		}
	}
	
	function delete_club_files($clubf_id=0){
		$this->db->where('clubf_id' , $clubf_id);
		$res = $this->db->get('club_files');
		$file = $res->row_array();
		$club_id = $file['club_id'];
		$this->db->where('clubf_id' , $clubf_id);
		$this->db->set('clubf_status' , 'Deleted');
		$this->db->update('club_files');
		redirect($this->mod_url."list_club_files/$club_id");
	}
	
	
	
	
	
	
	
	
	function ajax_load_club_content($club_id=0) {
		$this->db->join('club c' , 'c.club_id = club_content.club_id' , 'left'); 
		$this->db->where('clubc_status' , 'Active');
		$this->db->where('club_content.club_id' , $club_id);
		$res = $this->db->get('club_content');
		$maindata = $res->result_array();
		$this->sci->assign('maindata' , $maindata);
		 
		$this->sci->d('club_content/list.htm');
	}
	
	function _clubc_rules() {
		$this->form_validation->set_rules('clubc_title', 'Title', 'trim|required|xss_clean');
		$this->form_validation->set_rules('clubc_content', 'Content', 'trim');
		$this->form_validation->set_rules('clubc_intro', 'Content Intro', 'trim'); 
		$this->form_validation->set_rules('clubc_date', 'Date', 'trim');
		$this->form_validation->set_rules('clubc_type', 'Type', 'trim'); 
		$this->form_validation->set_rules('club_id', 'Club ID', 'trim'); 
	}

	function _clubc_db() { 
		$c_title = $this->input->post('clubc_title');
		$this->db->set('clubc_title' , $c_title); 
		$this->db->set('clubc_content' , $this->input->post('clubc_content') );
		$this->db->set('clubc_intro' , $this->input->post('clubc_intro') ); 
		$this->db->set('clubc_type' , $this->input->post('clubc_type') );
		$this->db->set('club_id' , $this->input->post('club_id') );  

		$c_date = $this->input->post('clubc_date');
		$c_date = $c_date ? $c_date : date('Y-m-d H:i:s');
		$this->db->set('clubc_date' , $c_date );
		
		if($_FILES['clubc_banner']['name'] != '' ) {
			$filename = $this->_upload_image('clubc_banner', TRUE);
			$this->db->set('clubc_banner' , $filename);
		}
	}
	
	
	function list_club_content($club_id=0) {
		$this->sci->assign('club_id' , $club_id); 
		
		$this->db->where('club_id' , $club_id);
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		
		$this->db->where('club_id' , $club_id);
		$this->db->where('clubc_status' , 'Active');
		$res = $this->db->get('club_content');
		$maindata = $res->result_array();
		$this->sci->assign('maindata' , $maindata);
		
		$this->sci->da('club_content/list.htm');
	}
	
	 function add_club_content($club_id=0) {
		$this->sci->assign('ajax_action' , 'add');
		$this->sci->assign('club_id' , $club_id); 
		
		$this->db->where('club_id' , $club_id);
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		  
		$this->load->library('form_validation');
		$this->_clubc_rules('add');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da("club_content/edit.htm");
		}else{
			$this->_clubc_db('add');
			$this->db->set("clubc_entry" , 'NOW()', FALSE );
			$ok = $this->db->insert("club_content");
			$insert_id = $this->db->insert_id(); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."add_club_content/$club_id");
			} else {
				$this->session->set_confirm(1); 
				redirect($this->mod_url."view/$club_id");
			}
		}
	}
	
	function edit_club_content($clubc_id=0) {
		$this->sci->assign('ajax_action' , 'add');
		$this->sci->assign('clubc_id' , $clubc_id);
		
		$this->db->where('clubc_id' , $clubc_id);
		$res = $this->db->get('club_content');
		$content = $res->row_array();
		$this->sci->assign('content' , $content);
		
		$club_id = $content['club_id'];
		$this->db->where('club_id' , $club_id);
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		  
		$this->load->library('form_validation');
		$this->_clubc_rules('edit');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da("club_content/edit.htm");
		}else{
			$this->_clubc_db('edit');
			$this->db->where('clubc_id' , $clubc_id);
			$ok = $this->db->update("club_content");
			$insert_id = $this->db->insert_id(); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."edit_club_content/$clubc_id");
			} else {
				$this->session->set_confirm(1); 
				redirect($this->mod_url."view/$club_id");
			}
		}
	}
	
	 
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function ajax_load_detail_tech_list($club_id=0) {
		$this->db->join('club t' , 't.club_id = club_detail_tech.club_id' , 'left');
		$this->db->join('club_tech tth' , 'tth.tth_id = club_detail_tech.tth_id' , 'left'); 
		$this->db->where('tdth_status' , 'Active');
		$this->db->where('club_detail_tech.club_id' , $club_id);
		$res = $this->db->get('club_detail_tech');
		$maindata = $res->resulclub_array();
		$this->sci->assign('maindata' , $maindata);
		 
		$this->sci->d('detail_tech/list.htm');
	}
	 
	function ajax_load_detail_tech_edit($club_id=0) {
		$this->sci->assign('action' , 'edit'); 
		$this->sci->assign('club_id' , $club_id);
		 
		$this->db->where('tdth_status' , 'Active');
		$this->db->where('club_id' , $club_id);
		$this->db->select('tth_id');
		$res = $this->db->get('club_detail_tech');
		$atechs = $res->resulclub_array();
		$currenclub_techs = array();
		foreach($atechs as $k=>$tmp) {
			$currenclub_techs[] = $tmp['tth_id'];
		}
		 
		$this->db->where('tth_status' , 'Active');
		$this->db->order_by('tth_order' , 'asc');
		$res = $this->db->get('club_tech');
		$club_tech = $res->resulclub_array();
		if(sizeof($currenclub_techs) > 0) {
			foreach($club_tech as $k=>$tmp) {
				if(in_array($tmp['tth_id'], $currenclub_techs)){ 
					$club_tech[$k]['on'] = 'Yes';
				} else {
					$club_tech[$k]['on'] = 'No';
				}
			} 
		} 
		$this->sci->assign('club_tech' , $club_tech);
		
		
		$this->load->library('form_validation'); 
		$this->sci->d('detail_tech/edit.htm');
	}
	
	function ajax_submiclub_detail_tech() {
		$action = $this->input->post('action');
		$club_id = $this->input->post('club_id'); 
		$tth_id = $this->input->post('tth_id'); 
		$this->load->library('form_validation'); 
		$this->form_validation->set_rules('tth_id', 'Tag', '');   
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{
			$this->db->where('club_id' , $club_id);
			$this->db->delete('club_detail_tech');
			foreach($tth_id as $k=>$tmp) {
				$this->db->set('club_id' , $club_id);
				$this->db->set('tth_id' , $tmp);
				$this->db->set('tdth_entry' , 'NOW()', FALSE);
				$this->db->insert('club_detail_tech');
			}
			$data['status'] = 'ok';
			$data['msg'] = 'ok'; 
		} 
		print json_encode($data);
	}
	
	
	
	
	
	
	
	
	function ajax_load_detail_tag_list($club_id=0) {
		$this->db->join('club t' , 't.club_id = club_detail_tag.club_id' , 'left');
		$this->db->join('club_tag tt' , 'tt.tclub_id = club_detail_tag.tclub_id' , 'left');
		$this->db->join('club_tag_category ttc' , 'ttc.ttc_id = tt.ttc_id' , 'left');
		$this->db->where('tdclub_status' , 'Active');
		$this->db->where('club_detail_tag.club_id' , $club_id);
		$res = $this->db->get('club_detail_tag');
		$maindata = $res->resulclub_array();
		$this->sci->assign('maindata' , $maindata);
		
		
		$this->sci->d('detail_tag/list.htm');
	}
	
	function ajax_load_detail_tag_edit($club_id=0) {
		$this->sci->assign('action' , 'edit'); 
		$this->sci->assign('club_id' , $club_id);
		 
		$this->db->where('tdclub_status' , 'Active');
		$this->db->where('club_id' , $club_id);
		$this->db->select('tclub_id');
		$res = $this->db->get('club_detail_tag');
		$atags = $res->resulclub_array();
		$currenclub_tags = array();
		foreach($atags as $k=>$tmp) {
			$currenclub_tags[] = $tmp['tclub_id'];
		}
		 
		$this->db->where('ttc_status' , 'Active');
		$this->db->order_by('ttc_order' , 'asc');
		$res = $this->db->get('club_tag_category');
		$club_tag_category = $res->resulclub_array();
		foreach($club_tag_category as $k=>$tmp) {
			$this->db->where('ttc_id' , $tmp['ttc_id']);
			$this->db->where('tclub_status' , 'Active');
			$this->db->order_by('tclub_order' , 'asc');
			$res = $this->db->get('club_tag');
			$club_tag_category[$k]['club_tag'] = $res->resulclub_array();
			if(sizeof($currenclub_tags) > 0) {
				foreach($club_tag_category[$k]['club_tag'] as $k2 => $tmp2) {
					if(in_array($tmp2['tclub_id'], $currenclub_tags)){ 
						$club_tag_category[$k]['club_tag'][$k2]['on'] = 'Yes';
					} else {
						$club_tag_category[$k]['club_tag'][$k2]['on'] = 'No';
					}
				}
			}
		} 
		$this->sci->assign('club_tag_category' , $club_tag_category);
		
		
		$this->load->library('form_validation'); 
		$this->sci->d('detail_tag/edit.htm');
	}
	
	function ajax_submiclub_detail_tag() {
		$action = $this->input->post('action');
		$club_id = $this->input->post('club_id'); 
		$tclub_id = $this->input->post('tclub_id'); 
		$this->load->library('form_validation'); 
		$this->form_validation->set_rules('tclub_id', 'Tag', '');   
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{
			$this->db->where('club_id' , $club_id);
			$this->db->delete('club_detail_tag');
			foreach($tclub_id as $k=>$tmp) {
				$this->db->set('club_id' , $club_id);
				$this->db->set('tclub_id' , $tmp);
				$this->db->set('tdclub_entry' , 'NOW()', FALSE);
				$this->db->insert('club_detail_tag');
			}
			$data['status'] = 'ok';
			$data['msg'] = 'ok'; 
		} 
		print json_encode($data);
	}
	
	
	
	
	
	function ajax_load_detail_rating_list($club_id=0) {
		$this->db->join('club' , 'club.club_id = club_detail_rating.club_id' , 'left');
		$this->db->join('club_rating trt' , 'trt.trclub_id = club_detail_rating.trclub_id' , 'left'); 
		$this->db->join('club_rating_category trtc' , 'trtc.trtc_id = trt.trtc_id' , 'left'); 
		$this->db->where('tdr_status' , 'Active');
		$this->db->where('club_detail_rating.club_id' , $club_id);
		$res = $this->db->get('club_detail_rating');
		$maindata = $res->resulclub_array();
		$this->sci->assign('maindata' , $maindata);
		
		$this->sci->d('detail_rating/list.htm');
	}
	
	function ajax_load_detail_rating_addedit($action='add', $tdr_id=0, $club_id=0) {
		$this->sci->assign('action' , $action);
		$this->sci->assign('tdr_id' , $tdr_id);
		$this->sci->assign('club_id' , $club_id);
		
		$this->db->join('club_rating_category trtc' , 'trtc.trtc_id = club_rating.trtc_id' , 'left'); 
		$this->db->where('trclub_status' , 'Active');
		$this->db->order_by('trtc.trtc_order' , 'asc');
		$res = $this->db->get('club_rating');
		$club_rating = $res->resulclub_array();
		$this->sci->assign('club_rating' , $club_rating);
		 
		
		if($tdr_id != 0) {
			$this->db->where('tdr_id' , $tdr_id);
			$res = $this->db->get('club_detail_rating');
			$data = $res->row_array();
			$this->sci->assign('data' , $data);
		}
		
		$this->load->library('form_validation'); 
		$this->sci->d('detail_rating/edit.htm');
	}
	
	function ajax_submiclub_detail_rating_addedit() {
		$action = $this->input->post('action');
		$club_id = $this->input->post('club_id');
		$tdr_id = $this->input->post('tdr_id');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('trclub_id', 'Rating', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('tdr_value', 'Rating Value', 'trim|required|xss_clean');  
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{
			$this->db->set('trclub_id' , $this->input->post('trclub_id') );
			$this->db->set('tdr_value' , $this->input->post('tdr_value') );
			$this->db->set('club_id' , $this->input->post('club_id') );
			if($action == 'add') {
				$this->db->set('tdr_entry' , 'NOW()', false);
				$this->db->insert('club_detail_rating');  
			}elseif($action == 'edit') {
				$this->db->where('tdr_id' , $tdr_id);
				$this->db->update('club_detail_rating');
			}
			$data['status'] = 'ok';
			$data['msg'] = 'ok'; 
		}
		print json_encode($data);
	}
	
	function ajax_delete_detail_rating($tdr_id=0) {
		$this->db->where('tdr_id' , $tdr_id);
		$this->db->set('tdr_status' , 'Deleted');
		$this->db->set('tdr_deletion' , 'NOW()', FALSE);
		$this->db->update('club_detail_rating');
		$data['status'] = 'ok';
		$data['msg'] = 'ok';
		print json_encode($data);
	}
	
	
	
	
	
	
	
	function ajax_load_detail_size_list($club_id=0) {
		$this->db->join('club' , 'club.club_id = club_detail_size.club_id' , 'left');
		$this->db->join('club_rim tr' , 'tr.tr_id = club_detail_size.tr_id' , 'left');
		$this->db->join('club_size ts' , 'ts.ts_id = club_detail_size.ts_id' , 'left');
		$this->db->where('tds_status' , 'Active');
		$this->db->where('club_detail_size.club_id' , $club_id);
		$res = $this->db->get('club_detail_size');
		$maindata = $res->resulclub_array();
		$this->sci->assign('maindata' , $maindata);
		
		$this->sci->d('detail_size/list.htm');
	}
	
	function ajax_load_detail_size_addedit($action='add', $tds_id=0, $club_id=0) {
		$this->sci->assign('action' , $action);
		$this->sci->assign('tds_id' , $tds_id);
		$this->sci->assign('club_id' , $club_id);
		
		$this->db->where('tr_status' , 'Active');
		$this->db->order_by('tr_order' , 'asc');
		$res = $this->db->get('club_rim');
		$club_rim = $res->resulclub_array();
		$this->sci->assign('club_rim' , $club_rim);
		
		$this->db->where('ts_status' , 'Active');
		$this->db->order_by('ts_order' , 'asc');
		$res = $this->db->get('club_size');
		$club_size = $res->resulclub_array();
		$this->sci->assign('club_size' , $club_size);
		
		if($tds_id != 0) {
			$this->db->where('tds_id' , $tds_id);
			$res = $this->db->get('club_detail_size');
			$data = $res->row_array();
			$this->sci->assign('data' , $data);
		}
		
		$this->load->library('form_validation'); 
		$this->sci->d('detail_size/edit.htm');
	}
	
	function ajax_submiclub_detail_size_addedit() {
		$action = $this->input->post('action');
		$club_id = $this->input->post('club_id');
		$tds_id = $this->input->post('tds_id');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('tr_id', 'Rim', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('ts_id', 'Size', 'trim|required|xss_clean');  
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{
			$this->db->set('tr_id' , $this->input->post('tr_id') );
			$this->db->set('ts_id' , $this->input->post('ts_id') );
			$this->db->set('club_id' , $this->input->post('club_id') );
			if($action == 'add') {
				$this->db->set('tds_entry' , 'NOW()', false);
				$this->db->insert('club_detail_size');  
			}elseif($action == 'edit') {
				$this->db->where('tds_id' , $tds_id);
				$this->db->update('club_detail_size');
			}
			$data['status'] = 'ok';
			$data['msg'] = 'ok'; 
		}
		print json_encode($data);
	}
	
	function ajax_delete_detail_size($tds_id=0) {
		$this->db->where('tds_id' , $tds_id);
		$this->db->set('tds_status' , 'Deleted');
		$this->db->set('tds_deletion' , 'NOW()', FALSE);
		$this->db->update('club_detail_size');
		$data['status'] = 'ok';
		$data['msg'] = 'ok';
		print json_encode($data);
	}

}
