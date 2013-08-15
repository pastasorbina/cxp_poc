<?php
class Download extends MY_Controller {

	var $mod_title = 'Download';

	var $table_name = 'download';
	var $id_field = 'dl_id';
	var $status_field = 'dl_status';
	var $entry_field = 'dl_entry';
	var $stamp_field = 'dl_stamp';
	var $deletion_field = 'dl_deletion';
	var $order_field = 'dl_entry';
	var $order_dir = 'DESC';
	var $label_field = 'dl_name';

	var $author_field = 'dl_author';
	var $editor_field = 'dl_editor';

	var $search_in = array('dl_name');

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
		$this->db->join('download_type dt' , 'dt.dt_id = download.dt_id' , 'left');
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active");
	}

	function validation_setting() {
		$this->form_validation->set_rules('dl_name', 'Name', 'trim|required|xss_clean');
		//$this->form_validation->set_rules('dl_type', 'Type', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('dl_date', 'Type', 'trim|xss_clean'); 
		$this->form_validation->set_rules('dt_id', 'Type', 'trim|xss_clean'); 
	}
	

	function database_setter() { 
		$this->db->set('dl_name' ,  $this->input->post('dl_name') ); 
		$this->db->set('dt_id' ,  $this->input->post('dt_id') );
		if($this->input->post('dl_date') == '') {
			$this->db->set('dl_date' , date('Y-m-d H:i:s') );
		} else {
			$this->db->set('dl_date' ,  $this->input->post('dl_date') ); 
		}
		
		if($_FILES['dl_image']['name'] != '' ) {
			$filename = $this->_upload_image('dl_image');
			$this->db->set('dl_image' , $filename);
		}
	}


	function pre_add_edit() {
		$this->db->where('dt_status' , 'Active');
		$res = $this->db->get('download_type');
		$download_type = $res->result_array();
		$this->sci->assign('download_type' , $download_type);
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
