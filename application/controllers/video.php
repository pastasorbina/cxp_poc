<?php

class Video extends MY_Controller {

	var $mod_title = '';

	function __construct() {
		parent::__construct();
		$this->sci->set_room('main');
		$this->_init();
 
		$this->load->model('mod_content');

		$this->session->validate_member(FALSE);
		  
		$this->sci->assign('curr_page' , 'video');
	}

	function index(){
		$this->sci->d('index.htm');
	}
	 
	 

}
