<?php

class Club extends MY_Controller {

	var $mod_title = '';

	function __construct() {
		parent::__construct();
		$this->sci->init('front');
		$this->_init();
		
		$error_string =  $this->session->flashdata('error_string');
		$this->sci->assign('error_string' , $error_string);
		$this->session->validate_club(); 
	}
	
	function get_articles($clubc_type='News', $club_id=0,  $limit=6) {
		if($club_id != 0) {
			$this->db->where('club_id' , $club_id);
		}
		$this->db->where('clubc_type' , $clubc_type);
		$this->db->order_by('clubc_date' , 'DATE');
		$this->db->limit($limit);
		$res = $this->db->get('club_content');
		$content = $res->result_array();
		return $content;
	}
	
	function load_sidebar($club_id=0) {
		$news = $this->get_articles('News', $club_id, 3);
		$this->sci->assign('news' , $news);
		
		$event = $this->get_articles('Event', $club_id, 3);
		$this->sci->assign('event' , $event);
		
		$this->sci->assign('club_id' , $club_id);
		if($club_id != 0) {
			$this->db->where('club_id' , $club_id);
			$res = $this->db->get('club_files');
			$files = $res->result_array();
			$this->sci->assign('files' , $files);
		}
		
		$html = $this->sci->fetch('club/sidebar.htm'); 
		$this->sci->assign('club_sidebar' , $html);
	}
	
	function index() {
		$this->load_sidebar();
		
		$this->db->where('club_status' , 'Active');
		$this->db->where('club_is_pending' , 'No');
		$res = $this->db->get('club');
		$clubs = $res->result_array();
		$this->sci->assign('clubs' , $clubs); 
		$this->sci->da('index.htm');
	}
	
	function view_list($offset=0) {
		$this->load_sidebar();
		
		$pagelimit = 10;
		$this->db->start_cache();
		$this->db->where('club_status' , 'Active');
		$this->db->where('club_is_pending' , 'No'); 
		//$this->db->order_by('c_date' , 'DESC'); 
		$this->db->stop_cache();
		
		$total = $this->db->count_all_results('club');
		$this->load->library('pagination');
		$config['base_url'] = site_url()."club/view_list/";
		$config['suffix'] = "" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get('club'); 
		$this->db->flush_cache();
		
		$maindata = $res->result_array();
		$this->sci->assign('maindata' , $maindata);
		$this->sci->assign('paging', $this->pagination->create_links() );
		
		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>"; 
		$breadcrumb[] = "<a href='".site_url()."club' >Clubs</a>"; 
		$breadcrumb[] = "View All";
		$this->sci->assign('breadcrumb' , $breadcrumb);
		
		$this->sci->da('view_list.htm');
	}
	
	function list_content($clubc_type='Any', $club_id=0, $offset=0) {
		$this->load_sidebar();
		
		if($clubc_type == 'Any') { $clubc_type = 'Event'; } 
		$this->sci->assign('clubc_type' , $clubc_type);
		$this->sci->assign('club_id' , $club_id);
		
		$pagelimit = 15;
		$this->db->start_cache();
		$this->db->join('club c' , 'c.club_id = club_content.club_id' , 'left'); 
		$this->db->where('club_status' , 'Active');
		$this->db->where('club_is_pending' , 'No'); 
		$this->db->order_by('clubc_date' , 'DESC');
		$this->db->where('clubc_status' , 'Active');
		$this->db->where('clubc_type' , $clubc_type);
		$this->db->stop_cache();
		
		$total = $this->db->count_all_results('club_content');
		$this->load->library('pagination');
		$config['base_url'] = site_url()."club/list_content/$clubc_type/$club_id/";
		$config['suffix'] = "" ;
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
		
		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>"; 
		$breadcrumb[] = "<a href='".site_url()."club' >Clubs</a>"; 
		$breadcrumb[] = "Club News / Event";
		$this->sci->assign('breadcrumb' , $breadcrumb);
		
		$this->sci->da('list_content.htm');
	}
	
	
	function list_download($dt_name='Wallpaper') {
		$this->db->join('download_type dt' , 'dt.dt_id = download.dt_id' , 'left');
		$this->db->where('dl_status' , 'Active');
		$this->db->where('dt_name' , $dt_name);
		$this->db->order_by('dl_date' , 'DESC');
		$res = $this->db->get('download');
		$downloads = $res->result_array();
		$this->sci->assign('downloads' , $downloads);
		$this->sci->d('list_download.htm');
	}
	
	function view($club_id=0) {
		$this->load_sidebar($club_id);
		
		$this->db->where('club_id' , $club_id);
		$res = $this->db->get('club');
		$club = $res->row_array(); 
		$this->sci->assign('club' , $club);
		
		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>"; 
		$breadcrumb[] = "<a href='".site_url()."club' >Clubs</a>"; 
		$breadcrumb[] = "<a href='".site_url()."club/view_list' >View All</a>"; 
		$breadcrumb[] = $club['club_name'];
		$this->sci->assign('breadcrumb' , $breadcrumb);
		
		$this->sci->da('view.htm');
	}
	
	function view_content($clubc_id=0) {
		$this->load_sidebar();
		
		$this->db->join('club c' , 'c.club_id = club_content.club_id' , 'left');
		$this->db->where('clubc_id' , $clubc_id);
		$res = $this->db->get('club_content');
		$content = $res->row_array();
		
		$this->sci->assign('content' , $content);
		$this->sci->da('view_content.htm');
	}
	
	
	function send_ecard($dl_id=0){
		$this->db->join('download_type dt' , 'dt.dt_id = download.dt_id' , 'left');
		$this->db->where('dl_id' , $dl_id);
		$res = $this->db->get('download');
		$download = $res->row_array();
		$this->sci->assign('download' , $download);
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('id', 'file_id', 'trim');
		$this->form_validation->set_rules('senderemail', 'Sender Email', 'trim|required|xss_clean');
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('message', 'Message', 'trim|xss_clean');
		$dl_id = $this->input->post('id');
		$senderemail = $this->input->post('senderemail');
		$email = $this->input->post('email');
		$name = $this->input->post('name');
		$message = $this->input->post('message');
		
		$data = array();
		if($this->form_validation->run() == FALSE) {
			$this->sci->da('send_ecard.htm');
		} else {
			$this->load->library('email');
			$config['mailtype'] = 'html';
			$config['charset'] = 'iso-8859-1';
			$this->email->initialize($config);
			$this->email->from($senderemail, $name );
			$this->email->to($email);
			$this->email->subject( $name.' sent you an e-card' );
	 
			$this->sci->assign('message' , $message );
			$this->sci->assign('name' , $name ); 
			$messagebody = $this->sci->fetch('club/ecard_mail.htm');
	
			$this->email->message($messagebody);
			$this->email->set_alt_message('This is the alternative message');
	
			$sendok = $this->email->send();
			if($sendok == TRUE) {
				redirect(site_url()."club/send_ecard_success/$dl_id");	
			} else {
				print 'send email error !';
			}
			
		}   
	}
	
	function send_ecard_success($dl_id=0){
		$this->sci->da('send_ecard_success.htm');
	}
	
	//
	//function send_mail($dl_id=0){ 
	//	$this->db->join('download_type dt' , 'dt.dt_id = download.dt_id' , 'left');
	//	$this->db->where('dl_id' , $dl_id);
	//	$res = $this->db->get('download');
	//	$download = $res->row_array();
	//	$this->sci->assign('download' , $download);
	//
	//	if(!$download) { return false; } 
	//
	//	$sendok = FALSE;
	//	$this->load->library('email');
	//	$config['mailtype'] = 'html';
	//	$config['charset'] = 'iso-8859-1';
	//	$this->email->initialize($config);
	//	$this->email->from('info@gudangbrands.com', 'Gudang Brands' );
	//	$this->email->to($email);
	//	$this->email->subject( $fullname.' would like to invite you to join gudangBrands' );
	//
	//	$this->sci->assign('invt_id' , $invt_id);
	//	$this->sci->assign('message' , $message );
	//	$this->sci->assign('fullname' , $fullname );
	//	$this->sci->assign('key' , $key.'/'.safe_base64_encode($email) );
	//	$messagebody = $this->sci->fetch('invite/email_invitation.htm');
	//
	//	$this->email->message($messagebody);
	//	$this->email->set_alt_message('This is the alternative message');
	//
	//	$sendok = $this->email->send();
	//	return $sendok;
	//}

}
