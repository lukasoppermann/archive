<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Config Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/config
 */
// --------------------------------------------------------------------
/**
 * config - retrieves config item
 *
 * @param string 
 * @param string 
 * @return string
 */
function config($item, $slash = FALSE)
{
	$CI =& get_instance();
	// get item
	$item = $CI->config->item($item);
	// check if item exists
	if ( ! isset($item) || empty($item) )
	{
		return FALSE;
	}
	// return without slash
	elseif($slash == FALSE)
	{
		if( is_array($item) )
		{
			return $item;
		}
		return trim($item,'/');
	}
	// return with slash
	else
	{
		if( is_array($item) )
		{
			return $item;
		}
		return trim($item,'/').'/';
	}
}
// --------------------------------------------------------------------
/**
 * mobile - retrieves config item mobile, if item does not exist, checks for mobile
 *
 * @return boolean
 */
function mobile()
{
	$CI =& get_instance();
	$item = $CI->config->item('mobile');
	// check if item is true
	if(isset($item) && $item === TRUE)
	{
		return TRUE;
	}
	// check if item is set to false
	elseif(isset($item) && $item == 'false')
	{
		return FALSE;
	}
	// check for mobile
	else
	{
		// get user agent
		$user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : "not reported";
		// check user agent for common phones
		if(preg_match('/(iphone|ipod|android|opera mini|blackberry|webOS|windows phone os)/i', $user_agent))
		{
			// set mobile to true and return true
			$CI->config->set_item('mobile', TRUE);
			return TRUE;
		}
		// check for less common phones
		elseif(preg_match('/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine|iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile|mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i', $user_agent))
		{
			// set mobile to true and return true
			$CI->config->set_item('mobile', TRUE);
			return TRUE;
		}
		else
		{
			// set and return false
			$CI->config->set_item('mobile', 'false');
			return FALSE;
		}
	}
}
// --------------------------------------------------------------------
/**
 * iphone - retrieves config item iphone, if item does not exist, checks for iphone
 *
 * @return boolean
 */
function iphone()
{
	$CI =& get_instance();
	$item = $CI->config->item('iphone');
	// check if item exists & is true
	if( isset($item) && $item === TRUE )
	{
		return TRUE;
	}
	// check if item exists & is false
	elseif( isset($item) && $item == 'false' )
	{
		return FALSE;
	}
	// check for iphone in user agent
	else
	{
		// get user agent
		$user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : "not reported";
		// check for iphone
		if( strpos($user_agent, 'iphone') !== FALSE )
		{
			// set and return true
			$CI->config->set_item('iphone', TRUE);
			return TRUE;
		}
		else
		{
			// set and return false
			$CI->config->set_item('iphone', 'false');
			return FALSE;			
		}
	}
}
// --------------------------------------------------------------------
/**
 * ipad - retrieves config item ipad, if item does not exist, checks for ipad
 *
 * @return boolean
 */
function ipad()
{
	$CI =& get_instance();
	$item = $CI->config->item('ipad');
	// check if item exists & is true	
	if( isset($item) && $item === TRUE )
	{
		return TRUE;
	}
	// check if item exists & is false	
	elseif( isset($item) && $item == 'false' )
	{
		return FALSE;
	}
	// check for ipad in user agent
	else
	{
		// get user agent
		$user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : "not reported";
		// check for ipad
		if( strpos($user_agent, 'ipad') !== FALSE )
		{
			// set and return true
			$CI->config->set_item('ipad', TRUE);
			return TRUE;
		}
		else
		{
			// set and return false
			$CI->config->set_item('ipad', 'false');
			return FALSE;			
		}
	}
}
// --------------------------------------------------------------------
/**
 * os - retrieves config item os, if item does not exist, checks for os
 *
 * @return boolean
 */
function os()
{
	$CI =& get_instance();
	$item = $CI->config->item('os');
	// check if item exists & return	
	if(isset($item) && !empty($item))
	{
		return $item;
	}
	// check for os
	else
	{
		// get user agent
		$user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : "not reported";
		// check for operating systems
		if ( strpos($user_agent, "win") !== FALSE )
		{
			$CI->config->set_item('os','windows');
			return 'windows';
		}
		elseif ( strpos($user_agent, "mac") !== FALSE )
		{
			$CI->config->set_item('os','mac');
			return 'mac';
		}
		elseif ( strpos($user_agent, "linux") !== FALSE )
		{
			$CI->config->set_item('os','linux');
			return 'linux';
		}
		elseif ( strpos($user_agent, "os/2") !== FALSE )
		{
			$CI->config->set_item('os','os/2');
			return 'os/2';
		}
		elseif ( strpos($user_agent, "beos") !== FALSE )
		{
			$CI->config->set_item('os','beos');
			return 'beos';
		}
	}
}
// --------------------------------------------------------------------
/**
 * browser - retrieves config item browser, if item does not exist, checks for browser
 *
 * @return boolean
 */
function browser()
{
	$CI =& get_instance();
	$item = $CI->config->item('browser');
	// check if item exists
	if(isset($item) && !empty($item))
	{
		return $item;
	}
	else
	{	
		// get user agent
		$user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : "not reported";
		// check for browsers
		if( strpos($user_agent, "chrome") !== FALSE )
		{
			$CI->config->set_item('browser','chrome');
			return 'chrome';
		}
		elseif( strpos($user_agent, "firefox") !== FALSE )
		{
			$CI->config->set_item('browser','firefox');
			return 'firefox';			
		}
		elseif( strpos($user_agent, "safari") !== FALSE )
		{
			$CI->config->set_item('browser','safari');
			return 'safari';
		}
		elseif( strpos($user_agent, "msie") !== FALSE && strpos($user_agent, "opera") === FALSE )
		{
			$CI->config->set_item('browser','msie');
			return 'msie';
		}
		elseif( strpos($user_agent, "opera") !== FALSE )
		{
			$CI->config->set_item('browser','opera');
			return 'opera';			
		}
	}
}
// --------------------------------------------------------------------
/**
 * compression - returns compression data
 *
 * @param string 
 * @param string 
 * @return string
 */	
function compression($item = null, $type = 'html')
{
	$CI =& get_instance();
	return $CI->config->compression($item, $type);		
}
// --------------------------------------------------------------------
/**
 * user_right - returns user_right data
 *
 * @param string 
 * @param string 
 * @return string
 */	
function user_right($item = null, $right = null)
{
	$CI =& get_instance();
	return $CI->config->user_right($item, $right);
}
// --------------------------------------------------------------------
/**
 * user_group - returns user_group data
 *
 * @param string 
 * @param string 
 * @return string
 */	
function user_group($item = null, $group = null)
{
	$CI =& get_instance();
	return $CI->config->user_group($item, $group);	
}
// --------------------------------------------------------------------
/**
 * languages - returns language item
 *
 * @param string 
 * @param int 
 * @return string
 */	
function language($item = null, $language_id = null)
{
	$CI =& get_instance();
	return $CI->config->language($item, $language_id);	
}
/* End of file config_helper.php */
/* Location: ./system/helpers/config_helper.php */