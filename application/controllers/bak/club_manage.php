<?php

class Club_manage extends MY_Controller {

	var $mod_title = '';

	function __construct() {
		parent::__construct();
		$this->sci->init('front');
		$this->_init(); 
		$status = $this->session->validate_club();
		if($status != 1) {
			redirect(site_url().'club');
		}
		
		$this->club_id = $this->userinfo['club_id'];
		 
	}
	 
	function load_sidebar() {
		//$news = $this->get_articles('News', $club_id, 3);
		//$this->sci->assign('news' , $news);
		//
		//$event = $this->get_articles('Event', $club_id, 3);
		//$this->sci->assign('event' , $event);
		//
		//$this->sci->assign('club_id' , $club_id);
		//if($club_id != 0) {
		//	$this->db->where('club_id' , $club_id);
		//	$res = $this->db->get('club_files');
		//	$files = $res->result_array();
		//	$this->sci->assign('files' , $files);
		//}
		//
		$html = $this->sci->fetch('club_manage/sidebar.htm'); 
		$this->sci->assign('sidebar' , $html);
		
		$this->db->where('club_id' , $this->club_id);
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
	}
	
	function index() {
		$this->sci->assign('current' , "club_manage");
		$this->load_sidebar(); 
		
		$this->sci->da('index.htm');
	}
	
	function edit_club_info(){
		$club_id = $this->club_id;
		$this->sci->assign('current' , "club_manage");
		$this->load_sidebar();
		
		$this->config->set_item('global_xss_filtering', FALSE);
		
		$this->db->where('club_id' , $this->club_id);
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		 
		//$this->form_validation->set_rules('club_login', 'Username', 'required|trim|xss_clean');
		//$this->form_validation->set_rules('club_admin_name', 'Admin Name', 'required|trim|xss_clean'); 
		//$this->form_validation->set_rules('club_pass', 'Password', 'trim|xss_clean');
		//if($this->input->post('club_pass') != '') {
		//	$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'trim|required|xss_clean|matches[club_pass]');
		//}
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('club_name', 'Club Name', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('club_email', 'Club Email', 'trim|xss_clean'); 
		$this->form_validation->set_rules('club_desc', 'Club Description', 'trim'); 
		$this->form_validation->set_rules('club_contact', 'Club Contact', 'trim|xss_clean'); 
		$this->form_validation->set_rules('club_address', 'Club Address', 'trim|xss_clean');
		$this->form_validation->set_rules('club_intro', 'Club Intro', 'trim|xss_clean');
		$this->form_validation->set_rules('club_website', 'Club Website', 'trim|xss_clean');
		$this->form_validation->set_rules('club_facebook', 'Club Facebook', 'trim|xss_clean');
		$this->form_validation->set_rules('club_twitter', 'Club Twitter', 'trim|xss_clean');
		$this->form_validation->set_rules('club_milis', 'Club Milis', 'trim|xss_clean');
		$this->form_validation->set_rules('club_other1', 'Club other links 1', 'trim|xss_clean');
		$this->form_validation->set_rules('club_other2', 'Club other links 2', 'trim|xss_clean');
		$this->form_validation->set_rules('club_other3', 'Club other links 3', 'trim|xss_clean');
		$this->form_validation->set_rules('club_join_date', 'Club Join Date', 'trim|xss_clean');
		$this->form_validation->set_rules('club_est_date', 'Club Established Date', 'trim|xss_clean');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('edit_club_info.htm');
		}else{
			$this->db->set('club_name' , $this->input->post('club_name') ); 
			$this->db->set('club_email' , $this->input->post('club_email') ); 
			$this->db->set('club_desc' , $this->input->post('club_desc') ); 
			$this->db->set('club_contact' , $this->input->post('club_contact') ); 
			$this->db->set('club_address' , $this->input->post('club_address') );
			$this->db->set('club_intro' , $this->input->post('club_intro') );
			$this->db->set('club_website' , $this->input->post('club_website') );
			$this->db->set('club_facebook' , $this->input->post('club_facebook') );
			$this->db->set('club_twitter' , $this->input->post('club_twitter') );
			$this->db->set('club_milis' , $this->input->post('club_milis') );
			$this->db->set('club_other1' , $this->input->post('club_other1') );
			$this->db->set('club_other2' , $this->input->post('club_other2') );
			$this->db->set('club_other3' , $this->input->post('club_other3') ); 
			$this->db->set('club_est_date' , $this->input->post('club_est_date') );
			   
			if($_FILES['club_logo']['name'] != '' ) {
				$filename = $this->_upload_image('club_logo');
				$this->db->set('club_logo' , $filename);
			}
			if($_FILES['club_mainphoto']['name'] != '' ) {
				$filename = $this->_upload_image('club_mainphoto');
				$this->db->set('club_mainphoto' , $filename);
			}
			$this->db->where('club_id' , $club_id);
			$ok = $this->db->update('club');
			
			if(!$ok) {
				$this->session->set_confirm(0); 
			} else {
				$this->session->set_confirm(1); 
			}
			redirect(site_url()."club_manage/");
		}
	}
	
	
	function content_change_filter( ) { 
		$clubc_type = $this->input->post('clubc_type'); 
		$offset = $this->input->post('offset');  
		redirect(site_url()."club_manage/list_content/$clubc_type/$offset");
	}
	
	function list_content($clubc_type='Any', $offset=0) { 
		$club_id = $this->club_id;
		$this->sci->assign('current' , "club_manage");
		$this->load_sidebar(); 
		$this->sci->assign('clubc_type' , $clubc_type);
		 
		$orderby = 'clubc_entry';
		$ascdesc = 'DESC';
		$pagelimit = 10; 
		$this->sci->assign('pagelimit' , $pagelimit);
		$this->sci->assign('offset' , $offset);
		$this->sci->assign('orderby' , $orderby);
		$this->sci->assign('ascdesc' , $ascdesc);
		/*--cache-start--*/
		$this->db->start_cache(); 
			$this->db->order_by($orderby , $ascdesc);
			if($clubc_type != 'Any') {
				$this->db->where('clubc_type' , $clubc_type);
			} 
			$this->db->where('clubc_status' , 'Active');
			$this->db->where('club_id' , $club_id);
			$res = $this->db->get('club_content');
		$this->db->stop_cache();
		/*--cache-stop--*/

		// Pagination
		$total = $this->db->count_all_results('club_content');
		$this->load->library('pagination');
		$config['base_url'] = site_url()."club_manage/list_content/$clubc_type";
		$config['suffix'] = "/$orderby" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 5;
		$this->pagination->initialize($config); 
		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get('club_content');
		$this->db->flush_cache();
		$maindata = $res->result_array(); 
		$this->sci->assign('maindata' , $maindata);
		$this->sci->assign('paging', $this->pagination->create_links() ); 
		$this->sci->da('content/list.htm');
	}
	
	function content_validation(){
		$this->form_validation->set_rules('clubc_type', 'Type', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('clubc_date', 'Date', 'trim|xss_clean'); 
		$this->form_validation->set_rules('clubc_title', 'Title', 'trim|required'); 
		$this->form_validation->set_rules('clubc_intro', 'Intro', 'trim');  
		$this->form_validation->set_rules('clubc_content', 'Content', 'trim'); 
	}
	
	function content_setter(){
		$club_id = $this->club_id;
		$this->db->set('club_id' , $club_id); 
		$this->db->set('clubc_type' , $this->input->post('clubc_type') );  
		$this->db->set('clubc_title' , $this->input->post('clubc_title') );  
		$this->db->set('clubc_intro' , $this->input->post('clubc_intro') );  
		$this->db->set('clubc_content' , $this->input->post('clubc_content') );   
		
		if($this->input->post('clubc_date') == '') {
			$date = date('Y-m-d H:i:s');
		} else {
			$date = $this->input->post('clubc_date');
		}
		$this->db->set('clubc_date' , $date);
		
		if($_FILES['clubc_banner']['name'] != '' ) {
			$filename = $this->_upload_image('clubc_banner');
			$this->db->set('clubc_banner' , $filename);
		} 
	}
	
	
	function add_content($clubc_type='News') {
		$club_id = $this->club_id;
		$this->sci->assign('current' , "club_manage");
		$this->sci->assign('ajax_action' , "add");
		$this->load_sidebar(); 
		$this->config->set_item('global_xss_filtering', FALSE);
		
		$this->db->where('club_id' , $this->club_id);
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		
		$content['clubc_type'] = $clubc_type;
		$this->sci->assign('content' , $content);
		
		$this->load->library('form_validation');
		$this->content_validation();
		
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('content/edit.htm');
		}else{
			$this->content_setter();
			$this->db->set('clubc_entry' , date('Y-m-d H:i:s'));
			//$this->db->where('club_id' , $club_id);
			//$ok = $this->db->update('club');
			$ok = $this->db->insert('club_content');
			
			if(!$ok) {
				$this->session->set_confirm(0); 
			} else {
				$this->session->set_confirm(1); 
			}
			redirect(site_url()."club_manage/list_content/$clubc_type");
		}
	}
	
	function edit_content($clubc_id=0) {
		$club_id = $this->club_id;
		$this->sci->assign('current' , "club_manage");
		$this->sci->assign('ajax_action' , "edit");
		$this->load_sidebar(); 
		$this->config->set_item('global_xss_filtering', FALSE);
		
		$this->db->where('club_id' , $this->club_id);
		$res = $this->db->get('club');
		$club = $res->row_array();
		$this->sci->assign('club' , $club);
		
		$this->db->where('clubc_id' , $clubc_id);
		$res = $this->db->get('club_content');
		$content = $res->row_array();
		$this->sci->assign('content' , $content);
		
		$clubc_type = $content['clubc_type'];
		
		$this->load->library('form_validation');
		$this->content_validation();
		
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('content/edit.htm');
		}else{
			$this->content_setter(); 
			$this->db->where('clubc_id' , $clubc_id);
			$ok = $this->db->update('club_content');  
			if(!$ok) {
				$this->session->set_confirm(0); 
			} else {
				$this->session->set_confirm(1); 
			}
			redirect(site_url()."club_manage/list_content/$clubc_type");
		}
	}
	
	function delete_content($clubc_id=0){
		$this->db->where('clubc_id' , $clubc_id);
		$res = $this->db->get('club_content');
		$content = $res->row_array();
		
		$this->db->where('clubc_id' , $clubc_id);
		$this->db->set('clubc_status' , 'Deleted');
		$ok = $this->db->update('club_content');
		if(!$ok) {
			$this->session->set_confirm(0); 
		} else {
			$this->session->set_confirm(1); 
		}
		redirect(site_url()."club_manage/list_content/".$content['clubc_type']);
	}
	
	//
	//function view_list($offset=0) {
	//	$this->load_sidebar();
	//	
	//	$pagelimit = 10;
	//	$this->db->start_cache();
	//	$this->db->where('club_status' , 'Active');
	//	$this->db->where('club_is_pending' , 'No'); 
	//	//$this->db->order_by('c_date' , 'DESC'); 
	//	$this->db->stop_cache();
	//	
	//	$total = $this->db->count_all_results('club');
	//	$this->load->library('pagination');
	//	$config['base_url'] = site_url()."club/view_list/";
	//	$config['suffix'] = "" ;
	//	$config['total_rows'] = $total;
	//	$config['per_page'] = $pagelimit;
	//	$config['uri_segment'] = 3;
	//	$this->pagination->initialize($config);
	//
	//	$this->db->limit($pagelimit, $offset);
	//	$res = $this->db->get('club'); 
	//	$this->db->flush_cache();
	//	
	//	$maindata = $res->result_array();
	//	$this->sci->assign('maindata' , $maindata);
	//	$this->sci->assign('paging', $this->pagination->create_links() );
	//	
	//	$breadcrumb = array();
	//	$breadcrumb[] = "<a href='".site_url()."' >Home</a>"; 
	//	$breadcrumb[] = "<a href='".site_url()."club' >Clubs</a>"; 
	//	$breadcrumb[] = "View All";
	//	$this->sci->assign('breadcrumb' , $breadcrumb);
	//	
	//	$this->sci->da('view_list.htm');
	//}
	//
	//function list_download($dt_name='Wallpaper') {
	//	$this->db->join('download_type dt' , 'dt.dt_id = download.dt_id' , 'left');
	//	$this->db->where('dl_status' , 'Active');
	//	$this->db->where('dt_name' , $dt_name);
	//	$this->db->order_by('dl_date' , 'DESC');
	//	$res = $this->db->get('download');
	//	$downloads = $res->result_array();
	//	$this->sci->assign('downloads' , $downloads);
	//	$this->sci->d('list_download.htm');
	//}
	//
	//function view($club_id=0) {
	//	$this->load_sidebar($club_id);
	//	
	//	$this->db->where('club_id' , $club_id);
	//	$res = $this->db->get('club');
	//	$club = $res->row_array(); 
	//	$this->sci->assign('club' , $club);
	//	
	//	$breadcrumb = array();
	//	$breadcrumb[] = "<a href='".site_url()."' >Home</a>"; 
	//	$breadcrumb[] = "<a href='".site_url()."club' >Clubs</a>"; 
	//	$breadcrumb[] = "<a href='".site_url()."club/view_list' >View All</a>"; 
	//	$breadcrumb[] = $club['club_name'];
	//	$this->sci->assign('breadcrumb' , $breadcrumb);
	//	
	//	$this->sci->da('view.htm');
	//}
	//
	//function view_content($clubc_id=0) {
	//	$this->load_sidebar();
	//	
	//	$this->db->where('clubc_id' , $clubc_id);
	//	$res = $this->db->get('club_content');
	//	$content = $res->row_array();
	//	
	//	$this->sci->assign('content' , $content);
	//	$this->sci->da('view_content.htm');
	//}

}
