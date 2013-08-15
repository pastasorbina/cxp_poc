<?php
class Tire_size_rim extends MY_Controller {

	var $mod_title = 'Tire Size Rim';

	var $table_name = 'tire_size_rim';
	var $id_field = 'tsr_id';
	var $status_field = 'tsr_status';
	var $entry_field = 'tsr_entry';
	var $stamp_field = 'tsr_stamp';
	var $deletion_field = 'tsr_deletion';
	var $order_field = 'tr_name';
	var $order_dir = 'ASC';
	var $label_field = 'tsr_name';

	var $author_field = 'tsr_author';
	var $editor_field = 'tsr_editor';

	var $search_in = array('tsr_name');

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
		$this->db->join('tire_size ts' , 'ts.ts_id = tire_size_rim.ts_id' , 'left');
		$this->db->join('tire_rim tr' , 'tr.tr_id = tire_size_rim.tr_id' , 'left');
		//$this->db->join('tire_size_category tsc' , 'tsc.tsc_id = tire_size_rim.tsc_id' , 'left');
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active");

	}

	function validation_setting() {
		$this->form_validation->set_rules('ts_id', 'Size', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('tr_id', 'Rim', 'trim|required|xss_clean');  
	}

	function database_setter() { 
		$this->db->set('ts_id' , $this->input->post('ts_id') ); 
		$this->db->set('tr_id' , $this->input->post('tr_id') );
		//$this->db->set('tsc_id' , $this->input->post('tsc_id') );
	}
	
	function get_rim_option() {
		$this->db->where('tr_status' , 'Active');
		$this->db->order_by('tr_name' , 'asc');
		$res = $this->db->get('tire_rim');
		$tire_rims = $res->result_array();
		$this->sci->assign('tire_rims' , $tire_rims);
		
		$this->sci->d('option/rim.htm');
	}
	
	function get_size_option() {
		$this->db->where('ts_status' , 'Active');
		$this->db->order_by('ts_name' , 'asc');
		$res = $this->db->get('tire_size');
		$tire_sizes = $res->result_array();
		$this->sci->assign('tire_sizes' , $tire_sizes);
		
		$this->sci->d('option/size.htm');
	}
	
	function _check_duplicate($ts_id, $tr_id){
		$this->db->where('ts_id' , $ts_id);
		$this->db->where('tr_id' , $tr_id);
		$res = $this->db->get('tire_size_rim');
		if($res->row_array()) {
			return FALSE;
		} else {
			return TRUE;
		}
	}


	function pre_add_edit() {
		//$this->db->where('tsc_status' , 'Active');
		//$res = $this->db->get('tire_size_category');
		//$category = $res->result_array();
		//$this->sci->assign('category' , $category);
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}
 
	function add() {
		$this->sci->assign('ajax_action' , 'add');
		$this->pre_add_edit();
		$this->pre_add();

		$this->load->library('form_validation');
		$this->validation_setting('add');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da($this->template_add);
		}elseif($this->_check_duplicate($this->input->post('ts_id'), $this->input->post('tr_id')) == FALSE){
			$this->sci->assign('error_string' , 'Duplicate Item !');
			$this->sci->da($this->template_add);
		}else{
			$this->database_setter('add');
			$this->db->set($this->entry_field , 'NOW()', FALSE );
			$ok = $this->db->insert($this->table_name);
			$insert_id = $this->db->insert_id();
			$this->post_add($insert_id);
			$this->post_add_edit($insert_id);
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url.'add');
			} else {
				$this->session->set_confirm(1);
				redirect($this->session->get_bread('list') );
			}
		}
	}
 
	function edit( $id=0 ) {
		$this->sci->assign('ajax_action' , 'edit');
		$this->pre_add_edit();
		$this->join_setting();
		$this->db->where($this->id_field , $id);
		$res = $this->db->get($this->table_name);
		$data = $res->row_array();
		$this->sci->assign('data' , $data);
		$this->pre_edit($id);

		$this->load->library('form_validation');
		$this->validation_setting('edit');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da($this->template_edit);
		}elseif($this->_check_duplicate($this->input->post('ts_id'), $this->input->post('tr_id')) == FALSE){
			$this->sci->assign('error_string' , 'Duplicate Item !');
			$this->sci->da($this->template_edit);
		}else{
			$this->database_setter('edit');
			$this->db->where($this->id_field , $id);
			$ok = $this->db->update($this->table_name);
			$this->post_edit($id);
			$this->post_add_edit($id);
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."edit/$id");
			} else {
				$this->session->set_confirm(1);
				redirect($this->session->get_bread('list') );
			}
		}
	}
	
	function add_rim() {
		$this->load->library('form_validation'); 
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('add_rim.htm');
		}else{ 
		}
	}
	
	function ajax_submit_add_rim() {
		$action = $this->input->post('action');
		$tr_name = $this->input->post('tr_name');
		$tr_name = trim($tr_name);
		$tr_order = $this->input->post('tr_order');  
		$this->load->library('form_validation'); 
		$this->form_validation->set_rules('tr_name', 'Rim', 'trim|required');   
		$this->form_validation->set_rules('tr_order', 'Order', 'trim');   
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{
			$this->db->where('tr_name' , $tr_name);
			$res = $this->db->get('tire_rim');
			if($res->row_array()) {
				$data['status'] = 'error';
				$data['msg'] = 'duplicate name'; 
			} else {
				$this->db->set('tr_name' , $tr_name);
				$this->db->set('tr_order' , $tr_order);
				$this->db->set('tr_entry' , date('Y-m-d H:i:s') );
				$this->db->insert('tire_rim');
				$data['status'] = 'ok';
				$data['msg'] = 'ok'; 
			} 
		} 
		print json_encode($data);
	}
	
	function add_size() {
		$this->load->library('form_validation'); 
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('add_size.htm');
		}else{ 
		}
	}
	
	function ajax_submit_add_size() {
		$action = $this->input->post('action');
		$ts_name = $this->input->post('ts_name');
		$ts_name = trim($ts_name);
		$ts_order = $this->input->post('ts_order');  
		$this->load->library('form_validation'); 
		$this->form_validation->set_rules('ts_name', 'Size', 'trim|required');   
		$this->form_validation->set_rules('ts_order', 'Order', 'tsize');   
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{
			$this->db->where('ts_name' , $ts_name);
			$res = $this->db->get('tire_size');
			if($res->row_array()) {
				$data['status'] = 'error';
				$data['msg'] = 'duplicate name'; 
			} else {
				$this->db->set('ts_name' , $ts_name);
				$this->db->set('ts_order' , $ts_order);
				$this->db->set('ts_entry' , date('Y-m-d H:i:s') );
				$this->db->insert('tire_size');
				$data['status'] = 'ok';
				$data['msg'] = 'ok';
			}
		} 
		print json_encode($data);
	}


}
