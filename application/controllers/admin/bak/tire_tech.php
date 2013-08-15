<?php
class Tire_tech extends MY_Controller {

	var $mod_title = 'Tire Technology';

	var $table_name = 'tire_tech';
	var $id_field = 'tth_id';
	var $status_field = 'tth_status';
	var $entry_field = 'tth_entry';
	var $stamp_field = 'tth_stamp';
	var $deletion_field = 'tth_deletion';
	var $order_field = 'tth_order';
	var $order_dir = 'ASC';
	var $label_field = 'tth_name';

	var $author_field = 'tth_author';
	var $editor_field = 'tth_editor';

	var $search_in = array('tth_name');

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
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active");

	}

	function validation_setting() {
		$this->form_validation->set_rules('tth_name', 'Name', 'trim|required'); 
		$this->form_validation->set_rules('tth_brief', 'Description', 'trim');  
		$this->form_validation->set_rules('tth_desc', 'Description', 'trim');  
		$this->form_validation->set_rules('tth_subtitle', 'Subtitle', 'trim');  
		$this->form_validation->set_rules('tth_order', 'Order', 'trim|xss_clean');  
	}

	function database_setter() { 
		$this->db->set('tth_name' , $this->input->post('tth_name') ); 
		$this->db->set('tth_brief' , $this->input->post('tth_brief') ); 
		$this->db->set('tth_subtitle' , $this->input->post('tth_subtitle') ); 
		$this->db->set('tth_desc' , $this->input->post('tth_desc') );
		$this->db->set('tth_order' , $this->input->post('tth_order') );
		if($_FILES['tth_logo']['name'] != '' ) {
			$filename = $this->_upload_image('tth_logo');
			$this->db->set('tth_logo' , $filename);
		}
	}


	function pre_add_edit() { 
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
