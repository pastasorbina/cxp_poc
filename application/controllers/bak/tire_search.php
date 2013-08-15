<?php

class Tire_search extends MY_Controller {

	var $mod_title = 'Tire_search';

	function __construct() {
		parent::__construct();
		$this->sci->init('front');
		$this->_init();
		$this->load->helper('captcha');
	}

	function index() {
		$this->sci->da('index.htm');
	}
	
	function load_searchbox() {
		
		$this->sci->assign('searchby' , $this->session->userdata('searchby') );
		$this->sci->assign('tr_id' , $this->session->userdata('tr_id') );
		$this->sci->assign('ts_id' , $this->session->userdata('ts_id') );
		$this->sci->assign('mb_id' , $this->session->userdata('mb_id') );
		$this->sci->assign('mt_id' , $this->session->userdata('mt_id') );
		$this->sci->assign('mm_id' , $this->session->userdata('mm_id') );
		
		$this->db->where('tr_status' , 'Active');
		$res = $this->db->get('tire_rim');
		$tire_rim = $res->result_array();
		$this->sci->assign('tire_rim' , $tire_rim);
		
		$this->db->where('ts_status' , 'Active');
		$res = $this->db->get('tire_size');
		$tire_size = $res->result_array();
		$this->sci->assign('tire_size' , $tire_size);
		
		$this->db->where('mb_status' , 'Active');
		$res = $this->db->get('motor_brand');
		$motor_brand = $res->result_array();
		$this->sci->assign('motor_brand' , $motor_brand);
		
		
		
		$this->db->where('mm_status' , 'Active');
		$res = $this->db->get('motor_model');
		$motor_model = $res->result_array();
		$this->sci->assign('motor_model' , $motor_model);
		
		$this->sci->d('searchbox/index.htm');
	}
	
	function get_list_rim() {
		$this->db->where('tr_status' , 'Active');
		$res = $this->db->get('tire_rim');
		$tire_rim = $res->result_array();
		$this->sci->assign('tire_rim' , $tire_rim);
		$this->sci->d('searchbox/select_rim.htm');
	}
	
	function do_search() {
		//print_r($_POST);
		$searchby = $this->input->post('searchby');
		$tr_id = $this->input->post('tr_id');
		$ts_id = $this->input->post('ts_id');
		$mt_id = $this->input->post('mt_id');
		$mb_id = $this->input->post('mb_id');
		$mm_id = $this->input->post('mm_id');
		$tsc_id = $this->input->post('tsc_id');
		$tsr_id = $this->input->post('tsr_id');
		if($searchby == 'motor') { $tr_id=0; $ts_id=0; } else {
			$mt_id = 0;
			$mb_id = 0;
			$mm_id = 0;
			$tsc_id = 0;
			$tsr_id = 0;
		}
		redirect(site_url()."tire_search/result/$searchby/$tr_id/$ts_id/$mb_id/$mm_id/$tsc_id/$tsr_id");
	}
	
	function result($searchby='motor', $tr_id=0, $ts_id=0, $mb_id=0, $mm_id=0, $tsc_id=0, $tsr_id=0) {
		$this->sci->assign('searchby' , $searchby);
		
		$this->sci->assign('tr_id' , $tr_id);
		$this->sci->assign('ts_id' , $ts_id);
		$this->sci->assign('mb_id' , $mb_id);
		//$this->sci->assign('mt_id' , $mt_id);
		$this->sci->assign('mm_id' , $mm_id);
		$this->sci->assign('tsc_id' , $tsc_id);
		$this->sci->assign('tsr_id' , $tsr_id);
		
		$this->session->set_userdata('searchby' , $searchby);
		$this->session->set_userdata('tr_id' , $tr_id);
		$this->session->set_userdata('ts_id' , $ts_id);
		$this->session->set_userdata('mb_id' , $mb_id);
		//$this->session->set_userdata('mt_id' , $mt_id);
		$this->session->set_userdata('mm_id' , $mm_id);
		
		$this->db->where('tr_id' , $tr_id);
		$res = $this->db->get('tire_rim');
		$tire_rim = $res->row_array();
		$this->sci->assign('tire_rim' , $tire_rim);
		
		$this->db->where('ts_id' , $ts_id);
		$res = $this->db->get('tire_size');
		$tire_size = $res->row_array();
		$this->sci->assign('tire_size' , $tire_size);
		
		$this->db->where('mb_id' , $mb_id);
		$res = $this->db->get('motor_brand');
		$motor_brand = $res->row_array();
		$this->sci->assign('motor_brand' , $motor_brand);
		 
		
		$this->db->where('mm_id' , $mm_id);
		$res = $this->db->get('motor_model');
		$motor_model = $res->row_array();
		$this->sci->assign('motor_model' , $motor_model);
		
		$this->db->where('tsr_id' , $tsr_id);
		$this->db->join('tire_size ts' , 'ts.ts_id = tire_size_rim.ts_id' , 'left');
		$this->db->join('tire_rim tr' , 'tr.tr_id = tire_size_rim.tr_id' , 'left');
		$res = $this->db->get('tire_size_rim');
		$tire_size_rim = $res->row_array();
		$this->sci->assign('tire_size_rim' , $tire_size_rim);
		
		$this->db->where('tsc_id' , $tsc_id);
		$res = $this->db->get('tire_size_category');
		$tire_size_category = $res->row_array();
		$this->sci->assign('tire_size_category' , $tire_size_category);
		
		if($searchby == 'motor') {
			$this->db->start_cache();
			
			$this->db->join('tire_detail_size tds' , 'tds.tds_id = tire_detail_motor.tds_id' , 'left');
			$this->db->join('tire_size_rim tsr' , 'tsr.tsr_id = tds.tsr_id' , 'left');
			$this->db->join('tire_size_category tsc' , 'tsc.tsc_id = tire_detail_motor.tsc_id' , 'left');
			$this->db->join('tire_size ts' , 'ts.ts_id = tsr.ts_id' , 'left');
			$this->db->join('tire_rim tr' , 'tr.tr_id = tsr.tr_id' , 'left');
			$this->db->join('motor_model mm' , 'mm.mm_id = tire_detail_motor.mm_id' , 'left');
			 
			$this->db->where('tdm_status' , 'Active');
			$this->db->where('tds_status' , 'Active');
			$this->db->where('tsr_status' , 'Active');
			$this->db->where('tsc_status' , 'Active');
			$this->db->join('tire t' , 't.t_id = tds.t_id' , 'left');
			$this->db->group_by('t.t_id');
			$this->db->where('t_status' , 'Active');
			$this->db->order_by('t_order' , 'asc');
			$this->db->from('tire_detail_motor');
			
			if($mb_id != 0) {
				$this->db->where('mm.mb_id' , $mb_id);
			} 
			if($mm_id != 0) {
				$this->db->where('mm.mm_id' , $mm_id);
			}
			
			if($tsc_id != 0) {
				$this->db->where('tire_detail_motor.tsc_id' , $tsc_id);
			}
			if($tsr_id != 0) {
				$this->db->where('tsr.tsr_id' , $tsr_id);
			}
			
			$this->db->stop_cache();
			
			$res = $this->db->get();
			$this->db->flush_cache();
			$tires = $res->result_array();	
			
		} elseif($searchby == 'size') {
			$this->db->start_cache();
			$this->db->join('tire t' , 't.t_id = tire_detail_size.t_id' , 'left');
			$this->db->join('tire_size_rim tsr' , 'tsr.tsr_id = tire_detail_size.tsr_id' , 'left');
			$this->db->join('tire_size_category tsc' , 'tsc.tsc_id = tsr.tsc_id' , 'left');
			$this->db->join('tire_size ts' , 'ts.ts_id = tsr.ts_id' , 'left');
			$this->db->join('tire_rim tr' , 'tr.tr_id = tsr.tr_id' , 'left');
			 
			
			$this->db->group_by('t.t_id');
			$this->db->where('t_status' , 'Active');
			$this->db->order_by('t_order' , 'asc');
			$this->db->from('tire_detail_size');
			
			if($tr_id != 0) {
				$this->db->where('tsr.tr_id' , $tr_id);
			}
			if($ts_id != 0) {
				$this->db->where('tsr.ts_id' , $ts_id);
			}
			
			$this->db->stop_cache();
			
			$res = $this->db->get();
			$this->db->flush_cache();
			$tires = $res->result_array();	
		}
		
		//get tire tags
		foreach($tires as $k=>$tmp) {
			$this->db->join('tire_tag' , 'tire_tag.tt_id = tire_detail_tag.tt_id' , 'left');
			$this->db->where('tire_tag.tt_status' , 'Active');
			$this->db->where('t_id' , $tmp['t_id']);
			$res = $this->db->get('tire_detail_tag');
			$tire_tag = $res->result_array();
			$tires[$k]['tags'] = $tire_tag;
		}
		
		$this->sci->assign('tires' , $tires);
		
		//get find tire disclaimer
		$this->db->where('c_code' , 'find-tire-disclaimer');
		$this->db->where('c_status' , 'Active');
		$res = $this->db->get('content');
		$findtire_disclaimer = $res->row_array();
		$this->sci->assign('findtire_disclaimer' , $findtire_disclaimer);
		
		$this->sci->da('result.htm');
	}
	
	function get_motor_type_option() {
		$this->db->where('mt_status' , 'Active');
		$this->db->order_by('mt_name' , 'asc');
		$res = $this->db->get('motor_type');
		$motor_type = $res->result_array();
		$this->sci->assign('motor_type' , $motor_type);
		$this->sci->d('searchbox/option_motor_type.htm');
	}
	
	function get_motor_model_option($mb_id = 0) {
		if($mb_id !=0) {
			$this->db->where('mb_id' , $mb_id);
		}
		$this->db->order_by('mm_name' , 'asc');
		$this->db->where('mm_status' , 'Active');
		$res = $this->db->get('motor_model');
		$motor_model = $res->result_array();
		$this->sci->assign('motor_model' , $motor_model);
		$this->sci->d('searchbox/option_motor_model.htm');
	}
	
	function get_motor_size_category($mm_id = 0) {
		
		if($mm_id !=0) {
			$this->db->where('mm_id' , $mm_id);
			$this->db->where('mm_id' , $mm_id); 
			$this->db->join('tire_size_category tsc' , 'tsc.tsc_id = tire_detail_motor.tsc_id' , 'left'); 
			$this->db->where('tdm_status' , 'Active'); 
			$this->db->where('tsc_status' , 'Active');
			$this->db->order_by('tsc.tsc_order' , 'asc');
			$this->db->group_by('tsc.tsc_id');
			$res = $this->db->get('tire_detail_motor');
			$motor_tire_category = $res->result_array();	
		} else {
			$this->db->where('tsc_status' , 'Active');
			$this->db->order_by('tsc_order' , 'asc');
			$res = $this->db->get('tire_size_category');
			$motor_tire_category = $res->result_array();	
		}
		 
		$this->sci->assign('motor_tire_category' , $motor_tire_category);
		$this->sci->d('searchbox/option_motor_size_category.htm');
	}
	
	function get_motor_size_option($mm_id = 0, $tsc_id=0) {
		if($mm_id !=0 && $tsc_id !=0) {
			$this->db->where('mm_id' , $mm_id);
			$this->db->where('tire_detail_motor.tsc_id' , $tsc_id);
			$this->db->join('tire_detail_size tds' , 'tds.tds_id = tire_detail_motor.tds_id' , 'left');
			$this->db->join('tire_size_rim tsr' , 'tsr.tsr_id = tds.tsr_id' , 'left'); 
			$this->db->join('tire_size ts' , 'ts.ts_id = tsr.ts_id' , 'left');
			$this->db->join('tire_rim tr' , 'tr.tr_id = tsr.tr_id' , 'left');  
			$this->db->where('tsr_status' , 'Active');
			$this->db->group_by('tsr.tsr_id');
			$res = $this->db->get('tire_detail_motor');
			$motor_tire_size = $res->result_array(); 
			//print_r($motor_tire_size);
		} elseif($mm_id == 0 && $tsc_id !=0) {
			$this->db->where('tsc_id' , $tsc_id);
			$this->db->join('tire_size ts' , 'ts.ts_id = tire_size_rim.ts_id' , 'left');
			$this->db->join('tire_rim tr' , 'tr.tr_id = tire_size_rim.tr_id' , 'left');
			$res = $this->db->get('tire_size_rim');
			$motor_tire_size = $res->result_array(); 
		} else {
			$motor_tire_size = array();
		}
		$this->sci->assign('motor_tire_size' , $motor_tire_size);
		//print $mm_id; print $tsc_id;
		$this->sci->d('searchbox/option_motor_size.htm');
	}
	
	function get_motor_type_option2() {
		$this->db->where('mt_status' , 'Active');
		$res = $this->db->get('motor_type');
		$motor_type = $res->result_array(); 
		print json_encode($motor_type);
	}
	
	
	function get_tire_rim() {
		$this->db->where('tr_status' , 'Active');
		$res = $this->db->get('tire_rim');
		$tire_rim = $res->result_array();
		$this->sci->assign('tire_rim' , $tire_rim);
		$this->sci->d('searchbox/option_tire_rim.htm');
	}
	
	function get_tire_size($tr_id=0) {
		if($tr_id !=0) { 
			$this->db->where('ts.ts_status' , 'Active');
			$this->db->where('tire_size_rim.tsr_status' , 'Active');
			$this->db->where('tire_size_rim.tr_id' , $tr_id);
			$this->db->group_by('tire_size_rim.ts_id');
			$this->db->order_by('ts_name' , 'ASC');
			$this->db->join('tire_size ts' , 'ts.ts_id = tire_size_rim.ts_id' , 'left');
			$res = $this->db->get('tire_size_rim');
			$tire_size = $res->result_array();
			$this->sci->assign('tire_size' , $tire_size);  
		} 
		$this->sci->d('searchbox/option_tire_size.htm');
	}
	
}

?>
