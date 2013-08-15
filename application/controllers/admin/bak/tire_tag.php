<?php
class Tire_tag extends MY_Controller {

	var $mod_title = 'Tire Tag';

	var $table_name = 'tire_tag';
	var $id_field = 'tt_id';
	var $status_field = 'tt_status';
	var $entry_field = 'tt_entry';
	var $stamp_field = 'tt_stamp';
	var $deletion_field = 'tt_deletion';
	var $order_field = 'ttc_order, tt_order';
	var $order_dir = 'ASC';
	var $label_field = 'tt_name';

	var $author_field = 'tt_author';
	var $editor_field = 'tt_editor';

	var $search_in = array('tt_name');

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
		$this->db->join('tire_tag_category ttc' , 'ttc.ttc_id = tire_tag.ttc_id' , 'left');
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active"); 
	}

	function validation_setting() {
		$this->form_validation->set_rules('tt_name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('tt_code', 'Code', 'trim|required|xss_clean');
		$this->form_validation->set_rules('tt_order', 'Order', 'trim|xss_clean'); 
		$this->form_validation->set_rules('ttc_id', 'Order', 'trim|xss_clean'); 
	}

	function database_setter() { 
		$this->db->set('tt_name' , $this->input->post('tt_name') );
		$this->db->set('tt_code' , $this->input->post('tt_code') );
		$this->db->set('tt_order' , $this->input->post('tt_order') ); 
		$this->db->set('ttc_id' , $this->input->post('ttc_id') );
		if($_FILES['tt_logo']['name'] != '' ) {
			$filename = $this->_upload_image('tt_logo');
			$this->db->set('tt_logo' , $filename);
		}
	}


	function pre_add_edit() {
		$this->db->where('ttc_status' , 'Active');
		$this->db->order_by('ttc_order' , 'asc');
		$res = $this->db->get('tire_tag_category');
		$categories = $res->result_array();
		$this->sci->assign('categories' , $categories);
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
