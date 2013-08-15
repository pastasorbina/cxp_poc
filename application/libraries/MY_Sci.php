<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_SCI extends SCI {

	protected $CI;

	public $config = array();
	public $head = '';
	public $mod = '';

	public $branch = array();
	public $view_path = '';

	private $room = 'main';
	private $room_path = 'main/';

	public $template_header = 'header.htm';
	public $template_footer = 'footer.htm';

	var $postlaunch = TRUE;

	public function __construct() {
		parent::__construct();
		$this->CI =& get_instance();

		$this->CI->load->library('user_agent');
		if($this->CI->agent->is_mobile() ){
			$this->assign('is_mobile' , 'Yes');
		} else {
			$this->assign('is_mobile' , 'No');
		}

		//mark start SCI
		$this->CI->benchmark->mark('sci_start');

		//get and assign branch data from branch library
		$this->CI->load->library('branch');
		$this->branch = $this->CI->branch->get_branch();
		$this->assign('_branch' , $this->branch);

		//$this->assign('SERVER_TIME' , date('Y-m-d H:i:s'));
		$server_time['year'] = date('Y');
		$server_time['month'] = date('m');
		$server_time['day'] = date('d');
		$server_time['hour'] = date('H');
		$server_time['minute'] = date('i');
		$server_time['second'] = date('s');
		$this->assign('SERVER_TIME' , $server_time);

		//assign site_url variable to smarty //TODO: is this necessary?
		$this->assign('site_url', site_url());


		//get and assign assets, js, and css paths
		$js_path = $this->CI->config->item('js_path');
		$css_path = $this->CI->config->item('css_path');
		$this->assign('js_path', $js_path);
		$this->assign('css_path', $css_path);

		//$this->init();
		$this->_assign_config();
		$this->_assign_misc();


		//assign current uri string
		$current_uri = site_url() . $this->CI->uri->uri_string();
		$this->assign('current_uri' , $current_uri);

		$this->_assign_environment();

		$filename = './postlaunch';
		if (file_exists($filename)) {
			$this->postlaunch = TRUE;
		} else {
			$this->postlaunch = FALSE;
		}

	}

	function _assign_environment() {
		$this->assign('_environment' , ENVIRONMENT);
		$this->assign('_server' , $_SERVER);
	}

	function _assign_config() {
		$this->assign('remote_asset_url', $this->CI->config->item('remote_asset_url'));
		$this->assign('remote_url', $this->CI->config->item('remote_url'));


		$this->CI->db->where('b_id' , $this->branch['b_id']);
		$this->CI->db->where('c_type' , 'config');
		$res = $this->CI->db->get('config');
		$config = $res->result_array();

		$this->assign('site_config', $config);

		if($this->CI->config->item('debug_mode') == TRUE) {
			define('DEBUG_MODE', TRUE);
		} else {
			define('DEBUG_MODE', FALSE);
		}

	}

	private function _assign_for_main() {

		//get all menu
		$this->CI->db->where('mp_code' , 'about_menu');
		$this->CI->db->join('menu_position mp' , 'mp.mp_id = menu.mp_id' , 'left');
		$this->CI->db->where('m_status' , 'Active');
		$res = $this->CI->db->get('menu');
		$about_menu = $res->result_array();
		$this->assign('about_menu' , $about_menu);

		$this->CI->db->where('mp.mp_code' , 'about_footer');
		$this->CI->db->join('menu_position mp' , 'mp.mp_id = menu.mp_id' , 'left');
		$this->CI->db->where('m_status' , 'Active');
		$res = $this->CI->db->get('menu');
		$about_footer = $res->result_array();
		$this->assign('about_footer' , $about_footer);

		//get all snippets
		$this->CI->db->join('content_label cl' , 'cl.cl_id = content.cl_id' , 'left');
		$this->CI->db->where('c_status' , 'Active');
		$this->CI->db->where('cl_code' , 'snippet');
		$res = $this->CI->db->get('content');
		$tmpcontent = $res->result_array();
		$snippet = array();
		foreach($tmpcontent as $k=>$tmp){
			$snippet[$tmp['c_code']] = $tmp;
		}
		$this->assign('snippet' , $snippet);

		//get startups
		$this->CI->db->where('s_status' , 'Active');
		$this->CI->db->order_by('s_date' , 'DESC');
		$this->CI->db->limit(5,0);
		$res = $this->CI->db->get('startup');
		$home_startup = $res->result_array();
		$this->assign('home_startup' , $home_startup);

		//GET Events for Footer
		$this->CI->db->where('e_status' , 'Active');
		$this->CI->db->limit(5,0);
		$this->CI->db->order_by('e_date' , 'DESC');
		$res = $this->CI->db->get('events');
		$footer_events = $res->result_array();
		$this->assign('footer_events' , $footer_events);

		//GET partners for Footer
		$this->CI->db->where('c_code' , 'partners');
		$res = $this->CI->db->get('content');
		$parent_partners = $res->row_array();
		if($parent_partners){
			$this->CI->db->where('c_parent_id' , $parent_partners['c_id']);
			$this->CI->db->where('c_status' , 'active');
			$res = $this->CI->db->get('content');
			$footer_partners = $res->result_array();
		} else {
			$footer_partners = array();
		}
		$this->assign('footer_partners' , $footer_partners);

	}

	private function _assign_for_admin() {
		//matikan global xss_filtering
		$this->CI->config->set_item('global_xss_filtering', FALSE);
		//get content labels, untuk diassign ke admin menu
		$query = "
			SELECT content_label.cl_id, cl_type, cl_name,
			COUNT(content.cl_id) AS count
			FROM content_label
			LEFT OUTER JOIN content
			ON content_label.cl_id = content.cl_id
			WHERE cl_status = 'Active'
			GROUP BY content_label.cl_id ";
		$res = $this->CI->db->query($query);
		$this->assign('sidebar_cl' , $res->result_array());
	}

	public function _assign_misc() {

	}






	//room setter
	public function set_postlaunch( $true=FALSE ) {
		$this->postlaunch = $true;
	}

	//room setter
	public function set_room( $room = 'main' ) {
		$this->CI->config->set_item('room', $room);
		$this->CI->config->set_item('room_path', $room."/" );
	}

	//init, alias for set_room TODO: changed to set room
	public function init( $room = 'main' ){
		$this->set_room($room);
	}

	//room getter
	public function get_room() {
		return $this->CI->config->item('room');
	}

	//room_path getter
	public function get_room_path() {
		//print 'asd';
		//print $this->CI->config->item('room_path');
		//print $this->CI->config->item('room');
		return $this->CI->config->item('room_path');
	}

	//module setter
	public function set_module($mod_name) {
		$this->mod = $mod_name;
	}

	//module getter
	public function get_module() {
		return $this->mod;
	}

	//pre display
	public function pre_display(){
		//mark SCI end, before displaying view
		$this->CI->benchmark->mark('sci_end');
		$elapsed = $this->CI->benchmark->elapsed_time('sci_start', 'sci_end');
		$this->assign('elapsed', $elapsed );

		$room = $this->get_room();
		if($room == 'admin') {
			$this->_assign_for_admin();
		} else {
			$this->_assign_for_main();
		}

		if($this->postlaunch == TRUE && $room != "admin") {
			redirect(site_url()."comingsoon");
		}

	}


	//public function da( $template='', $plain=FALSE ) {
	//	$this->pre_display();
	//	print APPPATH;
	//
	//	//get header and footer template (determined by room set by module)
	//	$header = $this->get_room_path() . $this->template_header;
	//	$footer = $this->get_room_path() . $this->template_footer;
	//
	//	//get template. if plain is set TRUE, then using full path of the template
	//	if($plain == FALSE) {
	//		$template = $this->get_room_path() . $this->mod .'/' . $template;
	//	}
	//	// START DISPLAYING !! :D
	//
	//	//first, display the header
	//	$this->display($header);
	//	//check if the aformentioned template is available, if not, then throw error 404
	//	if($this->check_is_file($template)) {
	//		$this->display($template);
	//	}else{
	//		echo 'Sorry, cannot find the page you\'re looking for'; //TODO: show error 404
	//	}
	//	//then display the footer
	//	$this->display($footer);
	//}

	public function find_template($template='') {
		$segment_arr = $this->CI->uri->segment_array();
		$path_arr = array();
		$path = '';
		$full_path = '';
		$ok = FALSE;
		if(sizeof($segment_arr) == 0){
			$this->display("index.htm");
			return TRUE;
		} else {
			$segment_temp = $segment_arr;
			foreach($segment_temp as $k=>$tmp) {
				$path = implode('/', $segment_temp);
				$file = APPPATH."views/".$path."/".$template;
				if (is_readable($file) ) {
					$full_path = $file; $ok = TRUE; break;
				} else {
					$ok = FALSE;
				}
				array_pop($segment_temp);
			}
		}
		if($ok == FALSE) {
			return $ok;
		} else {
			return $full_path;
		}
	}

	public function da( $template='', $noauto=FALSE ) {
		$this->pre_display();

		$param1 = $this->CI->uri->segment(1);
		if (is_readable(APPPATH."views/$param1/header.htm") && $param1 != "" ) {
			$this->display("$param1/header.htm");
		} else {
			$this->display("header.htm");
		}

		if($noauto == FALSE) {
			$get_template = $this->find_template($template);
			if($get_template == FALSE) {
				echo 'Sorry, cannot find the page you\'re looking for'; //TODO: show error 404
			} else { $this->display($get_template); }
		} else {
			$this->display($template);
		}

		if (is_readable(APPPATH."views/$param1/header.htm") && $param1 != "" ) {
			$this->display("$param1/footer.htm");
		} else {
			$this->display("footer.htm");
		}
	}


	public function d($template='', $noauto=FALSE){
		$this->pre_display();

		if($noauto == FALSE) {
			$get_template = $this->find_template($template);
			if($get_template == FALSE) {
				echo 'Sorry, cannot find the page you\'re looking for'; //TODO: show error 404
			} else { $this->display($get_template); }
		} else {
			$this->display($template);
		}
	}

	//TODO: move this to helper ?
	public function check_is_file( $template ='' ) {
		$filename = dirname(dirname(__FILE__)).'/views/'.$template;
		if(is_file($filename)){
			return TRUE;
		}else{
			return FALSE;
		}
	}




	//TODO: revisit this !
	function load_additional_info() {
		if ($this->CI->session->userdata('u_id') != FALSE) {
			$this->assign('LOGGED_IN' , TRUE);
			//$this->assign('user_info' , $this->CI->session->get_user_information() );
		}

		// Get online visitor
		$sql = "
			SELECT COUNT(*) AS total FROM ci_sessions
			WHERE last_activity > (UNIX_TIMESTAMP() - 60 * 15)
		";
		$res = $this->CI->db->query($sql);
		$online_now = $res->row()->total;
		$this->assign('ONLINE_VISITOR' , $online_now);

		$this->assign_confirm();
	}

	function in_development() {
		$this->da('main/default/development.htm', TRUE);
	}


}
