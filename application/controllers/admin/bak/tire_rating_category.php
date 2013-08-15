<?php
class Tire_rating_category extends MY_Controller {

	var $mod_title = 'Tire Rating Categories';

	var $table_name = 'tire_rating_category';
	var $id_field = 'trtc_id';
	var $status_field = 'trtc_status';
	var $entry_field = 'trtc_entry';
	var $stamp_field = 'trtc_stamp';
	var $deletion_field = 'trtc_deletion';
	var $order_field = 'trtc_order';
	var $order_dir = 'ASC';
	var $label_field = 'trtc_name';

	var $author_field = 'trtc_author';
	var $editor_field = 'trtc_editor';

	var $search_in = array('trtc_name');

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
		$this->form_validation->set_rules('trtc_name', 'Name', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('trtc_code', 'Code', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('trtc_order', 'Order', 'trim|xss_clean');
		$this->form_validation->set_rules('trtc_type', 'Type', 'trim|xss_clean');
	}

	function database_setter() { 
		$this->db->set('trtc_name' , $this->input->post('trtc_name') ); 
		$this->db->set('trtc_code' , $this->input->post('trtc_code') ); 
		$this->db->set('trtc_order' , $this->input->post('trtc_order') );
		$this->db->set('trtc_type' , $this->input->post('trtc_type') );
	}


	function pre_add_edit() { 
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
