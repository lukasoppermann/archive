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
		// get config from db
		$this->config->set_config_from_db();
		// get homepage_type
		$entry_types = $this->config->item('entry_types');
		foreach($entry_types as $id => $type)
		{
			// fetch homepage type
			if($type['name'] == 'homepage_type')
			{
				$this->config->set_item('homepage_type', $id);
				unset($entry_types[$id]);
			}
		}
		// reset entry types
		$this->config->set_item('entry_types', $entry_types);
		// set charset
		Header("Content-type: text/html;charset=UTF-8");
		// set header for browser to not cache stuff
		Header("Last-Modified: ". gmdate( "D, j M Y H:i:s" ) ." GMT"); 
		Header("Expires: ". gmdate( "D, j M Y H:i:s", time() ). " GMT"); 
		Header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		Header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		Header("Pragma: no-cache" ); // HTTP/1.0
		// --------------------------------------------------------------------
		// Orders
		$orders = db_select(config('db_orders'), '', array('json' => 'data', 'index' => 'status', 'unstack' => false));
		// define variables
		$this->data['orders']['open'] = array();
		$this->data['orders']['closed'] = array();
		// get customer_ids for orders
		foreach($orders as $status => $order)
		{
			foreach( $order as $pos => $item )
			{
				$customers[] = $item['customer_id'];
			}
		}
		// get customers from db
		$this->data['customers'] = db_select(config('db_customers'), array('id' => $customers), array('index' => 'id', 'index_single' => true));
		// loop through orders
		foreach($orders as $status => $order)
		{
			// sort orders
			if(strtolower($status) != 'closed')
			{
				$this->data['orders']['open'] = array_merge($orders[$status], $this->data['orders']['open']);
			}
			else
			{
				$this->data['orders']['closed'] = array_merge($orders[$status], $this->data['orders']['closed']);
			}
		}
		// --------------------------------------------------------------------
		// Banners
		$banners = db_select(config('system/current/db_prefix').config('db_data'), array('type' => 'banner'), 
								array('json' => array('data'), 'single' => false, 'unstack' => FALSE));
		// merge banners
		if( isset($banners) && is_array($banners) && count($banners) > 0 )
		{
			foreach($banners as $k => $banner)
			{
				if( isset($banner['data']) && $banner['data'] != '' && $banner['data'] != NULL && is_array($banner['data']) && count($banner['data']) > 0)
				{
					foreach( $banner['data'] as $img_id => $image )
					{
						$updates[$img_id] = $image;
						$entries[$img_id] = variable($image['entry']);
					}
				}
				// delete banner
				db_delete(config('system/current/db_prefix').config('db_data'), array('id' => $banner['id']));
			}
			if( isset($updates) )
			{
				// get entries
				array_filter($entries);
				$db_entries = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $entries), 
										array('json' => array('data'), 'single' => false, 'unstack' => FALSE, 'index' => 'id', 'index_single' => true));
				// update banners
				foreach( $updates as $id => $content )
				{
					if( !isset($content['entry']) || !isset($db_entries[$content['entry']]) )
					{
						unset($updates[$id]);
					}
				}
				// update data
				$updates = array_filter($updates);
				//
				db_insert( config('system/current/db_prefix').config('db_data'), array('key' => 'settings', 'type' => 'banner', 'data' => json_encode($updates) ) );
			}
		}
		//
		// if( user('store') != null && user('store') != "" )
		// {
		// 	$tickets = $this->data['tickets'];
		// 	if( user('store') != 'all')
		// 	{
		// 		$tickets = index_array($tickets , 'store_id', true);
		// 		$tickets = $tickets[user('store')];			
		// 	}
		// 	// count
		// 	$tickets = index_array($tickets , 'status', true);
		// 	if( isset($tickets[1]) )
		// 	{
		// 		$tickets = count($tickets[1]);
		// 	}
		// 	else
		// 	{
		// 		$tickets = null;
		// 	}
		// }
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
		if( isset($this->data['orders']['open']) && $this->data['orders']['open'] != '' && $this->data['orders']['open'] != null )
		{
			$main_nav[6]['after'] = '<span class="notification">'.count($this->data['orders']['open']).'</span>';
		}
		$this->data['menu']['main'] = $this->wx_navigation->init('main', $main_nav);
		// user menu
		$this->data['menu']['user'] = $this->wx_navigation->init('user', config('user_nav_array'), array('id' => 'user_menu'));
		// --------------------------------------------------------------------			
		// load assets
		css_add('base,menu,icons,gui,fs.dialog');
		js_add_lines('CI_ROOT = "'.base_url().'"; CI_BASE = "'.base_url().'";', 'default', TRUE);
		js_add('jquery, fs.base');
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