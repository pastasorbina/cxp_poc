<?php

require_once APPPATH.'libraries/GoogleClient/Google_Client.php';
require_once APPPATH.'libraries/GoogleClient/contrib/Google_CalendarService.php';

class Calendar extends MY_Controller {

	var $mod_title = '';

	function __construct() {
		parent::__construct();
		$this->sci->init('front');
		$this->_init();
		//$this->load->library('GoogleClient/Google_Client', 'Google');
		//$this->load->library('GoogleClient/contrib/Google_CalendarService.php', 'GoogleCal');
	}

	function index(){


		$this->sci->da('index.htm');
	}

}
