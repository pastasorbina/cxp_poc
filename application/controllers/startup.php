<?php

class Startup extends MY_Controller {

	var $mod_title = '';


	function __construct() {
		parent::__construct();
		$this->sci->init('front');
		$this->_init();
	}

	function index() {
		 $this->view_list();
	}

	function view_list(){
		$this->db->join('startup_category sc' , 'sc.sc_id = startup.sc_id' , 'left');
		$this->db->where('s_status' , 'Active');
		$this->db->limit(20,0);
		$res = $this->db->get('startup');
		$startup = $res->result_array();
		$this->sci->assign('startup' , $startup);

		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>";
		$breadcrumb[] = 'Startups';
		$this->sci->assign('breadcrumb' , $breadcrumb);

		$this->sci->da('index.htm');
	}

	function view($s_id=0){
		$this->db->where('s_id' , $s_id);
		$res = $this->db->get('startup');
		$startup = $res->row_array();
		$this->sci->assign('startup' , $startup);

		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>";
		$breadcrumb[] = "<a href='".site_url()."startup/view_list/' >Startups</a>";
		$breadcrumb[] = $startup['s_name'];
		$this->sci->assign('breadcrumb' , $breadcrumb);

		$this->sci->da('view.htm');
	}






}
