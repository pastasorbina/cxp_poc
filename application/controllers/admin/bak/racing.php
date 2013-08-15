<?php
class Racing extends MY_Controller {

	var $mod_title = '';

	var $table_name = 'racing';
	var $id_field = 'mb_id';
	var $status_field = 'mb_status';
	var $entry_field = 'mb_entry';
	var $stamp_field = 'mb_stamp';
	var $deletion_field = 'mb_deletion';
	var $order_field = 'mb_entry';
	var $order_dir = 'DESC';
	var $label_field = 'mb_name';

	var $author_field = 'mb_author';
	var $editor_field = 'mb_editor';

	var $search_in = array('mb_name');

	var $template_add = 'edit.htm';
	var $template_edit = 'edit.htm';


	function __construct() {
		parent::__construct();
		$this->sci->init('admin');
		$this->_init();
		$this->session->validate(array('ADMIN'), 'admin');
		$this->sci->assign('use_ajax' , FALSE); 
	}
	
	function racing_validation() {
		$this->form_validation->set_rules('r_name', 'Name', 'trim|required|xss_clean'); 
		$this->form_validation->set_rules('r_order', 'ORdering', 'trim|xss_clean'); 
	}

	function racing_setter() { 
		$this->db->set('r_name' , $this->input->post('r_name') );
		$this->db->set('r_order' , $this->input->post('r_order') ); 
		if($_FILES['r_banner']['name'] != '') { $this->db->set('r_banner' , $this->_upload_image('r_banner')); }
	}
	
	function racing_list($pagelimit='', $offset=0, $orderby='', $ascdesc='', $encodedkey='') {
		$this->session->set_bread('list');  
		if ($orderby == '') $orderby = 'r_order';
		if ($ascdesc == '') $ascdesc = 'DESC'; 
		if ($pagelimit == '') $pagelimit = $this->default_pagelimit;
		$this->sci->assign('pagelimit' , $pagelimit);
		$this->sci->assign('offset' , $offset);
		$this->sci->assign('orderby' , $orderby);
		$this->sci->assign('ascdesc' , $ascdesc);
		/*--cache-start--*/
		$this->db->start_cache();
			if($encodedkey != ''){ $this->pre_search($encodedkey); }
			$orderbyconv = preg_replace('/_DOT_/' , '.', $orderby);
			$this->db->order_by($orderbyconv , $ascdesc);
			$this->db->from('racing');
			$this->db->where('r_status' , 'Active');
		$this->db->stop_cache();
		/*--cache-stop--*/

		// Pagination
		$total = $this->db->count_all_results('racing');
		$this->load->library('pagination');
		$config['base_url'] = $this->mod_url."racing_list/". $pagelimit ."/";
		$config['suffix'] = "/$orderby/$ascdesc/$encodedkey" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 5;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get('racing');
		$this->db->flush_cache();
		$maindata = $res->result_array(); 
		$this->sci->assign('maindata' , $maindata);
		$this->sci->assign('paging', $this->pagination->create_links() );
		$this->post_index();
		$this->sci->da('racing_list.htm');
	}
	
	function racing_add() {
		$this->sci->assign('ajax_action' , 'add');
		$this->pre_add_edit();
		$this->pre_add();

		$this->load->library('form_validation');
		$this->racing_validation('add');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('racing_edit.htm');
		}else{
			$this->racing_setter('add');
			$this->db->set('r_entry' , 'NOW()', FALSE );
			$ok = $this->db->insert('racing');
			$insert_id = $this->db->insert_id(); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url.'racing_add');
			} else {
				$this->session->set_confirm(1);
				redirect($this->session->get_bread('list') );
			}
		}
	}
 
	function racing_edit( $id=0 ) {
		$this->sci->assign('ajax_action' , 'edit'); 
		$this->db->where('r_id' , $id);
		$res = $this->db->get('racing');
		$data = $res->row_array();
		$this->sci->assign('data' , $data); 

		$this->load->library('form_validation');
		$this->racing_validation('edit');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('racing_edit.htm');
		}else{
			$this->racing_setter('edit');
			$this->db->where('r_id' , $id);
			$ok = $this->db->update('racing'); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."racing_edit/$id");
			} else {
				$this->session->set_confirm(1);
				redirect($this->session->get_bread('list') );
			}
		}
	}
	
	
	
// RACING REGION

	function region_validation() {
		$this->form_validation->set_rules('rr_name', 'Name', 'trim|required|xss_clean');  
		$this->form_validation->set_rules('r_id', 'Race', 'trim|required|xss_clean');  
	}

	function region_setter() { 
		$this->db->set('rr_name' , $this->input->post('rr_name') ); 
		$this->db->set('r_id' , $this->input->post('r_id') ); 
	}
	
	function region_list($r_id=0, $pagelimit='', $offset=0, $orderby='', $ascdesc='', $encodedkey='') {
		$this->session->set_bread('list');
		$this->sci->assign('r_id' , $r_id);
		$this->db->where('r_id' , $r_id);
		$res = $this->db->get('racing');
		$racing = $res->row_array();
		$this->sci->assign('racing' , $racing);
		
		if ($orderby == '') $orderby = 'rr_name';
		if ($ascdesc == '') $ascdesc = 'ASC'; 
		if ($pagelimit == '') $pagelimit = $this->default_pagelimit;
		$this->sci->assign('pagelimit' , $pagelimit);
		$this->sci->assign('offset' , $offset);
		$this->sci->assign('orderby' , $orderby);
		$this->sci->assign('ascdesc' , $ascdesc);
		/*--cache-start--*/
		$this->db->start_cache();
			if($encodedkey != ''){ $this->pre_search($encodedkey); }
			$orderbyconv = preg_replace('/_DOT_/' , '.', $orderby);
			$this->db->order_by($orderbyconv , $ascdesc);
			$this->db->from('racing_region');
			$this->db->where('r_id' , $r_id);
			$this->db->where('rr_status' , 'Active');
		$this->db->stop_cache();
		/*--cache-stop--*/

		// Pagination
		$total = $this->db->count_all_results('racing_region');
		$this->load->library('pagination');
		$config['base_url'] = $this->mod_url."region_list/$r_id/". $pagelimit ."/";
		$config['suffix'] = "/$orderby/$ascdesc/$encodedkey" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 5;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get('racing_region');
		$this->db->flush_cache();
		$maindata = $res->result_array(); 
		$this->sci->assign('maindata' , $maindata);
		$this->sci->assign('paging', $this->pagination->create_links() );
		$this->post_index();
		$this->sci->da('region_list.htm');
	}
	
	function region_add($r_id=0) {
		$this->sci->assign('ajax_action' , 'add');
		$this->sci->assign('r_id' , $r_id);
		$this->db->where('r_id' , $r_id);
		$res = $this->db->get('racing');
		$racing = $res->row_array();
		$this->sci->assign('racing' , $racing);
		
		$this->load->library('form_validation');
		$this->region_validation('add');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('region_edit.htm');
		}else{
			$this->region_setter('add');
			$this->db->set('rr_entry' , 'NOW()', FALSE );
			$ok = $this->db->insert('racing_region');
			$insert_id = $this->db->insert_id(); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url.'region_add');
			} else {
				$this->session->set_confirm(1);
				redirect($this->session->get_bread('list') );
			}
		}
	}
 
	function region_edit( $id=0 ) {
		$this->sci->assign('ajax_action' , 'edit'); 
		$this->db->where('rr_id' , $id);
		$res = $this->db->get('racing_region');
		$data = $res->row_array();
		$this->sci->assign('data' , $data);
		 
		$this->db->where('r_id' , $data['r_id'] );
		$res = $this->db->get('racing');
		$racing = $res->row_array();
		$this->sci->assign('racing' , $racing);

		$this->load->library('form_validation');
		$this->region_validation('edit');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('region_edit.htm');
		}else{
			$this->region_setter('edit');
			$this->db->where('rr_id' , $id);
			$ok = $this->db->update('racing_region'); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."region_edit/$id");
			} else {
				$this->session->set_confirm(1);
				redirect($this->session->get_bread('list') );
			}
		}
	}
	



// RACING SERIES

	function series_validation() {
		$this->form_validation->set_rules('rs_name', 'Name', 'trim|required|xss_clean');  
		$this->form_validation->set_rules('rs_start_date', 'Start Date', 'trim|xss_clean');  
		$this->form_validation->set_rules('rs_end_date', 'End Date', 'trim|xss_clean');  
		$this->form_validation->set_rules('rr_id', 'Region', 'trim|required|xss_clean');  
	}

	function series_setter() { 
		$this->db->set('rs_name' , $this->input->post('rs_name') ); 
		$this->db->set('rs_start_date' , $this->input->post('rs_start_date') ); 
		$this->db->set('rs_end_date' , $this->input->post('rs_end_date') ); 
		$this->db->set('rr_id' , $this->input->post('rr_id') ); 
	}
	
	function series_list($rr_id=0, $pagelimit='', $offset=0, $orderby='', $ascdesc='', $encodedkey='') {
		$this->session->set_bread('list');
		$this->sci->assign('rr_id' , $rr_id);
		$this->db->join('racing r' , 'r.r_id = racing_region.r_id' , 'left'); 
		$this->db->where('rr_id' , $rr_id);
		$res = $this->db->get('racing_region');
		$racing_region = $res->row_array();
		$this->sci->assign('racing_region' , $racing_region);
		
		if ($orderby == '') $orderby = 'rs_name';
		if ($ascdesc == '') $ascdesc = 'ASC'; 
		if ($pagelimit == '') $pagelimit = $this->default_pagelimit;
		$this->sci->assign('pagelimit' , $pagelimit);
		$this->sci->assign('offset' , $offset);
		$this->sci->assign('orderby' , $orderby);
		$this->sci->assign('ascdesc' , $ascdesc);
		/*--cache-start--*/
		$this->db->start_cache();
			if($encodedkey != ''){ $this->pre_search($encodedkey); }
			$orderbyconv = preg_replace('/_DOT_/' , '.', $orderby);
			$this->db->order_by($orderbyconv , $ascdesc);
			$this->db->from('racing_series');
			$this->db->where('rr_id' , $rr_id);
			$this->db->where('rs_status' , 'Active');
		$this->db->stop_cache();
		/*--cache-stop--*/

		// Pagination
		$total = $this->db->count_all_results('racing_series');
		$this->load->library('pagination');
		$config['base_url'] = $this->mod_url."series_list/$rr_id/". $pagelimit ."/";
		$config['suffix'] = "/$orderby/$ascdesc/$encodedkey" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 5;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get('racing_series');
		$this->db->flush_cache();
		$maindata = $res->result_array(); 
		$this->sci->assign('maindata' , $maindata);
		$this->sci->assign('paging', $this->pagination->create_links() );
		$this->post_index();
		$this->sci->da('series_list.htm');
	}
	
	function series_add($rr_id=0) {
		$this->sci->assign('ajax_action' , 'add');
		$this->sci->assign('rr_id' , $rr_id);
		$this->db->where('rr_id' , $rr_id);
		$this->db->join('racing r' , 'r.r_id = racing_region.r_id' , 'left'); 
		$res = $this->db->get('racing_region');
		$racing = $res->row_array();
		$this->sci->assign('racing' , $racing);
		
		$this->load->library('form_validation');
		$this->series_validation('add');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('series_edit.htm');
		}else{
			$this->series_setter('add');
			$this->db->set('rs_entry' , 'NOW()', FALSE );
			$ok = $this->db->insert('racing_series');
			$insert_id = $this->db->insert_id(); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url.'series_add');
			} else {
				$this->session->set_confirm(1);
				redirect($this->session->get_bread('list') );
			}
		}
	}
 
	function series_edit( $id=0 ) {
		$this->sci->assign('ajax_action' , 'edit'); 
		$this->db->where('rs_id' , $id);
		$res = $this->db->get('racing_series');
		$data = $res->row_array();
		$this->sci->assign('data' , $data);
		 
		 $this->db->join('racing r' , 'r.r_id = racing_region.r_id' , 'left'); 
		$this->db->where('rr_id' , $data['rr_id'] );
		$res = $this->db->get('racing_region');
		$racing = $res->row_array();
		$this->sci->assign('racing' , $racing);

		$this->load->library('form_validation');
		$this->series_validation('edit');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('series_edit.htm');
		}else{
			$this->series_setter('edit');
			$this->db->where('rs_id' , $id);
			$ok = $this->db->update('racing_series'); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."series_edit/$id");
			} else {
				$this->session->set_confirm(1);
				redirect($this->session->get_bread('list') );
			}
		}
	}
	
	function series_delete($rs_id=0) {
		$this->db->where('rs_id' , $rs_id);
		$this->db->set('rs_status' , 'Deleted');
		$this->db->update('racing_series');
		$this->session->set_confirm(1);
		redirect($this->session->get_bread('list') );
	}
	
	
	
	
	
// RACING SERIES

	function racer_validation() {
		$this->form_validation->set_rules('rc_name', 'Name', 'trim|required|xss_clean');  
		$this->form_validation->set_rules('rc_team', 'Team', 'trim|xss_clean');  
		$this->form_validation->set_rules('rc_score', 'Score', 'trim|required|xss_clean');  
		$this->form_validation->set_rules('rs_id', 'Series', 'trim|required|xss_clean');  
	}

	function racer_setter() { 
		$this->db->set('rc_name' , $this->input->post('rc_name') ); 
		$this->db->set('rc_team' , $this->input->post('rc_team') ); 
		$this->db->set('rc_score' , $this->input->post('rc_score') ); 
		$this->db->set('rs_id' , $this->input->post('rs_id') ); 
	}
	
	function racer_list($rs_id=0, $pagelimit='', $offset=0, $orderby='', $ascdesc='', $encodedkey='') {
		$this->session->set_bread('list');
		$this->sci->assign('rs_id' , $rs_id); 
		$this->db->join('racing_region rr' , 'rr.rr_id = racing_series.rr_id' , 'left');
		$this->db->join('racing r' , 'r.r_id = rr.r_id' , 'left');
		$this->db->where('racing_series.rs_id' , $rs_id);
		$res = $this->db->get('racing_series');
		$racing_series = $res->row_array();
		$this->sci->assign('racing_series' , $racing_series);
		
		if ($orderby == '') $orderby = 'rc_score';
		if ($ascdesc == '') $ascdesc = 'DESC'; 
		if ($pagelimit == '') $pagelimit = $this->default_pagelimit;
		$this->sci->assign('pagelimit' , $pagelimit);
		$this->sci->assign('offset' , $offset);
		$this->sci->assign('orderby' , $orderby);
		$this->sci->assign('ascdesc' , $ascdesc);
		/*--cache-start--*/
		$this->db->start_cache();
			if($encodedkey != ''){ $this->pre_search($encodedkey); }
			$orderbyconv = preg_replace('/_DOT_/' , '.', $orderby);
			$this->db->order_by($orderbyconv , $ascdesc);
			$this->db->from('racing_racer');
			$this->db->where('rs_id' , $rs_id);
			$this->db->where('rc_status' , 'Active');
		$this->db->stop_cache();
		/*--cache-stop--*/

		// Pagination
		$total = $this->db->count_all_results('racing_racer');
		$this->load->library('pagination');
		$config['base_url'] = $this->mod_url."racer_list/$rs_id/". $pagelimit ."/";
		$config['suffix'] = "/$orderby/$ascdesc/$encodedkey" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 5;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get('racing_racer');
		$this->db->flush_cache();
		$maindata = $res->result_array(); 
		$this->sci->assign('maindata' , $maindata);
		$this->sci->assign('paging', $this->pagination->create_links() );
		$this->post_index();
		$this->sci->da('racer_list.htm');
	}
	
	function racer_add($rs_id=0) {
		$this->sci->assign('ajax_action' , 'add');
		$this->sci->assign('rs_id' , $rs_id);
		
		$this->db->where('rs_id' , $rs_id);
		$this->db->join('racing_region rr' , 'rr.rr_id = racing_series.rr_id' , 'left');
		$this->db->join('racing r' , 'r.r_id = rr.r_id' , 'left');
		$res = $this->db->get('racing_series');
		$racing = $res->row_array();
		$this->sci->assign('racing' , $racing);
		
		$this->load->library('form_validation');
		$this->racer_validation('add');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('racer_edit.htm');
		}else{
			$this->racer_setter('add');
			$this->db->set('rc_entry' , 'NOW()', FALSE );
			$ok = $this->db->insert('racing_racer');
			$insert_id = $this->db->insert_id(); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url.'racer_add');
			} else {
				$this->session->set_confirm(1);
				redirect($this->session->get_bread('list') );
			}
		}
	}
 
	function racer_edit( $id=0 ) {
		$this->sci->assign('ajax_action' , 'edit'); 
		$this->db->where('rc_id' , $id);
		$res = $this->db->get('racing_racer');
		$data = $res->row_array();
		$this->sci->assign('data' , $data);
		 
		$this->db->where('rs_id' , $data['rs_id'] );
		$this->db->join('racing_region rr' , 'rr.rr_id = racing_series.rr_id' , 'left');
		$this->db->join('racing r' , 'r.r_id = rr.r_id' , 'left');
		$res = $this->db->get('racing_series');
		$racing = $res->row_array();
		$this->sci->assign('racing' , $racing);

		$this->load->library('form_validation');
		$this->racer_validation('edit');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('racer_edit.htm');
		}else{
			$this->racer_setter('edit');
			$this->db->where('rc_id' , $id);
			$ok = $this->db->update('racing_racer'); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."racer_edit/$id");
			} else {
				$this->session->set_confirm(1);
				redirect($this->session->get_bread('list') );
			}
		}
	}	
	
	function racer_delete($rc_id=0) {
		$this->db->where('rc_id' , $rc_id);
		$this->db->set('rc_status' , 'Deleted');
		$this->db->update('racing_racer');
		$this->session->set_confirm(1);
		redirect($this->session->get_bread('list') );
	}
	
	

	function pre_search($encodedkey) {
		$searchkey = safe_base64_decode($encodedkey);
		if(!empty($this->search_in)) {
			foreach($this->search_in as $k=>$tmp) { $this->db->or_like($tmp, $searchkey); }
		} else {
			$this->search_setting($searchkey);
		}
		$this->sci->assign('searchkey' , $searchkey);
	}
 

}
