<?php

class Cron extends CI_Controller {

	var $mod_title = '';
	var $cl_code='article';

	function __construct() {
		parent::__construct();
	}

	function sendmail(){
		$this->load->library('email');

		$this->email->from('pastasorbina@gmail.com', 'William');
		$this->email->to('pastasorbina@gmail.com');

		$this->email->subject('Email Test');
		$this->email->message('Testing the email class.');

		$this->email->send();
		echo $this->email->print_debugger();
	}

	function debug2() {
		echo "printed";
	}

	function debug() {
		$res = $this->db->get('config');
		$config = $res->result_array();
		foreach($config as $k=>$tmp) {
			echo $tmp['c_value']; echo "\r";
		}
	}

	function writetext(){
		$myFile = "./text.txt";
		$fh = fopen($myFile, 'a') or die("can't open file");
		$date = date('Y-m-d H:i:s');
		$stringData = "$date\n";
		fwrite($fh, $stringData);
		fclose($fh);
	}


	function cart_check() {
		$this->load->library('icart');

		$this->db->where('c_key' , 'cart_timeout');
		$res = $this->db->get('config');
		$config = $res->row_array();
		$cart_timeout = $config['c_value'];

		$this->db->where('cart_status' , "Active");
		$res = $this->db->get('cart');
		$cart = $res->result_array();

		foreach($cart as $k=>$tmp) {
			$now = date('Y-m-d H:i:s');
			$cart_entry = $tmp['cart_entry'];
			$deadline = date_future($cart_entry, "+".$cart_timeout." second");
			if($now > $deadline) {
				$passed = TRUE;
				$config = array();
				$config['is_auto'] = 'Yes';
				$ok = $this->icart->remove($tmp['cart_id'], $config);
			} else {
				$passed = FALSE;
			}
		}
	}

	function check_pending_confirmation() {
		$this->db->join('member' , 'member.m_id = transaction.m_id' , 'left');
		$this->db->where('trans_status' , 'Active');
		$this->db->where('trans_payment_status' , 'Unconfirmed');
		$res = $this->db->get('transaction');
		$transaction = $res->result_array();
		foreach($transaction as $k=>$tmp) {
			$now = date('Y-m-d H:i:s');
			$entry = $tmp['trans_entry'];
			$deadline = date_future($entry, "+24 hour");
			//print $tmp['trans_id']." -> ";
			//print $tmp['trans_entry']." -> ".$deadline;

			if($now < $deadline) {

				//send email
				$email = $tmp['m_email'];
				$html = $this->pending_confirmation_email($tmp['trans_id']);
				$this->load->library('email');
				$config['mailtype'] = 'html';
				$config['charset'] = 'iso-8859-1';
				$this->email->initialize($config);
				$this->email->from( $this->site_config['ec_email_from'], $this->site_config['ec_email_name'] );
				$this->email->to($email);
				$this->email->subject( 'Pengingat Pembayaran' );
				$this->email->message($html);

				$ok = $this->email->send();
			} else {
			}
			print "<hr>";
		}
	}

	function pending_confirmation_email($id=4) {
		//get bank account numbers
		$this->db->where('ba_status' , 'Active');
		$res = $this->db->get('bank_account');
		$bank_account = $res->result_array();
		$this->sci->assign('bank_account' , $bank_account);

		$this->load->model('mod_transaction');
		$transaction = $this->mod_transaction->get_transaction_by_id($id);
		$this->sci->assign('transaction' , $transaction);

		$entry = $transaction['trans_entry'];
		$deadline = date_future($entry, "+24 hour");
		$this->sci->assign('deadline' , $deadline);

		$this->db->where('m_id' , $transaction['m_id']);
		$res = $this->db->get('member');
		$member = $res->row_array();
		$this->sci->assign('member' , $member);

		$html = $this->sci->fetch('cron/remind_confirmation.htm');
		echo $html;
		return $html;
	}


}
