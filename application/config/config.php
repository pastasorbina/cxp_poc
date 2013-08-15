<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| REQUIRED CONFIGURATION
|--------------------------------------------------------------------------
|
| Main Configuration
|
*/

$config['debug_mode'] = TRUE;

$config['branch_path']			= '';

if($_SERVER['HTTP_HOST'] == 'localhost') {
	$config['ground_url']  		= 'http://localhost/poc/';
	$config['remote_asset_url'] = 'http://localhost/poc/asset/';
	$config['remote_url']  		= 'http://localhost/poc/';
	$config['absolute_path']	= REALPATH; 
} else {
	$config['ground_url']  		= 'http://proofofconcept.co.id/demo/';
	$config['remote_asset_url'] = 'http://proofofconcept.co.id/demo/asset/';
	$config['remote_url']  		= 'http://proofofconcept.co.id/demo/';
	$config['absolute_path']	= REALPATH;
}
 


 

$config['base_url']			= $config['ground_url'].$config['branch_path'];
$config['userfiles_path']	= 'userfiles/';
$config['ckeditor_path']	= 'editor/';

$config['view_path'] = 'main/';
$config['asset_url'] = $config['ground_url'].'asset/';

//-------------------------------------------------------


$config['salt'] = "teddygantengmakansiomay";

$config['encryption_key'] = 'liberationarmy';

$config['environment'] = ENVIRONMENT;

$config['index_page'] = '';

$config['uri_protocol']	= 'AUTO';
$config['url_suffix'] = '';

$config['language']	= 'english';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = TRUE;
$config['subclass_prefix'] = 'MY_';
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';

$config['allow_get_array']		= TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger']	= 'c';
$config['function_trigger']		= 'm';
$config['directory_trigger']	= 'd'; // experimental not currently in use

$config['log_threshold'] = 0;
$config['log_path'] = '';
$config['log_date_format'] = 'Y-m-d H:i:s';

$config['cache_path'] = '';

$config['sess_cookie_name']		= 'ci_gudangbrands_csd'; //ci_gudangbrand_csd
$config['sess_expiration']		= 7200;
$config['sess_expire_on_close']	= TRUE; //TRUE
$config['sess_encrypt_cookie']	= FALSE;
$config['sess_use_database']	= TRUE; //TRUE
$config['sess_table_name']		= 'ci_sessions';
$config['sess_match_ip']		= FALSE;
$config['sess_match_useragent']	= FALSE; //FALSE
$config['sess_time_to_update']	= 300;

$config['cookie_prefix']	= "esturitarium";
$config['cookie_domain']	= "";
//$config['cookie_domain']	= $_SERVER['HTTP_HOST'];
$config['cookie_path']		= "/";
$config['cookie_secure']	= FALSE;



$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;

$config['compress_output'] = FALSE;

$config['time_reference'] = 'local';

$config['rewrite_short_tags'] = FALSE;

$config['proxy_ips'] = '';




/* End of file config.php */
/* Location: ./application/config/config.php */
