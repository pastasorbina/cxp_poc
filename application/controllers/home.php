<?php

class Home extends MY_Controller {

	var $mod_title = 'Home';

	function __construct() {
		parent::__construct();
		$this->sci->set_room('main');
		$this->_init();

		$this->load->model('mod_content');

		$this->session->validate_member(FALSE);

		$this->sci->assign('curr_page' , 'home');
	}

	function index(){
		$this->db->join('banner_category bnc' , 'bnc.bnc_id = banner.bnc_id' , 'left');
		$this->db->where('bnc_code' , 'main');
		$this->db->where('bn_status' , 'Active');
		$this->db->order_by('bn_order' , 'ASC');
		$res = $this->db->get('banner');
		$mainbanner = $res->result_array();
		$this->sci->assign('mainbanner' , $mainbanner);


		$this->db->join('content_label cl' , 'cl.cl_id = content.cl_id' , 'left');
		$this->db->where('c_status' , 'Active');
		$this->db->where('cl_code' , 'news');
		$this->db->limit(5,0);
		$res = $this->db->get('content');
		$news = $res->result_array();
		$this->sci->assign('news' , $news);

		$this->db->where('e_status' , 'Active');
		$this->db->order_by('e_date' , 'DESC');
		$this->db->limit(5,0);
		$res = $this->db->get('events');
		$events = $res->result_array();
		$this->sci->assign('events' , $events);

		$this->db->join('content_label cl' , 'cl.cl_id = content.cl_id' , 'left');
		$this->db->where('c_status' , 'Active');
		$this->db->where('cl_code' , 'blog');
		$this->db->limit(5,0);
		$res = $this->db->get('content');
		$blog = $res->result_array();
		$this->sci->assign('blog' , $blog);

		$this->db->join('startup_category sc' , 'sc.sc_id = startup.sc_id' , 'left');
		$this->db->where('s_status' , 'Active');
		$this->db->limit(5,0);
		$res = $this->db->get('startup');
		$startup = $res->result_array();
		$this->sci->assign('startup' , $startup);


		$this->sci->da('home/index.htm', TRUE);
	}


	function landing() {

		redirect(site_url());

		//get banners
		$this->db->join('banner_category bnc' , 'bnc.bnc_id = banner.bnc_id' , 'left');
		$this->db->where('bnc_code' , 'main');
		$this->db->where('bn_status' , 'Active');
		$this->db->order_by('bn_order' , 'ASC');
		$res = $this->db->get('banner');
		$mainbanner = $res->result_array();
		$this->sci->assign('mainbanner' , $mainbanner);
		//
		//$featured_news = $this->mod_content->get_content_list_by_label('news',3);
		//$this->sci->assign('featured_news' , $featured_news);
		//$featured_tips = $this->mod_content->get_content_list_by_label('tips',3, TRUE);
		//$this->sci->assign('featured_tips' , $featured_tips);
		//$featured_fdr_news = $this->mod_content->get_content_list_by_label('fdr_news',3, TRUE);
		//$this->sci->assign('featured_fdr_news' , $featured_fdr_news);
		//
		//
		//$events = $this->mod_content->get_content_list_by_label('events',3);
		//$this->sci->assign('events' , $events);
		//
		//$club_events = $this->get_club_articles('Event', 0,  5);
		//$this->sci->assign('club_events' , $club_events);
		//


		$this->sci->da('index.htm');
	}


}
