<?php 
if (! defined('BASEPATH')) exit('No direct script access');

// open class
class Base extends MY_Controller {

	//php 5 constructor
	function __construct() 
 	{
		parent::__construct();
	}
	
	function index($page = null)
	{
		// get menus, etc. to $data
		unset($data);
		$data = $this->data;
		// get teaser
		$teasers = index_array(get_db_data(config('prefix').config('db_entries'), array('where' => array('type' => '2'), 'select' => '*')), 'language', TRUE);
		
		if(array_key_exists($this->config->item('lang_id'), $teasers))
		{
			$teasers = $teasers[$this->config->item('lang_id')];
			// sort teaser by position
			$teasers = sort_array($teasers, 'position', TRUE);
			//
			$data['teaser'] = '';
			foreach($teasers as $teaser)
			{
				if($teaser['status'] == 1)
				{
					$length = 150;
					if (strlen($teaser['text']) <= $length) 
					{
					    $teaser['text'] = $teaser['text'];
					}
					else 
					{
						if(isset($teaser['excerpt']))
						{
							$teaser['text'] = substr($teaser, 0, strpos($teaser, "</h3>")).'<p>'.$teaser['excerpt'].'</p>';
						}
						else
						{
							$teaser['text'] = substr($teaser, 0, strpos($teaser, "</h3>"));
						}
					}
					$data['teaser'] .= $this->load->view('custom/news',$teaser, TRUE);
				}
			}
		}
		// if teaser active
		if($variable = $this->navigation->active('variables'))
		{
			$news = 'false';
			$link = explode('/', $variable);
			if($link[1] == 'news')
			{
				if(count($link) >= 2)
				{
					$news = 'true';
					end($link);
					$link = $link[key($link)];
					$teaser = index_array($teasers, 'permalink');
					$teaser = $teaser[$link];
					//
					$data['title'] = $teaser['title'];
					$data['page'] = $this->load->view('default', array('text' => $teaser['text'], 'title' => $teaser['title']), true);
					// set title 
					$this->config->set_item('title', $teaser['title']);	
					// get header image
					if(isset($teaser['header']))
					{
						$data['header'] = $teaser['header'];
						$data['slideshow'] = $teaser['slideshow'];
					}
					else
					{
						$data['header'] = null;
					}
				}
			}
		}
		if(!isset($news) || $news == 'false')
		{
			//
			$data['page'] = $this->entries->get($this->navigation->current('id'), $this->data, TRUE);
			// set tags
			if($tags = $this->entries->get_element('tags', $this->navigation->current('id')))
			{
			 	$this->config->set_item('tags', $tags);
			}
			// set description
			if($desc = $this->entries->get_element('description', $this->navigation->current('id')))
			{
			 	$this->config->set_item('description', $desc);
			}
			$data['header'] = null;
			// get header image
			if($header = $this->entries->get_element('header', $this->navigation->current('id')))
			{
				$data['header'] = $header;
			}
			// set title 
			$this->config->set_item('title', $this->entries->get_element('title', $this->navigation->current('id')));
		}
		// load view
		view('', $data);
	}
//
}	
/* End of file Base.php */
/* Location: ./application/controllers/Base.php */