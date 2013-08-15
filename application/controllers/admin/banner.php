<?php
class Banner extends MY_Controller {

	var $mod_title = 'Manage Banners';

	var $table_name = 'banner';
	var $id_field = 'bn_id';
	var $status_field = 'bn_status';
	var $entry_field = 'bn_entry';
	var $stamp_field = 'bn_stamp';
	var $deletion_field = 'bn_deletion';
	var $order_field = 'bn_order';
	var $order_dir = 'ASC';
	var $label_field = 'bn_title';

	var $search_in = array('bn_title', 'bn_desc');

	var $template_add = 'edit.htm';
	var $template_edit = 'edit.htm';


	function __construct() {
		parent::__construct();
		$this->sci->init('admin');
		$this->_init();
		$this->session->validate(array('ADMIN'), 'admin');
		$this->sci->assign('use_ajax' , FALSE);

		$this->image_directory = 'userfiles/upload/';
		$this->thumb_directory = 'userfiles/upload/thumb/';
		$this->thumb_width = 80;
		$this->thumb_height = 80;

		$this->userinfo = $this->session->get_userinfo();
	}

	function enum_setting($maindata=array()) {

		return $maindata;
	}

	function join_setting() {
		$this->db->join('banner_category bnc' , 'bnc.bnc_id = banner.bnc_id' , 'left');
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active");
	}

	function validation_setting() {
		$this->form_validation->set_rules('bn_title', 'Title', 'trim|xss_clean');
		$this->form_validation->set_rules('bn_desc', 'Desc', 'trim');
		$this->form_validation->set_rules('bn_url', 'URL', 'trim');
		$this->form_validation->set_rules('bn_order', 'Order', 'trim|numeric');
		//$this->form_validation->set_rules('bn_caption', 'Caption', 'trim|xss_clean');
		$this->form_validation->set_rules('bn_display_caption', 'Display Caption', 'trim|xss_clean');
		$this->form_validation->set_rules('bn_date', 'Date', 'trim|xss_clean');
		$this->form_validation->set_rules('bn_subtitle', 'Subtitle', 'trim');
	}

	function database_setter($action) {
		if($action == 'add'){
			$this->db->set('bn_author' , $this->userinfo['u_id'] );
		} else {
			$this->db->set('bn_editor' , $this->userinfo['u_id'] );
		}


		$this->db->set('bnc_id' , $this->input->post('bnc_id') );
		$this->db->set('bn_title' , $this->input->post('bn_title') );
		$this->db->set('bn_desc' , $this->input->post('bn_desc'));
		$this->db->set('bn_url' , $this->input->post('bn_url'));
		$this->db->set('bn_order' , $this->input->post('bn_order'));
		$this->db->set('bn_subtitle' , $this->input->post('bn_subtitle'));
		//$this->db->set('bn_caption' , $this->input->post('bn_caption'));
		if($this->input->post('bn_display_caption')) { $dispcaption = "Yes"; } else { $dispcaption = "No"; }
		$this->db->set('bn_display_caption' , $dispcaption);
		if($this->input->post('bn_display_button')) { $dispcaption = "Yes"; } else { $dispcaption = "No"; }
		$this->db->set('bn_display_button' , $dispcaption);

		$bn_date = $this->input->post('bn_date');
		$bn_date = ($bn_date!='') ? $bn_date : date('Y-m-d H:i:s');
		$this->db->set('bn_date' , $bn_date);

		if($_FILES['bn_image']['name'] != '') {
			$this->db->where('bnc_id' , $this->input->post('bnc_id') );
			$res = $this->db->get('banner_category');
			$category = $res->row_array();
			//$this->thumb_width = $category['bnc_width'];
			//$this->thumb_height = $category['bnc_height'];
			$this->db->set('bn_image' , $this->_upload_image('bn_image'));
		}

	}


	function pre_add_edit() {
		$this->config->set_item('global_xss_filtering', FALSE);
		$this->db->where('bnc_status' , "Active");
		$res = $this->db->get('banner_category');
		$categories = $res->result_array();
		$this->sci->assign('categories' , $categories);


	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}


	function index($bnc_id=0, $pagelimit='', $offset=0, $orderby='', $ascdesc='', $encodedkey='') {
		$this->session->set_bread('list');

		$this->db->where('bnc_status' , "Active");
		$res = $this->db->get('banner_category');
		$categories = $res->result_array();
		$this->sci->assign('categories' , $categories);
		if(sizeof($categories) > 0 && $bnc_id == 0) { $bnc_id = $categories[0]['bnc_id']; }
		$this->sci->assign('bnc_id' , $bnc_id);

		$this->pre_index();
		$this->determine_action();
		if ($orderby == '') $orderby = $this->order_field;
		if ($orderby == '') $orderby = $this->id_field;
		if ($ascdesc == '') $ascdesc = $this->order_dir;
		if ($pagelimit == '') $pagelimit = $this->default_pagelimit;
		$this->sci->assign('pagelimit' , $pagelimit);
		$this->sci->assign('offset' , $offset);
		$this->sci->assign('orderby' , $orderby);
		$this->sci->assign('ascdesc' , $ascdesc);
		/*--cache-start--*/
		$this->db->start_cache();
			if($encodedkey != ''){ $this->pre_search($encodedkey); }
			$orderbyconv = preg_replace('/_DOT_/' , '.', $orderby);
			$this->db->order_by($orderbyconv , $ascdesc);
			$this->db->from($this->table_name);
			$this->join_setting();
			$this->where_setting();
			$this->db->where('banner.bnc_id' , $bnc_id);
			$this->select_setting();
		$this->db->stop_cache();
		/*--cache-stop--*/

		// Pagination
		$total = $this->db->count_all_results($this->table_name);
		$this->load->library('pagination');
		$config['base_url'] = $this->mod_url."index/$bnc_id/". $pagelimit ."/";
		$config['suffix'] = "/$orderby/$ascdesc/$encodedkey" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 5;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get($this->table_name);
		$this->db->flush_cache();
		$maindata = $res->result_array();
		$maindata = $this->append_user($maindata);
		$maindata = $this->enum_setting($maindata);
		$maindata = $this->iteration_setting($maindata);
		$this->sci->assign('maindata' , $maindata);
		$this->sci->assign('paging', $this->pagination->create_links() );
		$this->post_index();
		$this->sci->da('index.htm');
	}

	function pre_search($encodedkey) {
		$searchkey = safe_base64_decode($encodedkey);
		if(!empty($this->search_in)) {
			foreach($this->search_in as $k=>$tmp) { $this->db->or_like($tmp, $searchkey); }
		} else {
			$this->search_setting($searchkey);
		}
		$this->sci->assign('searchkey' , $searchkey);
	}

	function add( $bnc_id=0 ) {
		$this->sci->assign('ajax_action' , 'add');
		$this->pre_add_edit();
		$this->pre_add();
		$this->sci->assign('bnc_id' , $bnc_id);

		$this->load->library('form_validation');
		$this->validation_setting('add');
		if($this->form_validation->run() == FALSE ) {
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

}
