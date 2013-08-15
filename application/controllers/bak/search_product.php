<?php

class Search_product extends MY_Controller {

	var $mod_title = '';


	function __construct() {
		parent::__construct();
		$this->sci->set_room('main');
		$this->_init();
		$this->session->validate_member(FALSE);
	}

	function do_search( ) {
		$page = $this->input->post('page');
		$searchkey = $this->input->post('searchkey');
		//$searchby = $this->input->post('searchby');
		//$pagelimit = $this->input->post('pagelimit');
		//$orderby = $this->input->post('orderby');
		//$offset = $this->input->post('offset');
		//$ascdesc = $this->input->post('ascdesc');
		$searchkey = safe_base64_encode($searchkey);
		if( !$searchkey ) { $searchkey = ''; }
		redirect($this->mod_url."index/0/$searchkey");
	}

	function index($offset=0, $encodedkey='') {
		//if($this->branch_id != 1) { show_404();  }

		$pagelimit = 16;

		$this->search_in = array('p_name' );

		$this->db->start_cache();
		$this->db->join('brand' , 'brand.br_id = product.br_id' , 'left');
		$this->db->join('product_category pc' , 'pc.pc_id = product.pc_id' , 'left');
		$this->db->join('product_type pt' , 'pt.pt_id = product.pt_id' , 'left');
		$this->db->where('br_status' , 'Active');
		$this->db->where('br_start_promo <=' , date('Y-m-d H:i:s') );
		$this->db->where('br_end_promo >' , date('Y-m-d H:i:s') );
		$this->db->where('p_status' , 'Active');
		$this->db->order_by('p_name' , 'ASC');
		if($encodedkey != ''){
			$searchkey = safe_base64_decode($encodedkey);
			foreach($this->search_in as $k=>$tmp) {
				$this->db->or_like($tmp, $searchkey);
			}
			$this->sci->assign('searchkey' , $searchkey);
		}
		$this->db->stop_cache();


		$total = $this->db->count_all_results('product');
		$this->load->library('pagination');
		$config['base_url'] = $this->mod_url."index/";
		$config['suffix'] = "/$encodedkey" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get('product');
		$this->db->flush_cache();
		$maindata = $res->result_array();

		foreach($maindata as $k=>$tmp) {
			//get product quantity
			$this->db->where('p_id' , $tmp['p_id']);
			$this->db->where('pq_status' , 'Active');
			$res = $this->db->get('product_quantity');
			$product_quantity = $res->result_array();
			//print_r($product_quantity);
			$maindata[$k]['quantity'] = $product_quantity;
		}

		$this->sci->assign('maindata' , $maindata);
		$this->sci->assign('paging', $this->pagination->create_links() );

		//assign breadcrumb
		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>";
		$breadcrumb[] = "Search";
		$this->sci->assign('breadcrumb' , $breadcrumb);

		$this->sci->da('index.htm');
	}


}
