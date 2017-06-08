<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
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
		// get config data
		$this->config->config_from_db();
		// set charset
		header("Content-type: text/html;charset=".$this->config->item('charset'));
		// --------------------------------------------------------------------
		// load assets
		css_add('screen',array('reset,screen'));
		// js_add('default',array('jquery', 'ui/minified/jquery.ui.core.min', 'ui/minified/jquery.ui.widget.min', 'ui/minified/jquery.ui.mouse.min', 'javascript'));
		// set language	
		$languages = $this->config->item('languages');
		$lang_id = $this->config->item('lang_id');
		// --------------------------------------------------------------------
		// Initialize Menus
		// Main
		$this->data['menu']['main'] = $this->fs_navigation->tree(array('menu' => '1', 'id' => 'nav', 'language' => $lang_id));
		// Metamenu
		$this->data['menu']['meta'] = $this->fs_navigation->tree(array('menu' => '2', 'id' => 'meta_menu', 'language' => $lang_id));
		// Footer Menu
		$this->data['menu']['footer'] = $this->fs_navigation->tree(array('menu' => '2', 'id' => 'meta_menu', 'language' => $lang_id));
		// --------------------------------------------------------------------		
		// Language Switch
		$lang_list = null;
		foreach($languages['array'] as $language)
		{
			$lang_list .= "<li class='".($language['id'] == $lang_id ? 'active ' : '').$language['abbr']."'>
				<a class='lang' href='".base_url(TRUE).$language['abbr'].'/home'."'>".$language['self_name']."</a>
			</li>";
		}
		$this->data['menu']['lang'] = "<ul id='lang_menu'>".$lang_list."</ul>";
	}
}	
/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */