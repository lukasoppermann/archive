<?php 
if (! defined('BASEPATH')) exit('No direct script access');

// open class
class Menu extends Controller {

	var $configs = NULL;
	var $data = NULL;
	var $client = NULL;

	//php 5 constructor
	function __construct() 
 	{
		parent::Controller();
	}
	
	//php 4 constructor
	function Menu() 
	{
		parent::Controller();
	}

	// setup 
	function setup($lang = NULL)
	{
		// Load Config files
		$this->config->load($this->config->item('client_config_default'), '','',$this->config->item('client_prefix'));
		$this->config->load($this->config->item('client_config_database'), '','',$this->config->item('client_prefix'));
		$this->config->load($this->config->item('client_config_language'), '','',$this->config->item('client_prefix'));	
		$this->config->load('cms_forms');
		// -----------------
		// Prepare Config 
		$this->configs = $this->config->item($this->config->item('client_prefix').'config');	
		$this->configs['forms'] = $this->config->item('form_menu');	
		// -----------------
		// Load Language files
		$this->lang->load('form', $this->config->item('language'));
		// -----------------
		// Load Libraries
		// $this->load->library('navigation','','client_navigation');	
		/*----------------------------------------------------------------------*/
		// client prefix
		$this->client['prefix'] = &$this->config->item('client_prefix');
		/*----------------------------------------------------------------------*/
		// Load Client Menu
		$this->load->library('navigation',array('db_table' => $this->client['prefix'].$this->configs['db_menu']),'client_menu');
		// -----------------
		// CSS		
		$this->stylesheet->add('base-0.0.1, form-0.0.1, menu-0.0.1, icons-0.0.1, dialog-0.0.1');
		// -----------------
		// Client Language
		$this->load->library('languages', array('prefix'=>$this->client['prefix']), 'client_lang');
		$this->client['languages'] 		= $this->client_lang->get();
		$this->client['default_lang'] 	= $this->client_lang->def('id');	
		$this->client['cms_lang']		= $this->client_lang->get_by_abbr($this->config->item('lang_abbr'));
		// /*----------------------------------------------------------------------*/
		// Navigation
		//
		$this->data['main_menu'] = $this->navigation->tree(array('menu' => 'main', 'lvl' =>'2'));
		$this->data['top_right'] = $this->navigation->tree(array('menu' => 'top_right', 'lvl' =>'1'));
		$this->data['footer'] = $this->navigation->tree(array('menu' => 'footer', 'lvl' =>'1'));
		$this->data['breadcrumbs'] = $this->navigation->path(array('path_class' => 'left', 'path_before' => 
									'<a href="http://www.veare.net">veare</a>'));		
		//
	}
	
	// index 
	function index($lang = NULL)
	{
		// -----------------
		// run setup
		$this->setup($lang);
		// -----------------
		// load assets
		$this->javascript->add('cms.menu');	
		$this->javascript->add('jquery.reveal');		
		// -----------------
		if(!array_key_exists($lang, $this->client['languages']))
		{
			$lang = $this->client['default_lang'];
		}
		// -----------------	
		$array = array('fn' =>'_item_edit', 'status' => 'all', 'list_class' => 'menu-edit sortable',
		'menu' => 'main', 'language' => $lang, 'menu_id' => 'main', 'show_empty' => TRUE);
		//
		$client_menu = "<div class=\"cms-box menu-edit-box float-left\"><h4>Hauptmenü<span class=\"icon add\"></h4>".$this->client_menu->tree($array)."</div>";
		//
		$array['menu'] = 'footer';
		$array['menu_id'] = 'footer';		
		$client_menu .= "<div class=\"cms-box menu-edit-box float-left\"><h4>Fußzeilenmenü<span class=\"icon add\"></h4>".$this->client_menu->tree($array)."</div>";	
		//
		$array['menu'] = 'meta';
		$array['menu_id'] = 'meta';		
		$client_menu .= "<div class=\"cms-box menu-edit-box float-left\"><h4>Metamenü<span class=\"icon add\"></span></h4>".$this->client_menu->tree($array)."</div>";
		//
		$this->data['content'] = $this->_language_menu($lang);
		$this->data['content'] .= $client_menu;
		//
		//
		/*----------------------------------------------------------------------*/
		// Data for Page		
		// Title
		$this->data['title'] = lang('title_edit_navigation');	
		// Content (Load Form)
		// $data['content'] = $this->load->view('forms/navigation_form', $data_form, TRUE);
		/*----------------------------------------------------------------------*/
		// Dialog
		$this->data['dialog'] =	"<div id=\"dialog_box\" class=\"dialog medium\">
									<h3><span class='title'>Neuen Menüpunkt erstellen</span><span class=\"close-dialog icon delete\"></span></h3>
									
									<p></p>
								</div>
								";
		/*----------------------------------------------------------------------*/
		// Load & Display Template 
		$this->load->view('custom/navigation_page', $this->data);		
	}
	// --------------------------------------------------------------------
	/**
	 * Connect
	 *
	 * @description	function to connect menu items of different languages 
	 */	
	function connect()
	{
		// run setup
		$this->setup();
		// -----------------
		// Title
		$this->data['title'] = "Menüs verbinden";
		// Content	
		$this->data['content'] = "<h1 class='tcd1 icl1'>Menüs verbinden</h1><p>
							Hier werden Sie später den Menüpunkten die jeweiligen Equivalente in den weiteren Sprachen zuweisen können.</p>";
		// -----------------
		// Load Template
		$this->load->view('custom/entries_page', $this->data);
	}
	// ############################################################################################################################
	// functions
	// --------------------------------------------------------------------
	/**
	 * Language menu
	 *
	 * @description	menu to switch between the different languages
	 */	
	function _language_menu($lang = NULL)
	{
		// -----------------
		// define variables
		$lang = !empty($lang) ? $lang : $this->client['default_lang'];
		$output = array('default' => null, 'normal' => null);
		// -----------------
		// loop through languages
		foreach($this->client['languages'] as $id => $array){
			// define class
			$class = ($id == $lang) ? 'class="active"': '';		
			// -----------------
			// define type
			$arr_type = $array['default'] == 'true' ? 'default' : 'normal';
			// write output
			$output[$arr_type] .= "<li ".$class." value=\"".$id."\">
								<a href='".lang_url()."/menu/".$id."'>"
								.$this->client['languages'][$this->client['default_lang']]['labels'][$id].
								"</a></li>";
		}
		// -----------------
		// return output
		return "<div id='lang_menu'><ul>".$output['default'].$output['normal']."</ul></div>";
	}
}
/* End of file Menu.php */
/* Location: ./application/web_site_name/controllers/Menu.php */