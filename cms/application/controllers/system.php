<?php 
if (! defined('BASEPATH')) exit('No direct script access');

// open class
class System extends Controller {

	//php 5 constructor
	function __construct() 
 	{
		parent::Controller();
	}
	
	//php 4 constructor
	function System() 
	{
		parent::Controller();
	}
	
	// system 
	function index()
	{
		// Navigation
		$data['main_menu'] = $this->navigation->tree(array('menu' => 'main', 'lvl' =>'2'));
		$data['top_right'] = $this->navigation->tree(array('menu' => 'top_right', 'lvl' =>'1'));
		$data['footer'] = $this->navigation->tree(array('menu' => 'footer', 'lvl' =>'1'));				
		$data['breadcrumbs'] = $this->navigation->path(array('path_class' => 'left', 'path_before' => '<a href="http://www.veare.net">veare</a>'));
	}

// close system controller	
}
	
/* End of file System.php */
/* Location: ./application/web_site_name/controllers/System.php */