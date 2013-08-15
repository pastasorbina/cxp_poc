<?php
class Tire extends MY_Controller {

	var $mod_title = 'Manage Tires';

	var $table_name = 'tire';
	var $id_field = 't_id';
	var $status_field = 't_status';
	var $entry_field = 't_entry';
	var $stamp_field = 't_stamp';
	var $deletion_field = 't_deletion';
	var $order_field = 't_entry';
	var $order_dir = 'DESC';
	var $label_field = 't_name';

	var $author_field = 't_author';
	var $editor_field = 't_editor';

	var $search_in = array('t_name');

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

	function iteration_setting($maindata=array()) {
		return $maindata;
	}

	function join_setting() {
		$this->db->join('tire_category tc' , 'tc.tc_id = tire.tc_id' , 'left');
	}

	function where_setting() {
		$this->db->where($this->status_field ,"Active");

	}

	function validation_setting() {
		$this->form_validation->set_rules('t_name', 'Name (IND)', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('t_name_en', 'Name (EN)', 'trim|xss_clean'); 
		$this->form_validation->set_rules('t_desc', 'Desc (IND)', 'trim'); 
		$this->form_validation->set_rules('t_desc_en', 'Desc (EN)', 'trim');
		$this->form_validation->set_rules('t_brief', 'Brief (IND)', 'trim'); 
		$this->form_validation->set_rules('t_brief_en', 'Brief (EN)', 'trim'); 
		$this->form_validation->set_rules('t_order', 'Order', 'trim|xss_clean');
		$this->form_validation->set_rules('t_is_featured', 'Is Featured', 'trim|xss_clean');
		$this->form_validation->set_rules('t_is_new', 'Is New', 'trim|xss_clean');
		$this->form_validation->set_rules('tc_id', 'Category', 'trim|xss_clean');
		$this->form_validation->set_rules('t_overview', 'Overview', 'trim');
		$this->form_validation->set_rules('t_sizetable', 'Size Table', 'trim');
		$this->form_validation->set_rules('t_feature_caption', 'Feature Caption', 'trim');
	}

	function database_setter() { 
		$this->db->set('t_name' , $this->input->post('t_name') ); 
		$this->db->set('t_name_en' , $this->input->post('t_name_en') ); 
		$this->db->set('t_desc' , $this->input->post('t_desc') ); 
		$this->db->set('t_desc_en' , $this->input->post('t_desc_en') ); 
		$this->db->set('t_brief' , $this->input->post('t_brief') ); 
		$this->db->set('t_brief_en' , $this->input->post('t_brief_en') ); 
		$this->db->set('t_order' , $this->input->post('t_order') );
		$this->db->set('t_is_featured' , $this->input->post('t_is_featured') );
		$this->db->set('t_is_new' , $this->input->post('t_is_new') );
		$this->db->set('tc_id' , $this->input->post('tc_id') );
		$this->db->set('t_overview' , $this->input->post('t_overview') );
		$this->db->set('t_sizetable' , $this->input->post('t_sizetable') );
		$this->db->set('t_feature_caption' , $this->input->post('t_feature_caption') );
		
		if($_FILES['t_image']['name'] != '' ) {
			$filename = $this->_upload_image('t_image');
			$this->db->set('t_image' , $filename);
		}
		if($_FILES['t_banner']['name'] != '' ) {
			$filename = $this->_upload_image('t_banner');
			$this->db->set('t_banner' , $filename);
		}
	}


	function pre_add_edit() {
		$this->db->where('tc_status' , 'Active');
		$res = $this->db->get('tire_category');
		$category = $res->result_array();
		$this->sci->assign('category' , $category);
	}

	function pre_add() {
	}

	function pre_edit($id=0) {
	}
	
	
	function add() {
		$this->sci->assign('ajax_action' , 'add');
		$this->pre_add_edit();
		$this->pre_add();

		$this->load->library('form_validation');
		$this->validation_setting('add');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da($this->template_add);
		}else{
			$this->database_setter('add');
			$this->db->set($this->entry_field , 'NOW()', FALSE );
			$ok = $this->db->insert($this->table_name);
			$insert_id = $this->db->insert_id();
			$this->post_add($insert_id);
			$this->post_add_edit($insert_id);
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url.'add');
			} else {
				$this->session->set_confirm(1);
				$insert_id = $this->db->insert_id();
				redirect($this->mod_url."view/$insert_id");
			}
		}
	}

	/**
	 * Edit
	 * @access public
	 *
	 */
	function edit( $id=0 ) {
		$this->sci->assign('ajax_action' , 'edit');
		$this->pre_add_edit();
		$this->join_setting();
		$this->db->where($this->id_field , $id);
		$res = $this->db->get($this->table_name);
		$data = $res->row_array();
		$this->sci->assign('data' , $data);
		$this->pre_edit($id);

		$this->load->library('form_validation');
		$this->validation_setting('edit');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da($this->template_edit);
		}else{
			$this->database_setter('edit');
			$this->db->where($this->id_field , $id);
			$ok = $this->db->update($this->table_name);
			$this->post_edit($id);
			$this->post_add_edit($id);
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."edit/$id");
			} else {
				$this->session->set_confirm(1);
				redirect($this->mod_url."view/$id");
			}
		}
	}
	
	
	function view($t_id=0) {
		$this->db->where('t_id' , $t_id);
		$this->join_setting() ;
		$res = $this->db->get('tire');
		$tire = $res->row_array();
		$this->sci->assign('tire' , $tire);
		
		//get this size detail
		$this->db->join('tire_size_rim tsr' , 'tsr.tsr_id = tire_detail_size.tsr_id' , 'left'); 
		$this->db->join('tire_size ts' , 'ts.ts_id = tsr.ts_id' , 'left');
		$this->db->join('tire_rim tr' , 'tr.tr_id = tsr.tr_id' , 'left');
		$this->db->join('tire_size_category tsc' , 'tsc.tsc_id = tsr.tsc_id' , 'left'); 
		$this->db->where('t_id' , $t_id);
		$this->db->where('tds_status' , 'Active');
		$res = $this->db->get('tire_detail_size');
		$detail_size = $res->result_array();
		$this->sci->assign('detail_size' , $detail_size);
		
		$this->sci->da('view.htm');
	}
	
	function ajax_load_detail_motor_list($tds_id=0) {
		$this->sci->assign('tds_id' , $tds_id);
		
		$this->db->where('tds_id' , $tds_id); 
		$this->db->join('tire_size_rim tsr' , 'tsr.tsr_id = tire_detail_size.tsr_id' , 'left'); 
		$this->db->join('tire_size ts' , 'ts.ts_id = tsr.ts_id' , 'left');
		$this->db->join('tire_rim tr' , 'tr.tr_id = tsr.tr_id' , 'left');
		
		$res = $this->db->get('tire_detail_size');
		$this_detail_size = $res->row_array();
		$this->sci->assign('this_detail_size' , $this_detail_size); 
		
		$this->db->join('motor_model mm' , 'mm.mm_id = tire_detail_motor.mm_id' , 'left');
		$this->db->join('motor_type mt' , 'mt.mt_id = mm.mt_id' , 'left');
		 $this->db->join('motor_brand mb' , 'mb.mb_id = mm.mb_id' , 'left'); 
		$this->db->join('tire_detail_size tds' , 'tds.tds_id = tire_detail_motor.tds_id' , 'left');
		$this->db->join('tire_size_category tsc' , 'tsc.tsc_id = tire_detail_motor.tsc_id' , 'left');
		$this->db->where('tdm_status' , 'Active');
		$this->db->where('tire_detail_motor.tds_id' , $tds_id);
		$res = $this->db->get('tire_detail_motor');
		$maindata = $res->result_array();
		$this->sci->assign('maindata' , $maindata);
		 
		$this->sci->d('detail_motor/list.htm');
	}
	
	function ajax_load_detail_motor_edit($tds_id=0) {
		$this->sci->assign('action' , 'edit'); 
		$this->sci->assign('tds_id' , $tds_id);
		
		//get tire size category
		$this->db->where('tsc_status' , 'Active');
		$this->db->order_by('tsc_order' , 'asc');
		$res = $this->db->get('tire_size_category');
		$size_category = $res->result_array();
		$this->sci->assign('size_category' , $size_category);
		 
		$this->db->where('tdm_status' , 'Active');
		$this->db->where('tds_id' , $tds_id);
		$this->db->select('mm_id');
		$res = $this->db->get('tire_detail_motor');
		$amotors = $res->result_array();
		$current_motors = array();
		foreach($amotors as $k=>$tmp) {
			$current_motors[] = $tmp['mm_id'];
		}
		
		$this->db->join('motor_type mt' , 'mt.mt_id = motor_model.mt_id' , 'left');
		$this->db->join('motor_brand mb' , 'mb.mb_id = motor_model.mb_id' , 'left'); 
		$this->db->where('mm_status' , 'Active'); 
		$res = $this->db->get('motor_model');
		$tire_motor = $res->result_array();
		if(sizeof($current_motors) > 0) {
			foreach($tire_motor as $k=>$tmp) {
				//get tiresize for this
				$this->db->where('tds_id' , $tds_id);
				$this->db->where('mm_id' , $tmp['mm_id']);
				$res = $this->db->get('tire_detail_motor');
				$tdm = $res->row_array(); 
				$tire_motor[$k]['tdm'] = $tdm;
				//if(in_array($tmp['mm_id'], $current_motors)){ 
				//	$tire_motor[$k]['on'] = 'Yes';
				//} else {
				//	$tire_motor[$k]['on'] = 'No';
				//}
			} 
		} 
		$this->sci->assign('tire_motor' , $tire_motor);
		
		
		$this->load->library('form_validation'); 
		$this->sci->d('detail_motor/edit.htm');
	}
	
	function get_tsc_option($mm_id){
		$this->sci->assign('mm_id' , $mm_id);
		
		$this->db->where('tsc_status' , 'Active');
		$this->db->order_by('tsc_order' , 'asc');
		$res = $this->db->get('tire_size_category');
		$size_category = $res->result_array();
		foreach($size_category as $k=>$tmp){
			//get tdm
			$this->db->where('mm_id' , $mm_id);
			$this->db->where('tsc_id' , $tmp['tsc_id']);
			$res = $this->db->get('tire_detail_motor');
			$tdm = $res->row_array();
			$size_category[$k]['tdm'] = $tdm;
			
		}
		$this->sci->assign('size_category' , $size_category);
		$this->sci->d('detail_motor/tsc_option.htm');
	}
	
	
	function ajax_submit_detail_motor() {
		$action = $this->input->post('action');
		$tds_id = $this->input->post('tds_id'); 
		$mm_id = $this->input->post('mm_id'); 
		$tsc_id = $this->input->post('tsc_id');
		
		$this->load->library('form_validation'); 
		$this->form_validation->set_rules('mm_id', 'motor', '');   
		$this->form_validation->set_rules('tsc_id', 'Size Category', '');   
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{ 
			$this->db->where('tds_id' , $tds_id);
			$this->db->delete('tire_detail_motor');
			foreach($mm_id as $k=>$tmp) {
				$this->db->set('tds_id' , $tds_id);
				$this->db->set('mm_id' , $tmp);
				$this->db->set('tsc_id' , $tsc_id[$k]);
				$this->db->set('tdm_entry' , 'NOW()', FALSE);
				$this->db->insert('tire_detail_motor'); 
			}
			$data['status'] = 'ok';
			$data['msg'] = 'ok'; 
		} 
		print json_encode($data);
	}
	
	
	
	
	
	function ajax_load_detail_tech_list($t_id=0) {
		$this->db->join('tire t' , 't.t_id = tire_detail_tech.t_id' , 'left');
		$this->db->join('tire_tech tth' , 'tth.tth_id = tire_detail_tech.tth_id' , 'left'); 
		$this->db->where('tdth_status' , 'Active');
		$this->db->where('tire_detail_tech.t_id' , $t_id);
		$res = $this->db->get('tire_detail_tech');
		$maindata = $res->result_array();
		$this->sci->assign('maindata' , $maindata);
		 
		$this->sci->d('detail_tech/list.htm');
	}
	 
	function ajax_load_detail_tech_edit($t_id=0) {
		$this->sci->assign('action' , 'edit'); 
		$this->sci->assign('t_id' , $t_id);
		 
		$this->db->where('tdth_status' , 'Active');
		$this->db->where('t_id' , $t_id);
		$this->db->select('tth_id');
		$res = $this->db->get('tire_detail_tech');
		$atechs = $res->result_array();
		$current_techs = array();
		foreach($atechs as $k=>$tmp) {
			$current_techs[] = $tmp['tth_id'];
		}
		 
		$this->db->where('tth_status' , 'Active');
		$this->db->order_by('tth_order' , 'asc');
		$res = $this->db->get('tire_tech');
		$tire_tech = $res->result_array();
		if(sizeof($current_techs) > 0) {
			foreach($tire_tech as $k=>$tmp) {
				if(in_array($tmp['tth_id'], $current_techs)){ 
					$tire_tech[$k]['on'] = 'Yes';
				} else {
					$tire_tech[$k]['on'] = 'No';
				}
			} 
		} 
		$this->sci->assign('tire_tech' , $tire_tech);
		
		
		$this->load->library('form_validation'); 
		$this->sci->d('detail_tech/edit.htm');
	}
	
	function ajax_submit_detail_tech() {
		$action = $this->input->post('action');
		$t_id = $this->input->post('t_id'); 
		$tth_id = $this->input->post('tth_id'); 
		$this->load->library('form_validation'); 
		$this->form_validation->set_rules('tth_id', 'Tag', '');   
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{
			$this->db->where('t_id' , $t_id);
			$this->db->delete('tire_detail_tech');
			foreach($tth_id as $k=>$tmp) {
				$this->db->set('t_id' , $t_id);
				$this->db->set('tth_id' , $tmp);
				$this->db->set('tdth_entry' , 'NOW()', FALSE);
				$this->db->insert('tire_detail_tech');
			}
			$data['status'] = 'ok';
			$data['msg'] = 'ok'; 
		} 
		print json_encode($data);
	}
	
	
	
	
	
	
	
	
	function ajax_load_detail_tag_list($t_id=0) {
		$this->db->join('tire t' , 't.t_id = tire_detail_tag.t_id' , 'left');
		$this->db->join('tire_tag tt' , 'tt.tt_id = tire_detail_tag.tt_id' , 'left');
		$this->db->join('tire_tag_category ttc' , 'ttc.ttc_id = tt.ttc_id' , 'left');
		$this->db->where('tdt_status' , 'Active');
		$this->db->where('tire_detail_tag.t_id' , $t_id);
		$res = $this->db->get('tire_detail_tag');
		$maindata = $res->result_array();
		$this->sci->assign('maindata' , $maindata);
		
		
		$this->sci->d('detail_tag/list.htm');
	}
	
	function ajax_load_detail_tag_edit($t_id=0) {
		$this->sci->assign('action' , 'edit'); 
		$this->sci->assign('t_id' , $t_id);
		 
		$this->db->where('tdt_status' , 'Active');
		$this->db->where('t_id' , $t_id);
		$this->db->select('tt_id');
		$res = $this->db->get('tire_detail_tag');
		$atags = $res->result_array();
		$current_tags = array();
		foreach($atags as $k=>$tmp) {
			$current_tags[] = $tmp['tt_id'];
		}
		 
		$this->db->where('ttc_status' , 'Active');
		$this->db->order_by('ttc_order' , 'asc');
		$res = $this->db->get('tire_tag_category');
		$tire_tag_category = $res->result_array();
		foreach($tire_tag_category as $k=>$tmp) {
			$this->db->where('ttc_id' , $tmp['ttc_id']);
			$this->db->where('tt_status' , 'Active');
			$this->db->order_by('tt_order' , 'asc');
			$res = $this->db->get('tire_tag');
			$tire_tag_category[$k]['tire_tag'] = $res->result_array();
			if(sizeof($current_tags) > 0) {
				foreach($tire_tag_category[$k]['tire_tag'] as $k2 => $tmp2) {
					if(in_array($tmp2['tt_id'], $current_tags)){ 
						$tire_tag_category[$k]['tire_tag'][$k2]['on'] = 'Yes';
					} else {
						$tire_tag_category[$k]['tire_tag'][$k2]['on'] = 'No';
					}
				}
			}
		} 
		$this->sci->assign('tire_tag_category' , $tire_tag_category);
		
		
		$this->load->library('form_validation'); 
		$this->sci->d('detail_tag/edit.htm');
	}
	
	function ajax_submit_detail_tag() {
		$action = $this->input->post('action');
		$t_id = $this->input->post('t_id'); 
		$tt_id = $this->input->post('tt_id'); 
		$this->load->library('form_validation'); 
		$this->form_validation->set_rules('tt_id', 'Tag', '');   
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{
			$this->db->where('t_id' , $t_id);
			$this->db->delete('tire_detail_tag');
			foreach($tt_id as $k=>$tmp) {
				$this->db->set('t_id' , $t_id);
				$this->db->set('tt_id' , $tmp);
				$this->db->set('tdt_entry' , 'NOW()', FALSE);
				$this->db->insert('tire_detail_tag');
			}
			$data['status'] = 'ok';
			$data['msg'] = 'ok'; 
		} 
		print json_encode($data);
	}
	
	
	
	
	
	function ajax_load_detail_rating_list($t_id=0) {
		$this->db->join('tire' , 'tire.t_id = tire_detail_rating.t_id' , 'left');
		$this->db->join('tire_rating trt' , 'trt.trt_id = tire_detail_rating.trt_id' , 'left'); 
		$this->db->join('tire_rating_category trtc' , 'trtc.trtc_id = trt.trtc_id' , 'left'); 
		$this->db->where('tdr_status' , 'Active');
		$this->db->where('tire_detail_rating.t_id' , $t_id);
		$res = $this->db->get('tire_detail_rating');
		$maindata = $res->result_array();
		$this->sci->assign('maindata' , $maindata);
		
		$this->sci->d('detail_rating/list.htm');
	}
	
	function ajax_load_detail_rating_addedit($action='add', $tdr_id=0, $t_id=0) {
		$this->sci->assign('action' , $action);
		$this->sci->assign('tdr_id' , $tdr_id);
		$this->sci->assign('t_id' , $t_id);
		
		$this->db->join('tire_rating_category trtc' , 'trtc.trtc_id = tire_rating.trtc_id' , 'left'); 
		$this->db->where('trt_status' , 'Active');
		$this->db->order_by('trtc.trtc_order' , 'asc');
		$res = $this->db->get('tire_rating');
		$tire_rating = $res->result_array();
		$this->sci->assign('tire_rating' , $tire_rating);
		 
		
		if($tdr_id != 0) {
			$this->db->where('tdr_id' , $tdr_id);
			$res = $this->db->get('tire_detail_rating');
			$data = $res->row_array();
			$this->sci->assign('data' , $data);
		}
		
		$this->load->library('form_validation'); 
		$this->sci->d('detail_rating/edit.htm');
	}
	
	function ajax_submit_detail_rating_addedit() {
		$action = $this->input->post('action');
		$t_id = $this->input->post('t_id');
		$tdr_id = $this->input->post('tdr_id');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('trt_id', 'Rating', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('tdr_value', 'Rating Value', 'trim|required|xss_clean');  
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{
			$this->db->set('trt_id' , $this->input->post('trt_id') );
			$this->db->set('tdr_value' , $this->input->post('tdr_value') );
			$this->db->set('t_id' , $this->input->post('t_id') );
			if($action == 'add') {
				$this->db->set('tdr_entry' , 'NOW()', false);
				$this->db->insert('tire_detail_rating');  
			}elseif($action == 'edit') {
				$this->db->where('tdr_id' , $tdr_id);
				$this->db->update('tire_detail_rating');
			}
			$data['status'] = 'ok';
			$data['msg'] = 'ok'; 
		}
		print json_encode($data);
	}
	
	function ajax_delete_detail_rating($tdr_id=0) {
		$this->db->where('tdr_id' , $tdr_id);
		$this->db->set('tdr_status' , 'Deleted');
		$this->db->set('tdr_deletion' , 'NOW()', FALSE);
		$this->db->update('tire_detail_rating');
		$data['status'] = 'ok';
		$data['msg'] = 'ok';
		print json_encode($data);
	}
	
	
	
	
	
	
	
	function ajax_load_detail_size_list($t_id=0) {
		$this->db->join('tire_size_rim tsr' , 'tsr.tsr_id = tire_detail_size.tsr_id' , 'left'); 
		$this->db->join('tire_rim tr' , 'tr.tr_id = tsr.tr_id' , 'left');
		$this->db->join('tire_size ts' , 'ts.ts_id = tsr.ts_id' , 'left');
		
		$this->db->where('tds_status' , 'Active');
		$this->db->where('tire_detail_size.t_id' , $t_id);  
		$res = $this->db->get('tire_detail_size');
		$maindata = $res->result_array();
		//foreach($maindata as $k=>$tmp) { 
		//	$this->db->where('ts_id' , $tmp['ts_id']);
		//	$res = $this->db->get('tire_size');
		//	$maindata[$k]['tire_size'] = $res->row_array();
		//	$this->db->where('tr_id' , $tmp['tr_id']);
		//	$res = $this->db->get('tire_rim');
		//	$maindata[$k]['tire_rim'] = $res->row_array();
		//}
		$this->sci->assign('maindata' , $maindata); 
		$this->sci->d('detail_size/list.htm');
	}
	
	function ajax_load_detail_size_addedit($action='add', $tds_id=0, $t_id=0) {
		$this->sci->assign('action' , $action);
		$this->sci->assign('tds_id' , $tds_id);
		$this->sci->assign('t_id' , $t_id);
		
		$this->db->where('tr_status' , 'Active');
		$this->db->order_by('tr_order' , 'asc');
		$res = $this->db->get('tire_rim');
		$tire_rim = $res->result_array();
		$this->sci->assign('tire_rim' , $tire_rim);
		
		$this->db->where('ts_status' , 'Active');
		$this->db->order_by('ts_order' , 'asc');
		$res = $this->db->get('tire_size');
		$tire_size = $res->result_array();
		$this->sci->assign('tire_size' , $tire_size);
		
		if($tds_id != 0) {
			$this->db->where('tds_id' , $tds_id);
			$res = $this->db->get('tire_detail_size');
			$data = $res->row_array();
			$this->sci->assign('data' , $data);
		}
		
		$this->load->library('form_validation'); 
		$this->sci->d('detail_size/edit.htm');
	}
	
	function ajax_submit_detail_size_addedit() {
		$action = $this->input->post('action');
		$t_id = $this->input->post('t_id');
		$tds_id = $this->input->post('tds_id');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('tr_id', 'Rim', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('ts_id', 'Size', 'trim|required|xss_clean');  
		if($this->form_validation->run() == FALSE ) {
			$data['status'] = 'error';
			$data['msg'] = strip_tags(validation_errors());
		}else{
			$this->db->set('tr_id' , $this->input->post('tr_id') );
			$this->db->set('ts_id' , $this->input->post('ts_id') );
			$this->db->set('t_id' , $this->input->post('t_id') );
			if($action == 'add') {
				$this->db->set('tds_entry' , 'NOW()', false);
				$this->db->insert('tire_detail_size');  
			}elseif($action == 'edit') {
				$this->db->where('tds_id' , $tds_id);
				$this->db->update('tire_detail_size');
			}
			$data['status'] = 'ok';
			$data['msg'] = 'ok'; 
		}
		print json_encode($data);
	}
	
	function ajax_delete_detail_size($tds_id=0) {
		$this->db->where('tds_id' , $tds_id);
		$this->db->set('tds_status' , 'Deleted');
		$this->db->set('tds_deletion' , 'NOW()', FALSE);
		$this->db->update('tire_detail_size');
		$data['status'] = 'ok';
		$data['msg'] = 'ok';
		print json_encode($data);
	}
	
	
	
	
	function ajax_load_tire_gallery($t_id=0) { 
		$this->db->where('tg_status' , 'Active');
		$this->db->where('t_id' , $t_id);
		$res = $this->db->get('tire_gallery');
		$maindata = $res->result_array();
		$this->sci->assign('maindata' , $maindata); 
		$this->sci->d('tire_gallery/list.htm');
	}
	
	function _tg_rules(){
		$this->form_validation->set_rules('tg_title', 'Title', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('tg_order', 'Order', 'trim'); 
		$this->form_validation->set_rules('t_id', 'Tire ID', 'trim'); 
	}

	function _tg_db() { 
		$tg_title = $this->input->post('tg_title');
		$this->db->set('tg_title' , $tg_title);  
		$this->db->set('t_id' , $this->input->post('t_id') );  
		$this->db->set('tg_order' , $this->input->post('tg_order') );   
		
		if($_FILES['tg_file']['name'] != '' ) {
			$filename = $this->_upload_image('tg_file', TRUE);
			$this->db->set('tg_file' , $filename);
		}
	}
	
	 function add_tire_gallery($t_id=0) {
		$this->sci->assign('ajax_action' , 'add');
		$this->sci->assign('t_id' , $t_id); 
		
		$this->db->where('t_id' , $t_id);
		$res = $this->db->get('tire');
		$tire = $res->row_array();
		$this->sci->assign('tire' , $tire);
		  
		$this->load->library('form_validation');
		$this->_tg_rules('add');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da("tire_gallery/edit.htm");
		}else{
			$this->_tg_db('add');
			$this->db->set("tg_entry" , 'NOW()', FALSE );
			$ok = $this->db->insert("tire_gallery");
			$insert_id = $this->db->insert_id(); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."add_tire_gallery/$t_id");
			} else {
				$this->session->set_confirm(1); 
				redirect($this->mod_url."view/$t_id");
			}
		}
	}
	
	function edit_tire_gallery($tg_id=0) {
		$this->sci->assign('ajax_action' , 'add');
		$this->sci->assign('tg_id' , $tg_id);
		
		$this->db->where('tg_id' , $tg_id);
		$res = $this->db->get('tire_gallery');
		$tire_gallery = $res->row_array();
		$this->sci->assign('tire_gallery' , $tire_gallery);
		
		$t_id = $tire_gallery['t_id'];
		$this->db->where('t_id' , $t_id);
		$res = $this->db->get('tire');
		$tire = $res->row_array();
		$this->sci->assign('tire' , $tire);
		  
		$this->load->library('form_validation');
		$this->_tg_rules('edit');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da("tire_gallery/edit.htm");
		}else{
			$this->_tg_db('edit');
			$this->db->where('tg_id' , $tg_id);
			$ok = $this->db->update("tire_gallery");
			$insert_id = $this->db->insert_id(); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."edit_tire_gallery/$tg_id");
			} else {
				$this->session->set_confirm(1); 
				redirect($this->mod_url."view/$t_id");
			}
		}
	}
	
	function delete_tire_gallery($tg_id=0) {
		$this->db->set('tg_status' , 'Deleted');
		$this->db->where('tg_id' , $tg_id);
		$this->db->update('tire_gallery');
		redirect($this->mod_url."view/$t_id"); 
	}
	
	
	
	
	
	
	
	
	
	
	function view_size($t_id=0) {
		$this->db->where('t_id' , $t_id);
		$res = $this->db->get('tire');
		$tire = $res->row_array();
		$this->sci->assign('tire' , $tire);
		
		//get this size detail
		$this->db->join('tire_size_rim tsr' , 'tsr.tsr_id = tire_detail_size.tsr_id' , 'left'); 
		$this->db->join('tire_size ts' , 'ts.ts_id = tsr.ts_id' , 'left');
		$this->db->join('tire_rim tr' , 'tr.tr_id = tsr.tr_id' , 'left'); 
		$this->db->where('t_id' , $t_id);
		$this->db->where('tds_status' , 'Active');
		$this->db->order_by('tr_name' , 'asc');
		$res = $this->db->get('tire_detail_size');
		$detail_size = $res->result_array();
		$this->sci->assign('detail_size' , $detail_size);
		
		//$this->db->join('tire_size_rim tsr' , 'tsr.tsr_id = tire_detail_size.tsr_id' , 'left'); 
		//$this->db->where('tds_status' , 'Active');
		//$this->db->where('tire_detail_size.t_id' , $t_id);
		//$this->db->group_by('tire_detail_size.tsr_id');
		//  
		//$res = $this->db->get('tire_detail_size');
		//$detail_size = $res->result_array();
		//foreach($detail_size as $k=>$tmp) { 
		//	$this->db->where('ts_id' , $tmp['ts_id']);
		//	$res = $this->db->get('tire_size');
		//	$detail_size[$k]['tire_size'] = $res->row_array();
		//	$this->db->where('tr_id' , $tmp['tr_id']);
		//	$res = $this->db->get('tire_rim');
		//	$detail_size[$k]['tire_rim'] = $res->row_array();
		//}
		//$this->sci->assign('detail_size' , $detail_size); 
		 
		
		//get all tiresizerim
		$this->db->join('tire_size ts' , 'ts.ts_id = tire_size_rim.ts_id' , 'left');
		$this->db->join('tire_rim tr' , 'tr.tr_id = tire_size_rim.tr_id' , 'left'); 
		$this->db->order_by('tr_name' , 'asc');
		$this->db->where('tsr_status' , 'Active');
		$res = $this->db->get('tire_size_rim');
		$tire_size_rim = $res->result_array();
		$this->sci->assign('tire_size_rim' , $tire_size_rim);
		
		$this->sci->da('view_size.htm');
	}
	
	function submit_add_size() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('t_id', 'T_ID', 'trim'); 
		$this->form_validation->set_rules('tsr_id', 'Size Rim', 'trim'); 
		$t_id = $this->input->post('t_id');
		$tsr_id = $this->input->post('tsr_id');
		
		$this->db->set('t_id' , $t_id);
		$this->db->set('tsr_id' , $tsr_id);
		$this->db->set('tds_entry' , date('Y-m-d H:i:s') );
		$this->db->insert('tire_detail_size');
		redirect($this->mod_url."view_size/$t_id");
	}
	
	function delete_size($tds_id = 0) {
		$this->db->where('tds_id' , $tds_id);
		$res = $this->db->get('tire_detail_size');
		$detail_size = $res->row_array();
		
		$this->db->where('tds_id' , $tds_id);
		$this->db->delete('tire_detail_size');
		redirect($this->mod_url."view_size/".$detail_size['t_id']);
	}

}
