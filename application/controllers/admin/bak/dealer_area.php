<?php
class Dealer_area extends MY_Controller {

	var $mod_title = 'Dealers Area';

	var $table_name = 'dealer_area';
	var $id_field = 'dlra_id';
	var $status_field = 'dlra_status';
	var $entry_field = 'dlra_entry';
	var $stamp_field = 'dlra_stamp';
	var $deletion_field = 'dlra_deletion';
	var $order_field = 'dlra_entry';
	var $order_dir = 'DESC';
	var $label_field = 'dlra_name';

	var $author_field = 'dlra_author';
	var $editor_field = 'dlra_editor';

	var $search_in = array('dlra_name');

	var $template_add = 'edit.htm';
	var $template_edit = 'edit.htm';


	function __construct() {
		parent::__construct();
		$this->sci->init('admin');
		$this->_init();
		$this->session->validate(array('ADMIN'), 'admin');
		$this->sci->assign('use_ajax' , FALSE);

	}

	function enum_setting($maindata=array()) {
		return $maindata;
	}

	function join_setting() {
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active");
	}

	function validation_setting() {
		$this->form_validation->set_rules('dlra_name', 'Name', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('dlra_lat', 'Latitude', 'trim|xss_clean');
		$this->form_validation->set_rules('dlra_long', 'Longitude', 'trim|xss_clean');
	}

	function database_setter() { 
		$this->db->set('dlra_name' ,  $this->input->post('dlra_name') ); 
		$this->db->set('dlra_lat' ,  $this->input->post('dlra_lat') );
		$this->db->set('dlra_long' ,  $this->input->post('dlra_long') );  
	}


	function pre_add_edit() { 
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
