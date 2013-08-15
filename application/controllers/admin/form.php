<?php

class Form extends MY_Controller {

	var $mod_title = 'Manage Forms';

	function __construct() {
		parent::__construct();
		$this->sci->init('admin');
		$this->_init();
		$this->session->validate(array('ADMIN'), 'admin');
	}

	function index($orderby = 'f_id' , $ascdesc = 'ASC' , $page_number = 0 , $searchkey = '') {
		$this->session->set_bread('list');

		// Get form database
		$this->db->from('form');
		$this->db->where('f_status' ,"Active");
		$this->db->where('b_id' , $this->branch_id );

		$res = $this->db->get();
		$this->sci->assign('maindata' , $res->result_array());

		// Form Validation class
		$this->load->library('form_validation');
		$this->form_validation->set_rules('f_name' , 'Name' , 'trim|required|strip_tags');
		$this->form_validation->set_rules('f_destination_emails' , 'Destination Emails' , 'trim|strip_tags|valid_emails');
		$this->form_validation->set_rules('f_header_text' , 'Header Text' , '');
		$this->form_validation->set_rules('f_footer_text' , 'Footer Text' , '');

		if ($this->form_validation->run() == FALSE) {
			$this->sci->da('index.htm');
		}
		else {
		$this->db->
			set('f_name' , set_value('f_name'))->
			set('f_destination_emails' , set_value('f_destination_emails'))->
			set('f_header_text' , $this->input->post('f_header_text'))->
			set('f_footer_text' , $this->input->post('f_footer_text'))->
			set('f_name' , $this->input->post('f_name'))->
			set('b_id' , $this->branch_id )->
			insert('form');

			$ii = $this->db->insert_id();

			// Insert all other information
			$uniq = $this->input->post('uniq');
			$key = $this->input->post('key');
			$type = $this->input->post('type');
			$regex = $this->input->post('regex');
			$options = $this->input->post('options');
			$required = $this->input->post('required');

			if ($uniq) {
				foreach($uniq as $k) {
					if (trim($key[$k]) != '') {
						$this->db->
							set('f_id' , $ii)->
							set('fd_key' , trim($key[$k]))->
							set('fd_type' , $type[$k])->
							set('fd_regex' , trim($regex[$k]))->
							set('fd_options' , trim($options[$k]))->
							set('fd_req' , (isset($required[$k])?'True' : 'False'))->
							insert('form_detail');
					}
				}
			}

			redirect($this->mod_url."edit/$ii");
		}
	}

	function delete($f_id = 0) {
		// Delete data
		$this->db->where('f_id' , $f_id);
		$this->db->set('f_status' , 'Deleted');
		$this->db->update('form');

		redirect($this->mod_url."index");
	}

	function edit($f_id = 0) {
		// Get Form information
		$this->sci->assign('f_id' , $f_id);

		$this->db->where('f_id' , $f_id);
		$this->db->where('f_status' ,"Active");
		$res = $this->db->get('form');
		if ($row = $res->row()) {
			$this->sci->assign('maindata' , $row);
			$maindata = $row;
		} else {
			redirect($this->mod_url."index");
		}

		// Get form detail
		$this->db->where('f_id' , $f_id);
		$this->db->order_by('fd_id');
		$res = $this->db->get('form_detail');
		$this->sci->assign('form_detail' , $res->result_array());

		//GET FORM GROUPING
		$this->db->where('f_id' , $f_id);
		$this->db->order_by('fg_order' , 'ASC');
		$this->db->where('fg_status' , 'Active');
		$res = $this->db->get('form_grouping');
		$allgrouping = $res->result_array();
		$this->sci->assign('allgrouping' , $allgrouping);

		// Form type definition
		$form_type = array(
			'TEXT' => 'Text',
			'TEXTAREA' => 'Text Area',
			'RADIO' => 'Radio',
			'SELECT' => 'Select',
			'CHECKBOX' => 'Checkbox',
			'FILE' => 'File',
			'DATE' => 'Date',
		);
		$this->sci->assign('form_type' , $form_type);

		// Form Validation class
		$this->load->library('form_validation');
		$this->form_validation->set_rules('f_name' , 'Name' , 'trim|strip_tags');
		$this->form_validation->set_rules('f_destination_emails' , 'Destination Emails' , 'trim|strip_tags|valid_emails');
		$this->form_validation->set_rules('f_header_text' , 'Header Text' , '');
		$this->form_validation->set_rules('f_footer_text' , 'Footer Text' , '');

		if ($this->form_validation->run() == FALSE) {
			$this->sci->da('edit.htm');
		} else {

		$this->db->set('f_name' , set_value('f_name'));
		$this->db->set('f_destination_emails' , set_value('f_destination_emails'));
		$this->db->set('f_header_text' , $this->input->post('f_header_text'));
		$this->db->set('f_footer_text' , $this->input->post('f_footer_text'));
		$this->db->set('f_position' , $this->input->post('f_position'));
		$this->db->set('f_code' , $this->input->post('f_code'));
		$this->db->set('f_education' , $this->input->post('f_education'));
		$this->db->where('f_id' , $f_id);
		$this->db->update('form');

			// Delete all data 1st
			$this->db->where('f_id' , $f_id);
			$this->db->delete('form_detail');

			// Insert all other information
			$uniq = $this->input->post('uniq');
			$key = $this->input->post('key');
			$type = $this->input->post('type');
			$regex = $this->input->post('regex');
			$options = $this->input->post('options');
			$required = $this->input->post('required');
			$grouping = $this->input->post('grouping');
			$displaytitle = $this->input->post('displaytitle');

			if ($uniq) {
				foreach($uniq as $k) {
					if (trim($key[$k]) != '') {
						$this->db->set('f_id' , $f_id);
						$this->db->set('fd_key' , trim($key[$k]));
						$this->db->set('fd_type' , $type[$k]);
						$this->db->set('fd_regex' , trim($regex[$k]));
						$this->db->set('fd_options' , trim($options[$k]));
						$this->db->set('fg_id' , trim($grouping[$k]));
						$this->db->set('fd_display_title' , trim($displaytitle[$k]));
						$this->db->set('fd_req' , (isset($required[$k])?'True' : 'False'));
						$this->db->insert('form_detail');
					}
				}
			}

			redirect($this->mod_url."index");
		}
	}

	function add_new_field($f_id=0) {
		$this->sci->assign('f_id' , $f_id);
		//GET FORM GROUPING
		$this->db->where('f_id' , $f_id);
		$this->db->order_by('fg_order' , 'ASC');
		$this->db->where('fg_status' , 'Active');
		$res = $this->db->get('form_grouping');
		$allgrouping = $res->result_array();
		$this->sci->assign('allgrouping' , $allgrouping);

		// Form type definition
		$form_type = array(
			'TEXT' => 'Text',
			'TEXTAREA' => 'Text Area',
			'RADIO' => 'Radio',
			'SELECT' => 'Select',
			'CHECKBOX' => 'Checkbox',
			'FILE' => 'File',
			'DATE' => 'Date',
		);
		$this->sci->assign('form_type' , $form_type);

		$this->sci->assign('uniq' , uniqid());
		$this->sci->d('add_new_field.htm');
	}

	function grouping_list($f_id=0) {
		$this->sci->assign('uniq' , uniqid());
		$this->db->where('f_id' , $f_id);
		$this->db->order_by('fg_order' , 'ASC');
		$this->db->where('fg_status' , 'Active');
		$res = $this->db->get('form_grouping');
		$form_grouping = $res->result_array();
		$this->sci->assign('form_grouping' , $form_grouping);
		$this->sci->d('grouping/list.htm');
	}

	function result_list($f_id=0) {
		$this->db->where('f_id' , $f_id);
		$res = $this->db->get('form');
		$form = $res->row_array();
		$this->sci->assign('form' , $form);

		$this->db->where('f_id' , $f_id);
		$res = $this->db->get('form_detail');
		$form_detail = $res->result_array();
		$this->sci->assign('form_detail' , $form_detail);

		$this->db->join('form f' , 'f.f_id = form_data.f_id' , 'left');
		$this->db->where('form_data.f_id' , $f_id);
		$this->db->where('ft_status' , 'Active');
		$this->db->order_by('ft_entry' , 'DESC');
		$res = $this->db->get('form_data');
		$form_data = $res->result_array();
		$total = sizeof($form_data); $this->sci->assign('total' , $total);
		foreach($form_data as $k=>$tmp) {
			$this->db->join('form_detail fd' , 'fd.fd_id = form_data_set.fd_id' , 'left');
			$this->db->where('ft_id' , $tmp['ft_id']);
			$res = $this->db->get('form_data_set');
			$form_data[$k]['data_set'] = $res->result_array();
		}
		$this->sci->assign('form_data' , $form_data);

		$this->sci->da('result_list.htm');

	}

}

?>
