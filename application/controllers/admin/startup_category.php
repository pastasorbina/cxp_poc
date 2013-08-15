<?php
class Startup_category extends MY_Controller {

	var $mod_title = 'Startup Category';

	var $table_name = 'startup_category';
	var $id_field = 'sc_id';
	var $status_field = 'sc_status';
	var $entry_field = 'sc_entry';
	var $stamp_field = 'sc_stamp';
	var $deletion_field = 'sc_deletion';
	var $order_field = 'sc_order';
	var $order_dir = 'ASC';
	var $label_field = 'sc_name';

	var $author_field = 'sc_author';
	var $editor_field = 'sc_editor';

	var $search_in = array('sc_name');

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
		$this->form_validation->set_rules('sc_name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('sc_order', 'Order', 'trim|xss_clean');
	}

	function database_setter() {
		$this->db->set('sc_name' , $this->input->post('sc_name') );
		$this->db->set('sc_order' , $this->input->post('sc_order') );
		//if($_FILES['sc']['name'] != '' ) {
		//	$filename = $this->_upload_image('sc_logo');
		//	$this->db->set('sc_logo' , $filename);
		//}
		//if($_FILES['sc_banner']['name'] != '' ) {
		//	$filename = $this->_upload_image('sc_banner');
		//	$this->db->set('sc_banner' , $filename);
		//}
	}


	function pre_add_edit() {
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
