<?php

class Product extends MY_Controller {

	var $mod_title = 'Form';

	function __construct() {
		parent::__construct();
		$this->sci->init('front');
		$this->_init();
		$this->load->helper('captcha');
		
		
	}

	function index($tc_id=0, $orderby='new', $offset=0) {
		
		$this->db->where('tc_status' , 'Active');
		$this->db->order_by('tc_order' , 'ASC');
		$res = $this->db->get('tire_category');
		$tire_category = $res->result_array();
		$this->sci->assign('tire_category' , $tire_category);
		
		if($tc_id != 0) {
			$this->db->where('tc_id' , $tc_id); 
			$res = $this->db->get('tire_category');
			$this_category = $res->row_array();
			$this->sci->assign('this_category' , $this_category);
		}
		$this->sci->assign('tc_id' , $tc_id);
		$this->sci->assign('orderby' , $orderby);
		$this->sci->assign('offset' , $offset); 
		
		//get data
		$pagelimit = 6;
		
		/*--cache-start--*/
		$this->db->start_cache();
			$this->db->join('tire_category tc' , 'tc.tc_id = tire.tc_id' , 'left');
			if($tc_id != 0) {
				$this->db->where('tire.tc_id' , $tc_id);
			}
			$this->db->where('t_status' , 'Active');
			switch($orderby) {
				case 'new' :
					$this->db->order_by('t_order' , 'DESC'); 
					break;
				default :
					$this->db->order_by('t_name' , 'asc'); 
					break;
			}
			
			$this->db->from('tire'); 
		$this->db->stop_cache();
		/*--cache-stop--*/

		// Pagination
		$total = $this->db->count_all_results('tire');
		$this->load->library('pagination');
		$config['base_url'] = site_url()."product/index/$tc_id/$orderby/";
		$config['suffix'] = "" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 5;
		$this->pagination->initialize($config); 
		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get('tire');
		$this->db->flush_cache();
		$tires = $res->result_array();
		
		foreach($tires as $k=>$tmp) {
			$this->db->join('tire_tag' , 'tire_tag.tt_id = tire_detail_tag.tt_id' , 'left');
			$this->db->where('tire_tag.tt_status' , 'Active');
			$this->db->where('t_id' , $tmp['t_id']);
			$res = $this->db->get('tire_detail_tag');
			$tire_tag = $res->result_array();
			$tires[$k]['tags'] = $tire_tag;
		}
		$this->sci->assign('tires' , $tires);
		$this->sci->assign('paging', $this->pagination->create_links() );
		 
		$this->sci->da('index.htm');
	}
	
	function filter() {
		$orderby = $this->input->post('orderby');
		$page = $this->input->post('page');
		$offset = $this->input->post('offset');
		$tc_id = $this->input->post('tc_id');
		redirect(site_url()."$page/$tc_id/$orderby/$offset/");
	}
	
	function view( $t_id=0, $show='sizerim') {
		$this->sci->assign('show' , $show);
		 
		$this->db->join('tire_category tc' , 'tc.tc_id = tire.tc_id' , 'left');
		$this->db->where('t_id' , $t_id);
		$res = $this->db->get('tire');
		$tire = $res->row_array(); 
		$this->sci->assign('tire' , $tire);
		
		//get tire_detail_tag
		$this->db->join('tire_tag tt' , 'tt.tt_id = tire_detail_tag.tt_id' , 'left');
		$this->db->where('t_id' , $tire['t_id']);
		$res = $this->db->get('tire_detail_tag');
		$tire_tags = $res->result_array();
		$this->sci->assign('tire_tags' , $tire_tags);
		
		//get tire detail rating
		$this->db->start_cache();
		$this->db->join('tire_rating trt' , 'trt.trt_id = tire_detail_rating.trt_id' , 'left');
		$this->db->join('tire_rating_category trtc' , 'trtc.trtc_id = trt.trtc_id' , 'left');
		$this->db->where('t_id' , $tire['t_id']);
		$this->db->where('tdr_status' , 'Active');
		$this->db->from('tire_detail_rating');
		$this->db->stop_cache();
		
		$this->db->where('trtc.trtc_code' , 'performance');
		$res = $this->db->get();
		$performance = $res->result_array();
		$this->sci->assign('performance' , $performance);
		
		$this->db->where('trtc.trtc_code' , 'usage');
		$res = $this->db->get();
		$usage = $res->result_array();
		$this->sci->assign('usage' , $usage);
		
		$this->db->where('trtc.trtc_code' , 'road');
		$res = $this->db->get();
		$road = $res->result_array();
		$this->sci->assign('road' , $road);
		
		$this->db->where('trtc.trtc_code' , 'track');
		$res = $this->db->get();
		$track = $res->result_array();
		$this->sci->assign('track' , $track); 
		
		$this->db->flush_cache();
		 
		
		$this->db->where('t_id' , $tire['t_id']);
		$this->db->join('tire_tech tth' , 'tth.tth_id = tire_detail_tech.tth_id' , 'left');
		$this->db->where('tdth_status' , 'Active');
		$res = $this->db->get('tire_detail_tech');
		$tire_tech = $res->result_array();
		$this->sci->assign('tire_tech' , $tire_tech);
		
		//get tire gallery
		$this->db->where('t_id' , $t_id);
		$this->db->where('tg_status' , 'Active');
		$this->db->order_by('tg_order' , 'asc');
		$res = $this->db->get('tire_gallery');
		$tire_gallery = $res->result_array();
		$this->sci->assign('tire_gallery' , $tire_gallery);
		
		//get tiretag
		$this->db->join('tire_tag' , 'tire_tag.tt_id = tire_detail_tag.tt_id' , 'left');
		$this->db->where('tire_tag.tt_status' , 'Active');
		$this->db->where('t_id' , $tire['t_id']);
		$res = $this->db->get('tire_detail_tag');
		$tire_tag = $res->result_array();
		$this->sci->assign('tire_tag' , $tire_tag);
		
		//get product disclaimer
		$this->db->where('c_code' , 'product-disclaimer');
		$this->db->where('c_status' , 'Active');
		$res = $this->db->get('content');
		$product_disclaimer = $res->row_array();
		$this->sci->assign('product_disclaimer' , $product_disclaimer);
		
		//get recomendded bike
		//$this->db->join('motor_model mm' , 'mm.mm_id = tire_detail_motor.mm_id' , 'left');
		//$this->db->join('motor_brand mb' , 'mb.mb_id = mm.mb_id' , 'left');
		//$this->db->where('tire_detail_motor.t_id' , $t_id);
		//$res = $this->db->get('tire_detail_motor');
		//$recommended = $res->result_array();
		////print_r($recommended);
		//$this->sci->assign('recommended' , $recommended); 
		
		$this->sci->da('view.htm');		
	}
}

?>
