<?php

class Inquiry_preorder extends MY_Controller {

	var $mod_title = 'Form';

	function __construct() {
		parent::__construct();
		$this->sci->init('main');
		$this->_init();
		$this->load->helper('captcha');
		$this->userinfo = $this->session->get_userinfo('member');
	}


	function view_form($p_id=0, $pq_id=0) {
		$this->sci->assign('p_id' , $p_id);
		$this->sci->assign('pq_id' , $pq_id);

		$this->db->join('brand br' , 'br.br_id = product.br_id' , 'left');
		$this->db->where('p_id' , $p_id);
		$res = $this->db->get('product');
		$product = $res->row_array();
		$this->sci->assign('product' , $product);

		$this->db->where('p_id' , $p_id);
		$this->db->where('pq_id' , $pq_id);
		$res = $this->db->get('product_quantity');
		$product_quantity = $res->row_array();
		$this->sci->assign('product_quantity' , $product_quantity);

		if($this->userinfo) {
			$email = $this->userinfo['m_email'];
			$this->sci->assign('email' , $email);
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('email', 'Email', 'trim|email|xss_clean|required');
		$this->form_validation->set_rules('p_id', 'product', 'trim|xss_clean|required');
		$this->form_validation->set_rules('pq_id', 'Size', 'trim|xss_clean|required');

		if ($this->form_validation->run() == FALSE) {
			$this->sci->d('form.htm');
		} else {
		}
	}

	function submit() {

		$this->load->library('form_validation');

		$this->form_validation->set_rules('email', 'Email', 'trim|email|xss_clean|required');
		$this->form_validation->set_rules('p_id', 'product', 'trim|xss_clean|required');
		$this->form_validation->set_rules('pq_id', 'Size', 'trim|xss_clean|required');

		$ret = array();
		if ($this->form_validation->run() == FALSE) {
			$ret['status'] = 'error';
			$ret['msg'] = strip_tags(validation_errors());
		} else {

			$p_id = $this->input->post('p_id');
			$pq_id = $this->input->post('pq_id');
			$email = $this->input->post('email');

			if($this->userinfo) {
				$this->db->where('m_login' , $email);
				$res = $this->db->get('member');
				$member = $res->row_array();
				if($member) { $this->db->set('m_id' , $member['m_id']); }
			}
			$this->db->set('pq_id' , $p_id);
			$this->db->set('p_id' , $p_id);
			$this->db->set('inqp_email' , $email);
			$this->db->set('inqp_entry' , "NOW()", FALSE);
			$this->db->insert('inquiry_preorder');

			$ret['status'] = 'ok';
		}
		echo json_encode($ret);
	}



	function success() {
		$this->sci->d('success.htm');
	}

 
}

?>
