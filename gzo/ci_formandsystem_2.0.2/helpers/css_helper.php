<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Form&System css helper
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/css
 */

// ------------------------------------------------------------------------
/**
 * css - returns all assigned css files
 *
 * @param string files 'file, file2'
 * @param boolean
 * @return string
 */
function css($group = NULL, $compress = TRUE)
{
	$CI = &get_instance();
	// get compression settings
	$compression = $CI->config->item('compression');
	//
	if( ($compress !== TRUE && $compress !== FALSE) && isset($compression['css']))
	{
		if($compression['css'] === FALSE || $compression['css'] == "FALSE")
		{
			$compress = FALSE;			
		}
		else
		{
			$compress = TRUE;
		}
	}
	// return files
	return $CI->css->get($group, $compress);
}
// ------------------------------------------------------------------------
/**
 * css_add - add css files to class
 *
 * @param string files 'file, file2'
 */
function css_add(/*group, value, value, value = array()*/)
{
	$CI = &get_instance();
	// get args
	$args = func_get_args();
	// assign args to class
	$CI->css->add($args);
}
// ------------------------------------------------------------------------
/**
 * css_add_lines - add css to class
 *
 * @param string group
 * @param string value
 */
function css_add_lines(/*group, value*/)
{
	$CI = &get_instance();
	// get args
	$args = func_get_args();
	// assign args to class
	if(count($args) > 1)
	{
		$CI->css->add_lines($args[0], $args[1]);	
	}
	else
	{
		$CI->css->add_lines($args[0]);
	}
}
// ------------------------------------------------------------------------
/**
 * css_variables - add css var to class
 */
function css_variables(/*group, value, value, value = array()*/)
{
	$CI = &get_instance();
	// get args
	$args = func_get_args();
	if(count($args) == 1)
	{
		$args = $args[0];
	}
	// assign args to class
	$CI->css->variables($args);
}
/* End of file css_helper.php */
/* Location: ./system/helpers/css_helper.php */