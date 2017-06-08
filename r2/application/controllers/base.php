<?php 
if (! defined('BASEPATH')) exit('No direct script access');

// open class
class Base extends MY_Controller {

	var $page_data;

	//php 5 constructor
	function __construct() 
 	{
		parent::__construct();
	}
	
	function index($page = null)
	{
		// --------------------------------------------------------------
		// load assets
		$this->load->model('base_model');
		
		// --------------------------------------------------------------
		// language assets
		$language = $this->config->item('languages');
		$language = index_array($language['array'],'abbr');
		$this->lang->load('text', $language[config('lang_abbr')]['name']);
		// --------------------------------------------------------------
		// get menus, etc. to $data
		$data = $this->data;
		// --------------------------------------------------------------
		// get pages
		if($page == 'home' || $page == '')
		{
			$teaser = $this->home();
			$data['link'] = '<div id="r2_link">
			<span class="before-logo">'.lang('before_logo').'</span>
			<a href="http://www.gzo-gmbh.de"><img src="http://www.r2-gmbh.com/media/layout/gzo_logo.jpg" alt="'.lang('alt_logo').'" /></a>
			<span class="after-logo">'.lang('after_logo').'</span>
			</div>';
		}
		elseif($page == 'news')
		{	
			$data = array_merge($data, $this->base_model->news($this->fs_navigation->variables()));
			// Header
			$data['header'] = $this->base_model->header($this->fs_navigation->variables());
			//
		}
		// --------------------------------------------------------------
		// get content pages
		//
		if($page != 'news')
		{
			// Header
			$data['header'] = $this->base_model->header($this->fs_navigation->current('id'));
			//
			$data['page'] = '<div class="entry">'.$this->fs_entries->get($this->fs_navigation->current('id'), $this->data, TRUE).'</div>';
			if($this->fs_entries->get_element('status', $this->fs_navigation->current('id')) == 1)
			{
				// get downloads
				$downloads = $this->base_model->downloads($this->fs_entries->get_element('id', $this->fs_navigation->current('id')));
				// set tags
				if($tags = $this->fs_entries->get_element('tags', $this->fs_navigation->current('id')))
				{
				 	$this->config->set_item('tags', $tags);		
				}
				// set description
				if($desc = $this->fs_entries->get_element('description', $this->fs_navigation->current('id')))
				{
				 	$this->config->set_item('description', $desc);		
				}
				// set title 
				$this->config->set_item('title_tag', $this->fs_entries->get_element('title', $this->fs_navigation->current('id')));	
			}
			else
			{
				// Header
				$data['header'] = $this->base_model->header();
				//
				$_data['headline'] = "Page not found.";
				$_data['message'] = "The page you are looking for does not exist.";
				$data['page'] = $this->load->view('error', $_data, TRUE);
			}
		}
		else
		{
			// get downloads
			$downloads = $this->base_model->downloads($this->fs_navigation->variables());
		}
		// add teaser
		$data['page'] = variable($data['page']).variable($teaser).variable($downloads);
		// load view
		view('', $data);
	}
	// --------------------------------------------------------------
	// home page
	function home()
	{
		return $this->base_model->teaser();
	}
//
}	
/* End of file Base.php */
/* Location: ./application/controllers/Base.php */