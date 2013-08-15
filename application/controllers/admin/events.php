<?php
class events extends MY_Controller {

	var $mod_title = 'Events';

	var $table_name = 'events';
	var $id_field = 'e_id';
	var $statue_field = 'e_status';
	var $entry_field = 'e_entry';
	var $stamp_field = 'e_stamp';
	var $deletion_field = 'e_deletion';
	var $order_field = 'e_entry';
	var $order_dir = 'ASC';
	var $label_field = 'e_name';

	var $author_field = 'e_author';
	var $editor_field = 'e_editor';

	var $search_in = array('e_name');

	var $template_add = 'edit.htm';
	var $template_edit = 'edit.htm';


	function __construct() {
		parent::__construct();
		$this->sci->init('admin');
		$this->_init();
		$this->session->validate(array('ADMIN'), 'admin');
		$this->sci->assign('use_ajax' , FALSE);

		$this->config->set_item('global_xse_filtering', FALSE);
	}

	

	function iteration_setting($maindata=array()) {
		return $maindata;
	}

	function join_setting() {
	}

	function where_setting() {
		$this->db->where($this->statue_field ,"Active");
	}

	function validation_setting() {
		$this->form_validation->set_rules('e_name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('e_date', 'Date', 'trim');
		$this->form_validation->set_rules('e_end_date', 'End Date', 'trim');
		$this->form_validation->set_rules('e_location', 'Location', 'trim');
		$this->form_validation->set_rules('e_brief', 'Brief', 'trim');
		$this->form_validation->set_rules('e_speakers', 'Speakers', 'trim');
		$this->form_validation->set_rules('e_content', 'Content', 'trim');
	}

	function database_setter() {
		$this->db->set('e_name' , $this->input->post('e_name') );
		$this->db->set('e_date' , $this->input->post('e_date') );
		$this->db->set('e_end_date' , $this->input->post('e_end_date') );
		$this->db->set('e_location' , $this->input->post('e_location') );
		$this->db->set('e_brief' , $this->input->post('e_brief') );
		$this->db->set('e_speakers' , $this->input->post('e_speakers') );
		$this->db->set('e_content' , $this->input->post('e_content') );
		if($_FILES['e_thumbnail']['name'] != '' ) {
			$filename = $this->_upload_image('e_thumbnail');
			$this->db->set('e_thumbnail' , $filename);
		}
	}


	function pre_add_edit() {
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
