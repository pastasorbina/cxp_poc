<?php
class Racing_motogp extends MY_Controller {

	var $mod_title = 'Moto GP';

	var $table_name = 'racing_motogp';
	var $id_field = 'gp_id';
	var $status_field = 'gp_status';
	var $entry_field = 'gp_entry';
	var $stamp_field = 'gp_stamp';
	var $deletion_field = 'gp_deletion';
	var $order_field = 'gp_score';
	var $order_dir = 'DESC';
	var $label_field = 'gp_name';

	var $author_field = 'gp_author';
	var $editor_field = 'gp_editor';

	var $search_in = array('gp_name');

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
		$this->form_validation->set_rules('gp_name', 'Name', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('gp_team', 'Team', 'trim|xss_clean'); 
		$this->form_validation->set_rules('gp_score', 'Score', 'trim|xss_clean'); 
	}

	function database_setter() { 
		$this->db->set('gp_name' , $this->input->post('gp_name') ) ;
		$this->db->set('gp_team' , $this->input->post('gp_team') ) ;
		$this->db->set('gp_score' , $this->input->post('gp_score') ) ;
	}


	function pre_add_edit() { 
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
