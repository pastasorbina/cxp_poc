<?php
class Tire_rim extends MY_Controller {

	var $mod_title = 'Tire Rim';

	var $table_name = 'tire_rim';
	var $id_field = 'tr_id';
	var $status_field = 'tr_status';
	var $entry_field = 'tr_entry';
	var $stamp_field = 'tr_stamp';
	var $deletion_field = 'tr_deletion';
	var $order_field = 'tr_name';
	var $order_dir = 'ASC';
	var $label_field = 'tr_name';

	var $author_field = 'tr_author';
	var $editor_field = 'tr_editor';

	var $search_in = array('tr_name');

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
		$this->form_validation->set_rules('tr_name', 'Name', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('tr_order', 'Order', 'trim|xss_clean');
	}

	function database_setter() { 
		$this->db->set('tr_name' , $this->input->post('tr_name') ); 
		$this->db->set('tr_order' , $this->input->post('tr_order') );
	}


	function pre_add_edit() { 
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
