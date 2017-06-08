<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 * CodeIgniter MY_Controller Libraries
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Controller
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/core/controller
 */
class MY_Controller extends CI_Controller {

	var $data	= null;
	//php 5 constructor
	function __construct( $ajax = false ) 
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
		// --------------------------------------------------------------------			
		// tickets
		$this->data['tickets'] = db_select(config('system/current/db_prefix').config('db_tickets'), '' ,array('json' => 'data'));
		//
		$tickets = null;
		if( user('store') != null && user('store') != "" )
		{
			$tickets = $this->data['tickets'];
			if( user('store') != 'all')
			{
				$tickets = index_array($tickets , 'store_id', true);
				if( isset($tickets[user('store')]) )
				{
					$tickets = $tickets[user('store')];
				}
				else
				{
					$tickets = null;
				}
			}
			// count
			$tickets = index_array($tickets , 'status', true);
			if( isset($tickets[1]) )
			{
				$tickets = count($tickets[1]);
			}
			else
			{
				$tickets = null;
			}
		}
		// --------------------------------------------------------------------	
		// test driver
		// $this->load->driver('fs_oauth');
		// $this->fs_oauth->initialize('test');
		// echo $this->fs_oauth->auth();
		// --------------------------------------------------------------------	
		// build menus
		$this->load->config('fs_menu');
		// main menu
		$main_nav = config('nav_array');
		if( isset($tickets) && $tickets != '' && $tickets != null )
		{
			$main_nav[5]['after'] = '<span class="notification">'.$tickets.'</span>';
		}
		$this->data['menu']['main'] = $this->wx_navigation->init('main', $main_nav);
		// user menu
		$this->data['menu']['user'] = $this->wx_navigation->init('user', config('user_nav_array'), array('id' => 'user_menu'));
		// --------------------------------------------------------------------			
		// load assets
		css_add('base,menu,icons,gui,fs.dialog');
		js_add_lines('CI_ROOT = "'.base_url().'"; CI_BASE = "'.base_url().'";', 'default', TRUE);
		js_add('https://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js');	
		js_add('jquery,fs.base');
		//
		// --------------------------------------------------------------------		
		// check for sufficient rights
		//
		// echo trim(_sha512(salt('lukas', 'exj5IJxo4UJ')));
		// if( $this->input->post('ajax') == true || login() == false ) 
		// {
		// 	$session_user_id 	= variable($this->session->userdata('fs_user_id'));
		// 	$session_token 		= variable($this->session->userdata('fs_token_name'));
		// 	$groups 			= $this->wx_navigation->get_groups();
		// 	// check if session is set
		// 	if( $session_user_id != null && $session_token != null )
		// 	{
		// 		// retrieve user-data from db
		// 		if( $user = $this->fs_authentication->_get_user($session_user_id, $session_token) )
		// 		{
		// 			if( !user_access($groups, $user['group']) )
		// 			{	
		// 				exit('An error occurred, please reload the page.');
		// 			}
		// 		}
		// 		else
		// 		{
		// 			exit('An error occurred, please reload the page.');
		// 		}
		// 	}
		// 	else
		// 	{
		// 		exit('An error occurred, please reload the page.');
		// 	}
		// }
		// else
		// {
			// $this->load->library('fs_analytics');
			// $this->fs_analytics->get_code();
			login($this->wx_navigation->get_groups());
		// }
	}
}
/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */