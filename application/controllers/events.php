<?php

class Events extends MY_Controller {

	var $mod_title = '';

	function __construct() {
		parent::__construct();
		$this->sci->init('front');
		$this->_init();
		$this->load->model('mod_content');
	}


	function load_sidebar( ) {
		$this->db->where('e_status' , 'Active');
		$this->db->order_by('e_date' , 'DESC');
		$this->db->limit(6,0);
		$res = $this->db->get('events');
		$sb_events = $res->result_array();
		$this->sci->assign('sb_events' , $sb_events);


		$html = $this->sci->fetch('events/sidebar.htm');
		$this->sci->assign('events_sidebar' , $html);
	}

	function index() {
		$this->view_list();
	}

	function view_list( $offset=0) {
		$this->load_sidebar();
		$pagelimit = 15;
		$this->sci->assign('pagelimit' , $pagelimit);
		$this->sci->assign('offset' , $offset);


		$this->db->start_cache();
		$this->db->order_by('e_date' , 'DESC');
		$this->db->where('e_status' , 'Active');
		$this->db->stop_cache();

		$total = $this->db->count_all_results('events');
		$this->load->library('pagination');
		$config['base_url'] = site_url()."events/view_list/";
		$config['suffix'] = "" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get('events');
		$this->db->flush_cache();

		$events = $res->result_array();
		$this->sci->assign('events' , $events);
		$this->sci->assign('paging', $this->pagination->create_links() );

		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>";
		$breadcrumb[] = "Events";
		$this->sci->assign('breadcrumb' , $breadcrumb);

		$this->sci->da('view_list.htm');
	}




	function view($e_id=0) {
		$this->sci->assign('e_id' , $e_id);
		$this->load_sidebar();

		//get content
		$this->db->where('e_id' , $e_id);
		$this->db->where('e_status' , 'Active');
		$res = $this->db->get('events');
		$event = $res->row_array();
		if(!$event) { show_404(); }


		$this->sci->assign('event' , $event);
		//
		////get parent
		//
		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>";
		$breadcrumb[] = "<a href='".site_url()."events/view_list/' >Events</a>";
		$breadcrumb[] = $event['e_name'];
		$this->sci->assign('breadcrumb' , $breadcrumb);

		//assign content
		$this->sci->assign('event' , $event);

		//display
		$this->sci->da('view.htm');
	}

	function comment_list($c_id=0) {
		$this->db->where('cc_status' , 'Active');
		$this->db->where('c_id' , $c_id);
		$this->db->limit(10);
		$this->db->order_by('cc_entry' , 'DESC');
		$res = $this->db->get('content_comment');
		$comments = $res->result_array();
		$this->sci->assign('comments' , $comments);
		$this->sci->d('comment_list.htm');

	}

	function comment_form($c_id=0) {
		$this->sci->assign('c_id' , $c_id);
		$this->load->library('form_validation');
		$this->_generate_captcha();
		$this->form_validation->set_rules('c_id', 'Content', 'trim|xss_clean|required');
		$this->form_validation->set_rules('name', 'Name', 'trim|xss_clean|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|xss_clean|required|valid_email');
		$this->form_validation->set_rules('message', 'Message', 'trim|xss_clean|required');
		$this->form_validation->set_rules('captcha_answer', 'Captcha', 'trim|alpha_numeric|callback__checkcaptcha|xss_clean|required');
		$this->sci->d('comment_form.htm');
	}

	function submit_comment() {

		$this->load->library('form_validation');
		$this->form_validation->set_rules('c_id', 'Content', 'trim');
		$this->form_validation->set_rules('name', 'Name', 'trim|xss_clean|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|xss_clean|required|');
		$this->form_validation->set_rules('message', 'Message', 'trim|xss_clean|required');
		$this->form_validation->set_rules('captcha_answer', 'Captcha', 'trim|alpha_numeric|callback__checkcaptcha|xss_clean|required');

		$c_id = $this->input->post('c_id');
		$this->sci->assign('c_id' , $c_id);

		if($this->form_validation->run() == FALSE) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
			//$data['msg'] = 'asd';
		} else {
			$this->db->set('cc_name' , $this->input->post('name') );
			$this->db->set('cc_email' , $this->input->post('email') );
			$this->db->set('cc_message' , $this->input->post('message') );
			$this->db->set('c_id' , $this->input->post('c_id') );
			$this->db->set('cc_entry' , date('Y-m-d H:i:s') );
			$this->db->insert('content_comment');
			$data['status'] = 'ok';
			$data['msg'] = 'ok';
		}
		print json_encode($data);

	}

	function _generate_captcha() {
		$this->load->helper('captcha');
		$word = rand(111111, 999999);
		$vals = array(
			'word' => $word,
			'img_path' => './userfiles/captcha/',
			'img_url' => site_url()."userfiles/captcha/",
			'font_path' => './fonts/calibri.ttf',
			'img_width' => 150,
			'img_height' => 30,
			'expiration' => 7200
			);
		$captcha = create_captcha($vals);
		$this->session->set_userdata('captcha_string', $captcha['word']);
		$this->sci->assign('captcha' , $captcha);
		return $captcha;
	}

	function _checkcaptcha($str){
		$captcha_string = $this->session->userdata('captcha_string');
		if ($str != $captcha_string) {
			$this->form_validation->set_message('_checkcaptcha', 'Captcha Answer not match !');
			return FALSE;
		} else {
			return TRUE;
		}
	}

}
