<?php

class Demo extends MY_Controller {

	var $mod_title = '';


	function __construct() {
		parent::__construct();
		$this->sci->init('front');
		$this->_init();
	}

	function index() {
		 $this->aboutus();
	}

	function aboutus(){
		$this->sci->da('aboutus.htm');
	}

	function startup(){
		$this->sci->da('startup.htm');
	}

	function service(){
		$this->sci->da('service.htm');
	}

	function calendar(){
		$this->sci->da('calendar.htm');
	}

	function blog(){
		$this->sci->da('blog.htm');
	}






}
