<?php
class Motor_model extends MY_Controller {

	var $mod_title = 'Motor Model';

	var $table_name = 'motor_model';
	var $id_field = 'mm_id';
	var $status_field = 'mm_status';
	var $entry_field = 'mm_entry';
	var $stamp_field = 'mm_stamp';
	var $deletion_field = 'mm_deletion';
	var $order_field = 'mm_entry';
	var $order_dir = 'DESC';
	var $label_field = 'mm_name';

	var $author_field = 'mm_author';
	var $editor_field = 'mm_editor';

	var $search_in = array('mm_name');

	var $template_add = 'edit.htm';
	var $template_edit = 'edit.htm';


	function __construct() {
		parent::__construct();
		$this->sci->init('admin');
		$this->_init();
		$this->session->validate(array('ADMIN'), 'admin');
		$this->sci->assign('use_ajax' , FALSE);


	}

	function iteration_setting($maindata=array()) {
		foreach($maindata as $k=>$tmp) {
			$this->db->where('mm_id' , $tmp['mm_id']); 
			$ass_total = $this->db->count_all_results('tire_detail_motor');
			$maindata[$k]['ass_total'] = $ass_total;
		}
		return $maindata;
	}

	function join_setting() {
		//$this->db->join('polling_category pollc' , 'pollc.pollc_id = polling_option.pollc_id' , 'left');
		$this->db->join('motor_type mt' , 'mt.mt_id = motor_model.mt_id' , 'left');
		$this->db->join('motor_brand mb' , 'mb.mb_id = motor_model.mb_id' , 'left');
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active");

	}

	function validation_setting() {
		$this->form_validation->set_rules('mm_name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('mt_id', 'Type', 'trim|xss_clean');
		$this->form_validation->set_rules('mb_id', 'Brand', 'trim|xss_clean'); 
	}

	function database_setter() { 
		$this->db->set('mm_name' , $this->input->post('mm_name') );
		$this->db->set('mb_id' , $this->input->post('mb_id') );
		$this->db->set('mt_id' , $this->input->post('mt_id') ); 
	}


	function pre_add_edit() {
		$this->db->where('mb_status' , 'Active');
		$res = $this->db->get('motor_brand');
		$brands = $res->result_array();
		$this->sci->assign('brands' , $brands);
		
		$this->db->where('mt_status' , 'Active');
		$res = $this->db->get('motor_type');
		$types = $res->result_array();
		$this->sci->assign('types' , $types);
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}

	function get_result(){
		$fieldname = 'file';
		if (!empty($_FILES)) {
			if ($_FILES[$fieldname]['tmp_name']) {
				$code = uniqid();
				$realFile = $_FILES[$fieldname]['name'];
				$extension = end(explode(".", $realFile));
				$tempFile = $_FILES[$fieldname]['tmp_name'];
				$filename = $code . '.' . $extension;
				$targetFile =  'temp/' . $filename;
				$ok = copy($tempFile, $targetFile);
				print $targetFile;
			}
		}
		
		$arrResult = array();
		$handle = fopen("temp/".$filename, "r");
		if( $handle ) {
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) { $result[] = $data; }
			fclose($handle);
		}
		unlink($targetFile);
		return $result;
	}
	
	function truncate() {
		print 'ad';
		$query = array();
		$query[] = "truncate table tire_size";
		$query[] = "truncate table tire_rim";
		$query[] = "truncate table tire_size_rim";
		$query[] = "truncate table tire_detail_size";
		$query[] = "truncate table tire_detail_motor";
		foreach($query as $k=>$tmp){
			$this->db->query($tmp);
		} 
		redirect($this->mod_url."import");
	}
	
	function _build_import($rawdata) {
		$temp = trim($rawdata);
		$data = array();
		$temp = explode("\n", $temp);
		foreach($temp as $k=>$tmp) {
			$ra = explode("\t", $tmp);
			@list($data[$k]['t_name'], $data[$k]['ts_name'], $data[$k]['tr_name'], $data[$k]['tsc_name']) = $ra;
		} 
		return $data;
	}
	
	function import($mm_id=0) {
		$this->sci->assign('mm_id' , $mm_id);
		
		$this->db->where('mm_id' , $mm_id);
		$res = $this->db->get('motor_model');
		$motor_model = $res->row_array();
		$this->sci->assign('motor_model' , $motor_model);
		
		$this->load->library('form_validation');
		$rawdata = $this->input->post('data');
		$this->form_validation->set_rules('data','','trim'); 
		if($this->form_validation->run() == FALSE){
			$this->sci->assign('rawdata' , $rawdata);
			$this->sci->da('import.htm');
		} else {
			$data = $this->_build_import($rawdata);
			foreach($data as $k=>$tmp){
				$t_name = $data[$k]['t_name'] = trim($tmp['t_name']);
				$ts_name = $data[$k]['ts_name'] = trim($tmp['ts_name']);
				$tr_name = $data[$k]['tr_name'] = trim($tmp['tr_name']);
				$tsc_name = $data[$k]['tsc_name'] = trim($tmp['tsc_name']);
				
				$this->db->where('t_name' , $t_name);
				$res = $this->db->get('tire'); 
				$data[$k]['tire'] = $res->row_array();
				
				$this->db->where('ts_name' , $ts_name);
				$res = $this->db->get('tire_size');
				$data[$k]['size']  = $res->row_array();
				
				$this->db->where('tr_name' , $tr_name);
				$res = $this->db->get('tire_rim');
				$data[$k]['rim']  = $res->row_array(); 
			} 
			$this->sci->assign('data' , $data);
			$this->sci->assign('rawdata' , $rawdata);
			$this->sci->da('import_validate.htm');
		} 
	}
	
	function do_import() {
		$this->load->library('form_validation');
		$rawdata = $this->input->post('data');
		$mm_id = $this->input->post('mm_id');
		$this->form_validation->set_rules('data','','trim'); 
		$this->form_validation->set_rules('mm_id','','trim'); 
		if($this->form_validation->run() == FALSE){ 
		} else {
			$data = $this->_build_import($rawdata);
			foreach($data as $k=>$tmp){
				$t_name = $data[$k]['t_name'] = trim($tmp['t_name']);
				$ts_name = $data[$k]['ts_name'] = trim($tmp['ts_name']);
				$tr_name = $data[$k]['tr_name'] = trim($tmp['tr_name']);
				$tsc_name = $data[$k]['tsc_name'] = trim($tmp['tsc_name']);
				
				$this->db->where('tsc_name' , $tsc_name);
				$res = $this->db->get('tire_size_category');
				$tire_size_category = $res->row_array();
				$tsc_id = $tire_size_category['tsc_id'];
				
				$this->db->where('t_name' , $t_name);
				$res = $this->db->get('tire'); 
				$tire = $res->row_array(); 
				
				$this->db->where('ts_name' , $ts_name);
				$res = $this->db->get('tire_size');
				$size = $res->row_array();
				if(sizeof($size) > 0) { $ts_id = $size['ts_id']; } else { 
					$this->db->set('ts_name' , $ts_name); $this->db->insert('tire_size'); $ts_id = $this->db->insert_id();
				}
				
				$this->db->where('tr_name' , $tr_name);
				$res = $this->db->get('tire_rim');
				$rim = $res->row_array();
				if(sizeof($rim) > 0) { $tr_id = $rim['tr_id']; } else { 
					$this->db->set('tr_name' , $tr_name); $this->db->insert('tire_rim'); $tr_id = $this->db->insert_id();
				}
				
				//find tire_size_rim
				$this->db->where('tr_id' , $tr_id);
				$this->db->where('ts_id' , $ts_id);
				$res = $this->db->get('tire_size_rim');
				$size_rim = $res->row_array();
				if(sizeof($size_rim) > 0) { $tsr_id = $size_rim['tsr_id']; } else {
					$this->db->set('tr_id' , $tr_id); $this->db->set('ts_id' , $ts_id);
					$this->db->insert('tire_size_rim');
					$tsr_id = $this->db->insert_id();
				}
				
				///RUNNN ASSOCIATION
				$this->db->set('t_id' , $tire['t_id']);
				$this->db->set('tsr_id' , $tsr_id);
				$this->db->insert('tire_detail_size');
				$tds_id = $this->db->insert_id();
				///
				$this->db->set('mm_id' , $mm_id);
				$this->db->set('tds_id' , $tds_id);
				$this->db->set('tsc_id' , $tsc_id);
				$this->db->insert('tire_detail_motor');
				$tdm_id = $this->db->insert_id(); 
			}
			redirect($this->mod_url."import/$mm_id");
		}
	}
	
	function view_table($mm_id=0){
		$this->sci->assign('mm_id' , $mm_id);
		
		$this->db->where('mm_id' , $mm_id);
		$res = $this->db->get('motor_model');
		$motor = $res->row_array();
		$this->sci->assign('motor' , $motor);
		
		$this->db->join('tire_size_category tsc' , 'tsc.tsc_id = tire_detail_motor.tsc_id' , 'left');
		$this->db->join('tire_detail_size tds' , 'tds.tds_id = tire_detail_motor.tds_id' , 'left'); 
		$this->db->join('tire_size_rim tsr' , 'tsr.tsr_id = tds.tsr_id' , 'left');
		$this->db->join('tire_size ts' , 'ts.ts_id = tsr.ts_id' , 'left');
		$this->db->join('tire_rim tr' , 'tr.tr_id = tsr.tr_id' , 'left');
		$this->db->join('tire t' , 't.t_id = tds.t_id' , 'left');
		$this->db->where('mm_id' , $mm_id);
		$res = $this->db->get('tire_detail_motor');
		$maindata = $res->result_array();
		$this->sci->assign('maindata' , $maindata);
		
		$this->sci->da('table.htm');
	}
	
	
	function _build_import2($rawdata) {
		$temp = trim($rawdata);
		$data = array();
		$temp = explode("\n", $temp);
		foreach($temp as $k=>$tmp) {
			$ra = explode("\t", $tmp);
			@list($data[$k]['mm_name'], $data[$k]['t_name'], $data[$k]['ts_name'], $data[$k]['tr_name'], $data[$k]['tsc_name']) = $ra;
		} 
		return $data;
	}	
	
	function import2() {
		
		$this->load->library('form_validation');
		$rawdata = $this->input->post('data');
		$this->form_validation->set_rules('data','','trim'); 
		if($this->form_validation->run() == FALSE){
			$this->sci->assign('rawdata' , $rawdata);
			$this->sci->da('import2.htm');
		} else {
			$data = $this->_build_import2($rawdata);
			foreach($data as $k=>$tmp){
				$mm_name = $data[$k]['mm_name'] = trim($tmp['mm_name']);
				$t_name = $data[$k]['t_name'] = trim($tmp['t_name']);
				$ts_name = $data[$k]['ts_name'] = trim($tmp['ts_name']);
				$tr_name = $data[$k]['tr_name'] = trim($tmp['tr_name']);
				$tsc_name = $data[$k]['tsc_name'] = trim($tmp['tsc_name']);
				
				$this->db->where('mm_name' , $mm_name);
				$this->db->where('mm_status' , 'Active');
				$res = $this->db->get('motor_model'); 
				$data[$k]['model'] = $res->row_array();
				
				$this->db->where('t_name' , $t_name);
				$this->db->where('t_status' , 'Active');
				$res = $this->db->get('tire'); 
				$data[$k]['tire'] = $res->row_array();
				
				$this->db->where('ts_name' , $ts_name);
				$this->db->where('ts_status' , 'Active');
				$res = $this->db->get('tire_size');
				$data[$k]['size']  = $res->row_array();
				
				$this->db->where('tr_name' , $tr_name);
				$this->db->where('tr_status' , 'Active');
				$res = $this->db->get('tire_rim');
				$data[$k]['rim']  = $res->row_array(); 
			} 
			$this->sci->assign('data' , $data);
			$this->sci->assign('rawdata' , $rawdata);
			$this->sci->da('import2_validate.htm');
		} 
	}
	
	function do_import2() {
		$this->load->library('form_validation');
		$rawdata = $this->input->post('data'); 
		$this->form_validation->set_rules('data','','trim');  
		if($this->form_validation->run() == FALSE){ 
		} else {
			$data = $this->_build_import2($rawdata);
			foreach($data as $k=>$tmp){
				$mm_name = $data[$k]['mm_name'] = trim($tmp['mm_name']);
				$t_name = $data[$k]['t_name'] = trim($tmp['t_name']);
				$ts_name = $data[$k]['ts_name'] = trim($tmp['ts_name']);
				$tr_name = $data[$k]['tr_name'] = trim($tmp['tr_name']);
				$tsc_name = $data[$k]['tsc_name'] = trim($tmp['tsc_name']);
				
				$this->db->where('mm_name' , $mm_name);
				$this->db->where('mm_status' , 'Active');
				$res = $this->db->get('motor_model'); 
				$motor_model = $res->row_array();
				$mm_id = $motor_model['mm_id'];
				
				$this->db->where('tsc_name' , $tsc_name);
				$this->db->where('tsc_status' , 'Active');
				$res = $this->db->get('tire_size_category');
				$tire_size_category = $res->row_array();
				$tsc_id = $tire_size_category['tsc_id'];
				
				$this->db->where('t_name' , trim($t_name));
				$this->db->where('t_status' , 'Active');
				$res = $this->db->get('tire'); 
				$tire = $res->row_array();
				$t_id = $tire['t_id'];
				
				$this->db->where('ts_name' , trim($ts_name));
				$this->db->where('ts_status' , 'Active');
				$res = $this->db->get('tire_size');
				$size = $res->row_array();
				if(sizeof($size) > 0) {
					$ts_id = $size['ts_id'];
				} else { 
					$this->db->set('ts_name' , $ts_name); $this->db->insert('tire_size'); $ts_id = $this->db->insert_id();
				}
				
				$this->db->where('tr_name' , $tr_name);
				$this->db->where('tr_status' , 'Active');
				$res = $this->db->get('tire_rim');
				$rim = $res->row_array();
				if(sizeof($rim) > 0) {
					$tr_id = $rim['tr_id'];
					} else { 
					$this->db->set('tr_name' , $tr_name); $this->db->insert('tire_rim'); $tr_id = $this->db->insert_id();
				}
				
				//find tire_size_rim
				$this->db->where('tr_id' , $tr_id);
				$this->db->where('ts_id' , $ts_id);
				$this->db->where('tsr_status' , 'Active');
				$res = $this->db->get('tire_size_rim');
				$size_rim = $res->row_array();
				if(sizeof($size_rim) > 0) { $tsr_id = $size_rim['tsr_id']; } else {
					$this->db->set('tr_id' , $tr_id); $this->db->set('ts_id' , $ts_id);
					$this->db->insert('tire_size_rim');
					$tsr_id = $this->db->insert_id();
				}
				
				///RUNNN ASSOCIATION
				$this->db->where('t_id' , $t_id);
				$this->db->where('tsr_id' , $tsr_id);
				$this->db->where('tds_status' , 'Active');
				$res = $this->db->get('tire_detail_size');
				$detail_size = $res->row_array();
				if(sizeof($detail_size) > 0) { $tds_id = $detail_size['tds_id']; } else {
					$this->db->set('t_id' , $t_id);
					$this->db->set('tsr_id' , $tsr_id);
					$this->db->insert('tire_detail_size');
					$tds_id = $this->db->insert_id();	
				}
				
				///
				$this->db->set('mm_id' , $mm_id);
				$this->db->set('tds_id' , $tds_id);
				$this->db->set('tsc_id' , $tsc_id);
				$this->db->insert('tire_detail_motor');
				$tdm_id = $this->db->insert_id(); 
			}
			redirect($this->mod_url."import2/");
		}
	}



}
