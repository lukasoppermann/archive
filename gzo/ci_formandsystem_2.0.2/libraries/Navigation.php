<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Navigation Class
 *
 * @version		0.1
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Navigation
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/libraries/navigation
 */
class CI_Navigation {

	var $CI;
	var $params 	= NULL;
	var $active		= NULL;
	var $current	= NULL;
	
	public function __construct($params = array())
	{
		$this->CI =& get_instance();
		// Automatically load the navigation helper
		$this->CI->load->helper('navigation');
		// intitialize params
		$this->initialize($params);
		// retrieve from database
		$menu_items = get_db_data($this->params['db_table'], 
		$db = array('select' => 'id, label, path, menu, type, parent_id, status, position, language, data', 'order' => 'position asc'));
		// loop through items
		foreach($menu_items as $array)
		{
			$this->data['path_by_id'][$array['id']] 																						=& $array['path'];
			// -----------------
			// id by path
			$this->data['id_by_path'][$array['path']]																						=& $array['id'];
			// -----------------
			// id by language and path
			$this->data['id_by_lang_path'][$array['language']][$array['path']]																=& $array['id'];
			// -----------------
			// group by id
			$this->data['by_id'][$array['id']] 																								=& $array;
			// -----------------
			// group by parent id			
			$this->data['by_parent'][$array['parent_id']][$array['id']] 																	=& $array;
			// -----------------
			// group by language & menu 			
			$this->data['lang_menu'][$array['language']][$array['menu']][$array['parent_id']][$array['id']] 								=& $array;
			// -----------------
			// group by language & status & menu 			
			$this->data['lang_status_menu'][$array['language']][$array['status']][$array['menu']][$array['parent_id']][$array['position']]	=& $array;
			//
			unset($array);
		}
		// -----------------
		// run active script
		if(!isset($this->params['passive']) || $this->params['passive'] != TRUE)
		{
			$this->_active();
		}
		// log once class is initialized	
		log_message('debug', "Navigation Class Initialized");
	}
	// --------------------------------------------------------------------
	/**
	 * initialize
	 *
	 * @description	produces a tree navigation
	 */
	function initialize($params = array(NULL))
	{	
		$this->params = array_merge(
			array(	'db_table' 		=> $this->CI->config->item('prefix').$this->CI->config->item('db_menu'),
					'status' 		=> '1', 
					'language' 		=> $this->CI->config->item('lang_id'), 
					'start_lvl' 	=> '1',
					'lvl' 			=> '4',
					'list' 			=> 'ul',
					'id' 			=> '',
					'class' 		=> 'menu',
					'fn' 			=> 
						array(
							'default' 	=> 'navigation_item',
							0 			=> 'navigation_seperator',
							2 			=> 'navigation_container',
							3 			=> 'navigation_image',
						),
					'fn_type' 		=> '',	
					'current_item' 	=> '',							
					'item' 			=> 'li',
					'item_class' 	=> '',
					'path_id' 		=> '',
					'path_class' 	=> '',
					'path_seperator'=> '&raquo;',
					'path_before' 	=> '',
			), $params);
	}
	// --------------------------------------------------------------------
	/**
	 * tree
	 *
	 * @description	produces a tree navigation
	 */	
	function tree($params = array())
	{
		$menu = NULL;
		// merge params
		$params = array_merge($this->params, $params);
		$params['start_lvl'] = $params['start_lvl']-1;	
		// define id of start level
		$start = ($params['start_lvl'] > 0 ? $this->active['id'][$params['start_lvl']-1] : 0);	
		// -----------------
		if(isset($params['menu_data']))
		{
			$menu = $params['menu_data'];
		}
		elseif($params['status'] == 'all')
		{
			$menu = &$this->data['lang_menu'][$params['language']][$params['menu']];
		}
		else
		{
			$menu = &$this->data['lang_status_menu'][$params['language']][$params['status']][$params['menu']];
		}
		if( isset($menu) && isset($menu[$start]))
		{	
			// -----------------		
			// show full menu
			//
			$count = 0;
			$output = "\n<".$params['list'].
					(!empty($params['id']) ? ' id="'.$params['id'].'"' : '').
					(!empty($params['class']) ? ' class="'.$params['class'].'"' : '').
					(!empty($params['menu_id']) ? ' menu_id = "'.$params['menu_id'].'"' : '').
					">\n".$this->_loop($menu, $start, $count, $params).'</'.$params['list'].">\n";
		}
		elseif(!isset($menu) && isset($params['show_empty']) && $params['show_empty'] === TRUE)
		{
			// -----------------
			// show empty menu				
			$output = "\n<".$params['list'].
					(!empty($params['id']) ? ' id="'.$params['id'].'"' : '').
					(!empty($params['class']) ? ' class="'.$params['class'].' empty"' : 'class="empty"').
					(!empty($params['menu_id']) ? ' menu_id = "'.$params['menu_id'].'"' : '').
					">\n".'</'.$params['list'].">\n";	
		}
		else
		{
			$output = NULL;
		}
		return $output;
	}
	// --------------------------------------------------------------------
	/**
	 * path
	 *
	 * @description	produces a breadcrumb-like path
	 */
	function path( $params = array(null) )
	{
		if(isset($this->active['items']))
		{
			// merge params
			$this->data = array_merge($this->data, $this->params, $params);
			// -----------------
			// define variables	
			// path seperatpr
			$path_seperator	= !empty($this->data['path_seperator']) ? "<span class='separator'>".$this->data['path_seperator']."</span>" : '';
			// path before
			$path = !empty($this->data['path_before']) ? $this->data['path_before'].$path_seperator : null;				
			// -----------------
			foreach($this->active['items'] as $key => $item)
			{
				if($item['path'] == $this->current['path'])
				{
					// show current item as span
					$path .= "<span>".$item['label']."</span>\n";
				}
				else
				{
					// show other items as link
					$path .= "<a href=\"".active_url(FALSE).$item['path']."\">".$item['label']."</a>\n".$path_seperator;
				}
			}
			// -----------------
			return "\n<div".(!empty($this->data['path_id']) ? ' id="'.$this->data['path_id'].'"' : '').
					(!empty($this->data['path_class']) ? ' class="'.$this->data['path_class'].'"' : '').">\n".$path."</div>\n";
		}
		else
		{
			return false;
		}
	}
	// --------------------------------------------------------------------
	/**
	 * sitemap
	 *
	 * @description	produces a sitemap
	 */
	function sitemap($menus = array(NULL))
	{
		$menu = '<div id="sitemap">';
		foreach((array) $menus as $title => $id)
		{
			$title = !is_string($title) || strlen($title) < 3 ? NULL : "<h4>".$title."</h4>";
			$menu .= "<div class='menu_".$id."'>".$this->tree(array('menu' => $id))."</div>\n";			
		}
		return $menu.'</div>';
	}
	// --------------------------------------------------------------------
	/**
	 * active
	 *
	 * @description	returns an array with all active menu items
	 */
	function active( $string = null )
	{
		if( $string === 'var' || $string === 'variables' )
		{
			return variable($this->active['variables']);
		}
		elseif( array_key_exists($string, $this->active) )
		{
			return $this->active[$string];
		}
	    else
		{
			return $this->active['items'];	
		}
	}
	// --------------------------------------------------------------------
	/**
	 * current
	 *
	 * @description	returns the last active page (e.g. /shop/products/shoes = 'shoes')
	 */
	function current( $string = null )
	{
		if( array_key_exists($string, $this->current) )
		{
			return $this->current[$string];
		}
		elseif( $string === 'array' )
		{
			return $this->current;
		}
	    else
		{
			return $this->current['id'];	
		}		
	}
	// --------------------------------------------------------------------
	/**
	 * variables
	 *
	 * @description	returns a string with all variables from url (e.g. /shop/products/shoes/product-name/id-2345 = 'product-name/id-2345')
	 */
	function variables( $string = null )
	{
		if( isset($this->active['variables']) )
		{
			if( $string === 'array' )
			{
				return array_filter(explode('/',$this->active['variables']));
			}
			else
			{
				return substr($this->active['variables'], 1);
			}
		}
	}
	// --------------------------------------------------------------------
	/**
	 * get
	 *
	 * @description	returns element
	 */
	function get($id, $element = null )
	{
		if( isset($this->data['by_id'][$id][$element]) )
		{
			return $this->data['by_id'][$id][$element];
		}
	}
	// --------------------------------------------------------------------
	/**
	 * get
	 *
	 * @description	returns element
	 */
	function get_by($name, $element = null)
	{
		if( array_key_exists($name, $this->data['by_id'][key($this->data['by_id'])]) )
		{
			$array = index_array($this->data['by_id'], $name, TRUE);
			if(isset($array[$element]))
			{
				return $array[$element];
			}
			else
			{
				return FALSE;
			}
		}
	}	
// ############################################################################################################################
// functions
	// --------------------------------------------------------------------
	/**
	 * loop
	 *
	 * @description	loops through array and produces items
	 */	
	function _loop(&$array, &$id = 0, &$count = 0, $params = NULL)
	{
		// initialize output
			$output = NULL;
			// increase count
			++$count;
			// get all positions of current array
			$tmp_array 	= array_keys(index_array($array[$id], 'position'));
			// unset all items which will not be shown due to access denial
			foreach($array[$id] as $position => $item)
			{
				if(!user_access(variable($item['user-group'])))
				{
					$key = array_search($position, $tmp_array);
					unset($tmp_array[$key], $array[$id][$position]);
				}
			}
			// loop through items
			foreach($array[$id] as $position => $item)
			{
				if(!isset($params['hide']) || ( isset($params['hide']) && !in_array($item['type'], $params['hide']) ) )
				{
					// get key of first array item
					reset($tmp_array);
					$first 		= current($tmp_array);
					// get key of last array item
					$last 		= end($tmp_array);
					// -----------------				
					$active = isset($params['active']) ? $params['active'] : $this->active['id'];
					
					$params['tmp_item_class'] = trim($params['item_class'].
							(isset($this->current['id']) && $this->current['id'] == $item['id'] ? ' current' : '').
							(isset($active) && in_array($item['id'], $active) ? ' active' : '')).
							(array_key_exists($item['id'], $array) && ($count < $params['lvl']) ? ' has-submenu' : '').
							($position === $first ? ' first' : '').($last === $position ? ' last' : '');
					$params['children'] = isset($array[$item['id']]) ? $array[$item['id']] : '';
					//
					// -----------------			
					// add item to output
					$fn = array_key_exists($item['type'], $params['fn']) ? $item['type'] : 'default';
					$fn = array_key_exists($params['fn_type'], $params['fn']) ? $params['fn_type'] : $fn;
					$output.= $params['fn'][$fn]($item, $params);
					unset($params['tmp_item_class'], $params['children']);
					// check for children
					if( array_key_exists($item['id'], $array) && ($count < $params['lvl']) )
					{
						// loop through children if they exist
						$output.= "\n<".$params['list'].' class="'.variable($params['class_lvl_'.($count-1)])."\">\n".$this->_loop($array, $item['id'], $count, $params)."</".$params['list'].">\n";		
					}
					// -----------------			
					// add closing tag to output			
					$output.="</".$params['item'].">\n";
				}
			}		
			// -----------------
			// decrease count
			--$count;   
			// -----------------
			return $output;		
	}
	// --------------------------------------------------------------------
	/**
	 * _active
	 *
	 * @description	produces an array of active menu items
	 */
	function _active()
	{
		if(!$this->active)
		{
			// -----------------
			// variables
			$array 		= explode('/',$this->CI->uri->uri_string);
			unset($array[0], $array[1]); // unset empty element and lang element
			$path 		= null;
			$array 		= array_values($array);
			if(!is_array($array) || empty($array[0]) || !in_array('/'.$array[0], $this->data['path_by_id']))
			{
				$array[0] = substr($this->CI->config->item('index_menu'), 1);
			}
			// -----------------
			// cycle through array
			foreach($array as $item)
			{
				// check if item exists
				if($item)
				{
					$pre = $path;
					$path .= '/'.$item;
					// check if item is menu item				
					if( in_array($path, $this->data['path_by_id']))
					{	
						$id = $this->data['id_by_lang_path'][$this->CI->config->item('lang_id')][$path];
						// array
						$this->active['items'][]	=& $this->data['by_id'][$id];	
						// path
						$this->active['path'][]		=& $this->data['by_id'][$id]['path'];
						// label
						$this->active['label'][]	=& $this->data['by_id'][$id]['label'];
						// id
						$this->active['id'][]		=& $this->data['by_id'][$id]['id'];
						//
						$this->active['page_path']	= variable($this->active['page_path']).$this->data['by_id'][$id]['path'];
					}
					// if not, assign rest of url to variables and return
					else
					{
						$regex = str_replace('/','\/','#(.?)*('.$pre.')#');
						$this->active['variables'] = preg_replace($regex,'',$this->CI->uri->uri_string);
						break;
					}
				}
			}
		}
		if(isset($this->active['items']))
		{
			// -----------------
			// count array elements, -1 = last id
			$count = count($this->active['items'])-1;
			// assign current
			$this->current =& $this->active['items'][$count];
		}
	}
	
}
// END Navigation Class

/* End of file Navigation.php */
/* Location: ./system/libraries/Navigation.php */