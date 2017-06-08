<?php 
if (! defined('BASEPATH')) exit('No direct script access');

// open class
class Dashboard extends MY_Controller {

	var $configs = NULL;

	//php 5 constructor
	function __construct() 
 	{
		parent::Controller();
	}
	
	//php 4 constructor
	function Dashboard() 
	{
		parent::Controller();
	}
	
	// index 
	function index()
	{
		// -----------------
		// CSS		
		$this->stylesheet->add('base-0.0.1, form-0.0.1, dashboard-0.0.1');
		// Navigation
		$data['main_menu'] = $this->navigation->tree(array('menu' => 'main', 'lvl' =>'2'));
		$data['top_right'] = $this->navigation->tree(array('menu' => 'top_right', 'lvl' =>'1'));
		$data['footer'] = $this->navigation->tree(array('menu' => 'footer', 'lvl' =>'1'));				
		$data['breadcrumbs'] = $this->navigation->path(array('path_class' => 'left', 'path_before' => '<a href="http://www.hygromatik.com">HygroMatik</a>'));
		//
		$data['title'] = "Dashboard";
		$data['content'] = "<h1 class='tcd1 icl3'>Willkommen im Form&System CMS</h1>
		<p>Bei dieser Version handelt es sich um eine frühe Alphaversion. Etwaige Bugs und Fehler können auftreten. Der Funktionsumfang ist stark beschränkt und einige Funktionen sind möglicherweise nicht voll funktionsfähig.</p>
		<p>
		Das Form&System Team freut sich über Bugreports, Anregungen, das Melden von Performanceproblemen und anderes Feedback. Bitte senden Sie uns eine email an <a href='mailto:support@formandsystem.com'>support@formandsystem.com</a>";
			
		$this->load->view('custom/dashboard_page', $data);
	}
// close controller	
}

/* End of file dashboard.php */
/* Location: ./application/formandsystem/controllers/dashboard.php */