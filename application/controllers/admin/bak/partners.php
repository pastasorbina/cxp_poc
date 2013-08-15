<?php
class Partners extends MY_Controller {

	var $mod_title = 'Partners';

	var $table_name = 'partners';
	var $id_field = 'prt_id';
	var $status_field = 'prt_status';
	var $entry_field = 'prt_entry';
	var $stamp_field = 'prt_stamp';
	var $deletion_field = 'prt_deletion';
	var $order_field = 'prt_entry';
	var $order_dir = 'DESC';
	var $label_field = 'prt_name';

	var $author_field = 'prt_author';
	var $editor_field = 'prt_editor';

	var $search_in = array('prt_name');

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
		$this->form_validation->set_rules('prt_name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('prt_url', 'URL', 'trim');
		$this->form_validation->set_rules('prt_order', 'Order', 'trim|xss_clean');
	}

	function database_setter() { 
		$this->db->set('prt_name' ,  $this->input->post('prt_name') );
		$this->db->set('prt_url' ,  $this->input->post('prt_url') );
		$this->db->set('prt_order' ,  $this->input->post('prt_order') );
		if($_FILES['prt_image']['name'] != '' ) {
			$filename = $this->_upload_image('prt_image');
			$this->db->set('prt_image' , $filename);
		}
	}


	function pre_add_edit() { 
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
