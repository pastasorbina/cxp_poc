<?php

class Find_dealer extends MY_Controller {

	var $mod_title = '';

	function __construct() {
		parent::__construct();
		$this->sci->init('front');
		$this->_init();
		
		
	}
	
	 
	function index($zoom=4, $lat='', $long='') {
		$this->sci->assign('lat' , $lat);
		$this->sci->assign('long' , $long);
		$this->sci->assign('zoom' , $zoom); 
		//
		//get dealer area
		$this->db->where('dlra_status' , 'Active');
		$this->db->order_by('dlra_name' , 'asc');
		$res = $this->db->get('dealer_area');
		$dealer_area = $res->result_array();
		$this->sci->assign('dealer_area' , $dealer_area);
		//print_r($dealer_area);
		
		$this->db->where('dlr_status' , 'Active');
		$res = $this->db->get('dealers');
		$all_dealers = $res->result_array();
		$this->sci->assign('all_dealers' , $all_dealers);
		
		$this->sci->da('index.htm');
	}
	 
	
	function _get_dealers() {
		
	}
	
	function ajax_view_dealers($dlr_id=0) {
		$this->db->where('dlr_id' , $dlr_id);
		$res = $this->db->get('dealers');
		$dealer = $res->row_array();
		$this->sci->assign('dealer' , $dealer);
		$this->sci->d('ajax_view.htm');
	}
	
	function fix_decimal() {
		$res = $this->db->get('dealer_area');
		$dealer_area = $res->result_array();
		foreach($dealer_area as $k=>$tmp) {
			$lat = str_replace(',', '.', $tmp['dlra_lat']);
			$long = str_replace(',', '.', $tmp['dlra_long']);
			$this->db->set('dlra_lat' , $lat);
			$this->db->set('dlra_long' , $long);
			$this->db->where('dlra_id' , $tmp['dlra_id']);
			$this->db->update('dealer_area');
		}
		
	}
	
	function map($markme='no', $zoom=4, $lat=5.315299, $long=110.735839) {
		$this->sci->assign('lat' , $lat);
		$this->sci->assign('long' , $long);
		$this->sci->assign('zoom' , $zoom);
		$this->sci->assign('markme' , $markme);
		
		if($markme == 'yes') {
			$this->sci->assign('mylat' , @$_SESSION['mylat']);
			$this->sci->assign('mylong' , @$_SESSION['mylong']); 
		}
		
		$this->db->where('dlr_status' , 'Active');
		$res = $this->db->get('dealers');
		$all_dealers = $res->result_array();
		$this->sci->assign('all_dealers' , $all_dealers);
		
		$this->sci->d('map.htm');
	}
	 
	
	function do_locator() {
		$this->sci->d('finder.htm');
	}
	
	function locator($zoom=8, $lat='', $long='') {
		$this->sci->assign('lat' , $lat);
		$this->sci->assign('long' , $long);
		$this->sci->assign('zoom' , $zoom);
		
		$_SESSION['mylat'] = $lat;
		$_SESSION['mylong'] = $long;
		
		$this->db->where('dlr_status' , 'Active');
		$res = $this->db->get('dealers');
		$all_dealers = $res->result_array();
		$this->sci->assign('all_dealers' , $all_dealers);
		 
		//get dealer area
		$this->db->where('dlra_status' , 'Active');
		$this->db->order_by('dlra_name' , 'asc');
		$res = $this->db->get('dealer_area');
		$dealer_area = $res->result_array();
		$this->sci->assign('dealer_area' , $dealer_area);
		//print_r($dealer_area);
		 
		$lat = str_replace('_' , '.' , $lat);
		$long = str_replace('_' , '.' , $long);
		
		$this->db->where('dlr_status' , 'Active');
		$res = $this->db->get('dealers');
		$all_dealers = $res->result_array();
		$this->sci->assign('all_dealers' , $all_dealers);
		
		
		//FOR DEALERS
		$this->db->where('dlr_status' , 'Active');
		//$this->db->where('dlr_type' , 'Dealer');
		$res = $this->db->get('dealers');
		$dealers = $res->result_array();
		$this->sci->assign('dealers' , $dealers);
		
		$dist_array = array();
		foreach($dealers as $k => $tmp) {
			$distance = $this->getDistanceBetweenPointsNew(floatval($lat) , floatval($long) , floatval($tmp['dlr_lat']) , floatval($tmp['dlr_long']));
			$dist_array[] = $distance;
			$dealers[$k]['distance'] = $distance; 
		}
		array_multisort($dist_array , $dealers);
		$dealers = array_slice($dealers , 0 , 15); 
		$this->sci->assign('closest_dealers' , $dealers);
		
		//FOR STORES
		//$this->db->where('dlr_status' , 'Active');
		//$this->db->where('dlr_type' , 'Store');
		//$res = $this->db->get('dealers');
		//$stores = $res->result_array();
		//$this->sci->assign('stores' , $stores);
		//
		//$dist_array = array();
		//foreach($stores as $k => $tmp) {
		//	$distance = $this->getDistanceBetweenPointsNew(floatval($lat) , floatval($long) , floatval($tmp['dlr_lat']) , floatval($tmp['dlr_long']));
		//	$dist_array[] = $distance;
		//	$stores[$k]['distance'] = $distance; 
		//}
		//array_multisort($dist_array , $stores);
		//$stores = array_slice($stores , 0 , 6); 
		//$this->sci->assign('closest_stores' , $stores); 
		
		$this->sci->da('locate_result.htm');
	} 
	
	function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Km') {
		$theta = $longitude1 - $longitude2;
		$distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
		$distance = acos($distance);
		$distance = rad2deg($distance);
		$distance = $distance * 60 * 1.1515;
		switch($unit) {
			case 'Mi': break;
			case 'Km' : $distance = $distance * 1.609344;
		} return (round($distance,2)); }
	

}
