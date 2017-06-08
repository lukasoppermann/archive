<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Ajax extends CI_Controller {
	
	function __construct() 
 	{
		parent::__construct();
		//
		// get config from db
		$this->config->set_config_from_db();
		// set charset
		Header("Content-type: text/html;charset=UTF-8");
		// set header for browser to not cache stuff
		Header("Last-Modified: ". gmdate( "D, j M Y H:i:s" ) ." GMT"); 
		Header("Expires: ". gmdate( "D, j M Y H:i:s", time() ). " GMT"); 
		Header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		Header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		Header("Pragma: no-cache" ); // HTTP/1.0
	}
	
	function index( $method = null )
	{
		if( $method == 'get_user_data' )
		{
			echo $this->_get_user_data();
		}
		elseif( $method == null )
		{
			return FALSE;
		}
	}
	
	public function _get_user_data()
	{
		$user = $this->fs_authentication->_get_user($this->input->post('username'));
		echo $user['firstname'].' '.$user['lastname'];
	}
// end of class 
}
/* End of file ajax.php */