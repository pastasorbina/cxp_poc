<?php
class Dealers extends MY_Controller {

	var $mod_title = 'Dealers';

	var $table_name = 'dealers';
	var $id_field = 'dlr_id';
	var $status_field = 'dlr_status';
	var $entry_field = 'dlr_entry';
	var $stamp_field = 'dlr_stamp';
	var $deletion_field = 'dlr_deletion';
	var $order_field = 'dlr_entry';
	var $order_dir = 'DESC';
	var $label_field = 'dlr_name';

	var $author_field = 'dlr_author';
	var $editor_field = 'dlr_editor';

	var $search_in = array('dlr_name');

	var $template_add = 'edit.htm';
	var $template_edit = 'edit.htm';


	function __construct() {
		parent::__construct();
		$this->sci->init('admin');
		$this->_init();
		$this->session->validate(array('ADMIN'), 'admin');
		$this->sci->assign('use_ajax' , FALSE);
		
		$this->config->set_item('global_xss_filtering', FALSE);

	}
	function index(){
		redirect(site_url()."admin/dealers/list_province/");
	}
	
	function fix() {
		$res = $this->db->get('dealers');
		$dealers = $res->result_array();
		foreach($dealers as $k=>$tmp){
			$lat = $tmp['dlr_lat'];
			$long = $tmp['dlr_long'];
			$lat = str_replace(',','.', $lat);
			$long = str_replace(',','.', $long);
			$this->db->where('dlr_id' , $tmp['dlr_id']);
			$this->db->set('dlr_long' , $long);
			$this->db->set('dlr_lat' , $lat);
			$this->db->update('dealers');
			
		}
	}
	
	function list_province($pagelimit='', $offset=0, $orderby='', $ascdesc='', $encodedkey='') {
		$this->session->set_bread('list');

		if ($orderby == '') $orderby = 'ap_name';
		if ($ascdesc == '') $ascdesc = 'ASC';
		if ($pagelimit == '') $pagelimit = 20;
		$this->sci->assign('pagelimit' , $pagelimit);
		$this->sci->assign('offset' , $offset);
		$this->sci->assign('orderby' , $orderby);
		$this->sci->assign('ascdesc' , $ascdesc);
		/*--cache-start--*/
		$this->db->start_cache();
			if($encodedkey != ''){ $this->pre_search($encodedkey); }
			$orderbyconv = preg_replace('/_DOT_/' , '.', $orderby);
			$this->db->order_by($orderbyconv , $ascdesc);
			$this->db->from('area_province');
			$this->db->where('ap_status' , 'Active');
			$this->db->select('*');
		$this->db->stop_cache();
		/*--cache-stop--*/

		// Pagination
		$total = $this->db->count_all_results('area_province');
		$this->load->library('pagination');
		$config['base_url'] = $this->mod_url."list_province/". $pagelimit ."/";
		$config['suffix'] = "/$orderby/$ascdesc/$encodedkey" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 5;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get('area_province');
		$this->db->flush_cache();
		$maindata = $res->result_array();
		foreach($maindata as $k=>$tmp) {
			$this->db->where('ap_id' , $tmp['ap_id']);
			$this->db->where('ac_status' , 'Active');
			$maindata[$k]['total_city'] = $this->db->count_all_results('area_city');
			
			$this->db->join('area_city ac' , 'ac.ac_id = dealers.ac_id' , 'left');
			$this->db->where('ap_id' , $tmp['ap_id']);
			$this->db->where('dlr_status' , 'Active');
			$maindata[$k]['total_dealers'] = $this->db->count_all_results('dealers');
			
		}
		$this->sci->assign('maindata' , $maindata);
		$this->sci->assign('paging', $this->pagination->create_links() );
		$this->post_index();
		$this->sci->da('list_province.htm');
	}
	
	function add_province(){
		$this->sci->assign('ajax_action' , 'add');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('ap_name', 'Name', 'trim|required');
		if($this->form_validation->run() == FALSE) {
			$this->sci->da('edit_province.htm');	
		}else {
			$this->db->set('ap_name' , $this->input->post('ap_name') );
			$this->db->set('ap_entry' , date('Y-m-d H:i:s') );
			$this->db->insert('area_province');
			redirect(site_url()."admin/dealers/list_province/");
		} 
	}
	
	function edit_province($ap_id=0){
		$this->sci->assign('ajax_action' , 'edit');
		
		$this->db->where('ap_id' , $ap_id);
		$res = $this->db->get('area_province');
		$data = $res->row_array();
		$this->sci->assign('data' , $data);
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('ap_name', 'Name', 'trim|required');
		if($this->form_validation->run() == FALSE) {
			$this->sci->da('edit_province.htm');	
		}else {
			$this->db->where('ap_id' , $ap_id);
			$this->db->set('ap_name' , $this->input->post('ap_name') ); 
			$this->db->update('area_province');
			redirect(site_url()."admin/dealers/list_province/");
		} 
	}
	
	function delete_province($ap_id=0) {
		$this->db->where('ap_id' , $ap_id);
		$this->db->set('ap_status' , 'Deleted');
		$this->db->update('area_province');
		redirect(site_url()."admin/dealers/list_province/");
	}
	
	
	function list_city($ap_id=0, $pagelimit='', $offset=0, $orderby='', $ascdesc='', $encodedkey='') {
		$this->session->set_bread('list');
		
		$this->sci->assign('ap_id' , $ap_id);
		$this->db->where('ap_id' , $ap_id);
		$res = $this->db->get('area_province');
		$area_province = $res->row_array();
		$this->sci->assign('area_province' , $area_province);

		if ($orderby == '') $orderby = 'ac_name';
		if ($ascdesc == '') $ascdesc = 'ASC';
		if ($pagelimit == '') $pagelimit = 20;
		$this->sci->assign('pagelimit' , $pagelimit);
		$this->sci->assign('offset' , $offset);
		$this->sci->assign('orderby' , $orderby);
		$this->sci->assign('ascdesc' , $ascdesc);
		/*--cache-start--*/
		$this->db->start_cache();
			if($encodedkey != ''){ $this->pre_search($encodedkey); }
			$orderbyconv = preg_replace('/_DOT_/' , '.', $orderby);
			$this->db->order_by($orderbyconv , $ascdesc);
			if($ap_id !=0) {
				$this->db->where('ap_id' , $ap_id);
			}
			$this->db->from('area_city');
			$this->db->where('ac_status' , 'Active');
			$this->db->select('*');
		$this->db->stop_cache();
		/*--cache-stop--*/

		// Pagination
		$total = $this->db->count_all_results();
		$this->load->library('pagination');
		$config['base_url'] = $this->mod_url."list_city/$ap_id/". $pagelimit ."/";
		$config['suffix'] = "/$orderby/$ascdesc/$encodedkey" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 6;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get();
		$this->db->flush_cache();
		$maindata = $res->result_array();
		foreach($maindata as $k=>$tmp) {
			$this->db->where('ac_id' , $tmp['ac_id']);
			$this->db->where('dlr_status' , 'Active');
			$maindata[$k]['total_dealers'] = $this->db->count_all_results('dealers');
		}
		$this->sci->assign('maindata' , $maindata);
		$this->sci->assign('paging', $this->pagination->create_links() );
		$this->post_index();
		$this->sci->da('list_city.htm');
	}
	
	function add_city($ap_id=0){
		$this->sci->assign('ajax_action' , 'add');
		
		$this->db->where('ap_id' , $ap_id);
		$res = $this->db->get('area_province');
		$this_province = $res->row_array();
		$this->sci->assign('this_province' , $this_province);

		$this->db->where('ap_status' , 'Active');
		$this->db->order_by('ap_name' , 'asc');
		$res = $this->db->get('area_province');
		$all_province = $res->result_array();
		$this->sci->assign('all_province' , $all_province);
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('ac_name', 'Name', 'trim|required');
		$this->form_validation->set_rules('ap_id', 'Province', 'trim|required');
		if($this->form_validation->run() == FALSE) {
			$this->sci->da('edit_city.htm');	
		}else {
			$this->db->set('ap_id' , $this->input->post('ap_id') );
			$this->db->set('ac_name' , $this->input->post('ac_name') );
			$this->db->set('ac_entry' , date('Y-m-d H:i:s') );
			$this->db->insert('area_city');
			redirect(site_url()."admin/dealers/list_city/$ap_id");
		} 
	}
	
	function edit_city($ac_id=0){
		$this->sci->assign('ajax_action' , 'edit');
		
		$this->db->where('ac_id' , $ac_id);
		$res = $this->db->get('area_city');
		$data = $res->row_array();
		$this->sci->assign('data' , $data);
		
		$this->db->where('ap_id' , $data['ap_id']);
		$res = $this->db->get('area_province');
		$this_province = $res->row_array();
		$this->sci->assign('this_province' , $this_province);

		$this->db->where('ap_status' , 'Active');
		$this->db->order_by('ap_name' , 'asc');
		$res = $this->db->get('area_province');
		$all_province = $res->result_array();
		$this->sci->assign('all_province' , $all_province);
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('ac_name', 'Name', 'trim|required');
		$this->form_validation->set_rules('ap_id', 'Province', 'trim|required');
		if($this->form_validation->run() == FALSE) {
			$this->sci->da('edit_city.htm');	
		}else {
			$this->db->where('ac_id' , $ac_id);
			$this->db->set('ap_id' , $this->input->post('ap_id') );
			$this->db->set('ac_name' , $this->input->post('ac_name') );
			$this->db->update('area_city');
			redirect(site_url()."admin/dealers/list_city/".$data['ap_id']);
		} 
	}
	
	
	
	
	function list_dealer($ac_id=0, $dlr_type='Any', $pagelimit='', $offset=0, $orderby='', $ascdesc='', $encodedkey='') {
		$this->session->set_bread('list');
		
		$this->db->join('area_province ap' , 'ap.ap_id = area_city.ap_id' , 'left');
		$this->sci->assign('ac_id' , $ac_id);
		$this->db->where('ac_id' , $ac_id);
		$res = $this->db->get('area_city');
		$area_city = $res->row_array();
		$this->sci->assign('area_city' , $area_city);
		
		$this->sci->assign('dlr_type' , $dlr_type); 
		 
		if ($orderby == '') $orderby = 'dlr_name';
		if ($ascdesc == '') $ascdesc = 'ASC';
		if ($pagelimit == '') $pagelimit = 20;
		$this->sci->assign('pagelimit' , $pagelimit);
		$this->sci->assign('offset' , $offset);
		$this->sci->assign('orderby' , $orderby);
		$this->sci->assign('ascdesc' , $ascdesc);
		/*--cache-start--*/
		$this->db->start_cache();
			if($encodedkey != ''){ $this->pre_search($encodedkey); }
			$orderbyconv = preg_replace('/_DOT_/' , '.', $orderby);
			$this->db->join('area_city ac' , 'ac.ac_id = dealers.ac_id' , 'left');
			$this->db->join('area_province ap' , 'ap.ap_id = ac.ap_id' , 'left');
			
			$this->db->order_by($orderbyconv , $ascdesc);
			if($ac_id !=0) {
				$this->db->where('dealers.ac_id' , $ac_id);
			}
			if($dlr_type != 'Any') {
				$this->db->where('dealers.dlr_type' , $dlr_type);
			}
			
			$this->db->from('dealers');
			$this->db->where('dlr_status' , 'Active');
			$this->db->select('*');
		$this->db->stop_cache();
		/*--cache-stop--*/

		// Pagination
		$total = $this->db->count_all_results();
		$this->load->library('pagination');
		$config['base_url'] = $this->mod_url."list_dealer/$ac_id/$dlr_type/". $pagelimit ."/";
		$config['suffix'] = "/$orderby/$ascdesc/$encodedkey" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 7;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get();
		$this->db->flush_cache();
		$maindata = $res->result_array(); 
		$this->sci->assign('maindata' , $maindata);
		$this->sci->assign('paging', $this->pagination->create_links() );
		$this->post_index();
		$this->sci->da('list_dealer.htm');
	}
	
	function change_dealer_filter(){
		$offset = $this->input->post('offset');
		$ascdesc = $this->input->post('ascdesc');
		$orderby = $this->input->post('orderby');
		$pagelimit = $this->input->post('pagelimit');
		$ac_id = $this->input->post('ac_id');
		$page = $this->input->post('page');
		$dlr_type = $this->input->post('dlr_type');
		redirect($page."$ac_id/$dlr_type/$pagelimit/$offset/$orderby/$ascdesc/");
		
	}
	
	function _dealer_set_rules() {
		$this->form_validation->set_rules('dlr_name', 'Name', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('dlr_address', 'Alamat', 'trim|xss_clean');
		$this->form_validation->set_rules('dlr_phone', 'Business No.', 'trim|xss_clean'); 
		$this->form_validation->set_rules('dlr_lat', 'Latitude', 'trim|xss_clean');
		$this->form_validation->set_rules('dlr_long', 'Longitude', 'trim|xss_clean');
		$this->form_validation->set_rules('ac_id', 'City', 'trim|required');
		$this->form_validation->set_rules('dlr_type', 'Type', 'trim|required');
		$this->form_validation->set_rules('dlr_desc', 'Description', 'trim');
		$this->form_validation->set_rules('dlr_fax', 'Fax', 'trim');
		$this->form_validation->set_rules('dlr_contact', 'Contact Person', 'trim');
		$this->form_validation->set_rules('dlr_email', 'Email', 'trim');
		$this->form_validation->set_rules('dlr_twitter', 'Twitter', 'trim');
		$this->form_validation->set_rules('dlr_facebook', 'Facebook', 'trim');
	}
	
	function _dealer_set_db(){
		$this->db->set('dlr_fax' , $this->input->post('dlr_fax') );
		$this->db->set('dlr_contact' , $this->input->post('dlr_contact') );
		$this->db->set('dlr_email' , $this->input->post('dlr_email') );
		$this->db->set('dlr_twitter' , $this->input->post('dlr_twitter') );
		$this->db->set('dlr_facebook' , $this->input->post('dlr_facebook') );
		
		$this->db->set('dlr_type' , $this->input->post('dlr_type') );
		$this->db->set('ac_id' , $this->input->post('ac_id') );
		$this->db->set('dlr_name' , $this->input->post('dlr_name') );
		$this->db->set('dlr_address' , $this->input->post('dlr_address') );
		$this->db->set('dlr_phone' , $this->input->post('dlr_phone') );
		$this->db->set('dlr_lat' , $this->input->post('dlr_lat') );
		$this->db->set('dlr_long' , $this->input->post('dlr_long') );
		$this->db->set('dlr_desc' , $this->input->post('dlr_desc') );
		
		if($_FILES['dlr_image']['name'] != '' ) {
			$filename = $this->_upload_image('dlr_image');
			$this->db->set('dlr_image' , $filename);
		}
	}
	
	
	function add_dealer($ac_id=0){
		$this->sci->assign('ajax_action' , 'add');
		
		$this->db->join('area_province ap' , 'ap.ap_id = area_city.ap_id' , 'left');
		$this->sci->assign('ac_id' , $ac_id);
		$this->db->where('ac_id' , $ac_id);
		
		$res = $this->db->get('area_city');
		$this_city = $res->row_array();
		$this->sci->assign('this_city' , $this_city);
		
		
		if($ac_id!=0){
			$this->db->join('area_province ap' , 'ap.ap_id = area_city.ap_id' , 'left');
			$this->db->where('ac_id' , $ac_id); 
			$res = $this->db->get('area_city');
			$this_city = $res->row_array();
			$this->sci->assign('this_city' , $this_city); 
		}
		
		$this->db->where('ap_status' , 'Active');
		$this->db->order_by('ap_name' , 'asc');
		$res = $this->db->get('area_province');
		$all_city = $res->result_array();
		foreach($all_city as $k=>$tmp) {
			$this->db->where('ap_id' , $tmp['ap_id']);
			$this->db->order_by('ac_name' , 'asc');
			$res = $this->db->get('area_city');
			$all_city[$k]['city'] = $res->result_array();
		}
		$this->sci->assign('all_city' , $all_city);	 

		$this->db->where('ap_status' , 'Active');
		$this->db->order_by('ap_name' , 'asc');
		$res = $this->db->get('area_province');
		$all_province = $res->result_array();
		$this->sci->assign('all_province' , $all_province);
		
		$this->load->library('form_validation');
		$this->_dealer_set_rules(); 
		if($this->form_validation->run() == FALSE) {
			$this->sci->da('edit_dealer.htm');	
		}else {
			$this->_dealer_set_db();
			$this->db->set('dlr_entry' , date('Y-m-d H:i:s') );
			$this->db->insert('dealers');
			redirect(site_url()."admin/dealers/list_dealer/$ac_id");
		} 
	}
	
	function edit_dealer($dlr_id=0){
		$this->sci->assign('ajax_action' , 'edit');
		$this->db->where('dlr_id' , $dlr_id);
		$res = $this->db->get('dealers');
		$data = $res->row_array();
		$this->sci->assign('data' , $data);
		
		$this->sci->assign('ac_id' , $data['ac_id']);
		
		$this->db->join('area_province ap' , 'ap.ap_id = area_city.ap_id' , 'left');
		$this->db->where('ac_id' , $data['ac_id']); 
		$res = $this->db->get('area_city');
		$this_city = $res->row_array();
		$this->sci->assign('this_city' , $this_city);
		
		$this->db->where('ap_status' , 'Active');
		$this->db->order_by('ap_name' , 'asc');
		$res = $this->db->get('area_province');
		$all_city = $res->result_array();
		foreach($all_city as $k=>$tmp) {
			$this->db->where('ap_id' , $tmp['ap_id']);
			$this->db->order_by('ac_name' , 'asc');
			$res = $this->db->get('area_city');
			$all_city[$k]['city'] = $res->result_array();
		}
		$this->sci->assign('all_city' , $all_city);
		

		$this->db->where('ap_status' , 'Active');
		$this->db->order_by('ap_name' , 'asc');
		$res = $this->db->get('area_province');
		$all_province = $res->result_array();
		$this->sci->assign('all_province' , $all_province);
		
		$this->load->library('form_validation');
		$this->_dealer_set_rules(); 
		if($this->form_validation->run() == FALSE) {
			$this->sci->da('edit_dealer.htm');	
		}else {
			$this->db->where('dlr_id' , $dlr_id);
			$this->_dealer_set_db();
			$this->db->update('dealers');
			redirect(site_url()."admin/dealers/list_dealer/".$data['ac_id']);
		} 
	}
	
	
	
	

	function enum_setting($maindata=array()) {
		return $maindata;
	}

	function join_setting() {
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active");
	}

	function validation_setting() {
		$this->form_validation->set_rules('dlr_name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('dlr_region', 'Wilayah', 'trim|xss_clean');
		$this->form_validation->set_rules('dlr_contact', 'Contact Person', 'trim|xss_clean');
		$this->form_validation->set_rules('dlr_address', 'Alamat', 'trim|xss_clean');
		$this->form_validation->set_rules('dlr_phone', 'Business No.', 'trim|xss_clean');
		$this->form_validation->set_rules('dlr_mobile', 'Mobile', 'trim|xss_clean');
		$this->form_validation->set_rules('dlr_fax', 'Business Fax', 'trim|xss_clean');
		$this->form_validation->set_rules('dlr_email', 'Email', 'trim|xss_clean|valid_email');
		$this->form_validation->set_rules('dlr_lat', 'Latitude', 'trim|xss_clean');
		$this->form_validation->set_rules('dlr_long', 'Longitude', 'trim|xss_clean');
		$this->form_validation->set_rules('dlr_desc', 'Description', 'trim');
	}

	function database_setter() { 
		$this->db->set('dlr_name' ,  $this->input->post('dlr_name') );
		$this->db->set('dlr_region' ,  $this->input->post('dlr_region') );
		$this->db->set('dlr_contact' ,  $this->input->post('dlr_contact') );
		$this->db->set('dlr_address' ,  $this->input->post('dlr_address') );
		$this->db->set('dlr_phone' ,  $this->input->post('dlr_phone') );
		$this->db->set('dlr_mobile' ,  $this->input->post('dlr_mobile') );
		$this->db->set('dlr_fax' ,  $this->input->post('dlr_fax') );
		$this->db->set('dlr_email' ,  $this->input->post('dlr_email') );
		$this->db->set('dlr_lat' ,  $this->input->post('dlr_lat') );
		$this->db->set('dlr_long' ,  $this->input->post('dlr_long') ); 
		$this->db->set('dlr_desc' ,  $this->input->post('dlr_desc') ); 
		if($_FILES['dlr_image']['name'] != '' ) {
			$filename = $this->_upload_image('dlr_image');
			$this->db->set('dlr_image' , $filename);
		}
	}


	function pre_add_edit() { 
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}




}
