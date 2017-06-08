<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter MY_url Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 */

// ------------------------------------------------------------------------
/**
 * base_url - returns the base_url with the current language part
 *
 * @param boolean 
 * @return string
 */
function base_url($slash = TRUE)
{
	$CI =& get_instance();
	
	if($slash == TRUE)
	{
		return $CI->config->slash_item('base_url');
	}
	else
	{
		return $CI->config->unslash_item('base_url');		
	}
}
// ------------------------------------------------------------------------
/**
 * current_path - returns current url without the base_url
 *
 * @param boolean 
 * @return string
 */
 function current_path()
 {
	return str_replace(base_url(), '', current_url());
 }
// ------------------------------------------------------------------------
/**
 * active_url - returns current url with x params
 *
 * @param boolean 
 * @return string
 */
 function active_url($count)
{
	$params = explode('/',current_path());
	//
	$output = '';
	for($c = 0; $c < $count; $c++)
	{
		$output .= $params[$c];
	}
	// return
	return base_url().$output;
}
// ------------------------------------------------------------------------
/**
 * active_item - returns active item x from current url
 *
 * @param boolean 
 * @return string
 */
 function active_item($num)
{
	$params = explode('/',current_path());
	// return
	if(isset($params[$num-1]))
	{
		return $params[$num-1];
	}
}