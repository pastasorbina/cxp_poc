<?php

class Racing extends MY_Controller {

	var $mod_title = 'Racing';

	function __construct() {
		parent::__construct();
		$this->sci->init('front');
		$this->_init();
		$this->load->helper('captcha');
		
		
	}
	
	function index() {
		$this->view_race();
	}
	
	function _sidebar() {
		$this->db->where('r_status' , 'Active');
		$this->db->order_by('r_order' , 'asc');
		$res = $this->db->get('racing');
		$all_racing = $res->result_array();
		$this->sci->assign('all_racing' , $all_racing);
	}
	
	function view_race($r_id=0) {
		
		$this->_sidebar();
		
		//get all race
		$this->db->where('r_status' , 'Active');
		$res = $this->db->get('racing');
		$racing = $res->result_array();
		$this->sci->assign('racing' , $racing);
		
		if($r_id == 0) {
			$this->db->where('r_status' , 'Active');
			$this->db->order_by('r_order' , 'DESC');
			$res = $this->db->get('racing');
			$racing = $res->row_array();
			$r_id = $racing['r_id'];
		}
		
		$this->sci->assign('r_id' , $r_id);
		$this->db->where('r_id' , $r_id);
		$res = $this->db->get('racing');
		$this_racing = $res->row_array();
		$this->sci->assign('this_racing' , $this_racing);
		
		//get region
		$this->db->where('r_id' , $r_id);
		$this->db->where('rr_status' , 'Active');
		$this->db->order_by('rr_name' , 'ASC');
		$res = $this->db->get('racing_region');
		$regions = $res->result_array();
		$this->sci->assign('regions' , $regions); 
		
		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>";
		$breadcrumb[] = 'Racing';
		$this->sci->assign('breadcrumb' , $breadcrumb);
		
		$this->sci->da('view_race.htm');
	}
	
	
	function view_region( $rr_id=0) {
		
		$this->_sidebar();
		
		$this->sci->assign('rr_id' , $rr_id);
		$this->db->where('rr_id' , $rr_id);
		$res = $this->db->get('racing_region');
		$this_region = $res->row_array();
		$this->sci->assign('this_region' , $this_region);
		
		//get racing
		$this->db->where('r_id' , $this_region['r_id']);
		$res = $this->db->get('racing');
		$racing = $res->row_array();
		$this->sci->assign('racing' , $racing);
		
		//get all region
		$this->db->where('r_id' , $racing['r_id']);
		$this->db->where('rr_status' , 'Active');
		$this->db->order_by('rr_name' , 'asc');
		$res = $this->db->get('racing_region');
		$regions = $res->result_array();
		$this->sci->assign('regions' , $regions);
		
		//get series
		$this->db->where('rr_id' , $rr_id);
		$this->db->where('rs_status' , 'Active');
		$this->db->order_by('rs_start_date' , 'DESC');
		$res = $this->db->get('racing_series');
		$series = $res->result_array();
		$this->sci->assign('series' , $series);
		
		 
		
		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>";
		$breadcrumb[] = "<a href='".site_url()."racing/view_race/".$racing['r_id']."' >".$racing['r_name']."</a>";
		$breadcrumb[] = $this_region['rr_name'];
		$this->sci->assign('breadcrumb' , $breadcrumb);
		
		$this->sci->da('view_region.htm');
	}
	
	function view_series( $rs_id=0) {
		
		$this->_sidebar();
		
		$this->sci->assign('rs_id' , $rs_id);
		$this->db->where('rs_id' , $rs_id);
		$res = $this->db->get('racing_series');
		$this_series = $res->row_array();
		$this->sci->assign('this_series' , $this_series);
		
		//get region
		$this->db->where('rr_id' , $this_series['rr_id']);
		$res = $this->db->get('racing_region');
		$region = $res->row_array();
		$this->sci->assign('region' , $region);
		//get racing
		$this->db->where('r_id' , $region['r_id']);
		$res = $this->db->get('racing');
		$racing = $res->row_array();
		$this->sci->assign('racing' , $racing);
		
		//get all series
		$this->db->where('rr_id' , $region['rr_id']);
		$this->db->where('rs_status' , 'Active');
		$this->db->order_by('rs_start_date' , 'DESC');
		$res = $this->db->get('racing_series');
		$series = $res->result_array();
		$this->sci->assign('series' , $series);
		
		//get racer
		$this->db->where('rs_id' , $rs_id);
		$this->db->where('rc_status' , 'Active');
		$this->db->order_by('rc_score' , 'DESC');
		$res = $this->db->get('racing_racer');
		$racers = $res->result_array();
		$this->sci->assign('racers' , $racers);
		
		 
		
		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>";
		$breadcrumb[] = "<a href='".site_url()."racing/view_race/".$racing['r_id']."' >".$racing['r_name']."</a>";
		$breadcrumb[] = "<a href='".site_url()."racing/view_region/".$region['rr_id']."' >".$region['rr_name']."</a>";
		$breadcrumb[] = $this_series['rs_name'];
		$this->sci->assign('breadcrumb' , $breadcrumb);
		
		$this->sci->da('view_series.htm');
	}
 
}

?>
