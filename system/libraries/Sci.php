<?php

// load Smarty library
require( BASEPATH . 'libraries/Smarty/Smarty.class.php');

class SCI extends Smarty {

	public function __construct(){
        parent::__construct();

        // Store the Codeigniter super global instance... whatever
        $CI =& get_instance();

        $CI->load->config('smarty');
		$smarty_config = $CI->config->item('smarty');

		foreach( $smarty_config as $k=>$tmp ) {
			$this->$k = $tmp;
		}

		//DEBUG == uncomment below line to test smarty installations
		//$this->testInstall();

		$this->assign("this", $CI);

		//Add all helpers to plugins_dir
        $helpers = glob( APPPATH.  'helpers/' , GLOB_ONLYDIR | GLOB_MARK);
        $helpers[] = BASEPATH. 'helpers/';
        foreach ($helpers as $helper)   { $this->plugins_dir[] = $helper;  }

        // register all User defined functions (in this case, loaded helpers) into smarty's plugins
        $functions = get_defined_functions();
        foreach($functions['user'] as $k=>$tmp) { $this->registerPlugin("function", $tmp , $tmp); }

        // Should let us access Codeigniter stuff in views
        $this->assign("this", $this->CI);

    }


//	public function __construct( )
//    {
//        parent::__construct();
//
//        // Store the Codeigniter super global instance... whatever
//        $CI =& get_instance();
//
//
//        $CI->load->config('smarty');
//		print $this->config->item('template_directory');
//
//        $this->template_dir      = config_item('template_directory');
//        $this->compile_dir       = config_item('compile_directory');
//        $this->cache_dir         = config_item('cache_directory');
//        $this->config_dir        = config_item('config_directory');
//        $this->template_ext      = config_item('template_ext');
//        $this->exception_handler = null;
//
//		$this->security 		= config_item('security');
//		$this->php_handling 	= config_item('php_handling');
//		$this->php_functions 	= config_item('php_functions');
//		$this->php_modifiers 	= config_item('php_functions');
//		$this->streams 			= config_item('streams');
//		$this->allow_php_tag 	= config_item('allow_php_tag');
//		$this->force_compile 	= config_item('force_compile');
//
//        // Only show serious errors. Without this if you try and use variables that
//        // do not exist, Smarty will throw variable does not exist errors
//        $this->error_reporting   = config_item('error_reporting');
//
//        // Add all helpers to plugins_dir
//        $helpers = glob(APPPATH . 'helpers/', GLOB_ONLYDIR | GLOB_MARK);
//
//        foreach ($helpers as $helper)
//        {
//            $this->plugins_dir[] = $helper;
//        }
//
//        // Should let us access Codeigniter stuff in views
//        $this->assign("this", $CI);
//    }

//	public function __construct()
//    {
//        parent::__construct();
//
//        // Store the Codeigniter super global instance... whatever
//        $CI =& get_instance();
//		//$this->Smarty();
//
//        $CI->load->config('smarty');
//		print_r($this->config);
//
//        $this->template_dir      = config_item('template_directory');
//        $this->compile_dir       = config_item('compile_directory');
//        $this->cache_dir         = config_item('cache_directory');
//        $this->config_dir        = config_item('config_directory');
//        $this->template_ext      = config_item('template_ext');
//        $this->exception_handler = null;
//
//		$this->security 		= config_item('security');
//		$this->php_handling 	= config_item('php_handling');
//		$this->php_functions 	= config_item('php_functions');
//		$this->php_modifiers 	= config_item('php_functions');
//		$this->streams 			= config_item('streams');
//		$this->allow_php_tag 	= config_item('allow_php_tag');
//		$this->force_compile 	= config_item('force_compile');
//
//        // Only show serious errors. Without this if you try and use variables that
//        // do not exist, Smarty will throw variable does not exist errors
//        $this->error_reporting   = config_item('error_reporting');
//
//        // Add all helpers to plugins_dir
//        $helpers = glob(APPPATH . 'helpers/', GLOB_ONLYDIR | GLOB_MARK);
//
//        foreach ($helpers as $helper)
//        {
//            $this->plugins_dir[] = $helper;
//        }
//
//        // Should let us access Codeigniter stuff in views
//        $this->assign("this", $CI);
//    }

//
//   function SCI($config = array())
//   {
//		//parent::__construct();
//        $this->Smarty();
//
//        if (count($config) > 0) {
//            $this->initialize($config);
//        }
//
//		log_message('debug', "Smarty Loaded");
//   }
//
//   function disall($path){
//		echo "Header";
//		$this->display($path);
//		$this->display($path);
//		echo "footer";
//   }
//
//    function initialize($config = array()){
//        foreach ($config as $key => $val){
//            if (isset($this->$key)){
//                $method = 'set_'.$key;
//                if (method_exists($this, $method)){
//                    $this->$method($val);
//                }
//                else{
//                    $this->$key = $val;
//                }
//            }
//        }
//    }
//
//    function set_delimiters($l = '{', $r = '}'){
//        $this->left_delimiter = $l;
//        $this->right_delimiter = $r;
//    }

}
