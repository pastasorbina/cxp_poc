<?php

class Cart extends MY_Controller {

	var $mod_title = '';
	var $use_cookies = FALSE;
	var $userinfo;

	function __construct() {
		parent::__construct();
		$this->sci->set_room('main');
		$this->_init();
		$this->userinfo = $this->session->get_userinfo('member');
		$this->load->library('icart');
	}
 

	function debug() {
		$userinfo = $this->session->get_userinfo();
		print_r($this->userinfo);
	}
	
	function ajax_get_cart_subtotal() {
		$cart = $this->icart->get_cart();
		if($cart) {
			$return['cart_subtotal'] = price_format($cart['cart_subtotal']);
		} else {
			$return['cart_subtotal'] = 0;
		}
		
		echo json_encode($return);
	}

	function insert() {
		$p_id = $this->input->post('p_id');
		$quantity = $this->input->post('quantity');
		$pq_id = $this->input->post('size');
		$p_type = $this->input->post('p_type');
		$p_type = 'Product';
		$pr_id = $this->input->post('pr_id'); 

		$option = '';
		
		//get product
		$this->db->where('p_id' , $p_id);
		$res = $this->db->get('product');
		$product = $res->row_array();

		//if($this->icart->check_if_cart_overlimit($p_id, $quantity)) {
		//	$return['status'] = 'error';
		//	$return['msg'] = 'cannot add item to the basket, you have exceeded allowed number of items for this product';
		//	echo json_encode($return);
		//	return FALSE;
		//}
		//check if selected quantity is over the stock
		//$this->db->where('p_id' , $p_id);
		//$this->db->where('pq_id' , $pq_id);
		//$res = $this->db->get('product_quantity');
		//$product_quantity = $res->row_array();
		//if($quantity > $product_quantity['pq_quantity'] ) {
		//	$return['status'] = 'error';
		//	$return['msg'] = 'cannot add item to the basket, quantity you selected exceeded number of stock';
		//	echo json_encode($return);
		//	return FALSE;
		//}

		//if ok to submit
		$ok = $this->icart->insert($p_id, $pr_id, $pq_id, $quantity, $p_type);
		
		if( $ok ) {
			$return['status'] = 'ok';
		} else {
			$return['status'] = 'error';
			$return['msg'] = "cannot add item to cart";
		}
		
		//$return['status'] = 'error';
		//$return['msg'] = $p_id."=".$pr_id."=".$pq_id."=".$quantity."=".$p_type."=".$p_id."=";
		echo json_encode($return);
	}

	function get_cart() {
		$cart = $this->icart->get_cart();
		return $cart;
	}

	function display_cart() {
		$cart = $this->icart->get_cart();
		$this->sci->assign('cart' , $cart);
		$this->sci->d('cart_snippet.htm');
	}

	function ajax_get_cart_qty() {
		$total_qty=0;
		if($this->use_cookies == TRUE){
			$total_qty = $this->cart->total_items();
		} else {
			$cart = $this->icart->get_cart();
			foreach($cart['items'] as $tmp) { $total_qty += $tmp['cart_quantity']; }
		}
		$data['total_qty'] = $total_qty;
		print json_encode($data);
	}

	function destroy(){
		$this->cart->destroy();
	}

	function ajax_remove($id = '') {
		$ok = $this->icart->remove($id);
		//$ok = true;
		if( $ok ) {
			$cart_count = $this->mod_cart->get_cart_count();
			if($cart_count == 0){
				$return['status'] = 'ok';
				$return['substatus'] = 'cart empty';
			} else {
				$return['status'] = 'ok';
				$return['substatus'] = 'cart not empty';
			}


		} else {
			$return['status'] = 'error';
			$return['msg'] = 'error removing item';
		}
		echo json_encode($return);
	}

	function ajax_success() {
		$this->sci->d('cart_ajax_success.htm');
	}

	function ajax_reset() {
		$ok = $this->icart->reset_by_m_id($this->userinfo['m_id']);

		$ret = array();
		if($ok) {
			$ret['status'] = 'ok';
		} else {
			$ret['status'] = 'error';
		}
		echo json_encode($ret);
	}
	
	
	
	//view cart snipped
	function ajax_view_cart_snippet() {
		$cart = $this->icart->get_cart(); 
		$this->sci->assign('cart' , $cart);
		$this->sci->d('cart_snippet.htm');
	}


}
