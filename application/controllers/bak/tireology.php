<?php

class Tireology extends MY_Controller {

	var $mod_title = '';

	var $table_name = 'gallery';
	var $id_field = 'g_id';
	var $status_field = 'g_status';
	var $entry_field = 'g_entry';
	var $stamp_field = 'g_stamp';
	var $deletion_field = 'g_deletion';
	var $order_field = 'g_date';

	var $search_in = array('g_title', 'g_desc');

	function __construct() {
		parent::__construct();
		$this->sci->init('front');
		$this->_init();
	}

	function index() {
		$this->view_technology(1);
	}
	
	function technology() { 
		$this->view_technology(1);
	}
	
	function view_technology($id=1) {
		$this->sci->assign('id' , $id);
		$this->db->where('tth_status' , 'Active');
		$res = $this->db->get('tire_tech');
		$tech = $res->result_array();
		$data = array();
		foreach($tech as $k=>$tmp){ $data[$tmp['tth_name']] = $tmp; }
		$this->sci->assign('data' , $data);
		
		switch($id) { 
			case 2 :
				$template = 'inset_tech2.htm';
				break;
			case 3 : 
				$template = 'inset_tech3.htm';
				break;
			default :
				$template = 'inset_tech1.htm';
				break;
		}
		$inset = $this->sci->fetch("tireology/$template");
		$this->sci->assign('inset' , $inset);
		$this->sci->assign('curr_tireology' , 'technology');
		
		//get technology snippet
		$this->db->where('cl_code' , 'tireology');
		$this->db->join('content_label cl' , 'cl.cl_id = content.cl_id' , 'left');
		$this->db->where('c_code' , 'tire-technology');
		$res = $this->db->get('content');
		$content = $res->row_array(); 
		$this->sci->assign('content' , $content);
		
		$this->sci->da('technology.htm');
	}
	
	function detail_technology($tth_id=0) {
		$this->db->where('tth_id' , $tth_id);
		$res = $this->db->get('tire_tech');
		$data = $res->row_array();
		$this->sci->assign('data' , $data);
		$this->sci->d('tech_detail.htm');
	}
	
	
	function get_technology($id=1) { 
		switch($id) {
			case 1 :
				$this->sci->assign('curr_tireology' , 'technology');
				$this->db->where('tth_status' , 'Active');
				$this->db->order_by('tth_order' , 'asc');
				$res = $this->db->get('tire_tech');
				$tire_tech = $res->result_array();
				$this->sci->assign('tire_tech' , $tire_tech);
				$this->sci->d('inset_technology1.htm');
				break;
			case 2 :
				$this->sci->d('inset_technology3.htm');
				break;
			case 3 :
				$this->sci->d('inset_technology3.htm');
				break;
		}
		
	}
	
	function knowledge() { 
		$this->view_knowledge(1);
	}
	
	function view_knowledge($id=1) {
		$this->sci->assign('id' , $id);
		switch($id) {
			
			case 2 :
				$template = 'inset_knowledge2.htm';
				break;
			case 3 : 
				$template = 'inset_knowledge3.htm';
				break;
			default :
				$template = 'inset_knowledge1.htm';
				break;
		}
		$inset = $this->sci->fetch("tireology/$template");
		$this->sci->assign('inset' , $inset);
		$this->sci->assign('curr_tireology' , 'knowledge');
		
		//get knowledge snippet
		$this->db->where('cl_code' , 'tireology');
		$this->db->join('content_label cl' , 'cl.cl_id = content.cl_id' , 'left');
		$this->db->where('c_code' , 'tire-knowledge');
		$res = $this->db->get('content');
		$content = $res->row_array(); 
		$this->sci->assign('content' , $content);
		
		$this->sci->da('knowledge.htm');
	}
	
	function detail_knowledge($id=1) {
		$this->sci->d('knowledge_detail.htm');
	}
	
	function get_knowledge($id=1) {
		switch($id) {
			case 1 : 
				$this->sci->d('inset_knowledge1.htm');
				break;
			case 2 :
				$this->sci->d('inset_knowledge2.htm');
				break;
			case 3 :
				$this->sci->d('inset_knowledge3.htm');
				break;
		}
		
	}
	
	
	function safety() {
		$this->sci->assign('curr_tireology' , 'safety'); 
		$this->db->where('c_code' , 'tire-safety');  
		$res = $this->db->get('content');
		$page = $res->row_array();
		$this->sci->assign('page' , $page); 
		$this->sci->da('page.htm');
	}
	
	function usage() {
		$this->sci->assign('curr_tireology' , 'usage'); 
		$this->db->where('c_code' , 'tire-usage-maintenance');  
		$res = $this->db->get('content');
		$page = $res->row_array();
		$this->sci->assign('page' , $page); 
		$this->sci->da('page.htm');
	}
	
	function sample() {
		$this->sci->da('sample.htm');
	}

 
}
