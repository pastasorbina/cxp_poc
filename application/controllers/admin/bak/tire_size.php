<?php
class Tire_size extends MY_Controller {

	var $mod_title = 'Tire Size';

	var $table_name = 'tire_size';
	var $id_field = 'ts_id';
	var $status_field = 'ts_status';
	var $entry_field = 'ts_entry';
	var $stamp_field = 'ts_stamp';
	var $deletion_field = 'ts_deletion';
	var $order_field = 'ts_name';
	var $order_dir = 'ASC';
	var $label_field = 'ts_name';

	var $author_field = 'ts_author';
	var $editor_field = 'ts_editor';

	var $search_in = array('ts_name');

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
		$this->form_validation->set_rules('ts_name', 'Name', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('ts_order', 'Order', 'trim|xss_clean');
	}

	function database_setter() { 
		$this->db->set('ts_name' , $this->input->post('ts_name') ); 
		$this->db->set('ts_order' , $this->input->post('ts_order') );
	}


	function pre_add_edit() { 
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
