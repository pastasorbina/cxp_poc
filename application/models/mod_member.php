<?php

class Mod_member extends MY_Model {

	var $table_name = 'member';
	var $_table_name = 'member';
	var $id_field = 'm_id';
	var $entry_field = 'm_entry';
	var $stamp_field = 'm_stamp';
	var $status_field = 'm_status';
	var $deletion_field = 'm_deletion';
	var $order_by = 'm_entry';
	var $order_dir = 'DESC';

	function __construct(){
		parent::__construct();
	}

	function check_email_registered($email='') {
		$this->db->where('m_login' , $email);
		$this->db->where('m_status' , 'Active');
		$res = $this->db->get('member');
		$member = $res->row_array();
		if($member) {
			return $member;
		} else {
			return FALSE;
		}
	}

	function get_by_email($email=''){
		$this->db->where('m_login' , $email);
		$this->db->where('m_status' , 'Active');
		$res = $this->db->get($this->table_name);
		$member = $res->row_array();
		return $member;
	}

	function get_saldo($m_id) {
		$this->db->where('m_id' , $m_id);
		$res = $this->db->get('member');
		$result = $res->row_array();
		$saldo = $result['m_saldo']?$result['m_saldo']:0;
		return($saldo);
	}

	function topup_saldo($m_id=0, $topup_nominal=0) {
		$this->db->where('m_id', $m_id);
		$res = $this->db->get('member');
		$member = $res->row_array();

		if(!$member) { return FALSE; }

		$this->db->set('m_saldo' , $member['m_saldo'] + $topup_nominal);
		$this->db->where('m_id' , $member['m_id']);
		$ok = $this->db->update('member');

		//TODO: saldo_history here
		return $ok;
	}


}

?>
