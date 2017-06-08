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
class CI_FS_navigation {

	var $CI;
	var $params 	= NULL;
	var $active		= NULL;
	var $current	= array(NULL);
	var $group		= array();
	
	public function __construct( $params = array() )
	{
		$this->CI =& get_instance();
		// Automatically load the navigation helper
		$this->CI->load->helper(array('fs_navigation'));
		// log once class is initialized	
		log_message('debug', "FS Navigation Class Initialized");
	}
	// --------------------------------------------------------------------
	/**
	 * initialize
	 *
	 * @description	initialize class with given params
	 */
	function initialize($params = array(NULL))
	{
		// merge params
		$this->params = array_merge(array(	
			'status' 			=> '1', 
			'language' 		=> '', 
			'start_lvl' 	=> '1',
			'lvl' 				=> '4',
			'list' 				=> 'ul',
			'id' 					=> '',
			'class' 			=> 'menu',
			'fn' 					=> 
				array(
					'default' 	=> 'navigation_item',
					0 					=> 'navigation_seperator',
					2 					=> 'navigation_container',
					3 					=> 'navigation_image',
				),
			'fn_type' 			=> '',
			'current_item' 	=> '',
			'item' 					=> 'li',
			'item_class' 		=> '',
			'item_before' 	=> '',
			'item_after' 		=> '',
			'sub_before' 		=> '',
			'sub_after' 		=> '',
			'path_id' 			=> '',
			'path_class' 		=> '',
			'path_item' 		=> 'path-item',
			'path_seperator'=> '&raquo;',
			'path_before' 	=> '',
			'replace_label' => '',
			'passive' 			=> TRUE,
			'active_unset' 	=> ''
		), $params);
			
		// if menu_array is given, prepare it
		if( isset($this->params['menu_array']) )
		{
			// loop through items
			foreach($this->params['menu_array'] as $array)
			{
				$this->data['path_by_id'][$array['id']] 																					=& $array['path'];
				// -----------------
				// id by path
				$this->data['id_by_path'][$array['path']]																					=& $array['id'];
				// -----------------
				// id by language and path
				$this->data['id_by_lang_path'][$array['language']][$array['path']]															=& $array['id'];
				// -----------------
				// group by id
				$this->data['by_id'][$array['id']] 																							=& $array;
				// -----------------
				// group by parent id			
				$this->data['by_parent'][$array['parent_id']][$array['id']] 																=& $array;
				// -----------------
				// group by language & menu 			
				$this->data['lang_menu'][$array['language']][$array['menu']][$array['parent_id']][$array['id']] 							=& $array;
				// -----------------
				// group by language & status & menu 			
				$this->data['lang_status_menu'][$array['language']][$array['status']][$array['menu']][$array['parent_id']][$array['position']]	=& $array;
				// -----------------
				// set main default
				if( isset($array['base_default']) && ( $array['base_default'] == "1" || strtolower($array['base_default']) == "true") )
				{
					$this->data['base_default']																								=& $array;
					$this->CI->config->set_item('menu_default', $array);
				}
				//
				unset($array);
			}
		}
		// -----------------
		// run active script
		if( !isset($this->params['passive']) || $this->params['passive'] != TRUE)
		{
			$this->_active();
		}
	}
	// --------------------------------------------------------------------
	/**
	 * tree
	 *
	 * @description	produces a tree navigation
	 */	
	function tree( $params = array() )
	{
		// define output
		$output = NULL;
		// initialize
		$this->initialize($params);
		// 
		$this->_active();
		// -----------------
		// define id of start level
		$start = 0;
		// check if level is bigger 0
		if( ($this->params['start_lvl'] - 1) > 0 )
		{
			// if item is active, use this item 
			if( isset($this->active['id']) )
			{
				// get id from active array
				$start = $this->active['id'][$this->params['start_lvl']-2];
			}
		}
		// if status is all
		if($this->params['status'] == 'all')
		{
			// use no matter what status
			$menu = &$this->data['lang_menu'][$this->params['language']][$this->params['menu']];
		}
		// else use only given status
		else
		{
			$menu = &$this->data['lang_status_menu'][$this->params['language']][$this->params['status']][$this->params['menu']];
		}
		// -----------------------
		// if menu items given
		if( isset($menu) && isset($menu[$start]) )
		{	
			// -----------------		
			// show full menu
			$count = 0;
			// get menu elements
			$elements = $this->_loop($menu, $start, $count, $this->params);
			// if menu elements exist build menu
			if($elements != null)
			{
				$output = "\n<".$this->params['list'].
					(!empty($this->params['id']) ? ' id="'.$this->params['id'].'"' : '').
					(!empty($this->params['class']) ? ' class="'.$this->params['class'].(isset($this->params['class_lvl_'.($count)]) ? ' '.$this->params['class_lvl_'.($count)] : '').'"' : '').
					(!empty($this->params['menu_id']) ? ' menu_id = "'.$this->params['menu_id'].'"' : '').
					">\n".$elements.'</'.$this->params['list'].">\n";
			}
		}
		// no menu given but show_empty true
		elseif( (!isset($menu) || !isset($menu[$start]) ) && isset($this->params['show_empty']) && $this->params['show_empty'] === TRUE)
		{
			// -----------------
			// show empty menu				
			$output = "\n<".$this->params['list'].
					(!empty($this->params['id']) ? ' id="'.$this->params['id'].'"' : '').
					(!empty($this->params['class']) ? ' class="'.$this->params['class'].' empty"' : 'class="empty"').
					(!empty($this->params['menu_id']) ? ' menu_id = "'.$this->params['menu_id'].'"' : '').
					">\n".'</'.$this->params['list'].">\n";	
		}
		// get active
		$this->_active(TRUE);
		// return menu
		return $output;
	}
	// --------------------------------------------------------------------
	/**
	 * active
	 *
	 * @description	returns an array with all active menu items
	 */
	function active( $string = null )
	{
		// if variables
		if( $string === 'var' || $string === 'variables' )
		{
			// return variables
			return (isset($this->active['variables']) ? $this->active['variables'] : '');
		}
		// if key exists
		elseif( array_key_exists($string, $this->active) )
		{
			// return item
			return $this->active[$string];
		}
		// if key is array
	    elseif( $string === 'array' || $string == null )
		{
			// return all itens
			return $this->active['items'];	
		}
		// else return false
		return FALSE;
	}
	// --------------------------------------------------------------------
	/**
	 * current
	 *
	 * @description	returns the last active page (e.g. /shop/products/shoes = 'shoes')
	 */
	function current( $string = null )
	{
		// check if requested item exists
		if( array_key_exists($string, $this->current) )
		{
			// if so, return it
			return $this->current[$string];
		}
		// if array requested
		elseif( $string === 'array' )
		{
			// return array
			return $this->current;
		}
		// if id requested
	    elseif( $string === 'id' )
		{
			// return id
			return $this->current['id'];	
		}
		// else return false
		return FALSE;
	}
	// --------------------------------------------------------------------
	/**
	 * variables
	 *
	 * @description	returns a string with all variables from url (e.g. /shop/products/shoes/product-name/id-2345 = 'product-name/id-2345')
	 */
	function variables( $string = null )
	{
		// check if variables are set
		if( isset($this->active['variables']) )
		{
			// if array resuested
			if( $string === 'array' )
			{
				// return all variables in array
				return array_filter(explode('/',$this->active['variables']));
			}
			else
			{	
				// else return as string
				return substr($this->active['variables'], 1);
			}
		}
		// if no variables set
		return FALSE;
	}
	// --------------------------------------------------------------------
	/**
	 * get
	 *
	 * @description	returns element by given id
	 */
	function get($id, $element = null )
	{
		// check if element exists
		if( isset($this->data['by_id'][$id][$element]) )
		{
			// return element
			return $this->data['by_id'][$id][$element];
		}
		// if element does NOT exist
		return FALSE;
	}
	// --------------------------------------------------------------------
	/**
	 * get_by
	 *
	 * @description	returns array by given key value pair
	 */
	function get_by($key, $value = null)
	{
		// checks if key exists in array
		if( array_key_exists( $key, $this->data['by_id'][key($this->data['by_id'])] ) )
		{
			// index itesm by key
			$array = index_array($this->data['by_id'], $key, TRUE);
			// check if value for this key exists
			if(isset($array[$element]))
			{
				// if key/value exists, return array
				return $array[$element];
			}
			// if value does not exists return false
			else
			{
				return FALSE;
			}
		}
		// else return FALSE
		return FALSE;
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
		// -----------------
		// sort current array by positions
		$array_by_position = index_array($array[$id], 'position');
		ksort($array_by_position);
		$array_by_position = array_keys($array_by_position);
		// -----------------
		// unset all items which will not be shown due to access denial
		foreach( $array[$id] as $position => $item )
		{
			// if user_access is false
			if( function_exists('user_access') && user_access( isset($item['group']) ? $item['group'] : '' ) === FALSE )
			{
				// get key
				$key = array_search($position, $array_by_position);
				// unset item
				unset($array_by_position[$key], $array[$id][$position]);
			}
		} 
		// resort array by postion
		ksort($array[$id]);
		// -----------------
		// remove seperators
		if( isset($array[$id]) && count($array[$id]) > 0)
		{
			// remove first seperators
			// -----------------
			// reset array to first
			reset($array[$id]);
			// check if first element is seperator
			while(count($array[$id]) > 0 && $array[$id][key($array[$id])]['type'] === "0" )
			{
				// remove element from array
				unset($array[$id][key($array[$id])]);
			}
			// remove last seperators
			// -----------------
			// set to last element
			end($array[$id]);
			// check if last element is seperator				
			while(count($array[$id]) > 0 && $array[$id][key($array[$id])]['type'] === "0" )
			{
				// remove element from array
				unset($array[$id][key($array[$id])]);
				// set to last element
				end($array[$id]);
			}
		}
		// -----------------
		// loop through prepared items
		foreach($array[$id] as $position => $item)
		{
			// check if item is not to be hidden
			if( !isset($params['hide']) || $params['hide'] == FALSE || ( !in_array($item['type'], $params['hide']) ) )
			{		
				// get key of first array item
				reset($array_by_position);
				$first 		= current($array_by_position);
				// get key of last array item
				$last 		= end($array_by_position);
				// -----------------
				// get active array
				if( isset($params['active']) )
				{
					// if params-active are set
					$active = $params['active'];
				}
				elseif( isset($this->active['id']) )
				{
					// if this->active is set
					$active = $this->active['id'];
				}
				// -----------------
				// get classes for item
				$params['tmp_item_class'] = trim($params['item_class'].
						(isset($this->current['id']) && $this->current['id'] == $item['id'] ? ' current' : '').
						(isset($active) && in_array($item['id'], $active) ? ' active' : '')).
						(array_key_exists($item['id'], $array) && ($count < $params['lvl'] || $params['lvl'] == '') ? ' has-submenu' : '').
						($position === $first ? ' first' : '').($last === $position ? ' last' : '');
				// get children if exist	
				$params['children'] = isset($array[$item['id']]) ? $array[$item['id']] : '';
				// -----------------			
				// define function to use for item
				if( array_key_exists($params['fn_type'], $params['fn']) )
				{
					// use fn from parameter if given
					$fn = $params['fn_type'];	
				}
				// if function for type exists, use this fn
				elseif( array_key_exists($item['type'], $params['fn']) )
				{
					$fn = $item['type'];
				}
				// else use default fn
				else
				{
					$fn = 'default';
				}
				// -----------------
				// add item to output
				$output .= $params['fn'][$fn]($item, $params);
				// unset tmp_item_class & children
				unset($params['tmp_item_class'], $params['children']);
				// check for children
				if( array_key_exists($item['id'], $array) && ($count < $params['lvl'] || $params['lvl'] == '') )
				{
					// loop through children if they exist
					$output.= $params['sub_before']."\n<".$params['list'].' class="'.$params['class'].(isset($params['class_lvl_'.($count)]) ? ' '.$params['class_lvl_'.($count)] : '')."\">\n".
								$this->_loop($array, $item['id'], $count, $params)."</".$params['list'].">".$params['sub_after']."\n";		
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
		// if active is not set
		if(!$this->active)
		{
			// variables
			$path = null;
			// get current items from url
			$array = explode('/',$this->CI->uri->uri_string);
			$array = array_filter($array); // remove empty values
			// unset unwanted element
			$array = array_values($array);
			foreach( (array) $this->params['active_unset'] as $pos )
			{
				unset($array[$pos-1]);
			}
			// reset ids
			$array = array_values($array);
			// prepare first argument
			if( !is_array($array) || empty($array[0]) || !in_array('/'.$array[0], $this->data['path_by_id']) )
			{
				$array[0] = substr($this->CI->config->item('index_menu'), 1);
			}
			// pepare count
			$i = 0;
			$count = count($array);
			// -----------------
			// cycle through array
			foreach($array as $item)
			{
				++$i;
				// check if item exists
				if( isset($item) )
				{
					$pre = $path;
					$path .= '/'.$item;
					// check if item is menu item				
					if( in_array($path, $this->data['path_by_id']) )
					{
						// get id of item
						$id = $this->data['id_by_lang_path'][$this->CI->config->item('lang_id')][$path];
						// check if is active
						if( $this->data['by_id'][$id]['type'] == 2 && $i == $count && isset($this->data['by_parent'][$id]) )
						{
							// set container active							
							if( !function_exists('user_access') || user_access( isset($this->data['by_id'][$id]['group']) ? $this->data['by_id'][$id]['group'] : '' ) !== FALSE )
							{
								// array
								$this->active['items'][]	=& $this->data['by_id'][$id];	
								// path
								$this->active['path'][]		=& $this->data['by_id'][$id]['path'];
								// label
								$this->active['label'][]	=& $this->data['by_id'][$id]['label'];
								// id
								$this->active['id'][]		=& $this->data['by_id'][$id]['id'];
								// active path
								$this->active['page_path']	= (isset($this->active['page_path']) ? $this->active['page_path'] : '').$this->data['by_id'][$id]['path'];
							}
							// -----------------
							// point to first item or default item
							// sort by position
							$child_array = index_array($this->data['by_parent'][$id], 'position');
							ksort($child_array);
							// reset array to first
							reset($child_array);
							// count array items (children)
							$child_count = 	count($this->data['by_parent'][$id]);
							// cycle through children until a fitting one is found
							for( $i = 0; $i < $child_count; ++$i )
							{
								// extract current child item from array
								$tmp_child = array_slice($child_array, $i, 1);
								$tmp_child = $tmp_child[0];
								// check if rights of child aka rights of parent match user rights
								if( isset($tmp_child['group']) )
								{
									// if acces granted
									if( !function_exists('user_access') || user_access( isset($tmp_child['group']) ? $tmp_child['group'] : '' ) !== FALSE )
									{
										$child = $tmp_child;
										$i = $child_count;
									}	
								}
								// if no child rights check if parent rights match
								else
								{
									if( !function_exists('user_access') || user_access(isset($this->data['by_id'][$id]['group']) ? $this->data['by_id'][$id]['group'] : '') !== FALSE )
									{
										$child = $tmp_child;
										$i = $child_count;
									    $this->current['set_default'] = TRUE;
									}
								}
							}
							// -----------------
							// loop through items by parent id
							foreach($this->data['by_parent'][$id] as $key => $child_item)
							{
								// check if default item is set
								if( array_key_exists('default', $child_item) )
								{
									// check if default has group
									if( isset($child_item['group']) )
									{
										// check if access is granted
										if( !function_exists('user_access') || user_access(isset($child_item['group']) ? $child_item['group'] : '') !== FALSE )
										{
											$child = $child_item;
										}
									}
									// if no child rights check if parent right
									else
									{
										// check if access is granted
										if( !function_exists('user_access') || user_access(isset($this->data['by_id'][$id]['group']) ? $this->data['by_id'][$id]['group'] : '') !== FALSE )
										{
											$child = $this->data['by_id'][$id];
										    $this->current['set_default'] = TRUE;
										}
									}
								}
							}
							// if child item has sufficient rights, set active
							if( isset($child) )
							{
								$id = $child['id'];
							}
						}
						// -----------------
						// check if acces is granted for main item
						if( !function_exists('user_access') || user_access(isset($this->data['by_id'][$id]['group']) ? $this->data['by_id'][$id]['group'] : '') !== FALSE )
						{
							// array
							$this->active['items'][]	=& $this->data['by_id'][$id];	
							// path
							$this->active['path'][]		=& $this->data['by_id'][$id]['path'];
							// label
							$this->active['label'][]	=& $this->data['by_id'][$id]['label'];
							// id
							$this->active['id'][]		=& $this->data['by_id'][$id]['id'];
							//
							$this->active['page_path']	= (isset($this->active['page_path']) ? $this->active['page_path'] : '' ).$this->data['by_id'][$id]['path'];
						}
						// check if group is set for current item
						if( array_key_exists('group', $this->data['by_id'][$id]) )
						{
							// loop through all groups of current item
							foreach( (array) $this->data['by_id'][$id]['group'] as $group )
							{
								// add to group array 
								$this->group[$id][] = $group;
							}
						}
					}
					// -----------------
					// if item is not menu item, assign rest of url to variables
					else
					{
						$regex = str_replace('/','\/','#(.?)*('.$pre.')#');
						$this->active['variables'] = preg_replace($regex,'',$this->CI->uri->uri_string);
						break;
					}
				}
			}
		}
		// -----------------
		// set current if active item is set
		if( isset($this->active['items']) )
		{
			// count array elements, -1 = last id
			$count = count($this->active['items'])-1;
			// get current url without vars
			$current_url = str_replace( (isset($this->active['variables']) ? $this->active['variables'] : ''), '', current_url() );
			// split url at current item path (e.g. /content/)
			if( $this->active['items'][$count]['path'] )
			{
				$current_parts = explode($this->active['items'][$count]['path'], $current_url);
				// if this is the last part, there is only one entry in the array
				if( !isset($current_parts[1]) || $current_parts[1] == null || $current_parts[1] == '/' )
				{
					// assign current
					$this->current =& $this->active['items'][$count];
					// if group is set for current item
					if( array_key_exists('group', $this->current) && $this->current['group'] != null )
					{
						// add group to group array
						$this->current['group'] = (array) $this->current['group'];
					}
					// if group but parent group is set
					elseif( isset( $this->group[$this->current['parent_id']] ) )
					{
						// add parent group 
						$this->current['group'] = (array) $this->group[$this->current['parent_id']];
					}
				}
			}
		}
		// -----------------
		// if no active item is set use default
		else
		{
			// array
			$this->active['items'][]	=& $this->data['base_default'];	
			// path
			$this->active['path'][]		=& $this->data['base_default']['path'];
			// label
			$this->active['label'][]	=& $this->data['base_default']['label'];
			// id
			$this->active['id'][]		=& $this->data['base_default']['id'];
			//
			$this->active['page_path']	= (isset($this->active['page_path']) ? $this->active['page_path'] : '').$this->data['base_default']['path'];
			// assign current
			$this->current =& $this->data['base_default'];
			// indicate that current was set to default
			$this->current['set_default'] = TRUE;
		}
	}	
// END Navigation Class
}
/* End of file FS_Navigation.php */
/* Location: ./system/libraries/FS_Navigation.php */