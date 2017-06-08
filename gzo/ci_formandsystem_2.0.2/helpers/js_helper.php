<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Form&System Javascript helper
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/js
 */

// ------------------------------------------------------------------------
/**
 * js - returns all assigned js files
 *
 * @param string files 'file, file2'
 * @param boolean
 * @return string
 */
function js($group = NULL, $compress = NULL)
{
	$CI = &get_instance();
	// get compression settings
	$compression = $CI->config->item('compression');
	//
	if( ($compress !== TRUE && $compress !== FALSE) && isset($compression['js']))
	{
		if($compression['js'] === FALSE || $compression['js'] == "FALSE")
		{
			$compress = FALSE;			
		}
		else
		{
			$compress = TRUE;
		}
	}
	// return files
	return $CI->js->get($group, $compress);
	
}
// ------------------------------------------------------------------------
/**
 * js_add - add js files to class
 *
 * @param string files 'file, file2'
 */
function js_add(/*group, value, value, value = array()*/)
{
	$CI = &get_instance();
	// get args
	$args = func_get_args();
	// assign args to class
	$CI->js->add($args);
}
// ------------------------------------------------------------------------
/**
 * js_add_lines - add js to class
 *
 * @param string group
 * @param string value
 */
function js_add_lines(/*group, value*/)
{
	$CI = &get_instance();
	// get args
	$args = func_get_args();
	// assign args to class
	if(count($args) > 1)
	{
		$CI->js->add_lines($args[0], $args[1]);	
	}
	else
	{
		$CI->js->add_lines($args[0]);
	}
}
// ------------------------------------------------------------------------
/**
 * js_variables - add js var to class
 */
function js_variables(/*group, value, value, value = array()*/)
{
	$CI = &get_instance();
	// get args
	$args = func_get_args();
	if(count($args) == 1)
	{
		$args = $args[0];
	}
	// assign args to class
	$CI->js->variables($args);
}
/* End of file js_helper.php */
/* Location: ./system/helpers/js_helper.php */