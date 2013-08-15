<?php
class Motor_type extends MY_Controller {

	var $mod_title = 'Motor Type';

	var $table_name = 'motor_type';
	var $id_field = 'mt_id';
	var $status_field = 'mt_status';
	var $entry_field = 'mt_entry';
	var $stamp_field = 'mt_stamp';
	var $deletion_field = 'mt_deletion';
	var $order_field = 'mt_entry';
	var $order_dir = 'DESC';
	var $label_field = 'mt_name';

	var $author_field = 'mt_author';
	var $editor_field = 'mt_editor';

	var $search_in = array('mt_name');

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
		//$this->db->join('polling_category pollc' , 'pollc.pollc_id = polling_option.pollc_id' , 'left');
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active");

	}

	function validation_setting() {
		$this->form_validation->set_rules('mt_name', 'Name', 'trim|required|xss_clean');
		//$this->form_validation->set_rules('pollc_id', 'Category', 'trim|required|xss_clean');
		//$this->form_validation->set_rules('mt_order', 'Order', 'trim|xss_clean');
	}

	function database_setter() {
		$mt_name = $this->input->post('mt_name');
		$this->db->set('mt_name' , $mt_name );
		//$this->db->set('pollc_id' , $this->input->post('pollc_id') );
		//$this->db->set('mt_order' , $this->input->post('mt_order') );
	}


	function pre_add_edit() {
		//$polling_category = $this->mod_global->get_options('polling_category' , 'pollc_id' , 'pollc_name' , "pollc_status = 'Active'" , 'pollc_id');
		//$this->sci->assign('polling_category' , $polling_category);
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
