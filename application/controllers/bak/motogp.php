<?php

class Motogp extends MY_Controller {

	var $mod_title = 'Moto GP';

	function __construct() {
		parent::__construct();
		$this->sci->init('front');
		$this->_init(); 
	}
	
	function index() {
		$this->view();
	}
	
	function view($gp_id=0) {
		
		//get all motogp
		$this->db->where('gp_status' , 'Active');
		$res = $this->db->get('motogp');
		$motogp = $res->result_array();
		$this->sci->assign('motogp' , $motogp);
		
		if($gp_id == 0) {
			$this->db->where('gp_status' , 'Active');
			$this->db->order_by('gp_entry' , 'DESC');
			$res = $this->db->get('motogp');
			$motogp = $res->row_array();
			$gp_id = $motogp['gp_id'];
		}
		
		$this->sci->assign('gp_id' , $gp_id);
		$this->db->where('gp_id' , $gp_id);
		$res = $this->db->get('motogp');
		$this_motogp = $res->row_array();
		$this->sci->assign('this_motogp' , $this_motogp);
		
		//get racers
		$this->db->where('gp_id' , $gp_id);
		$this->db->where('gpc_status' , 'Active');
		$this->db->order_by('gpc_score' , 'DESC');
		$res = $this->db->get('motogp_racer');
		$racers = $res->result_array();
		$this->sci->assign('racers' , $racers);
		
		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>";
		$breadcrumb[] = 'Moto GP';
		$this->sci->assign('breadcrumb' , $breadcrumb);
		
		$this->sci->da('view.htm');
	}

}

?>
