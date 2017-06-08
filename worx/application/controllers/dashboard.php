<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Dashboard extends MY_Controller {

	public function index()
	{
		// load assets
		css_add('widgets');
	    // load into template
        view('dashboard/index', $this->data);
	}
	
	function logout()
	{
		logout();
	}
}

/* End of file dashboard.php */
/* Location: ./application/controllers/dashboard.php */