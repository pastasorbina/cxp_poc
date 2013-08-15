<?php
class Tire_rating extends MY_Controller {

	var $mod_title = 'Tire Ratings';

	var $table_name = 'tire_rating';
	var $id_field = 'trt_id';
	var $status_field = 'trt_status';
	var $entry_field = 'trt_entry';
	var $stamp_field = 'trt_stamp';
	var $deletion_field = 'trt_deletion';
	var $order_field = 'trt_entry';
	var $order_dir = 'DESC';
	var $label_field = 'trt_name';

	var $author_field = 'trt_author';
	var $editor_field = 'trt_editor';

	var $search_in = array('trt_name');

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
		$this->db->join('tire_rating_category trtc' , 'trtc.trtc_id = tire_rating.trtc_id' , 'left');
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active"); 
		//$this->db->order_by('trtc.trtc_order' , 'asc');
		//$this->db->order_by('trt_order' , 'asc');
	}

	function validation_setting() {
		$this->form_validation->set_rules('trt_name', 'Name', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('trt_order', 'Order', 'trim|xss_clean');
		$this->form_validation->set_rules('trt_tooltip', 'Tooltip', 'trim|xss_clean');
		$this->form_validation->set_rules('trtc_id', 'Category', 'trim|required|xss_clean');
	}

	function database_setter() { 
		$this->db->set('trt_name' , $this->input->post('trt_name') ); 
		$this->db->set('trt_tooltip' , $this->input->post('trt_tooltip') );
		$this->db->set('trt_order' , $this->input->post('trt_order') );
		$this->db->set('trtc_id' , $this->input->post('trtc_id') );
	}


	function pre_add_edit() {
		$this->db->where('trtc_status' , 'Active');
		$this->db->order_by('trtc_order' , 'asc');
		$res = $this->db->get('tire_rating_category');
		$tire_rating_category = $res->result_array();
		$this->sci->assign('tire_rating_category' , $tire_rating_category);
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
