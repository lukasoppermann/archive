<?php 
if (! defined('BASEPATH')) exit('No direct script access');

class Error_handler extends Controller {

	//php 5 constructor
	function __construct() 
 	{
		parent::Controller();
	}
	
	//php 4 constructor
	function Error_handler() 
	{
		parent::Controller();			
	}

	function error_404()
	{
		$CI = & get_instance();
		$CI->uri->analyse();
		$CI->output->set_status_header('404');
		$config = $CI->config->item('templates'); 
		//
		$templates = $CI->config->item('templates');
		//
		$data['title'] = 'ERROR NEW';
		$data['message'] = 'ERROR NEW';
		//
		$data['meta_menu'] = NULL;			
		// $data['footer'] = $this->navigation->output('footer');

		// $this->page->add_data($data);

		// $this->page->load();
		$data['main_menu'] = $CI->navigation->output('main');
		// $data['sub_menu'] = $this->navigation->output('sub');
		//
		$data['header_page'] = $config['header_default'];
		$data['footer_page'] = $config['footer_default'];
		$data['error_page'] = $config['error_404'];
		
			
		$CI->load->view($templates['error_page'], $data);
	}
}
