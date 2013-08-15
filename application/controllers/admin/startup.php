<?php
class startup extends MY_Controller {

	var $mod_title = 'Startup';

	var $table_name = 'startup';
	var $id_field = 's_id';
	var $status_field = 's_status';
	var $entry_field = 's_entry';
	var $stamp_field = 's_stamp';
	var $deletion_field = 's_deletion';
	var $order_field = 's_entry';
	var $order_dir = 'ASC';
	var $label_field = 's_name';

	var $author_field = 's_author';
	var $editor_field = 's_editor';

	var $search_in = array('s_name');

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
		$this->db->join('startup_category sc' , 'sc.sc_id = startup.sc_id' , 'left');
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active");
	}

	function validation_setting() {
		$this->form_validation->set_rules('s_name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('sc_id', 'Category', 'trim');
		$this->form_validation->set_rules('s_desc', 'Order', 'trim');
		$this->form_validation->set_rules('s_company', 'Company', 'trim');
	}

	function database_setter() {
		$this->db->set('s_name' , $this->input->post('s_name') );
		$this->db->set('sc_id' , $this->input->post('sc_id') );
		$this->db->set('s_desc' , $this->input->post('s_desc') );
		$this->db->set('s_company' , $this->input->post('s_company') );
		if($_FILES['s_thumbnail']['name'] != '' ) {
			$filename = $this->_upload_image('s_thumbnail');
			$this->db->set('s_thumbnail' , $filename);
		}
	}


	function pre_add_edit() {
		$this->db->where('sc_status' , 'Active');
		$res = $this->db->get('startup_category');
		$category = $res->result_array();
		$this->sci->assign('category' , $category);
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
