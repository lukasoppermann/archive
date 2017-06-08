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
		// Prepare stores
		$this->data['inquire_menu'] = '';
		// set HQ
		if( config('contact') != null )
		{
			$stores_footer[0][0] = $this->load->view('footer_store',config('contact'),TRUE);
		}
		// sort stores by position
		$stores = config('store');
		if( isset($stores) )
		{
			foreach( (array) $stores as $k => $_store )
			{
				$this->data['stores'][$_store['position']] = $_store;
			}
			ksort($this->data['stores']);
			// set stores
			$i = 0;
			$c = 1;
			foreach( $this->data['stores'] as $key => $store )
			{
				// stores footer
				$i = 1 - $i;
				$stores_footer[$i][$store['position']] = $this->load->view('footer_store',$store,TRUE);
				// add to inquire menu
				if( $c <= 10)
				{
					++$c;
					$this->data['inquire_menu'][] = '<li class="store"><a href="'.base_url().'stores/'.$store['permalink'].'">
							<span class="name">'.$store['name'].'</span><span class="tel">'.$store['phone'].'</span>
					</a></li>';
				}
			}
			if( $c >= 10)
			{
				$this->data['inquire_menu'][] = '<li class="store more-stores"><a href="'.base_url().'stores/">
						<span class="name">more stores</span>
				</a></li>';
			}
			else
			{
				$this->data['inquire_menu'][] = '<li class="store more-stores"><span class="name"></span></li>';
			}
			//
			$this->data['inquire_menu'] = implode('',$this->data['inquire_menu']);
			//
			$this->data['stores_footer'] = '<div class="column">'.implode('',$stores_footer[0]).'</div>';
			$this->data['stores_footer'] .= '<div class="column">'.implode('',$stores_footer[1]).'</div>';
		}
		elseif( isset($stores_footer[0]) )
		{
			$this->data['stores_footer'] = '<div class="column">'.implode('',$stores_footer[0]).'</div>';
		}
		// --------------------------------------------------------------------			
		// load assets
		css_add('base,menu,icons,gui,fs.dialog');
		js_add_lines('CI_ROOT = "'.base_url().'"; CI_BASE = "'.base_url().'";', 'default', TRUE);
		js_add('https://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js');	
		js_add('jquery,base,fs.dialog');
		//
		// --------------------------------------------------------------------		
		// check for sufficient rights
	}
}
/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */