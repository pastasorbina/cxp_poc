<?php
class Tire_category extends MY_Controller {

	var $mod_title = 'Tire Category';

	var $table_name = 'tire_category';
	var $id_field = 'tc_id';
	var $status_field = 'tc_status';
	var $entry_field = 'tc_entry';
	var $stamp_field = 'tc_stamp';
	var $deletion_field = 'tc_deletion';
	var $order_field = 'tc_order';
	var $order_dir = 'ASC';
	var $label_field = 'tc_name';

	var $author_field = 'tc_author';
	var $editor_field = 'tc_editor';

	var $search_in = array('tc_name');

	var $template_add = 'edit.htm';
	var $template_edit = 'edit.htm';


	function __construct() {
		parent::__construct();
		$this->sci->init('admin');
		$this->_init();
		$this->session->validate(array('ADMIN'), 'admin');
		$this->sci->assign('use_ajax' , FALSE);


	}

	function iteration_setting($maindata=array()) {
		return $maindata;
	}

	function join_setting() { 
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active"); 
	}

	function validation_setting() {
		$this->form_validation->set_rules('tc_name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('tc_code', 'Code', 'trim|required|xss_clean');
		$this->form_validation->set_rules('tc_order', 'Order', 'trim|xss_clean');  
	}

	function database_setter() { 
		$this->db->set('tc_name' , $this->input->post('tc_name') );
		$this->db->set('tc_code' , $this->input->post('tc_code') );
		$this->db->set('tc_order' , $this->input->post('tc_order') );  
		if($_FILES['tc_logo']['name'] != '' ) {
			$filename = $this->_upload_image('tc_logo');
			$this->db->set('tc_logo' , $filename);
		}
		if($_FILES['tc_banner']['name'] != '' ) {
			$filename = $this->_upload_image('tc_banner');
			$this->db->set('tc_banner' , $filename);
		}
	}


	function pre_add_edit() { 
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
