<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Gallery Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Content - Gallery
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/libraries/portfolio
 */
class CI_Gallery {

	var $CI;
	
	public function __construct($params = array(NULL))
	{
		$this->CI =& get_instance();
		//
		log_message('debug', "Portfolio Class Initialized");
	}
	// --------------------------------------------------------------------
	/**
	 * gallery
	 *
	 * gallery
	 *
	 * @access	public
	 * @param	array	 
	 */
	function gallery($args = array(), $data)
	{
		// merge provided arguments with defaults
		$args = array_merge(array(
				'db_table' 				=> $this->CI->config->item('prefix').'items',
				'db_table_tags' 		=> $this->CI->config->item('prefix').'data',
				'tag_menu_id' 			=> 'tag_menu',
				'group' 					=> 'portfolio',
				'name' 					=> '',			
				'gallery'				=> array(
					'template_item' 	=> 'custom/portfolio-item',
					'template' 			=> 'custom/portfolio',
					'img_path' 			=> 'media/images/portfolio/',
					'gallery_id' 		=> 'portfolio_items'
				),
				'detail' 				=> array(
					'template' 			=> 'custom/portfolio-item-detail',
					'img_path' 			=> 'media/images/portfolio/',
				)
				),
			$args);
		// if name is empty, show gallery
		if(empty($args['name']))
		{
			$data['tags'] 		= $this->tags($args);
			$data['items']		= $this->items($args);
			return $this->CI->load->view($args['gallery']['template'], $data, TRUE);
		}
		// if name is set, show detail page
		else
		{
			return $this->item($args);
		}
		
	}
	// --------------------------------------------------------------------
	/**
	 * items
	 *
	 * get items and prepare for display
	 *
	 * @access	public
	 * @param	array	 
	 */
	function items($args = array())
	{
		js_add('gallery');
		// define variable
		$output = null;
		$tags = array();
		// merge provided arguments with defaults
		$args = array_merge(array(	
			'db_table' 				=> $this->CI->config->item('prefix').'items',
			'db_table_tags' 		=> $this->CI->config->item('prefix').'data',
			'group' 					=> 'portfolio',
			'name' 					=> '',			
			'gallery'		=> array(
				'template_item' 	=> 'custom/portfolio-item',
				'template' 			=> 'custom/portfolio',
				'img_path' 			=> 'media/images/portfolio/',
				'gallery_id' 		=> 'portfolio_items'
			)),
			$args);
		// retrieve data from DB
		$items = get_db_data($args['db_table'], $db = array('where'=> array('group' => $args['group']),'order'=>'position','select' => '*'));
		$tags = index_array(get_db_data($args['db_table_tags'], $db = array('where'=> array('key' => 'tag'),'select' => 'value, type')), 'type');
		// prepare items
		foreach($items as $item)
		{
			$item = array_merge($args['gallery'], $item);
			//
			$_tags = $item['tags'];
			unset($item['tags']);
			foreach($_tags as $tag)
			{
				$item['tags'][$tag] = $tags[$tag]['name'];
			}
			$output .= $this->CI->load->view($args['gallery']['template_item'], $item, TRUE);
		}
				// retur portfolio
		return '<div id="'.$args['gallery']['gallery_id'].'">'.$output.'</div>';
	}
	// --------------------------------------------------------------------
	/**
	 * item
	 *
	 * get specific item and prepare for display
	 *
	 * @access	public
	 * @param	array	 
	 */
	function item($args = array())
	{
		// define variable
		$output = null;
		// merge provided arguments with defaults
		$args = array_merge(array(
				'db_table' 		=> $this->CI->config->item('prefix').'items',
				'db_table_tags' 		=> $this->CI->config->item('prefix').'data',
				'group' 			=> 'portfolio',
				'name' 			=> '',
				'detail' 		=> array(
					'template' 		=> 'custom/portfolio-item-detail',
					'img_path' 		=> 'media/images/portfolio/',
				)),
			$args);	
		// retrieve data from DB
		$items = get_db_data($args['db_table'], $db = array('where'=> array('group' => $args['group']),'select' => '*'));
		$items = index_array($items, 'name');
		// get current item
		$item['current'] = $items[$args['name']];
		// sort by position
		$items = index_array($items, 'position');
		// get next item
		if(array_key_exists($item['current']['position']+1, $items))
		{
			$item['next'] = $items[$item['current']['position']+1];
		}
		else
		{
			$item['next'] = min($items);
		}
		// get previous item
		if(array_key_exists($item['current']['position']-1, $items))
		{
			$item['prev'] = $items[$item['current']['position']-1];
		}
		else
		{
			$item['prev'] = max($items);
		}
		//
		$item = array_merge($item, $args);
		// (SELECT * FROM $args['db_table'] WHERE ('name'=$args['name'] AND 'group'=$args['group']))		
		return $this->CI->load->view($args['detail']['template'], $item, TRUE);
	}
	// --------------------------------------------------------------------
	/**
	 * tags
	 *
	 * get menu with all tags
	 *
	 * @access	public
	 * @param	array	 
	 */
	function tags($args = array())
	{
		// merge provided arguments with defaults
		$args = array_merge(array('db_table_tags'	=> $this->CI->config->item('prefix').'data'),$args);	
		// retrieve data from DB
		$tags = index_array(get_db_data($args['db_table_tags'], $db = array('where'=> array('key' => 'tag'),'select' => 'value, type')), 'type');	
		// prepare tag menu
		$tag_menu = "<ul id='".$args['tag_menu_id']."'>".'<li><a class="tag all active" href="#all">'.lang('all').'</a></li>';
		foreach($tags as $tag => $data)
		{
			$tag_menu .= '<li><a class="tag '.$tag.'" href="#'.$tag.'">'.$data['name'].'</a></li>';
		}
		return $tag_menu."</ul>";
	}
	// --------------------------------------------------------------------
// END Gallery Class
}
/* End of file Portfolio.php */
/* Location: ./system/libraries/Portfolio.php */