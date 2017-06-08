<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Dashboard extends MY_Controller {

	public function index()
	{
	    // load into template
        view('custom/dashboard', $this->data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */