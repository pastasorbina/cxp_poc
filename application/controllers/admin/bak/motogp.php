<?php
class Motogp extends MY_Controller {

	var $mod_title = '';

	var $table_name = 'motogp';
	var $id_field = 'gp_id';
	var $status_field = 'gp_status';
	var $entry_field = 'gp_entry';
	var $stamp_field = 'gp_stamp';
	var $deletion_field = 'gp_deletion';
	var $order_field = 'gp_entry';
	var $order_dir = 'DESC';
	var $label_field = 'gp_name';

	var $author_field = 'gp_author';
	var $editor_field = 'gp_editor';

	var $search_in = array('gp_name');

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
		$this->form_validation->set_rules('gp_name', 'Name', 'trim|required|xss_clean');  
	}

	function racing_setter() {  
		$this->db->set('gp_name' , $this->input->post('gp_name') ); 			
	}
	
	function racing_list($pagelimit='', $offset=0, $orderby='', $ascdesc='', $encodedkey='') {
		$this->session->set_bread('list');  
		if ($orderby == '') $orderby = 'gp_name';
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
			$this->db->from('motogp');
			$this->db->where('gp_status' , 'Active');
		$this->db->stop_cache();
		/*--cache-stop--*/

		// Pagination
		$total = $this->db->count_all_results('motogp');
		$this->load->library('pagination');
		$config['base_url'] = $this->mod_url."racing_list/". $pagelimit ."/";
		$config['suffix'] = "/$orderby/$ascdesc/$encodedkey" ;
		$config['total_rows'] = $total;
		$config['pegp_page'] = $pagelimit;
		$config['uri_segment'] = 5;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get('motogp');
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
			$this->db->set('gp_entry' , 'NOW()', FALSE );
			$ok = $this->db->insert('motogp');
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
		$this->db->where('gp_id' , $id);
		$res = $this->db->get('motogp');
		$data = $res->row_array();
		$this->sci->assign('data' , $data); 

		$this->load->library('form_validation');
		$this->racing_validation('edit');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('racing_edit.htm');
		}else{
			$this->racing_setter('edit');
			$this->db->where('gp_id' , $id);
			$ok = $this->db->update('motogp'); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."racing_edit/$id");
			} else {
				$this->session->set_confirm(1);
				redirect($this->session->get_bread('list') );
			}
		}
	}
	
	function racing_set_featured($gp_id=0) { 
		$this->db->set('gp_is_featured' , 'No');
		$this->db->update('motogp');
		
		$this->db->where('gp_id' , $gp_id);
		$this->db->set('gp_is_featured' , 'Yes');
		$ok = $this->db->update('motogp'); 
		if(!$ok) {
			$this->session->set_confirm(0); 
		} else {
			$this->session->set_confirm(1); 
		}
		redirect($this->session->get_bread('list') );
	}
	
	
	 
	
// RACING SERIES

	function racer_list($gp_id=0, $pagelimit='', $offset=0, $orderby='', $ascdesc='', $encodedkey='') {
		$this->session->set_bread('list');
		$this->sci->assign('gp_id' , $gp_id);  
		$this->db->where('gp_id' , $gp_id);
		$res = $this->db->get('motogp');
		$motogp = $res->row_array();
		$this->sci->assign('motogp' , $motogp);
		
		if ($orderby == '') $orderby = 'gpc_score';
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
			$this->db->from('motogp_racer');
			$this->db->where('gp_id' , $gp_id);
			$this->db->where('gpc_status' , 'Active');
		$this->db->stop_cache();
		/*--cache-stop--*/

		// Pagination
		$total = $this->db->count_all_results('motogp_racer');
		$this->load->library('pagination');
		$config['base_url'] = $this->mod_url."racer_list/$gp_id/". $pagelimit ."/";
		$config['suffix'] = "/$orderby/$ascdesc/$encodedkey" ;
		$config['total_rows'] = $total;
		$config['per_page'] = $pagelimit;
		$config['uri_segment'] = 5;
		$this->pagination->initialize($config);

		$this->db->limit($pagelimit, $offset);
		$res = $this->db->get('motogp_racer');
		$this->db->flush_cache();
		$maindata = $res->result_array(); 
		$this->sci->assign('maindata' , $maindata);
		$this->sci->assign('paging', $this->pagination->create_links() );
		$this->post_index();
		$this->sci->da('racer_list.htm');
	}
	
	function racer_validation() {
		$this->form_validation->set_rules('gpc_name', 'Name', 'trim|required|xss_clean');  
		$this->form_validation->set_rules('gpc_team', 'Team', 'trim|xss_clean');  
		$this->form_validation->set_rules('gpc_score', 'Score', 'trim|required|xss_clean');  
		$this->form_validation->set_rules('gp_id', 'Moto GP Race', 'trim|required|xss_clean');  
	}

	function racer_setter() { 
		$this->db->set('gpc_name' , $this->input->post('gpc_name') ); 
		$this->db->set('gpc_team' , $this->input->post('gpc_team') ); 
		$this->db->set('gpc_score' , $this->input->post('gpc_score') ); 
		$this->db->set('gp_id' , $this->input->post('gp_id') ); 
	}
	
	function racer_add($gp_id=0) {
		$this->sci->assign('ajax_action' , 'add');
		$this->sci->assign('gp_id' , $gp_id);
		
		$this->db->where('gp_id' , $gp_id);
		$res = $this->db->get('motogp');
		$motogp = $res->row_array();
		$this->sci->assign('motogp' , $motogp);
		  
		$this->load->library('form_validation');
		$this->racer_validation('add');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('racer_edit.htm');
		}else{
			$this->racer_setter('add');
			$this->db->set('gpc_entry' , 'NOW()', FALSE );
			$ok = $this->db->insert('motogp_racer');
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
		$this->db->where('gpc_id' , $id);
		$res = $this->db->get('motogp_racer');
		$data = $res->row_array();
		$this->sci->assign('data' , $data);
		 
		$this->db->where('gp_id' , $data['gp_id']);
		$res = $this->db->get('motogp');
		$motogp = $res->row_array();
		$this->sci->assign('motogp' , $motogp);

		$this->load->library('form_validation');
		$this->racer_validation('edit');
		if($this->form_validation->run() == FALSE ) {
			$this->sci->da('racer_edit.htm');
		}else{
			$this->racer_setter('edit');
			$this->db->where('gpc_id' , $id);
			$ok = $this->db->update('motogp_racer'); 
			if(!$ok) {
				$this->session->set_confirm(0);
				redirect($this->mod_url."racer_edit/$id");
			} else {
				$this->session->set_confirm(1);
				redirect($this->session->get_bread('list') );
			}
		}
	}
	
	function racer_delete($id=0) {
		$this->db->where('gpc_id' , $id);
		$this->db->set('gpc_status' , 'Deleted');
		$this->db->update('motogp_racer');
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
