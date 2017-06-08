<?php if (! defined('BASEPATH')) exit('No direct script access');
/** R2
 * CodeIgniter MY_Controller Libraries
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Controller
 * @author		Lukas Oppermann - veare.net
 */
class MY_Controller extends CI_Controller {

	var $data	= null;
	var $system = null;
	//php 5 constructor
	function __construct() 
 	{
		parent::__construct();
		// set charset
		header("Content-type: text/html;charset=".$this->config->item('charset'));
		// --------------------------------------------------------------------
		// load assets
		css_add('screen',array('reset,fonts,screen'));
		// set language	
		$languages = Array(
	    'abbr' => Array(
				1 => 'en',
				2 => 'de'
			),
			'id' => Array(
				'en' => 1,
				'de' => 2
			),
			'array' => array(
				1 => Array
				(
					'id' => 1,
					'abbr' => 'en',
					'status' => 1,
					'name' => 'english',
					'label' => 'english'
				),
				2 => Array
				(
					'id' => 2,
					'abbr' => 'de',
					'status' => 1,
					'name' => 'deutsch',
					'label' => 'deutsch',
					'default' => TRUE
				)
			)	
		);
		$this->config->set_item('languages', $languages);
		// define current language
		$segment = $this->config->item('url_lang');
		if(isset($segment))
		{
			$cur = $this->uri->segment($this->config->item('url_lang'));
		}
		else
		{
			$cur = $this->uri->segment(1);			
		}
		if(isset($cur) && isset($languages['id'][$cur]))
		{
			$this->config->set_item('lang_abbr', $cur);
			$this->config->set_item('lang_id', $languages['id'][$cur]);
		}
		else
		{
			$this->config->set_item('lang_abbr', 'de');
			$this->config->set_item('lang_id', 2);
		}
		$lang_id = $this->config->item('lang_id');
		
		$this->load->library('Navigation');
		// --------------------------------------------------------------------
		// Initialize Menus
		// Main
		$this->data['menu']['main'] = $this->navigation->tree(array('menu' => '1', 'id' => 'nav', 'language' => $lang_id));
		// Metamenu
		$this->data['menu']['meta'] = $this->navigation->tree(array('menu' => '2', 'id' => 'meta_menu', 'language' => $lang_id));
		// Footer Menu
		$this->data['menu']['footer'] = $this->navigation->tree(array('menu' => '2', 'id' => 'meta_menu', 'language' => $lang_id));
		// --------------------------------------------------------------------		
		// Language Switch
		$lang_list = null;
		foreach($languages['array'] as $language)
		{
			$lang_list .= "<li class='".($language['id'] == $lang_id ? 'active ' : '').$language['abbr']."'>
				<a class='lang' href='".base_url(TRUE).$language['abbr'].'/home'."'>".$language['label']."</a>
			</li>";
		}
		$this->data['menu']['lang'] = "<ul id='lang_menu'>".$lang_list."</ul>";
	}
}	
/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */