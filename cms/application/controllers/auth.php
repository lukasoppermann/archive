<?php 
if (! defined('BASEPATH')) exit('No direct script access');

// open class
class Auth extends Controller {

	var $configs = NULL;

	//php 5 constructor
	function __construct() 
 	{
		parent::Controller();
	}
	
	//php 4 constructor
	function Auth() 
	{
		parent::Controller();
	}
	
	// index 
	function index()
	{
		// -----------------
		// CSS		
		$this->stylesheet->add('base-0.0.1, form-0.0.1, dialog-0.0.1');
		// -----------------
		if($this->uri->segment(3) == 'logout')
		{
			$this->logout();
		}
			$this->load->library('session');

			// url geht nicht weil session current_url nach redirect verschwindet
			if(!$this->input->post('submit') && $this->auth->_validate_login() != TRUE)
			{
				$data['title'] = "Please Login";
				$opt['config'] = 'form_login';	
				$opt['form_attributes']['id'] = "login";
				$opt['opt']['class'] = "rounded";		
				$opt['url'] = '/auth';		
				$data['content'] = form($opt, 'default_form');
				$this->load->view('custom/page', $data);
			}
			else
			{
				if($this->auth->_try_login() || $this->auth->_validate_login())
				{	
					$url = $this->session->userdata('current_url');
					$url = !empty($url) ? $url : base_url().$this->config->item('lang_default_abbr').'/dashboard';							
					redirect($url);
				}
				else
				{
					$data['title'] = "Please Login";
					$opt['config'] = 'form_login';	
					$opt['form_attributes']['id'] = "login";
					$opt['opt']['class'] = "rounded";
					$opt['legend'] = false;					
					$data['content'] = form($opt, 'default_form');
					$this->load->view('custom/page', $data);	
				}
			}
	}
	
	// logout
	function logout()
	{	// See http://codeigniter.com/forums/viewreply/662369/ as the reason for the next line
		$this->load->library('session');
		$this->session->set_userdata(array('user_id' => '', 'user' => '', 'email' => '', 'logged_in' => ''));
		$this->session->sess_destroy();

		// $path = '/';
		// $array = array('session_name','user_id_name','token_name');
		// 
		// foreach($array as $string)
		// {
		// 	delete_cookie($this->config[$string], $this->config['domain'], $path, $this->config['prefix']);
		// }
	}
// close controller	
}

/* End of file auth.php */
/* Location: ./application/formandsystem/controllers/auth.php */