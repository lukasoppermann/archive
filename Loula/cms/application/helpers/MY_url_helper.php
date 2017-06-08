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
 * tiny_url - returns url as tinyurl
 *
 * @param boolean 
 * @return string
 */
 function tiny_url($url)
 {
	// init cUrl
	$cURL = curl_init();  
	$timeout = 5;  
	// create tiny_url with url
	curl_setopt($cURL,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.urlencode($url));  
	curl_setopt($cURL,CURLOPT_RETURNTRANSFER,1);  
	curl_setopt($cURL,CURLOPT_CONNECTTIMEOUT,$timeout);
	// get tiny URL
	$tinyURL = curl_exec($cURL);  
	curl_close($cURL);  
	// return URL
	return $tinyURL;
 }