<?php
class Career extends MY_Controller {

	var $mod_title = 'Career Results';

	var $table_name = 'form_data';
	var $id_field = 'ft_id';
	var $status_field = 'ft_status';
	var $entry_field = 'ft_entry';
	var $stamp_field = 'ft_stamp';
	var $deletion_field = 'ft_deletion';
	var $order_field = 'ft_entry';
	var $order_dir = 'DESC';
	var $label_field = 'ft_name';

	var $author_field = 'ft_author';
	var $editor_field = 'ft_editor';

	var $search_in = array('ft_name');

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
		$this->db->join('ft_id' , 'condition' , 'left');
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active"); 
	}

	function validation_setting($action="add") {
		$this->form_validation->set_rules('ft_name', 'Club Name', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('ft_email', 'Club Email', 'trim|xss_clean'); 
		$this->form_validation->set_rules('ft_desc', 'Club Description', 'trim'); 
		$this->form_validation->set_rules('ft_contact', 'Club Contact', 'trim|xss_clean'); 
		$this->form_validation->set_rules('ft_address', 'Club Address', 'trim|xss_clean');
		$this->form_validation->set_rules('ft_intro', 'Club Intro', 'trim|xss_clean');
		$this->form_validation->set_rules('ft_website', 'Club Website', 'trim|xss_clean');
		$this->form_validation->set_rules('ft_facebook', 'Club Facebook', 'trim|xss_clean');
		$this->form_validation->set_rules('ft_twitter', 'Club Twitter', 'trim|xss_clean');
		$this->form_validation->set_rules('ft_milis', 'Club Milis', 'trim|xss_clean');
		$this->form_validation->set_rules('ft_other1', 'Club other links 1', 'trim|xss_clean');
		$this->form_validation->set_rules('ft_other2', 'Club other links 2', 'trim|xss_clean');
		$this->form_validation->set_rules('ft_other3', 'Club other links 3', 'trim|xss_clean');
		$this->form_validation->set_rules('ft_join_date', 'Club Join Date', 'trim|xss_clean');
		$this->form_validation->set_rules('ft_est_date', 'Club Established Date', 'trim|xss_clean');
	}

	function database_setter($action='add') { 
		$this->db->set('ft_name' , $this->input->post('ft_name') ); 
		$this->db->set('ft_email' , $this->input->post('ft_email') ); 
		$this->db->set('ft_desc' , $this->input->post('ft_desc') ); 
		$this->db->set('ft_contact' , $this->input->post('ft_contact') ); 
		$this->db->set('ft_address' , $this->input->post('ft_address') );
		$this->db->set('ft_intro' , $this->input->post('ft_intro') );
		$this->db->set('ft_website' , $this->input->post('ft_website') );
		$this->db->set('ft_facebook' , $this->input->post('ft_facebook') );
		$this->db->set('ft_twitter' , $this->input->post('ft_twitter') );
		$this->db->set('ft_milis' , $this->input->post('ft_milis') );
		$this->db->set('ft_other1' , $this->input->post('ft_other1') );
		$this->db->set('ft_other2' , $this->input->post('ft_other2') );
		$this->db->set('ft_other3' , $this->input->post('ft_other3') );
		$this->db->set('ft_join_date' , $this->input->post('ft_join_date') );
		$this->db->set('ft_est_date' , $this->input->post('ft_est_date') );
		
		if($_FILES['ft_logo']['name'] != '' ) {
			$filename = $this->_upload_image('ft_logo');
			$this->db->set('ft_logo' , $filename);
		}
		if($_FILES['ft_mainphoto']['name'] != '' ) {
			$filename = $this->_upload_image('ft_mainphoto');
			$this->db->set('ft_mainphoto' , $filename);
		}
		if($action == "add") {
			$this->db->set('ft_entry' , 'NOW()', FALSE);
		}
	}
	
	function validation_setting_member($action="add") {
		$this->form_validation->set_rules('m_login', 'User Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('m_firstname', 'First Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('m_lastname', 'Last Name', 'trim|xss_clean');
		$this->form_validation->set_rules('m_pass', 'Password', 'required|trim|xss_clean');
		$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'required|trim|xss_clean|matches[m_pass]'); 
		$this->form_validation->set_rules('m_mobile', 'Mobile Number', 'trim|xss_clean'); 
		$this->form_validation->set_rules('m_phone', 'Phone Number', 'trim|xss_clean'); 
	}
	
	function database_setter_member($action="add") { 
		$this->db->set('m_firstname' , $this->input->post('m_firstname'));
		$this->db->set('m_lastname' , $this->input->post('m_lastname'));
		$this->db->set('m_phone' , $this->input->post('m_phone'));
		$this->db->set('m_mobile' , $this->input->post('m_mobile'));

		$m_login = $this->input->post('m_login');
		$this->db->set('m_email' , $m_login);
		$this->db->set('m_login' , $m_login); 

		$this->load->helper('member');
		$m_registration_key = generate_registration_key($m_login);
		$m_activation_key = generate_activation_key($m_login);
		$this->db->set('m_registration_key' , $m_login);
		$this->db->set('m_activation_key' , $m_login);
		
		$this->db->set('m_is_active' , 'Yes');

		$salt = $this->config->item('salt');
		$password = $this->input->post('m_pass');
		$password = md5($salt.$password);
		$this->db->set('m_pass' , $password);
		if($action == "add") {
			$this->db->set('m_entry' , 'NOW()', FALSE);
		} 
	}


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
		$this->validation_setting_member('add');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('add.htm');
		}else{
			$this->database_setter('add');  
			$ok = $this->db->insert('club');
			$insert_id = $this->db->insert_id();
			
			$this->database_setter_member('add');
			$this->db->set('ft_id' , $insert_id);
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
	
	function edit_club($ft_id=0) {
		$this->sci->assign('ajax_action' , 'edit');
		$this->pre_add_edit();
		$this->pre_edit();
		
		$this->db->where('club.ft_id' , $ft_id);
		$this->db->join('member m' , 'm.ft_id = club.ft_id' , 'left');
		$res = $this->db->get('club');
		$data = $res->row_array();
		$this->sci->assign('data' , $data);

		$this->load->library('form_validation');
		$this->validation_setting('edit');
		
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('edit.htm');
		}else{
			$this->database_setter('edit');
			$this->db->where('ft_id' , $ft_id);
			$ok = $this->db->update('club'); 
			
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url.'edit_club');
			} else {
				$this->session->set_confirm(1); 
				redirect($this->mod_url."view/$ft_id");
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
	
	
	function view($ft_id=0) {
		$this->db->where('ft_id' , $ft_id); 
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		
		if($club) {
			$this->db->where('m_id' , $club['ft_id']);
			$res = $this->db->get('member');
			$member = $res->row_array();
			$this->sci->assign('member' , $member);
		}
		
		$this->sci->da('view.htm');
	}
	
	
	function ajax_load_ft_files($ft_id=0) {
		$this->db->join('club c' , 'c.ft_id = ft_files.ft_id' , 'left'); 
		$this->db->where('clubft_status' , 'Active');
		$this->db->where('ft_files.ft_id' , $ft_id);
		$res = $this->db->get('ft_files');
		$maindata = $res->result_array();
		$this->sci->assign('maindata' , $maindata);
		 
		$this->sci->d('ft_files/list.htm');
	}
	
	function _clubft_rules() {
		$this->form_validation->set_rules('clubft_title', 'Title', 'trim|xss_clean'); 
		$this->form_validation->set_rules('clubft_date', 'Date', 'trim'); 
		$this->form_validation->set_rules('ft_id', 'Club ID', 'trim'); 
	}

	function _clubft_db() { 
		$c_title = $this->input->post('clubft_title');
		$this->db->set('clubft_title' , $c_title);  
		$this->db->set('ft_id' , $this->input->post('ft_id') );  

		$c_date = $this->input->post('clubft_date');
		$c_date = $c_date ? $c_date : date('Y-m-d H:i:s');
		$this->db->set('clubft_date' , $c_date );
		
		if($_FILES['clubft_file']['name'] != '' ) {
			$filename = $this->_upload_image('clubft_file', TRUE);
			$this->db->set('clubft_file' , $filename);
		}
	}
	
	 function add_ft_files($ft_id=0) {
		$this->sci->assign('ajax_action' , 'add');
		$this->sci->assign('ft_id' , $ft_id); 
		
		$this->db->where('ft_id' , $ft_id);
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		  
		$this->load->library('form_validation');
		$this->_clubft_rules('add');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da("ft_files/edit.htm");
		}else{
			$this->_clubft_db('add');
			$this->db->set("clubft_entry" , 'NOW()', FALSE );
			$ok = $this->db->insert("ft_files");
			$insert_id = $this->db->insert_id(); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."add_ft_files/$ft_id");
			} else {
				$this->session->set_confirm(1); 
				redirect($this->mod_url."view/$ft_id");
			}
		}
	}
	
	function edit_ft_files($clubft_id=0) {
		$this->sci->assign('ajax_action' , 'add');
		$this->sci->assign('clubft_id' , $clubft_id);
		
		$this->db->where('clubft_id' , $clubft_id);
		$res = $this->db->get('ft_files');
		$content = $res->row_array();
		$this->sci->assign('content' , $content);
		
		$ft_id = $content['ft_id'];
		$this->db->where('ft_id' , $ft_id);
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		  
		$this->load->library('form_validation');
		$this->_clubft_rules('edit');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da("ft_files/edit.htm");
		}else{
			$this->_clubft_db('edit');
			$this->db->where('clubft_id' , $clubft_id);
			$ok = $this->db->update("ft_files");
			$insert_id = $this->db->insert_id(); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."edit_ft_files/$clubft_id");
			} else {
				$this->session->set_confirm(1); 
				redirect($this->mod_url."view/$ft_id");
			}
		}
	}
	
	
	
	
	
	
	
	
	function ajax_load_ft_content($ft_id=0) {
		$this->db->join('club c' , 'c.ft_id = ft_content.ft_id' , 'left'); 
		$this->db->where('clubc_status' , 'Active');
		$this->db->where('ft_content.ft_id' , $ft_id);
		$res = $this->db->get('ft_content');
		$maindata = $res->result_array();
		$this->sci->assign('maindata' , $maindata);
		 
		$this->sci->d('ft_content/list.htm');
	}
	
	function _clubc_rules() {
		$this->form_validation->set_rules('clubc_title', 'Title', 'trim|required|xss_clean');
		$this->form_validation->set_rules('clubc_content', 'Content', 'trim');
		$this->form_validation->set_rules('clubc_intro', 'Content Intro', 'trim'); 
		$this->form_validation->set_rules('clubc_date', 'Date', 'trim');
		$this->form_validation->set_rules('clubc_type', 'Type', 'trim'); 
		$this->form_validation->set_rules('ft_id', 'Club ID', 'trim'); 
	}

	function _clubc_db() { 
		$c_title = $this->input->post('clubc_title');
		$this->db->set('clubc_title' , $c_title); 
		$this->db->set('clubc_content' , $this->input->post('clubc_content') );
		$this->db->set('clubc_intro' , $this->input->post('clubc_intro') ); 
		$this->db->set('clubc_type' , $this->input->post('clubc_type') );
		$this->db->set('ft_id' , $this->input->post('ft_id') );  

		$c_date = $this->input->post('clubc_date');
		$c_date = $c_date ? $c_date : date('Y-m-d H:i:s');
		$this->db->set('clubc_date' , $c_date );
		
		if($_FILES['clubc_banner']['name'] != '' ) {
			$filename = $this->_upload_image('clubc_banner', TRUE);
			$this->db->set('clubc_banner' , $filename);
		}
	}
	
	 function add_ft_content($ft_id=0) {
		$this->sci->assign('ajax_action' , 'add');
		$this->sci->assign('ft_id' , $ft_id); 
		
		$this->db->where('ft_id' , $ft_id);
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		  
		$this->load->library('form_validation');
		$this->_clubc_rules('add');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da("ft_content/edit.htm");
		}else{
			$this->_clubc_db('add');
			$this->db->set("clubc_entry" , 'NOW()', FALSE );
			$ok = $this->db->insert("ft_content");
			$insert_id = $this->db->insert_id(); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."add_ft_content/$ft_id");
			} else {
				$this->session->set_confirm(1); 
				redirect($this->mod_url."view/$ft_id");
			}
		}
	}
	
	function edit_ft_content($clubc_id=0) {
		$this->sci->assign('ajax_action' , 'add');
		$this->sci->assign('clubc_id' , $clubc_id);
		
		$this->db->where('clubc_id' , $clubc_id);
		$res = $this->db->get('ft_content');
		$content = $res->row_array();
		$this->sci->assign('content' , $content);
		
		$ft_id = $content['ft_id'];
		$this->db->where('ft_id' , $ft_id);
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		  
		$this->load->library('form_validation');
		$this->_clubc_rules('edit');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da("ft_content/edit.htm");
		}else{
			$this->_clubc_db('edit');
			$this->db->where('clubc_id' , $clubc_id);
			$ok = $this->db->update("ft_content");
			$insert_id = $this->db->insert_id(); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."edit_ft_content/$clubc_id");
			} else {
				$this->session->set_confirm(1); 
				redirect($this->mod_url."view/$ft_id");
			}
		}
	}
	
	 
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function ajax_load_detail_tech_list($ft_id=0) {
		$this->db->join('club t' , 't.ft_id = ft_detail_tech.ft_id' , 'left');
		$this->db->join('ft_tech tth' , 'tth.tth_id = ft_detail_tech.tth_id' , 'left'); 
		$this->db->where('tdth_status' , 'Active');
		$this->db->where('ft_detail_tech.ft_id' , $ft_id);
		$res = $this->db->get('ft_detail_tech');
		$maindata = $res->resulft_array();
		$this->sci->assign('maindata' , $maindata);
		 
		$this->sci->d('detail_tech/list.htm');
	}
	 
	function ajax_load_detail_tech_edit($ft_id=0) {
		$this->sci->assign('action' , 'edit'); 
		$this->sci->assign('ft_id' , $ft_id);
		 
		$this->db->where('tdth_status' , 'Active');
		$this->db->where('ft_id' , $ft_id);
		$this->db->select('tth_id');
		$res = $this->db->get('ft_detail_tech');
		$atechs = $res->resulft_array();
		$currenft_techs = array();
		foreach($atechs as $k=>$tmp) {
			$currenft_techs[] = $tmp['tth_id'];
		}
		 
		$this->db->where('tth_status' , 'Active');
		$this->db->order_by('tth_order' , 'asc');
		$res = $this->db->get('ft_tech');
		$ft_tech = $res->resulft_array();
		if(sizeof($currenft_techs) > 0) {
			foreach($ft_tech as $k=>$tmp) {
				if(in_array($tmp['tth_id'], $currenft_techs)){ 
					$ft_tech[$k]['on'] = 'Yes';
				} else {
					$ft_tech[$k]['on'] = 'No';
				}
			} 
		} 
		$this->sci->assign('ft_tech' , $ft_tech);
		
		
		$this->load->library('form_validation'); 
		$this->sci->d('detail_tech/edit.htm');
	}
	
	function ajax_submift_detail_tech() {
		$action = $this->input->post('action');
		$ft_id = $this->input->post('ft_id'); 
		$tth_id = $this->input->post('tth_id'); 
		$this->load->library('form_validation'); 
		$this->form_validation->set_rules('tth_id', 'Tag', '');   
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{
			$this->db->where('ft_id' , $ft_id);
			$this->db->delete('ft_detail_tech');
			foreach($tth_id as $k=>$tmp) {
				$this->db->set('ft_id' , $ft_id);
				$this->db->set('tth_id' , $tmp);
				$this->db->set('tdth_entry' , 'NOW()', FALSE);
				$this->db->insert('ft_detail_tech');
			}
			$data['status'] = 'ok';
			$data['msg'] = 'ok'; 
		} 
		print json_encode($data);
	}
	
	
	
	
	
	
	
	
	function ajax_load_detail_tag_list($ft_id=0) {
		$this->db->join('club t' , 't.ft_id = ft_detail_tag.ft_id' , 'left');
		$this->db->join('ft_tag tt' , 'tt.tft_id = ft_detail_tag.tft_id' , 'left');
		$this->db->join('ft_tag_category ttc' , 'ttc.ttc_id = tt.ttc_id' , 'left');
		$this->db->where('tdft_status' , 'Active');
		$this->db->where('ft_detail_tag.ft_id' , $ft_id);
		$res = $this->db->get('ft_detail_tag');
		$maindata = $res->resulft_array();
		$this->sci->assign('maindata' , $maindata);
		
		
		$this->sci->d('detail_tag/list.htm');
	}
	
	function ajax_load_detail_tag_edit($ft_id=0) {
		$this->sci->assign('action' , 'edit'); 
		$this->sci->assign('ft_id' , $ft_id);
		 
		$this->db->where('tdft_status' , 'Active');
		$this->db->where('ft_id' , $ft_id);
		$this->db->select('tft_id');
		$res = $this->db->get('ft_detail_tag');
		$atags = $res->resulft_array();
		$currenft_tags = array();
		foreach($atags as $k=>$tmp) {
			$currenft_tags[] = $tmp['tft_id'];
		}
		 
		$this->db->where('ttc_status' , 'Active');
		$this->db->order_by('ttc_order' , 'asc');
		$res = $this->db->get('ft_tag_category');
		$ft_tag_category = $res->resulft_array();
		foreach($ft_tag_category as $k=>$tmp) {
			$this->db->where('ttc_id' , $tmp['ttc_id']);
			$this->db->where('tft_status' , 'Active');
			$this->db->order_by('tft_order' , 'asc');
			$res = $this->db->get('ft_tag');
			$ft_tag_category[$k]['ft_tag'] = $res->resulft_array();
			if(sizeof($currenft_tags) > 0) {
				foreach($ft_tag_category[$k]['ft_tag'] as $k2 => $tmp2) {
					if(in_array($tmp2['tft_id'], $currenft_tags)){ 
						$ft_tag_category[$k]['ft_tag'][$k2]['on'] = 'Yes';
					} else {
						$ft_tag_category[$k]['ft_tag'][$k2]['on'] = 'No';
					}
				}
			}
		} 
		$this->sci->assign('ft_tag_category' , $ft_tag_category);
		
		
		$this->load->library('form_validation'); 
		$this->sci->d('detail_tag/edit.htm');
	}
	
	function ajax_submift_detail_tag() {
		$action = $this->input->post('action');
		$ft_id = $this->input->post('ft_id'); 
		$tft_id = $this->input->post('tft_id'); 
		$this->load->library('form_validation'); 
		$this->form_validation->set_rules('tft_id', 'Tag', '');   
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{
			$this->db->where('ft_id' , $ft_id);
			$this->db->delete('ft_detail_tag');
			foreach($tft_id as $k=>$tmp) {
				$this->db->set('ft_id' , $ft_id);
				$this->db->set('tft_id' , $tmp);
				$this->db->set('tdft_entry' , 'NOW()', FALSE);
				$this->db->insert('ft_detail_tag');
			}
			$data['status'] = 'ok';
			$data['msg'] = 'ok'; 
		} 
		print json_encode($data);
	}
	
	
	
	
	
	function ajax_load_detail_rating_list($ft_id=0) {
		$this->db->join('club' , 'club.ft_id = ft_detail_rating.ft_id' , 'left');
		$this->db->join('ft_rating trt' , 'trt.trft_id = ft_detail_rating.trft_id' , 'left'); 
		$this->db->join('ft_rating_category trtc' , 'trtc.trtc_id = trt.trtc_id' , 'left'); 
		$this->db->where('tdr_status' , 'Active');
		$this->db->where('ft_detail_rating.ft_id' , $ft_id);
		$res = $this->db->get('ft_detail_rating');
		$maindata = $res->resulft_array();
		$this->sci->assign('maindata' , $maindata);
		
		$this->sci->d('detail_rating/list.htm');
	}
	
	function ajax_load_detail_rating_addedit($action='add', $tdr_id=0, $ft_id=0) {
		$this->sci->assign('action' , $action);
		$this->sci->assign('tdr_id' , $tdr_id);
		$this->sci->assign('ft_id' , $ft_id);
		
		$this->db->join('ft_rating_category trtc' , 'trtc.trtc_id = ft_rating.trtc_id' , 'left'); 
		$this->db->where('trft_status' , 'Active');
		$this->db->order_by('trtc.trtc_order' , 'asc');
		$res = $this->db->get('ft_rating');
		$ft_rating = $res->resulft_array();
		$this->sci->assign('ft_rating' , $ft_rating);
		 
		
		if($tdr_id != 0) {
			$this->db->where('tdr_id' , $tdr_id);
			$res = $this->db->get('ft_detail_rating');
			$data = $res->row_array();
			$this->sci->assign('data' , $data);
		}
		
		$this->load->library('form_validation'); 
		$this->sci->d('detail_rating/edit.htm');
	}
	
	function ajax_submift_detail_rating_addedit() {
		$action = $this->input->post('action');
		$ft_id = $this->input->post('ft_id');
		$tdr_id = $this->input->post('tdr_id');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('trft_id', 'Rating', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('tdr_value', 'Rating Value', 'trim|required|xss_clean');  
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{
			$this->db->set('trft_id' , $this->input->post('trft_id') );
			$this->db->set('tdr_value' , $this->input->post('tdr_value') );
			$this->db->set('ft_id' , $this->input->post('ft_id') );
			if($action == 'add') {
				$this->db->set('tdr_entry' , 'NOW()', false);
				$this->db->insert('ft_detail_rating');  
			}elseif($action == 'edit') {
				$this->db->where('tdr_id' , $tdr_id);
				$this->db->update('ft_detail_rating');
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
		$this->db->update('ft_detail_rating');
		$data['status'] = 'ok';
		$data['msg'] = 'ok';
		print json_encode($data);
	}
	
	
	
	
	
	
	
	function ajax_load_detail_size_list($ft_id=0) {
		$this->db->join('club' , 'club.ft_id = ft_detail_size.ft_id' , 'left');
		$this->db->join('ft_rim tr' , 'tr.tr_id = ft_detail_size.tr_id' , 'left');
		$this->db->join('ft_size ts' , 'ts.ts_id = ft_detail_size.ts_id' , 'left');
		$this->db->where('tds_status' , 'Active');
		$this->db->where('ft_detail_size.ft_id' , $ft_id);
		$res = $this->db->get('ft_detail_size');
		$maindata = $res->resulft_array();
		$this->sci->assign('maindata' , $maindata);
		
		$this->sci->d('detail_size/list.htm');
	}
	
	function ajax_load_detail_size_addedit($action='add', $tds_id=0, $ft_id=0) {
		$this->sci->assign('action' , $action);
		$this->sci->assign('tds_id' , $tds_id);
		$this->sci->assign('ft_id' , $ft_id);
		
		$this->db->where('tr_status' , 'Active');
		$this->db->order_by('tr_order' , 'asc');
		$res = $this->db->get('ft_rim');
		$ft_rim = $res->resulft_array();
		$this->sci->assign('ft_rim' , $ft_rim);
		
		$this->db->where('ts_status' , 'Active');
		$this->db->order_by('ts_order' , 'asc');
		$res = $this->db->get('ft_size');
		$ft_size = $res->resulft_array();
		$this->sci->assign('ft_size' , $ft_size);
		
		if($tds_id != 0) {
			$this->db->where('tds_id' , $tds_id);
			$res = $this->db->get('ft_detail_size');
			$data = $res->row_array();
			$this->sci->assign('data' , $data);
		}
		
		$this->load->library('form_validation'); 
		$this->sci->d('detail_size/edit.htm');
	}
	
	function ajax_submift_detail_size_addedit() {
		$action = $this->input->post('action');
		$ft_id = $this->input->post('ft_id');
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
			$this->db->set('ft_id' , $this->input->post('ft_id') );
			if($action == 'add') {
				$this->db->set('tds_entry' , 'NOW()', false);
				$this->db->insert('ft_detail_size');  
			}elseif($action == 'edit') {
				$this->db->where('tds_id' , $tds_id);
				$this->db->update('ft_detail_size');
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
		$this->db->update('ft_detail_size');
		$data['status'] = 'ok';
		$data['msg'] = 'ok';
		print json_encode($data);
	}

}
