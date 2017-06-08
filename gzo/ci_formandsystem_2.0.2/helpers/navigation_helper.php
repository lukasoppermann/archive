<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Form&System Navigation Helper
 *
 * @package		Form&System
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://formandsystem.com/navigation-helper
 */

// --------------------------------------------------------------------
/**
 * navigation_item - renders menu item
 *
 * @param array
 * @param array
 * @return string
 */
function navigation_item(&$item, $opt = array(NULL))
{
	$CI =& get_instance();
	// check if http:// or www. is in path
	if(preg_match("[http://|http:|www.|ww.|.*\.]", $item['path']))
	{
		$path = $item['path'];		
	}
	// if not add treat as relative path
	else
	{
		$languages = $CI->config->item('languages');
		$tmp_lang = explode('/',$item['path']);
		// check for language in url
		if(!isset($tmp_lang[1]) || !in_array($tmp_lang[1], $languages['abbr']))
		{		
			// no language -> add it
			$path = active_url().trim($item['path'],'/');
		}
		else
		{
			// language in path -> add base_url only
			$path = base_url().trim($item['path'],'/');
		}
	}
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
	$tmp_class = trim($opt['tmp_item_class'].' '.variable($item['class']));
	$class 	= (isset($tmp_class) && !empty($tmp_class) ? " class='".$tmp_class."'" : "");
	// prepare id attribute
	$id		= (isset($opt['item_id']) ? " id='".trim($opt['item_id'])."'" : "");
	// image only
	if(isset($item['img-only']))
	{
		$title 	= !isset($title) ? " title='".$item['label']."'" : '';
		return "<li".$class.$id."><a href=\"".$path."\"".$title.$target."></a>";		
	}
	
	if( $item['type'] == 5 )
	{
		$class = ' class="'.$tmp_class.' active container-link"';
		return "<li".$class.$id."><span>".$item['label']."</span></a>";
	}
	else
	{
		// -----------------
		// return item
		return "<li".$class.$id."><a href=\"".$path."\"".$title.$target.$target.">".$item['label'].variable($shortcut)."</a>";		
	}

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
	// point to first item or default item
	if(isset($opt['children']) && is_array($opt['children']))
	{
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
	$CI =& get_instance();
	// check if http:// or www. is in path
	if(preg_match("[http://|http:|www.|ww.|.*\.]", $item['path']))
	{
		$path = $item['path'];		
	}
	// if not add treat as relative path
	else
	{
		$languages = $CI->config->item('languages');
		$tmp_lang = explode('/',$item['path']);
		// check for language in url
		if(!in_array($tmp_lang[1], $languages['abbr']))
		{		
			// no language -> add it
			$path = active_url().trim($item['path'],'/');
		}
		else
		{
			// language in path -> add base_url only
			$path = base_url().trim($item['path'],'/');
		}
	}
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
	$tmp_class = trim($opt['tmp_item_class'].' '.variable($item['class']));
	$class 	= (isset($tmp_class) && !empty($tmp_class) ? " class='".$tmp_class." img'" : " class='img'");
	// prepare id attribute
	$id		= (isset($opt['item_id']) ? " id='".trim($opt['item_id'])."'" : "");
	// image only
	if(isset($item['img-only']))
	{
		$title 	= !isset($title) ? " title='".$item['label']."'" : '';
		return "<li".$class.$id."><a href=\"".$path."\"".$title.$target."></a>";		
	}
	// -----------------
	// return item
	return "<li".$class.$id."><a href=\"".$path."\"".$title.$target.$target."><img src='".base_url(TRUE).variable($item['img'])."' alt='".$item['label'].variable($shortcut)."' /></a>";
}
/* End of file navigation_helper.php */
/* Location: ./system/helpers/navigation_helper.php */