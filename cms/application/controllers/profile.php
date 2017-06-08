<?php 
if (! defined('BASEPATH')) exit('No direct script access');

// open class
class Profile extends Controller {

	var $configs = NULL;

	//php 5 constructor
	function __construct() 
 	{
		parent::Controller();
	}
	
	//php 4 constructor
	function Profile() 
	{
		parent::Controller();
	}
	
	// index 
	function index()
	{
		// Navigation
		$this->navigation->initialize();
		//
		$data['footer'] = $this->navigation->output('footer');
		$data['main_menu'] = $this->navigation->output('main');
		$data['top_right'] = $this->navigation->output('top_right');
		//
		$data['title'] = "Profil";
		$data['content'] = "<h1 class='tcd1 icl1'>Ihr Profil</h1>
		<p>Sie befinden sich in Ihrem profil. Momentan können Sie hier noch nichts ändern, später werden Sie hier Ihren Account bearbeiten können.</p>";
		$data['breadcrumbs'] = "<span>Dashboard</span><span class='arrow'>&raquo;</span><span>Profil</span>";	
		$this->load->view('custom/dashboard_page', $data);
	}
// close controller	
}

/* End of file dashboard.php */
/* Location: ./application/formandsystem/controllers/profile.php */