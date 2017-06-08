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
function js($group = 'default', $compress = NULL, $data = FALSE)
{
	$CI = &get_instance();
	// check for compression argument
	if( $compress !== TRUE && $compress !== FALSE )
	{
		$config = $CI->config->item('compression');
		$compress = $config['js']['compression'];
	}
	$CI->fs_optimize->js->get($group, $compress, FALSE, $data);
	// return files
	return $CI->fs_optimize->js->get($group, $compress, FALSE, $data);
	
}
// ------------------------------------------------------------------------
/**
 * js_link - returns all assigned js files
 *
 * @param string files 'file, file2'
 * @param boolean
 * @return string
 */
 function js_link($group = 'default', $compress = TRUE, $link = TRUE)
 {
 	$CI = &get_instance();
	// check for compression argument
	if( $compress !== TRUE && $compress !== FALSE )
	{
		$config = $CI->config->item('compression');
		$compress = $config['js']['compression'];
	}
 	// return files
 	return $CI->fs_optimize->js->get($group, $compress, $link);
 }
// ------------------------------------------------------------------------
/**
 * js_add - add js files to class
 *
 * @param string | array 
 * @param string
 */
function js_add($files, $group = 'default')
{
	$CI = &get_instance();
	// assign files
	$CI->fs_optimize->js->add($files, $group);
}
// ------------------------------------------------------------------------
/**
 * js_add_lines - add js to class
 *
 * @param string
 * @param string
 */
function js_add_lines($lines, $group = 'default', $before = false)
{
	$CI = &get_instance();
	// assign lines
	$CI->fs_optimize->js->add_lines($lines, $group, $before);
}
// ------------------------------------------------------------------------
/**
 * js_variables - add js var to class
 * @param array('var_name' => 'value')
 */
function js_variables($variables)
{
	$CI = &get_instance();
	// assign variables
	$CI->fs_optimize->js->variables($variables);
}
/* End of file js_helper.php */
/* Location: ./system/helpers/js_helper.php */