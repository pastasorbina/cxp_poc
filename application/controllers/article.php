<?php

class Article extends MY_Controller {

	var $mod_title = '';

	function __construct() {
		parent::__construct();
		$this->sci->init('front');
		$this->_init();
		$this->load->model('mod_content');
	}


	function load_sidebar($cl_code='blog') {
		$sb_articles = $this->mod_content->get_content_list_by_label($cl_code,6);
		$this->sci->assign('sb_articles' , $sb_articles);

		$this->db->where('c_code' , 'sidebar-address');
		$res = $this->db->get('content');
		$sidebar_address = $res->row_array();
		$this->sci->assign('sidebar_address' , $sidebar_address);

		$html = $this->sci->fetch('article/sidebar.htm');
		$this->sci->assign('article_sidebar' , $html);
	}

	function index() {
		$this->view_list('blog');
	}

	function view_list($cl_code='blog', $offset=0) {

		$pagelimit = 15;
		$this->sci->assign('pagelimit' , $pagelimit);
		$this->sci->assign('offset' , $offset);

		$this->db->where('cl_code' , $cl_code);
		$res = $this->db->get('content_label');
		$content_label = $res->row_array();
		$this->sci->assign('content_label' , $content_label);
		if(!$content_label){
			show_404();
		}
		$cl_id = $content_label['cl_id'];

		$this->sci->assign('cl_code' , $cl_code);
		$this->sci->assign('cl_id' , $content_label['cl_id'] );

		$this->db->start_cache();
		$this->db->where('cl_id' , $cl_id);
		$this->db->join('content_newstag nt' , 'nt.nt_id = content.nt_id' , 'left');
		$this->db->order_by('c_date' , 'DESC');
		$this->db->where('c_status' , 'Active');
		$this->db->where('c_publish_status' , 'Published');
		/*ONLY LOAD CONTENTS BEFORE TODAY*/
		$this->db->where('c_date <=' , date('Y-m-d H:i:s') );
		$this->db->stop_cache();

		$total = $this->db->count_all_results('content');
		$this->load->library('pagination');
		$config['base_url'] = site_url()."article/view_list/". $cl_id ."/";
		$config['suffix'] = "" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 4;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get('content');
		$this->db->flush_cache();

		$content = $res->result_array();
		foreach($content as $k=>$tmp) { 
			$this->db->join('content_tag' , 'content_tag.t_id = content_detail_tag.t_id' , 'left');
			$this->db->where('c_id' , $tmp['c_id']);
			$res = $this->db->get('content_detail_tag');
			$content[$k]['tags'] = $res->result_array();
		}
		$this->sci->assign('content' , $content);
		$this->sci->assign('paging', $this->pagination->create_links() );

		//Load sidebar
		$this->load_sidebar($cl_code);

		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>";
		$breadcrumb[] = $content_label['cl_name'];
		$this->sci->assign('breadcrumb' , $breadcrumb);

		$this->sci->da('view_list.htm');
	}



	function view_list_by_id($cl_id=0, $offset=0) {
		$this->load_sidebar();
		$pagelimit = 15;
		$this->sci->assign('pagelimit' , $pagelimit);
		$this->sci->assign('offset' , $offset);
		$this->sci->assign('cl_id' , $cl_id);

		$this->db->where('cl_id' , $cl_id);
		$res = $this->db->get('content_label');
		$content_label = $res->row_array();
		$this->sci->assign('content_label' , $content_label);

		$this->db->start_cache();
		$this->db->where('cl_id' , $cl_id);
		$this->db->join('content_newstag nt' , 'nt.nt_id = content.nt_id' , 'left');
		$this->db->order_by('c_date' , 'DESC');
		$this->db->where('c_status' , 'Active');
		$this->db->where('c_publish_status' , 'Published');
		/*ONLY LOAD CONTENTS BEFORE TODAY*/
		$this->db->where('c_date <=' , date('Y-m-d H:i:s') );
		$this->db->stop_cache();

		$total = $this->db->count_all_results('content');
		$this->load->library('pagination');
		$config['base_url'] = site_url()."article/view_list/". $cl_id ."/";
		$config['suffix'] = "" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 4;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get('content');
		$this->db->flush_cache();

		$content = $res->result_array();
		$this->sci->assign('content' , $content);
		$this->sci->assign('paging', $this->pagination->create_links() );

		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>";
		$breadcrumb[] = "<a href='".site_url()."article' >".$content_label['cl_name']."</a>";
		$breadcrumb[] = character_limiter($content['c_title'],120);
		$this->sci->assign('breadcrumb' , $breadcrumb);

		$this->sci->da('view_list.htm');
	}


	function view($c_id=0) {
		$this->sci->assign('c_id' , $c_id);
		$this->load_sidebar();

		//get content
		$this->db->join('content_newstag nt' , 'nt.nt_id = content.nt_id' , 'left');
		$this->db->join('content_label' , 'content_label.cl_id = content.cl_id' , 'left');
		$this->db->join('user' , 'user.u_id = content.c_author_id' , 'left');
		$this->db->where('c_id' , $c_id);
		$this->db->where('content.b_id' , $this->branch_id);
		$this->db->where('c_status' , 'Active');
		$res = $this->db->get('content');
		$content = $res->row_array();
		if(!$content) { show_404(); }

		$this->sci->assign('module_title' , $content['c_title']);

		$this->sci->assign('content' , $content);
		//
		////get parent
		$this->db->where('b_id' , $this->branch_id);
		$this->db->where('c_id' , $content['c_parent_id']);
		$this->db->where('c_status' , "Active");
		$res = $this->db->get('content');
		$parent = $res->row_array();
		//
		$breadcrumb = array();
		$breadcrumb[] = "<a href='".site_url()."' >Home</a>";
		$breadcrumb[] = "<a href='".site_url()."article/view_list/".$content['cl_code']."' >".$content['cl_name']."</a>";
		if($parent) { $breadcrumb[] = $parent['c_title']; }
		$breadcrumb[] = $content['c_title'];
		$this->sci->assign('breadcrumb' , $breadcrumb);

		//assign content
		$this->sci->assign('content' , $content);



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
