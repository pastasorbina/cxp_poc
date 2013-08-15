<?php

class Home extends MY_Controller {

	var $mod_title = 'Dashboard';

	function __construct() {
		parent::__construct();
		$this->sci->set_room('admin');
		$this->_init();
		$this->session->validate(array(), 'admin');
	}

	function index() { 
		$userinfo = $this->session->get_userinfo(); 
		$this->sci->da('index.htm');
	}

}
