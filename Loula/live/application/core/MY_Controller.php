<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 * CodeIgniter MY_Controller Libraries
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Controller
 * @author		Lukas Oppermann - veare.net
 */
class MY_Controller extends CI_Controller {

	var $data;
	//php 5 constructor
	function __construct() 
 	{
		parent::__construct();
		// get config
		$this->config->config_from_db('client_data');
		// set charset
		Header("Content-type: text/html;charset=UTF-8");
		// set header for browser to not cache stuff
		Header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); 
		Header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); 
		Header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		Header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		Header("Pragma: no-cache" ); // HTTP/1.0
		// --------------------------------------------------------------------	
		// sales
		$this->load->model('store_model');
		// --------------------------------------------------------------------	
		// current page as var
		if( $this->store_model->check_sales() !== false )
		{
			$show[] = 5;
		}
		
		if( $this->store_model->check_instore() !== false )
		{
			$show[] = 'instore';
		}
		
		// init nav
		$this->cms->init_nav($show);			
		//
		$_cur = explode('/',trim($this->cms->current(),'/'));
		$this->data['current'] = $_cur[0];
		if( $this->data['current'] == '')
		{
			$this->data['current'] = 'home';
		}
		// --------------------------------------------------------------------	
		// add facebook like
		$this->data['fb_meta'] = '
		<meta property="og:type" content="product" />
		<meta property="og:site_name" content="Loula - High Fashion Shoes" />
		<meta property="fb:app_id" content="339368402781341" />
		<meta property="og:image" content="'.base_url().'media/layout/loula_fb.jpg" />'."\n";
		// --------------------------------------------------------------------	
		// get pages
		$this->load->model('page_model');
		// contact us
		$footer_contact = $this->page_model->get_page(2);
		$this->data['contact_us'] = '<div id="contact_us">'.$footer_contact[0]['text'].'</div>';
		// map
		$this->data['map'] = '<div id="map" class="dialog-box-link" data-dialog-type="gmap"><img src="'.base_url().'media/layout/loula_map.jpg" alt="Find the Loula Shoe store" /></div>';
		// about us
		$footer_about = $this->page_model->get_page(3);
		$this->data['about_us'] = '<div id="about_us">'.$footer_about[0]['text'].'</div>';
		// about images
		$this->data['footer_images'] = $this->page_model->get_images($footer_about[0]['images']);
		// check login
	}
// close class
}