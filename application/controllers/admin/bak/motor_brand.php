<?php
class Motor_brand extends MY_Controller {

	var $mod_title = 'Motor Brand';

	var $table_name = 'motor_brand';
	var $id_field = 'mb_id';
	var $status_field = 'mb_status';
	var $entry_field = 'mb_entry';
	var $stamp_field = 'mb_stamp';
	var $deletion_field = 'mb_deletion';
	var $order_field = 'mb_entry';
	var $order_dir = 'DESC';
	var $label_field = 'mb_name';

	var $author_field = 'mb_author';
	var $editor_field = 'mb_editor';

	var $search_in = array('mb_name');

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
		$this->form_validation->set_rules('mb_name', 'Name', 'trim|required|xss_clean');
		//$this->form_validation->set_rules('pollc_id', 'Category', 'trim|required|xss_clean');
		//$this->form_validation->set_rules('mb_order', 'Order', 'trim|xss_clean');
	}

	function database_setter() {
		$mb_name = $this->input->post('mb_name');
		$this->db->set('mb_name' , $mb_name );
		//$this->db->set('pollc_id' , $this->input->post('pollc_id') );
		//$this->db->set('mb_order' , $this->input->post('mb_order') );
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
