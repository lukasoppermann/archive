<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Form&System Navigation Helper
 *
 * @package		Form&System
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/navigation
 */
// --------------------------------------------------------------------
/**
 * nav_check_url - check url for menu item
 *
 * @param array
 * @param array
 * @return string
 */
function nav_check_url( $path )
{
	$CI =& get_instance();
	// check if http:// or www. is in path
	if( substr($path, 0, 5) == 'http:' || substr($path, 0, 4) == 'www.' )
	{
		return $path;		
	}
	// if item should be attached to current page
	elseif( substr($path,0,1) == '%' )
	{
		return page_url().substr(trim($path,'/'),2);
	}
	// if not treat as relative path
	else
	{
		// get languages
		$languages = $CI->config->item('languages_id');
		// explode path
		$path_lang = explode('/',$path);
		// check for language in url
		if( !isset($path_lang[1]) || !in_array($path_lang[1], $languages) )
		{		
			// no language -> add it
			return active_url().trim($path,'/');
		}
		else
		{
			// language in path -> add base_url only
			return base_url().trim($path,'/');
		}
	}
}
// --------------------------------------------------------------------
/**
 * navigation_item - renders menu item
 *
 * @param array
 * @param array
 * @return string
 */
function navigation_item( &$item, $opt = array(NULL) )
{
	// check & build item path
	$path = nav_check_url( $item['path'] );
	// -----------------
	// check for label replacement
	if( isset($opt['replace_label']) && is_array($opt['replace_label']) )
	{
		// repare replacements
		foreach( $opt['replace_label'] as $key => $label )
		{
			$replace[strtolower($key)] = $label;
		}
		// check if label is in replace array
		if( array_key_exists( strtolower($item['label']), $replace ) )
		{
			// replace label
			$item['label'] = $replace[strtolower($item['label'])];
		}
	}
	// -----------------
	// prepare title attribute
	$title 	= isset($item['title']) ? " title='".$item['title']."'" : '';
	// prepare shortcut attribute
	if( !isset($opt['hide']) || $opt['hide'] == FALSE || !in_array('shortcut', $opt['hide']) )
	{
		$shortcut = isset($item['shortcut']) ? " <span class='shortcut float-right'>".$item['shortcut']."</span>" : '';
	}
	// prepare target attribute
	$target = (isset($opt['target']) ? " target='".$opt['target']."'" : '');
	// prepare class attribute
	$tmp_class 		= trim($opt['tmp_item_class'].' '.(isset($item['class']) ? $item['class'] : ''));
	$class 				= (isset($tmp_class) && !empty($tmp_class) ? " class='".$tmp_class."'" : "");
	$link_class 	= (isset($opt['link_class']) ? $opt['link_class']." " : "");
	$item_data		= (isset($opt['item_data']) ? " ".$opt['item_data'] : "");
	$link_data		= (isset($opt['link_data']) ? " ".$opt['link_data'] : "");
	// prepare id attribute
	$id		= (isset($opt['item_id']) ? " id='".trim($opt['item_id'])."'" : (isset($item['item_id']) ?  " id='".trim($item['item_id'])."'" : ""));
	// image only
	if(isset($item['img-only']))
	{
		$title 	= !isset($title) ? " title='".$item['label']."'" : '';
		return "<li".$class.$id.$item_data."><a class='menu-item-link' href=\"".$path."\"".$title.$target.$link_data."></a>";
	}
	// -----------------
	// return item
	return "<li".$class.$id.$item_data.">"."<a class='".$link_class."menu-item-link' href=\"".$path."\"".$title.$target.$link_data.">".$opt['item_before'].$item['label'].(isset($shortcut) ? $shortcut : '').$opt['item_after']."</a>";
}
// --------------------------------------------------------------------
/**
 * navigation_seperator - renders seperator 
 *
 * @param array
 * @param array
 * @return string
 */
function navigation_seperator(&$item, $opt = array(NULL))
{
	return "<li class='seperator'>";
}

// --------------------------------------------------------------------
/**
 * navigation_container - renders menu item which links to first item or default item
 *
 * @param array
 * @param array
 * @return string
 */
function navigation_container(&$item, $opt = array(NULL))
{
	$CI =& get_instance();
	// load auth helper for user_access
	$CI->load->helper(array('fs_authentication','fs_variable'));
	// remove all items without access rights
	if(isset($opt['children']) && is_array($opt['children']))
	{
		ksort($opt['children']);
		foreach($opt['children'] as $ch_key => $ch_item)
		{
			if(user_access($ch_item['group']) === FALSE)
			{
				unset($opt['children'][$ch_key]);
			}
		}
		// reset array to first
		reset($opt['children']);
		// check if first element is seperator -> remove
		if(is_array($opt['children']))
		{
			while(count($opt['children']) > 0 && $opt['children'][key($opt['children'])]['type'] == "0" )
			{
				// remove element from array
				unset($opt['children'][key($opt['children'])]);
			}
			// set to last element
			end($opt['children']);
			// check if last element is seperator				
			while(count($opt['children']) > 0 && $opt['children'][key($opt['children'])]['type'] == "0" )
			{
				// remove element from array
				unset($opt['children'][key($opt['children'])]);
				// set to last element
				end($opt['children']);
			}
		}
		// point to first item or default item
		reset($opt['children']);
		$key = current($opt['children']);
		$item['path'] = $key['path'];
		foreach($opt['children'] as $key => $child_item)
		{
			if(array_key_exists('default', $child_item))
			{
				$item['path'] = $child_item['path'];
			}
		}
	}
	// return with default function for items
	return navigation_item($item, $opt);
}

// --------------------------------------------------------------------
/**
 * navigation_image - renders menu item with image instead of text
 *
 * @param array
 * @param array
 * @return string
 */
function navigation_image(&$item, $opt = array(NULL))
{
	// check & build item path
	$path = nav_check_url( $item['path'] );
	// prepare title attribute
	$title 	= isset($item['title']) ? " title='".$item['title']."'" : '';
	// prepare title shortcut
	if(!isset($opt['hide']) || !in_array('shortcut', $opt['hide']))
	{
		$shortcut 	= isset($item['shortcut']) ? " <span class='shortcut float-right'>".$item['shortcut']."</span>" : '';
	}
	// prepare target attribute
	$target = (isset($opt['target']) ? " target='".$opt['target']."'" : '');
	// prepare class attribute
	$tmp_class = trim($opt['tmp_item_class'].' '.(isset($item['class']) ? $item['class'] : ''));
	$class 	= (isset($tmp_class) && !empty($tmp_class) ? " class='".$tmp_class." img'" : " class='img'");
	// prepare id attribute
	$id		= (isset($opt['item_id']) ? " id='".trim($opt['item_id'])."'" : "");
	// image only
	if(isset($item['img-only']))
	{
		$title 	= !isset($title) ? " title='".$item['label']."'" : '';
		return "<li".$class.$id."><a class='menu-item-link' href=\"".$path."\"".$title.$target."></a>";		
	}
	// -----------------
	// return item
	return "<li".$class.$id."><a class='menu-item-link' href=\"".$path."\"".$title.$target."><img src='".base_url(TRUE).(isset($item['img']) ? $item['img'] : '')."' alt='".$item['label'].(isset($shortcut) ? $shortcut : '')."' /></a>";
}
// -------------------------------------------------------------------------------------
// functions for the use outside of navigation class
function current_nav( $key = null, $no_default = true )
{
	$CI =& get_instance();
	// get current navigation item
	$current = $CI->fs_navigation->current('array');
	// check if item exists
	if( $no_default != true || ( isset($current['set_default']) && $current['set_default'] != true) )
	{
		// return item if key exists 
		if( isset($current[$key]) && $current[$key] != null )
		{
			return $current[$key];
		}
		elseif( $key == 'array' )
		{
			return $current;		
		}
	}
	// if no item
	return "FALSE";
}

/* End of file navigation_helper.php */
/* Location: ./system/helpers/navigation_helper.php */