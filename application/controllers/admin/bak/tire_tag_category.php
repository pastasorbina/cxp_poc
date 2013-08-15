<?php
class Tire_tag_category extends MY_Controller {

	var $mod_title = 'Tire Tag Category';

	var $table_name = 'tire_tag_category';
	var $id_field = 'ttc_id';
	var $status_field = 'ttc_status';
	var $entry_field = 'ttc_entry';
	var $stamp_field = 'ttc_stamp';
	var $deletion_field = 'ttc_deletion';
	var $order_field = 'ttc_order';
	var $order_dir = 'ASC';
	var $label_field = 'ttc_name';

	var $author_field = 'ttc_author';
	var $editor_field = 'ttc_editor';

	var $search_in = array('ttc_name');

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
		$this->form_validation->set_rules('ttc_name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ttc_code', 'Code', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ttc_order', 'Order', 'trim|xss_clean'); 
	}

	function database_setter() { 
		$this->db->set('ttc_name' , $this->input->post('ttc_name') );
		$this->db->set('ttc_code' , $this->input->post('ttc_code') ); 
		$this->db->set('ttc_order' , $this->input->post('ttc_order') ); 
	}


	function pre_add_edit() { 
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
